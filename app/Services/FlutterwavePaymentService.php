<?php

namespace App\Services;

use App\Models\Level;
use App\Models\Transaction;
use App\Models\UserPaymentPlan;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class FlutterwavePaymentService
{

    private string $baseUrl;
    private string $secretKey;
    public TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->baseUrl = rtrim(config('services.env.flutterwave_base_url'), '/');
        $this->secretKey = config('services.env.flutterwave_secret_key');
        $this->transactionService = $transactionService;
    }

    public function getPaymentPlan($user, $levelId, $currency)
    {
        $createdUserPaymentPlan = UserPaymentPlan::query()
            ->where('level_id', $levelId)
            ->where('user_id', $user->id)
            ->where('currency', $currency)
            ->where('payment_gateway', 'flutterwave')
            ->latest()
            ->first(); //->payment_plan_id;

        if ($createdUserPaymentPlan) {
            //validate level id
            $level = Level::find($levelId);
            if (!$level) {
                Log::warning('Level not found for payment plan', [
                    'level_id' => $levelId,
                ]);
                return null;
            }

            if ($level) {
                $this->createPaymentPlan($levelId);
            }

            return $createdUserPaymentPlan->payment_plan_id;
        }
    }

    private function amount($levelId)
    {

        $level = Level::findOrFail($levelId);
        $user = auth()->user();
        $userCurrency = userBaseCurrency();

        if (!$userCurrency) {
            $userCurrency = $user->wallet->currency;
        }
        if ($userCurrency == 'NGN') {

            $discountRate = 0.1; // 10% discount for recurring subscription
            $discountedAmount =  $level->amount * (1 - $discountRate);
        } else {
            $discountRate = 0.0; // 10% discount for recurring subscription
            $discountedAmount =  $level->amount * (1 - $discountRate);
        }
        return $discountedAmount;
    }

    public function createPaymentPlan($levelId)
    {
        $level = Level::findOrFail($levelId);

        $user = auth()->user();

        $userCurrency = userBaseCurrency();
        if (!$userCurrency) {
            $userCurrency = $user->wallet->currency;
        }
        if ($userCurrency == 'NGN') {

            $discountRate = 0.1; // 10% discount for recurring subscription
            $discountedAmount =  $level->amount * (1 - $discountRate);
        } else {
            $discountRate = 0.0; // 10% discount for recurring subscription
            $discountedAmount =  $level->amount * (1 - $discountRate);
        }

        $payload = [
            'name' => "Subscription Plan for Level {$level->name}",
            'amount' => convertToBaseCurrency($this->amount($levelId),  $userCurrency) * 100, // amount in kobo
            'currency' => $userCurrency,
            'interval' => 'monthly',
            'duration' => 24, // 2 years
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/payment-plans", $payload);

        if (!$response->successful()) {
            Log::error('Flutterwave payment plan creation failed', [
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to create payment plan. Please try again.');
        }

        $responseData = $response->json();

        Log::info('Flutterwave payment plan creation successful', [
            'response' => $responseData,
        ]);

        UserPaymentPlan::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'level_id' => $level->id,
            'name' => data_get($responseData, 'data.name'),
            'payment_plan_id' => data_get($responseData, 'data.id'),
            'amount' => data_get($responseData, 'data.amount') / 100, //$payload['amount'] / 100, // convert back to main currency unit
            'currency' => data_get($responseData, 'data.currency'),
            'interval' => data_get($responseData, 'data.interval'),
            'duration' => data_get($responseData, 'data.duration'),
            'status' => data_get($responseData, 'data.status'),
            'payment_gateway' => 'flutterwave',
            'payment_plan_token' => data_get($responseData, 'data.plan_token'),
        ]);

        return data_get($responseData, 'data.id');
    }



    public function createCharge($levelId, ?string $paymentPlanId)
    {
        return DB::transaction(function () use ($levelId, $paymentPlanId) {

            $level = Level::findOrFail($levelId);

            $user = auth()->user();

            $userCurrency = $user->wallet->currency;

            /**
             * =========================================================
             * IDEMPOTENCY
             * =========================================================
             */
            $idempotencyKey = Str::uuid()->toString();

            $existingTransaction = Transaction::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existingTransaction) {

                return [
                    'success' => true,
                    'message' => 'Existing transaction returned',
                    'data' => $existingTransaction,
                ];
            }

            /**
             * =========================================================
             * PAYMENT DETAILS
             * =========================================================
             */
            // $discountRate = 0.1; // 10% discount for recurring subscription
            // $discountedAmount =  $level->amount * (1 - $discountRate);
            
            $amount = convertToBaseCurrency(
                $this->amount($levelId), // $level->amount,
                $userCurrency
            );

            $reference = generateTransactionRef();

            /**
             * =========================================================
             * STORE TRANSACTION
             * =========================================================
             */
            $transaction = $this->transactionService->createTransaction(
                user: $user,
                idempotencyKey: $idempotencyKey,
                provider: 'flutterwave',
                reference: $reference,
                amount: $amount,
                currency: $userCurrency,
                status: 'initiated',
                action: 'Debit',
                type: 'subscription_upgrade',
                description: $user->name . ' upgrade to ' . $level->name,
                meta: [
                    'level_id' => $level->id,
                ]
            );

            /**
             * =========================================================
             * FLUTTERWAVE PAYMENTS API PAYLOAD
             * =========================================================
             */
            $payload = [
                'tx_ref' => $reference,

                'amount' => $amount,

                'currency' => $userCurrency,

                'redirect_url' => route(
                    'verify.flutterwave.charge'
                ),

                'customer' => [
                    'email' => $user->email,
                    'phone_number' => null,
                    'name' => $user->name,
                ],

                'customizations' => [
                    'title' => "Upgrade to Level {$level->name}",
                    'logo' => '', //asset('logo.png'),
                ],

                'configuration' => [
                    'session_duration' => 30,
                ],

                "payment_plan" => $paymentPlanId,

                'max_retry_attempt' => 5,

                /**
                 * Payment Channels
                 */
                // 'payment_options' => 'card, ussd, mobilemoneyghana',

                'payment_options' => implode(',', [
                    'card',
                    'banktransfer',
                    'ussd',
                    'mobilemoneyghana',
                    'mobilemoneytanzania',
                    'mobilemoneyzambia',
                    'mobilemoneyrwanda',
                    'mobilemoneyuganda',
                    'mpesa',
                    'opay',
                    'paypal',
                    'googlepay',
                    'applepay'
                ]),

                /**
                 * Optional Payment Plan
                 */
                // 'payment_plan' => 3807,

                /**
                 * Link Expiration
                 */
                'link_expiration' => now()
                    ->addMinutes(30)
                    ->toIso8601String(),

                /**
                 * Additional Metadata
                 */
                'meta' => [
                    'transaction_id' => $transaction->id,
                    'level_id' => $level->id,
                    'user_id' => $user->id,
                    'type' => 'subscription_upgrade',
                ],
            ];

            /**
             * =========================================================
             * HTTP REQUEST
             * =========================================================
             */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Idempotency-Key' => $idempotencyKey,
            ])
                ->timeout(30)
                ->connectTimeout(10)
                ->retry(3, 1000)
                ->post(
                    "{$this->baseUrl}/payments",
                    $payload
                );

            /**
             * =========================================================
             * HANDLE FAILURE
             * =========================================================
             */
            if (!$response->successful()) {

                Log::error('Flutterwave payment initialization failed', [
                    'payload' => $payload,
                    'response' => $response->json(),
                ]);

                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => $response->json(),
                ]);

                throw new \Exception(
                    data_get(
                        $response->json(),
                        'message',
                        'Failed to initialize payment.'
                    )
                );
            }

            /**
             * =========================================================
             * RESPONSE
             * =========================================================
             */
            $responseData = $response->json();

            Log::info('Flutterwave payment initialization successful', [
                'response' => $responseData,
            ]);

            /**
             * =========================================================
             * STORE PAYMENT LINK + GATEWAY RESPONSE
             * =========================================================
             */
            $transaction->update([
                'status' => 'initiated',
                'payment_link' => data_get($responseData, 'data.link'),
                'gateway_response' => $responseData,
            ]);

            /**
             * =========================================================
             * RETURN PAYMENT LINK
             * =========================================================
             */
            $paymentLink = data_get($responseData, 'data.link');

            if (!$paymentLink) {

                throw new \Exception(
                    'Flutterwave payment link missing.'
                );
            }

            return $paymentLink;
        });
    }

    public function verifyTransaction(string $txRef)
    {

        $transaction = Transaction::query()
            ->where('ref', $txRef)
            ->lockForUpdate()
            ->first();

        if (!$transaction) {

            Log::warning('Transaction not found locally', [
                'tx_ref' => $txRef,
            ]);

            throw new \Exception('Transaction not found.');
        }

        if ($transaction->status === 'successful') {

            return [
                'success' => true,
                'message' => 'Transaction already verified.',
                'data' => $transaction,
            ];
        }


        try {

            //call flutterwave api
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
                ->timeout(30)
                ->connectTimeout(10)

                /**
                 * Retry:
                 * - Connection failures
                 * - Temporary 5xx server errors
                 */
                ->retry(
                    3,
                    1000,
                    function ($exception, $request) {

                        if (
                            $exception instanceof ConnectionException
                        ) {
                            return true;
                        }

                        if (
                            method_exists(
                                $exception,
                                'response'
                            )
                        ) {

                            return optional(
                                $exception->response
                            )->serverError();
                        }

                        return false;
                    }
                )
                ->get(
                    "{$this->baseUrl}/transactions/verify_by_reference",
                    [
                        'tx_ref' => $txRef,
                    ]
                );

            //handle response

            if (!$response->successful()) {

                Log::error(
                    'Flutterwave verification failed',
                    [
                        'tx_ref' => $txRef,
                        'response' => $response->json(),
                    ]
                );

                $transaction->update([
                    'meta' => $response->json(),
                ]);

                throw new \Exception(
                    data_get(
                        $response->json(),
                        'message',
                        'Unable to verify payment.'
                    )
                );
            }

            $responseData = $response->json();

            //security validation

            $gatewayStatus = strtolower(
                data_get($responseData, 'data.status', '')
            );

            $gatewayAmount = (float) data_get(
                $responseData,
                'data.amount',
                0
            );

            $gatewayCurrency = strtoupper(
                data_get($responseData, 'data.currency', '')
            );

            $gatewayTxRef = data_get(
                $responseData,
                'data.tx_ref'
            );

            //prevent transaction alteration

            if ($gatewayTxRef !== $transaction->ref) {

                Log::critical(
                    'Transaction reference mismatch',
                    [
                        'local_reference' => $transaction->ref,
                        'gateway_reference' => $gatewayTxRef,
                    ]
                );

                throw new \Exception(
                    'Transaction reference mismatch.'
                );
            }

            /**
             * Prevent amount manipulation
             */
            if (
                round($gatewayAmount, 2)
                !== round($transaction->amount, 2)
            ) {

                Log::critical(
                    'Transaction amount mismatch',
                    [
                        'local_amount' => $transaction->amount,
                        'gateway_amount' => $gatewayAmount,
                    ]
                );

                throw new \Exception(
                    'Transaction amount mismatch.'
                );
            }


            if (
                strtoupper($transaction->currency)
                !== $gatewayCurrency
            ) {

                Log::critical(
                    'Transaction currency mismatch',
                    [
                        'local_currency' => $transaction->currency,
                        'gateway_currency' => $gatewayCurrency,
                    ]
                );

                throw new \Exception(
                    'Transaction currency mismatch.'
                );
            }


            //handle successful transaction


            if ($gatewayStatus === 'successful') {

                DB::transaction(function () use (
                    $transaction,
                    $responseData
                ) {

                    /**
                     * Prevent race conditions
                     */
                    $transaction->refresh();

                    if (
                        $transaction->status === 'successful'
                    ) {
                        return;
                    }

                    $this->transactionService->markProcessing($transaction, $responseData);

                    /**
                     * =================================================
                     * BUSINESS LOGIC
                     * =================================================
                     * - Upgrade subscription
                     * - Credit wallet
                     * - Activate order
                     * - Send notification
                     * - Dispatch jobs/events
                     * =================================================
                     */
                });

                Log::info(
                    'Flutterwave transaction verified successfully',
                    [
                        'tx_ref' => $txRef,
                    ]
                );

                return [
                    'success' => true,
                    'status' => 'successful',
                    'message' => 'Payment verified successfully.',
                    'data' => $transaction->fresh(),
                ];
            }


            //handle failed transaction

            $this->transactionService->markFailed($transaction, $responseData);

            return [
                'success' => false,
                'message' => 'Payment not successful.',
                'status' => $gatewayStatus,
            ];
        } catch (Throwable $e) {

            Log::error(
                'Flutterwave verification exception',
                [
                    'tx_ref' => $txRef,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            throw $e;
        }
    }

    public function createAdminCharge($levelId) //relatively decomissioned, but can be used for testing and admin initiated charges
    {

        return DB::transaction(function () use ($levelId) {

            $level = Level::findOrFail($levelId);

            if (!$level) {
                throw new \Exception('Invalid subscription level');
            }


            $user = auth()->user();
            $userCurrency = $user->wallet->currency;

            $idempotencyKey = $data['idempotency_key']
                ?? Str::uuid()->toString();

            $existingTransaction = Transaction::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existingTransaction) {

                return [
                    'success' => true,
                    'message' => 'Existing transaction returned',
                    'data' => $existingTransaction,
                ];
            }

            $amount = 200; //convertToBaseCurrency($level->amount, $userCurrency);

            $reference = generateTransactionRef();

            // Create transaction
            $transaction = $this->transactionService->createTransaction(
                user: $user,
                idempotencyKey: $idempotencyKey,
                provider: 'flutterwave',
                reference: $reference,
                amount: $amount,
                currency: $userCurrency,
                status: 'pending',
                action: 'Debit',
                type: 'subscription_upgrade',
                description: $user->name . ' upgrade to ' . $level->name,
                meta: [
                    'level_id' => $level->id,
                ]
            );

            // $payload = [
            //     'tx_ref' => $reference,
            //     'amount' => $amount,
            //     'currency' => $userCurrency,
            //     "phone_number" => "054709929220",
            //     "network" => "MTN",
            //     'email' => $user->email,
            //     'redirect_url' => '', //route('verify.flutterwave.subscription.payment'),
            //     'customer' => [
            //         'email' => $user->email,
            //         'name' => $user->name,
            //     ],
            //     'customizations' => [
            //         'title' => "Upgrade to Level {$levelId}",
            //         'description' => "Subscription payment for Level {$levelId}",
            //     ],
            // ];

            // // 'https://api.flutterwave.com/v3/charges?type=mobile_money_ghana' 

            // $response = Http::withHeaders([
            //     'Authorization' => 'Bearer ' . $this->secretKey,
            //     'Content-Type' => 'application/json',
            // ])->post("{$this->baseUrl}/charges?type=mobile_money_ghana", $payload);


            $payload = [
                'tx_ref' => $reference,

                'amount' => $amount,

                'currency' => $userCurrency,

                'redirect_url' => route(
                    'verify.flutterwave.charge.admin'
                ),

                'customer' => [
                    'email' => $user->email,
                    'phone_number' => null,
                    'name' => $user->name,
                ],

                'customizations' => [
                    'title' => "Upgrade to Level {$level->name}",
                    'logo' => '', //asset('logo.png'),
                ],

                'configuration' => [
                    'session_duration' => 30,
                ],

                // "payment_plan" => $paymentPlanId,

                'max_retry_attempt' => 5,

                /**
                 * Payment Channels
                 */
                // 'payment_options' => 'card, ussd, mobilemoneyghana',

                'payment_options' => implode(',', [
                    'card',
                    'banktransfer',
                    'ussd',
                    'mobilemoneyghana',
                    'mobilemoneytanzania',
                    'mobilemoneyzambia',
                    'mobilemoneyrwanda',
                    'mobilemoneyuganda',
                    'mpesa',
                    'opay',
                    'paypal',
                    'googlepay',
                    'applepay'
                ]),

                /**
                 * Optional Payment Plan
                 */
                // 'payment_plan' => 3807,

                /**
                 * Link Expiration
                 */
                'link_expiration' => now()
                    ->addMinutes(30)
                    ->toIso8601String(),

                /**
                 * Additional Metadata
                 */
                'meta' => [
                    'transaction_id' => $transaction->id,
                    'level_id' => $level->id,
                    'user_id' => $user->id,
                    'type' => 'subscription_upgrade',
                ],
            ];

            /**
             * =========================================================
             * HTTP REQUEST
             * =========================================================
             */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Idempotency-Key' => $idempotencyKey,
            ])
                ->timeout(30)
                ->connectTimeout(10)
                ->retry(3, 1000)
                ->post(
                    "{$this->baseUrl}/payments",
                    $payload
                );


            /**
             * =========================================================
             * HANDLE FAILURE
             * =========================================================
             */
            if (!$response->successful()) {

                Log::error('Flutterwave payment initialization failed', [
                    'payload' => $payload,
                    'response' => $response->json(),
                ]);

                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => $response->json(),
                ]);

                throw new \Exception(
                    data_get(
                        $response->json(),
                        'message',
                        'Failed to initialize payment.'
                    )
                );
            }

            /**
             * =========================================================
             * RESPONSE
             * =========================================================
             */
            $responseData = $response->json();

            Log::info('Flutterwave payment initialization successful', [
                'response' => $responseData,
            ]);

            /**
             * =========================================================
             * STORE PAYMENT LINK + GATEWAY RESPONSE
             * =========================================================
             */
            $transaction->update([
                'status' => 'initiated',
                'payment_link' => data_get($responseData, 'data.link'),
                'gateway_response' => $responseData,
            ]);

            /**
             * =========================================================
             * RETURN PAYMENT LINK
             * =========================================================
             */
            $paymentLink = data_get($responseData, 'data.link');

            if (!$paymentLink) {

                throw new \Exception(
                    'Flutterwave payment link missing.'
                );
            }

            return $paymentLink;


            // if (!$response->successful()) {
            //     Log::error('Flutterwave payment initialization failed', [
            //         'response' => $response->body(),
            //     ]);
            //     throw new \Exception('Failed to initialize payment. Please try again.');
            // }

            // $responseData = json_decode($response->getBody()->getContents(), true);


            // Log::info('Flutterwave payment initialization successful', [
            //     'response' => $response->body(),
            // ]);
            // return data_get($responseData, 'meta.authorization.redirect');
        });
    }
}
