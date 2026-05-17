<?php

namespace App\Http\Controllers;

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



        /**
         * =====================================================
         * HANDLE SUCCESSFUL PAYMENT
         * =====================================================
         */
        if ($gatewayStatus === 'successful') {

            DB::transaction(function () use (
                $transaction,
                $gatewayData,
                $payload
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
