<?php

namespace App\Services;

use App\Mail\GeneralMail;
use App\Models\SubscriptionStat;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UpgradeSubscriptionService
{

    //this handles subscription value only after payment is successfully verified via the webhook

    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function upgradeSubscription($user, $level, $transaction, $payload): void
    {

        DB::transaction(function () use ($user, $level, $transaction, $payload) {

            $subject = 'Core Operation: Subscription Upgrade Initiated';
            $content = "Your subscription has been upgraded to {$level->name} Initiated.";


            Mail::to('solotob3@gmail.com')
                ->send(new GeneralMail(
                    (object)[
                        'name' => 'Oluwatobi Solomon',
                        'email' => 'solotob3@gmail.com'
                    ],
                    $subject,
                    $content
                ));



            $nextPaymentDate = now()->addMonth();

            $this->transactionService->markSuccessful($transaction, $payload); //first mark transaction successful
            $user = User::with('wallet')->findOrFail($transaction->user_id);
            $currency = $user->wallet->currency; // get user currency

            $upgradeAmount = convertToBaseCurrency($level->amount, $currency);

            // Subscription update
            UserLevel::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'level_id'          => $level->id,
                    'plan_name'         => $level->name,
                    'plan_code'         => $level->id,
                    'subscription_code' => $level->id,
                    'email_token'       => $level->id,
                    'start_date'        => now(),
                    'status'            => 'active',
                    'next_payment_date' => $nextPaymentDate,
                ]
            );

            // Prevent duplicate bonus (lock check)
            $hasReceivedBonus = SubscriptionStat::where('user_id', $user->id)
                ->lockForUpdate()
                ->exists();

            if (!$hasReceivedBonus) {
                $bonus = convertToBaseCurrency($level->reg_bonus, $currency);

                $user->wallet->increment('balance', $bonus);

                Transaction::create([
                    'user_id'     => $user->id,
                    'ref'         => $transaction->ref . '-bonus',
                    'amount'      => $bonus,
                    'currency'    => $currency,
                    'status'      => 'successful',
                    'type'        => 'reg_bonus',
                    'action'      => 'Credit',
                    'description' => "Upgrade bonus for {$level->name}",
                ]);
            }

            SubscriptionStat::create([
                'user_id'    => $user->id,
                'level_id'   => $level->id,
                'plan_name'  => $level->name,
                'amount'     => $upgradeAmount,
                'currency'   => $currency,
                'start_date' => now(),
                'end_date'   => $nextPaymentDate,
            ]);


            $subject = 'Core Operation: Subscription Upgrade Successful';
            $content = "Your subscription has been upgraded to {$level->name} successfully.";


            Mail::to('solotob3@gmail.com')
                ->send(new GeneralMail(
                    (object)[
                        'name' => 'Oluwatobi Solomon',
                        'email' => 'solotob3@gmail.com'
                    ],
                    $subject,
                    $content
                ));


            // userActivity('subscribed');

            return [
                'level_name' => $level->name,
                'next_payment_date' => $nextPaymentDate,
            ];
        });
    }
}
