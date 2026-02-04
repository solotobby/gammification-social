<?php

namespace App\Services;

use App\Models\Level;
use App\Models\SubscriptionStat;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{

    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function processSubscriptionPayment($levelId)
    {


        $user = Auth::user();
        $level = Level::find($levelId);

        $userCurrency = userBaseCurrency($user->id);

        $convertedAmount = convertToBaseCurrency($level->amount, 'NGN'); ///convert all currency to NGN Via route

        if (in_array($userCurrency, ['NGN', 'USD', 'EUR', 'GBP'])) {
            return $this->callKoraPay($convertedAmount, $level);
        }

        // if ($level) {

        //     if ($userCurrency == 'NGN' || $userCurrency == 'USD' || $userCurrency == 'EUR' || $userCurrency == 'GBP') {
        //         return $this->callKoraPay($convertedAmount, $level); //initializeKorayPay($convertedAmount, $level);
        //     }
        // }
    }

    private function callKoraPay($amount, $level)
    {
        $user = Auth::user();
        $reference = generateTransactionRef();

        $transaction = $this->transactionService->createTransaction(
            user: $user,
            reference: $reference,
            amount: $amount,
            currency: 'NGN',
            status: 'pending',
            action: 'Debit',
            type: 'subscription_upgrade',
            description: $user->name . ' upgrade to ' . $level->name
            // meta: [
            //     'level_id' => $level->id,
            //     'level_name' => $level->name,
            // ],
            // customer: [
            //     'name' => $user->name,
            //     'email' => $user->email,
            // ]
        );

        $payloadNGN = [
            "amount" => $amount,
            "redirect_url" => route('verify.subscription'), //url('wallet/fund/redirect'),
            "currency" => "NGN",
            "reference" => $reference,
            "narration" => $level->name . " Upgrade",
            "channels" => [
                "card",
                "bank_transfer",
                "pay_with_bank"
            ],
            // "default_channel"=> "card",
            "customer" => [
                "name" => $user->name,
                "email" => $user->email
            ],
            // "notification_url" => "https://webhook.site/eb6e001a-efd8-471d-81c2-866170abd550",
            "metadata" => [
                'user_id' => $user->id,
                'level' => $level->name,
                'level_id' => $level->id,
                'name' => $user->name
            ]
        ];

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.env.kora_sec')
        ])->post('https://api.korapay.com/merchant/api/v1/charges/initialize', $payloadNGN)->throw();

        if (!$res->successful()) {
            $transaction->update(['status' => 'failed']);
            session()->flash('error', 'Unable to initialize payment.');
            return null;
        }


        return json_decode($res->getBody()->getContents(), true)['data']['checkout_url'];
    }


    public function verifyWithKoraPay($reference)
    {

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.env.kora_sec')
        ])->get('https://api.korapay.com/merchant/api/v1/charges/' . $reference)->throw();

        return json_decode($res->getBody()->getContents(), true);
    }

    public function verifySubscriptionPayment(string $reference): array
    {

        $transaction = Transaction::where('ref', $reference)->first();

        if (!$transaction) {
            throw new \Exception('Transaction not found');
        }

        // 🔁 Idempotency: if transaction already verified
        if ($transaction->status === 'success') {
            $nextPaymentDate = UserLevel::where('user_id', $transaction->user_id)
                ->value('next_payment_date');

            return [
                'level_name' => null,
                'next_payment_date' => \Carbon\Carbon::parse($nextPaymentDate),
            ];
        }

        $response = $this->verifyWithKoraPay($reference);

        $data = $response['data'];

        if (!$data || $data['status'] !== 'success' || $data['status'] !== 'pending') {
            $this->transactionService->markFailed($transaction, $data);

            throw new \Exception('Payment verification failed or was not successful.');

        }

        // 🔐 Security checks
        if (
            $data['amount'] != $transaction->amount ||
            $data['currency'] !== $transaction->currency
        ) {
            $this->transactionService->markFailed($transaction, [
                'reason' => 'Amount or currency mismatch',
                // 'kora' => $data
            ]);
            throw new \Exception('Payment validation failed');
        }

        
        $planName = $data['metadata']['level'];
        $planCode = $data['metadata']['level_id'];
        $level = Level::where('name', $planName)->firstOrFail();
        $nextPaymentDate = now()->addMonth(); // 30 days time


        DB::transaction(function () use ($transaction, $data, $planName, $planCode, $reference, $level, $nextPaymentDate) {
            $this->transactionService->markSuccessful($transaction, $data);

            $user = User::with('wallet')->findOrFail($transaction->user_id);

            $currency = $user->wallet->currency;

            $upgradeAmount = convertToBaseCurrency(
                $level->amount,
                $currency
            );


            // Subscription update
            UserLevel::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'level_id'          => $level->id,
                    'plan_name'         => $level->name,
                    'plan_code'         => $planCode,
                    'subscription_code' => $planCode,
                    'email_token'       => $planCode,
                    'start_date'        => now(),
                    'status'            => 'active',
                    'next_payment_date' => $nextPaymentDate,
                ]
            );

            //checkSubscription
            $checkSubs = SubscriptionStat::where('user_id', $user->id)->exists();

            if (!$checkSubs) {

                $regBonus = convertToBaseCurrency(
                    $level->reg_bonus,
                    $currency
                );
                // Wallet credit (increment, not overwrite)
                $user->wallet->increment('balance', $regBonus);
                Transaction::create([
                    'user_id'    => $user->id,
                    'ref'        => $reference . '-bonus',
                    'amount'     => $regBonus,
                    'currency'   => $currency,
                    'status'     => 'successful',
                    'type'       => 'reg_bonus',
                    'action'     => 'Credit',
                    'description' => "Upgrade bonus for {$level->name}",
                ]);
            }


            SubscriptionStat::create([
                'user_id'   => $user->id,
                'level_id'  => $level->id,
                'plan_name' => $level->name,
                'amount'    => $upgradeAmount,
                'currency'  => $currency,
                'start_date' => now(),
                'end_date'  => $nextPaymentDate,
            ]);
        });

        return [
            'level_name' => $level->name,
            'next_payment_date' => $nextPaymentDate,
        ];
    }
}
