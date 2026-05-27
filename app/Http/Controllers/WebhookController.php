<?php

namespace App\Http\Controllers;

use App\Mail\GeneralMail;
use App\Models\ApiResponse;
use App\Models\Level;
use App\Models\Partner;
use App\Models\PartnerSlot;
use App\Models\Transaction;
use App\Models\Webhook;
use App\Services\TransactionService;
use App\Services\UpgradeSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{

    protected TransactionService $transactionService;
    protected UpgradeSubscriptionService $upgradeSubscriptionService;

    public function __construct(TransactionService $transactionService, UpgradeSubscriptionService $upgradeSubscriptionService)
    {
        $this->transactionService = $transactionService;
        $this->upgradeSubscriptionService = $upgradeSubscriptionService;
    }

    public function korapay(Request $request)
    {
        /**
         * =========================================================
         * STEP 1: VERIFY SIGNATURE
         * =========================================================
         */

        // $signature = $request->header('X-Korapay-Signature');

        // if (!$signature) {
        //     Log::warning('Kora webhook missing signature');

        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Unauthorized'
        //     ], Response::HTTP_UNAUTHORIZED);
        // }

        // $secret = config('services.korapay.webhook_secret');

        // $computedSignature = hash_hmac(
        //     'sha256',
        //     $request->getContent(),
        //     $secret
        // );

        // if (!hash_equals($computedSignature, $signature)) {
        //     Log::warning('Invalid Kora webhook signature');

        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Invalid signature'
        //     ], Response::HTTP_UNAUTHORIZED);
        // }

        /**
         * =========================================================
         * STEP 2: VALIDATE PAYLOAD STRUCTURE
         * =========================================================
         */

        $payload = $request->validate([
            'event' => 'required|string',
            'data.reference' => 'required|string',
            'data.currency' => 'required|string',
            'data.amount' => 'required|numeric',
            'data.status' => 'required|string',
            'data.payment_method' => 'nullable|string',
            'data.transaction_date' => 'nullable|string',
        ]);

        $event = $payload['event'];

        /**
         * =========================================================
         * STEP 3: ONLY PROCESS SUPPORTED EVENTS
         * =========================================================
         */

        $supportedEvents = [
            'charge.success',
        ];

        if (!in_array($event, $supportedEvents, true)) {

            Log::info("Ignoring unsupported Kora event: {$event}");

            return response()->json([
                'status' => 'ignored'
            ], Response::HTTP_OK);
        }

        $reference = $payload['data']['reference'];

          Mail::to('solotob3@gmail.com')
                        ->send(
                            new GeneralMail(
                                (object)[
                                    'name' => 'Admin',
                                    'email' => 'solotob3@gmail.com'
                                ],
                                'Kora Webhook Received',
                                "Webhook {$event} received successfully for {$reference}"
                            )
                        );

        /**
         * =========================================================
         * STEP 4: PREVENT REPLAY ATTACKS / DUPLICATE EVENTS
         * =========================================================
         */

        // $alreadyProcessed = WebhookEvent::query()
        //     ->where('provider', 'korapay')
        //     ->where('event_reference', $reference)
        //     ->where('event_type', $event)
        //     ->exists();

        // if ($alreadyProcessed) {

        //     Log::info("Duplicate webhook ignored: {$reference}");

        //     return response()->json([
        //         'status' => 'duplicate'
        //     ], Response::HTTP_OK);
        // }

        /**
         * =========================================================
         * STEP 5: STORE RAW PAYLOAD FOR AUDIT
         * =========================================================
         */

        ApiResponse::create([
            'provider' => 'korapay',
            'response' => $payload,
        ]);

        try {

            DB::transaction(function () use (
                $payload,
                $reference,
                $event
            ) {

                  Mail::to('solotob3@gmail.com')
                        ->send(
                            new GeneralMail(
                                (object)[
                                    'name' => 'Admin',
                                    'email' => 'solotob3@gmail.com'
                                ],
                                'Kora Webhook Transaction Started',
                                "Webhook {$event} started successfully for {$reference}"
                            )
                        );

                /**
                 * =====================================================
                 * LOCK TRANSACTION ROW
                 * =====================================================
                 */

                $transaction = Transaction::query()
                    ->where('ref', $reference)
                    ->lockForUpdate()
                    ->first();

                if (!$transaction) {

                    Log::error("Transaction not found: {$reference}");

                    throw new \Exception('Transaction not found');
                }

                /**
                 * =====================================================
                 * IDEMPOTENCY CHECK
                 * =====================================================
                 */

                if ($transaction->status === 'successful') {

                    Log::info("Transaction already processed: {$reference}");

                    return;
                }

                /**
                 * =====================================================
                 * VERIFY PAYMENT DETAILS
                 * =====================================================
                 */

                $incomingAmount = round(
                    $payload['data']['amount'] / 100,
                    2
                );

                $incomingCurrency = strtoupper(
                    $payload['data']['currency']
                );

                if (
                    round($transaction->amount, 2) !== $incomingAmount
                ) {

                    Log::critical(
                        "Amount mismatch for transaction {$reference}"
                    );

                    throw new \Exception('Amount mismatch');
                }

                if (
                    strtoupper($transaction->currency)
                    !==
                    $incomingCurrency
                ) {

                    Log::critical(
                        "Currency mismatch for transaction {$reference}"
                    );

                    throw new \Exception('Currency mismatch');
                }



                /**
                 * =====================================================
                 * VERIFY PAYMENT STATUS
                 * =====================================================
                 */

                if (
                    $payload['data']['status'] !== 'success'
                ) {

                        Log::critical(
                            "Payment not successful for transaction {$reference}"
                        );
    
                        $this->transactionService->markFailed($transaction, $payload);

                    // $transaction->update([
                    //     'status' => 'failed',
                    //     'gateway_response' => $payload,
                    // ]);

                    return;
                }

                  Mail::to('solotob3@gmail.com')
                        ->send(
                            new GeneralMail(
                                (object)[
                                    'name' => 'Admin',
                                    'email' => 'solotob3@gmail.com'
                                ],
                                'Kora Webhook Payment Status Verified',
                                "Webhook {$event} payment status verified successfully for {$reference}"
                            )
                        );

                /**
                 * =====================================================
                 * FETCH SUBSCRIPTION LEVEL
                 * =====================================================
                 */

                $levelId = data_get(
                    $transaction->meta,
                    'level_id'
                );

                $level = Level::find($levelId);

                if (!$level) {

                    Log::critical(
                        "Level not found for transaction {$reference}"
                    );

                    throw new \Exception('Subscription level missing');
                }

                  Mail::to('solotob3@gmail.com')
                        ->send(
                            new GeneralMail(
                                (object)[
                                    'name' => 'Admin',
                                    'email' => 'solotob3@gmail.com'
                                ],
                                'Kora Webhook Level Fetched Successfully',
                                "Webhook {$event} level fetched successfully for {$reference}"
                            )
                        );

                /**
                 * =====================================================
                 * PERFORM SUBSCRIPTION UPGRADE
                 * =====================================================
                 */

                $this->upgradeSubscriptionService
                    ->upgradeSubscription(
                        $transaction->user,
                        $level,
                        $transaction,
                        $payload
                    );

              
                /**
                 * =====================================================
                 * OPTIONAL ASYNC ALERTING
                 * =====================================================
                 */

                // dispatch(function () use ($reference, $event) {

                    Mail::to('solotob3@gmail.com')
                        ->send(
                            new GeneralMail(
                                (object)[
                                    'name' => 'Admin',
                                    'email' => 'solotob3@gmail.com'
                                ],
                                'Kora Webhook Processed',
                                "Webhook {$event} processed successfully for {$reference}"
                            )
                        );

                // })->afterCommit();

            }, 3);

        } catch (\Throwable $e) {

            Log::critical('Kora webhook processing failed', [
                'reference' => $reference,
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

              Mail::to('solotob3@gmail.com')
                        ->send(
                            new GeneralMail(
                                (object)[
                                    'name' => 'Admin - Failed Webhook Alert',
                                    'email' => 'solotob3@gmail.com'
                                ],
                                'Kora Webhook Process failed',
                                "Webhook {$event} processed failed for {$reference}"
                            )
                        );


            return response()->json([
                'status' => 'error',
                'message' => 'Webhook processing failed'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'status' => 'success'
        ], Response::HTTP_OK);
    }



    // public function korapay(Request $request)
    // {
    //     $payload = $request->all();
    //     $event = $request['event'];

    //     ApiResponse::create(['response' => $request]); //store the webhook payload for debugging and auditing purposes

    //     if ($event == 'charge.success') {
    //         // Handle successful charge event

    //         $amount = $request['data']['amount'] / 100;
    //         $status = $request['data']['status'];
    //         $reference = $request['data']['reference'];
    //         $channel = $request['data']['payment_method'];
    //         $currency = $request['data']['currency'];

    //         //fetch payment information from the database using the reference
    //         $transaction = Transaction::query()
    //             ->where('ref', $reference)
    //             ->lockForUpdate()
    //             ->first();


    //         if (!$transaction) {
    //             Log::error('Transaction not found for reference: ' . $reference);
    //             return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
    //         }

    //         //verify amount and currency match
    //         if (round($transaction->amount, 2) != round($amount, 2)) {
    //             Log::error('Amount mismatch for reference: ' . $reference);
    //             return response()->json(['status' => 'error', 'message' => 'Amount mismatch'], 400);
    //         }

    //         if (strtoupper($transaction->currency) != strtoupper($currency)) {
    //             Log::error('Currency mismatch for reference: ' . $reference);
    //             return response()->json(['status' => 'error', 'message' => 'Currency mismatch'], 400);
    //         }

    //         $subject = 'Webhook Received: Kora Pay Security Checks Passed';
    //         $content = "Security checks passed for event: {$request['event']}. Transaction ref: {$reference} is being processed.";


    //         Mail::to('solotob3@gmail.com')
    //             ->send(new GeneralMail(
    //                 (object)[
    //                     'name' => 'Oluwatobi Solomon',
    //                     'email' => 'solotob3@gmail.com'
    //                 ],
    //                 $subject,
    //                 $content
    //             ));


    //         if ($status === 'success') {

    //             DB::transaction(function () use ($transaction, $payload, $event, $reference) {

    //                 /**
    //                  * Refresh transaction
    //                  */
    //                 $transaction->refresh();

    //                 /**
    //                  * Double-check idempotency
    //                  */
    //                 if (
    //                     $transaction->status === 'successful'
    //                 ) {
    //                     return;
    //                 }

    //                 $level = Level::where('id', $transaction->meta['level_id'])
    //                     ->first();

    //                 //mark transaction successful now and then perform subscription upgrade in the service to ensure atomicity and prevent issues with failed upgrades after marking transaction successful
    //                 $this->upgradeSubscriptionService->upgradeSubscription(
    //                     $transaction->user,
    //                     $level,
    //                     $transaction,
    //                     $payload
    //                 );

    //                 $subject = 'Webhook Received: Upgrade Processed Successfully';
    //                 $content = "Upgrade processed successfully for event: {$event}. Transaction ref: {$reference} has been marked successful and subscription upgraded.";


    //                 Mail::to('solotob3@gmail.com')
    //                     ->send(new GeneralMail(
    //                         (object)[
    //                             'name' => 'Oluwatobi Solomon',
    //                             'email' => 'solotob3@gmail.com'
    //                         ],
    //                         $subject,
    //                         $content
    //                     ));



    //                 return response()->json(['status' => 'success'], 200);
    //             });
    //             return response()->json(['status' => 'success'], 200);
    //         } else {
    //             // Handle failed payment or other statuses if needed
    //             $this->transactionService->markFailed($transaction, $payload);
    //             return response()->json(['status' => 'error', 'message' => 'Payment failed'], 400);
    //         }
    //     } else {
    //         // Handle other events or ignore
    //         return response()->json(['status' => 'error'], 500);
    //     }

    //     /**
    //      * =====================================================
    //      * HANDLE FAILED PAYMENTS
    //      * =====================================================
    //      */
    //     $transaction->update([
    //         'status' => $gatewayStatus,
    //         'meta'
    //         => $payload,
    //     ]);

    //     $this->transactionService->markFailed($transaction, $payload);

    //     return response(
    //         'Webhook processed',
    //         200
    //     );
    // }


    public function handle(Request $request)
    {

        $event = $request['event'];

        ApiResponse::create(['response' => $request]);

        if ($event == 'charge.success') {
            $amount = $request['data']['amount'] / 100;
            $status = $request['data']['status'];
            $reference = $request['data']['reference'];
            $channel = $request['data']['channel'];
            $currency = $request['data']['currency'];
            $email = $request['data']['customer']['email'];
            $customer_code = $request['data']['customer']['customer_code'];

            $dollarEqv = $amount / 1500; //conver to dollar using base rate of 1500
            //fetch partner information
            $partner = Partner::where('customer_code', $customer_code)->first();
            $partner->balance_dollar += number_format($dollarEqv, 1);
            $partner->balance_naira += $amount;
            $partner->save();
            // '25f5b37e-aff2-44df-9c21-3ad565df09db',
            Transaction::create([
                'user_id' => $partner->user_id,
                'ref' => $reference,
                'amount' => $amount,
                'currency' => $currency,
                'status' =>  $status,
                'type' => 'partner_wallet_topup',
                'action' => 'Credit',
                'description' => $partner->user->name . ' topped up partner wallet',
                'meta' => $event,
                'customer' => null
            ]);
            return response()->json(['status' => 'success'], 200);
        } else {

            return response()->json(['status' => 'error'], 500);
        }


        // if($event == 'charge.completed'){

        //     Webhook::create(['event' => $event, 'content' => $request]);
        //     $transactionRef = $request['data']['tx_ref']; //get tx ref from webhook

        //     //validate transaction
        //     $transaction = Transaction::where('ref', $transactionRef)->first();

        //     $string = $transaction->meta;
        //     $data = json_decode($string, true);
        //     $package = htmlspecialchars($data['package']);
        //     $slotNumber = htmlspecialchars($data['number_of_slot']);

        //     //get per info
        //     $partnerId = @$transaction->user->partner->id;
        //     $partner = PartnerSlot::where('partner_id', $partnerId)->first();


        //     if($partner->status == true){
        //         if($package == 'Influencer'){
        //             $partner->influencer += $slotNumber;
        //             $partner->save();

        //             $transaction->status = 'allocated';
        //             $transaction->save();

        //             // return response()->json(['status' => 'success']);

        //             // $this->updateTx($request->tx_ref);

        //             // return back()->with('success', 'Influencer slot Updated successfully');

        //         } elseif($package == 'Creator'){
        //             $partner->creator += $slotNumber;
        //             $partner->save();

        //             $transaction->status = 'allocated';
        //             $transaction->save();

        //             // return response()->json(['status' => 'success']);

        //             // $this->updateTx($request->tx_ref);

        //             // return back()->with('success', 'Creator slot Updated successfully');
        //         }else{

        //             $partner->beginner += $slotNumber;
        //             $partner->save();

        //             $transaction->status = 'allocated';
        //             $transaction->save();

        //             // return response()->json(['status' => 'success']);

        //             // $this->updateTx($request->tx_ref);

        //             // return back()->with('success', 'Beginner slot Updated successfully');

        //         }
        //     }



        // }


    }

    public function flutterwave(Request $request)
    {

        $signature = $request->header('verif-hash');

        /**
         * Secret Hash from Flutterwave Dashboard
         */
        $secretHash = config(
            'services.env.flutterwave_webhook_hash'
        );

        /**
         * Reject invalid requests
         */
        if (
            !$signature ||
            !hash_equals($secretHash, $signature)
        ) {

            Log::warning(
                'Invalid Flutterwave webhook signature',
                [
                    'ip' => $request->ip(),
                    'signature' => $signature,
                ]
            );

            abort(401, 'Invalid signature.');
        }

        $subject = 'Webhook Received: Secure Signature Verified';
        $content = "Signature verification successful for event: {$request['event']}";


        Mail::to('solotob3@gmail.com')
            ->send(new GeneralMail(
                (object)[
                    'name' => 'Oluwatobi Solomon',
                    'email' => 'solotob3@gmail.com'
                ],
                $subject,
                $content
            ));





        $event = $request['event'];

        ApiResponse::create(['response' => $request]);

        $payload = $request->all();

        Log::info('Flutterwave webhook received', [
            'payload' => $payload,
        ]);

        /**
         * =====================================================
         * EVENT TYPE
         * =====================================================
         */
        $event = data_get($payload, 'event');

        /**
         * Example:
         * charge.completed
         */
        if ($event !== 'charge.completed') {

            return response(
                'Webhook ignored',
                200
            );
        }


        $gatewayData = data_get($payload, 'data');

        $txRef = data_get($gatewayData, 'tx_ref');

        $gatewayStatus = strtolower(
            data_get($gatewayData, 'status')
        );

        $gatewayAmount = (float) data_get(
            $gatewayData,
            'amount'
        );

        $gatewayCurrency = strtoupper(
            data_get($gatewayData, 'currency')
        );


        $transaction = Transaction::query()
            ->where('ref', $txRef)
            ->lockForUpdate()
            ->first();

        if (!$transaction) {

            Log::critical(
                'Flutterwave webhook transaction not found',
                [
                    'tx_ref' => $txRef,
                ]
            );

            return response(
                'Transaction not found',
                404
            );
        }

        if ($transaction->status === 'successful') {

            return response(
                'Already processed',
                200
            );
        }


        if (
            round($transaction->amount, 2)
            !== round($gatewayAmount, 2)
        ) {

            Log::critical(
                'Flutterwave webhook amount mismatch',
                [
                    'tx_ref' => $txRef,
                    'local_amount' => $transaction->amount,
                    'gateway_amount' => $gatewayAmount,
                ]
            );

            $transaction->update([
                'status' => 'flagged',
                'meta'
                => $payload,
            ]);

            return response(
                'Amount mismatch',
                400
            );
        }



        if (
            strtoupper($transaction->currency)
            !== $gatewayCurrency
        ) {

            Log::critical(
                'Flutterwave webhook currency mismatch',
                [
                    'tx_ref' => $txRef,
                    'local_currency' => $transaction->currency,
                    'gateway_currency' => $gatewayCurrency,
                ]
            );

            $transaction->update([
                'status' => 'flagged',
                'meta'
                => $payload,
            ]);

            return response(
                'Currency mismatch',
                400
            );
        }

        $subject = 'Webhook Received: Security Checks Passed';
        $content = "Security checks passed for event: {$request['event']}. Transaction ref: {$txRef} is being processed.";


        Mail::to('solotob3@gmail.com')
            ->send(new GeneralMail(
                (object)[
                    'name' => 'Oluwatobi Solomon',
                    'email' => 'solotob3@gmail.com'
                ],
                $subject,
                $content
            ));



        /**
         * =====================================================
         * HANDLE SUCCESSFUL PAYMENT
         * =====================================================
         */
        if ($gatewayStatus === 'successful') {

            DB::transaction(function () use (
                $transaction,
                $gatewayData,
                $payload,
                $txRef,
                $event
            ) {

                /**
                 * Refresh transaction
                 */
                $transaction->refresh();

                /**
                 * Double-check idempotency
                 */
                if (
                    $transaction->status === 'successful'
                ) {
                    return;
                }

                //perform the subscription upgrade

                $level = Level::where('id', $transaction->meta['level_id'])
                    ->first();

                //mark transaction successful now and then perform subscription upgrade in the service to ensure atomicity and prevent issues with failed upgrades after marking transaction successful
                $this->upgradeSubscriptionService->upgradeSubscription(
                    $transaction->user,
                    $level,
                    $transaction,
                    $payload
                );

                $subject = 'Webhook Received: Upgrade Processed Successfully';
                $content = "Upgrade processed successfully for event: {$event}. Transaction ref: {$txRef} has been marked successful and subscription upgraded.";


                Mail::to('solotob3@gmail.com')
                    ->send(new GeneralMail(
                        (object)[
                            'name' => 'Oluwatobi Solomon',
                            'email' => 'solotob3@gmail.com'
                        ],
                        $subject,
                        $content
                    ));

                // event(new PaymentSuccessful($transaction));
            });

            Log::info(
                'Flutterwave webhook processed successfully',
                [
                    'tx_ref' => $txRef,
                ]
            );

            return response(
                'Webhook processed',
                200
            );
        }

        /**
         * =====================================================
         * HANDLE FAILED PAYMENTS
         * =====================================================
         */
        $transaction->update([
            'status' => $gatewayStatus,
            'meta'
            => $payload,
        ]);

        $this->transactionService->markFailed($transaction, $payload);

        return response(
            'Webhook processed',
            200
        );
    }
}
