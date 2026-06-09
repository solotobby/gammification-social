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


            $subject = '🎉 Your Payhankey Subscription Upgrade is Successful';

            $content = "


                <p>
                    <strong>Congratulations!</strong> 🎉 Your subscription has been successfully upgraded to the
                    <strong>{$level->name}</strong> plan.
                </p>

                <p>
                    Thank you for choosing Payhankey. Your payment has been received, your account has been updated, and you can now enjoy all the benefits and features included in your new subscription plan.
                </p>

                <div style='background-color:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:20px;margin:20px 0;'>
                    <h3 style='margin-top:0;color:#333;'>Payment Summary</h3>

                    <p style='margin:8px 0;'>
                        <strong>Amount Paid:</strong> {$currency} {$upgradeAmount}
                    </p>

                    <p style='margin:8px 0;'>
                        <strong>Transaction Reference:</strong> {$transaction->ref}
                    </p>

                    <p style='margin:8px 0;'>
                        <strong>Subscription Plan:</strong> {$level->name}
                    </p>

                    <p style='margin:8px 0;'>
                        <strong>Next Billing Date:</strong> {$nextPaymentDate->toFormattedDateString()}
                    </p>
                </div>

                <p>
                    We're excited to continue supporting your journey and helping you get the most value from Payhankey.
                </p>

                <p>
                    If you have any questions or need assistance, our support team is always here to help.
                </p>

                <p>
                    Thank you for being a valued member of the Payhankey community.
                </p>

                <p>
                    Warm regards,<br>
                    <strong>The Payhankey Team</strong>
                </p>
            ";

            Mail::to($user->email)
                ->send(new GeneralMail(
                    (object)[
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    $subject,
                    $content
                ));

            $subject = 'Core Operation: Subscription Upgrade Successful';
            $content = "
                <p>
                    A subscription upgrade has been successfully processed for user: <strong>{$user->name}</strong> (ID: {$user->id}).
                </p>

                <p>
                    <strong>Transaction Reference:</strong> {$transaction->ref}<br>
                    <strong>Amount Paid:</strong> {$currency} {$upgradeAmount}<br>
                    <strong>Subscription Plan:</strong> {$level->name}<br>
                    <strong>Next Billing Date:</strong> {$nextPaymentDate->toFormattedDateString()}
                </p>

                <p>
                    The user's account has been updated to reflect the new subscription level, and any applicable bonuses have been credited.
                </p>

                <p>
                    Please review the transaction details in the admin dashboard for further insights.
                </p>

                <p>
                    Best regards,<br>
                    <strong>The Payhankey System</strong>
                </p>
             ";


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
