<?php

namespace App\Services\Admin;

use App\Mail\GeneralMail;
use App\Models\EngagementMonthlyStat;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Wallet;
use App\Models\WithdrawalMethod;
use App\Notifications\GeneralNotification;
use App\Services\AdminAuditService;
use App\Services\FundTransferService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminPayoutService
{
    public function __construct(
        protected FundTransferService $fundTransferService,
        protected TransactionService $transactionService,
        protected AdminAuditService $audit,
    ) {}

    public function processPremium(string $level, string $lastMonth): array
    {
        $planPrices = [
            'Creator' => 1,
            'Influencer' => 5,
        ];

        $members = EngagementMonthlyStat::with(['user.wallet'])
            ->where('level', $level)
            ->where('month', $lastMonth)
            ->get();

        $memberCount = $members->count();

        if ($memberCount === 0) {
            return ['error' => 'No users found.'];
        }

        $revenue = $memberCount * $planPrices[$level];
        $levelPool = round($revenue * 0.50, 2);
        $totalEngagement = $members->sum('points');
        $userEngagements = [];

        foreach ($members as $member) {
            $percentage = $totalEngagement > 0
                ? round(($member->points / $totalEngagement) * 100, 2)
                : 0;

            $payout = $totalEngagement > 0
                ? round(($member->points / $totalEngagement) * $levelPool, 2)
                : 0;

            $member->update(['amount' => $payout]);

            $user = $member->user;

            $userEngagements[] = [
                'id' => $member->id,
                'user_id' => $member->user_id,
                'name' => $user->name ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'engagement' => $member->points ?? 0,
                'userPercentage' => $percentage,
                'userPayout' => $payout,
                'userWallet' => $user->wallet->currency ?? 'USD',
                'status' => $member->status,
            ];
        }

        return [
            'userEngagement' => $userEngagements,
            'totalEngagement' => $totalEngagement,
            'revenue' => $revenue,
            'memberCount' => $memberCount,
            'levelPool' => $levelPool,
            'poolLabel' => 'Level pool',
        ];
    }

    public function processBasic(string $lastMonth): array
    {
        $members = EngagementMonthlyStat::with(['user.wallet'])
            ->where('level', 'Basic')
            ->where('month', $lastMonth)
            ->get();

        if ($members->isEmpty()) {
            return ['error' => 'No Basic engagement stats for ' . $lastMonth . '. Process engagement stats first.'];
        }

        $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $lastMonth)->startOfMonth();
        $planPrices = ['Creator' => 1, 'Influencer' => 5];
        $fremiumPool = 0;

        foreach (['Creator', 'Influencer'] as $paidLevel) {
            $count = UserLevel::query()
                ->where('status', 'active')
                ->where('plan_name', $paidLevel)
                ->where('next_payment_date', '>=', $monthStart)
                ->count();

            $fremiumPool += round($count * $planPrices[$paidLevel] * 0.10, 2);
        }

        $totalEngagement = $members->sum('points');
        $userEngagements = [];

        foreach ($members as $member) {
            $percentage = $totalEngagement > 0
                ? round(($member->points / $totalEngagement) * 100, 2)
                : 0;

            $payout = $totalEngagement > 0
                ? round(($member->points / $totalEngagement) * $fremiumPool, 2)
                : 0;

            $member->update(['amount' => $payout]);

            $user = $member->user;

            $userEngagements[] = [
                'id' => $member->id,
                'user_id' => $member->user_id,
                'name' => $user->name ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'engagement' => $member->points ?? 0,
                'userPercentage' => $percentage,
                'userPayout' => $payout,
                'userWallet' => $user->wallet->currency ?? 'USD',
                'status' => $member->status,
            ];
        }

        return [
            'userEngagement' => $userEngagements,
            'totalEngagement' => $totalEngagement,
            'revenue' => $fremiumPool,
            'memberCount' => $members->count(),
            'levelPool' => $fremiumPool,
            'poolLabel' => 'Freemium pool',
        ];
    }

    public function queuePayout(string $engagementStatId): Payout
    {
        $engagementStat = EngagementMonthlyStat::query()
            ->with('user')
            ->findOrFail($engagementStatId);

        $payout = Payout::create([
            'engagement_monthly_stats_id' => $engagementStatId,
            'user_id' => $engagementStat->user_id,
            'level' => $engagementStat->level,
            'amount' => convertToBaseCurrency($engagementStat->amount, 'NGN'),
            'total_engagement' => $engagementStat->points,
            'month' => $engagementStat->month,
            'currency' => $engagementStat->currency ?? 'NGN',
            'status' => 'Queued',
            'type' => $engagementStat->level === 'Basic' ? 'Freemium' : 'Premium',
        ]);

        $engagementStat->update(['status' => 'Queued']);

        $this->audit->log('payout.queued', $payout, [
            'user_id' => $payout->user_id,
            'amount' => $payout->amount,
        ]);

        $amount = number_format($payout->amount, 2);
        $duration = \Carbon\Carbon::createFromFormat('Y-m', $payout->month)->format('F Y');

        $engagementStat->user->notify(
            (new GeneralNotification([
                'title' => '🚀 Payhankey Payout Processed!!',
                'message' => 'Great news! Your Payhankey payout has been successfully processed!',
                'icon' => 'fa-heart text-danger',
                'url' => url('wallets'),
            ]))->delay(now()->addSeconds(1))
        );

        Mail::to($engagementStat->user->email)->send(new GeneralMail(
            (object) [
                'name' => $engagementStat->user->name,
                'email' => $engagementStat->user->email,
            ],
            '🎉 Your Payhankey payout has been processed!',
            "<p>Your payout of NGN {$amount} for {$duration} has been processed.</p>"
        ));

        return $payout;
    }

    public function markPayoutPaid(string $payoutId): Payout
    {
        $payoutInfo = Payout::query()->with('user')->findOrFail($payoutId);
        $payoutInfo->update(['status' => 'Paid']);

        EngagementMonthlyStat::query()
            ->where('id', $payoutInfo->engagement_monthly_stats_id)
            ->update(['status' => 'Paid']);

        $wallet = Wallet::query()->where('user_id', $payoutInfo->user_id)->first();

        if ($wallet && $wallet->balance > 0) {
            Payout::create([
                'engagement_monthly_stats_id' => $payoutInfo->engagement_monthly_stats_id,
                'user_id' => $payoutInfo->user_id,
                'level' => $payoutInfo->level,
                'amount' => $wallet->balance,
                'total_engagement' => 0.00,
                'month' => $payoutInfo->month,
                'currency' => $wallet->currency ?? 'NGN',
                'status' => 'Queued',
                'type' => 'Bonus',
            ]);

            $wallet->balance = 0.00;
            $wallet->save();
        }

        $payoutInfo->user->notify(
            (new GeneralNotification([
                'title' => '🚀 Payhankey Payout Sent!!',
                'message' => 'Great news! Your payment has been sent to your account!',
                'icon' => 'fa-heart text-danger',
                'url' => url('wallets'),
            ]))->delay(now()->addSeconds(1))
        );

        Transaction::create([
            'user_id' => $payoutInfo->user_id,
            'ref' => generateTransactionRef(),
            'amount' => $payoutInfo->amount,
            'currency' => $payoutInfo->currency,
            'status' => 'successful',
            'type' => 'payhankey_payout_and_bonus',
            'action' => 'Credit',
            'description' => 'Payhankey payout for: ' . $payoutInfo->month,
        ]);

        $this->audit->log('payout.marked_paid', $payoutInfo, [
            'amount' => $payoutInfo->amount,
            'user_id' => $payoutInfo->user_id,
        ]);

        return $payoutInfo;
    }

    public function fundTransfer(array $validated): array
    {
        return DB::transaction(function () use ($validated) {
            $payoutInfo = Payout::query()
                ->where('id', $validated['payout_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($payoutInfo->status === 'Paid') {
                throw new \RuntimeException('Payment already processed');
            }

            $user = User::query()->findOrFail($validated['user_id']);

            $withdrawal = WithdrawalMethod::query()
                ->where('user_id', $validated['user_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $wallet = Wallet::query()
                ->where('user_id', $validated['user_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $walletBalance = $wallet->balance ?? 0;
            $transferAmount = $payoutInfo->amount + $walletBalance;

            if ($transferAmount <= 0) {
                throw new \RuntimeException('No funds available');
            }

            $transactionProcess = $this->transactionService->createTransaction(
                $user,
                Str::uuid()->toString(),
                'kora',
                generateTransactionRef(),
                (float) $transferAmount,
                $payoutInfo->currency ?? 'NGN',
                'successful',
                'Credit',
                'payhankey_payout_and_bonus',
                'Payhankey payout for: ' . $payoutInfo->month
            );

            if (! $transactionProcess) {
                throw new \RuntimeException('Failed to create transaction record');
            }

            $fundTransferResponse = $this->fundTransferService->transfer(
                $user,
                $transferAmount,
                $validated['bank_code'],
                $withdrawal->account_number
            );

            $payoutInfo->update(['status' => 'Paid']);

            EngagementMonthlyStat::query()
                ->where('id', $payoutInfo->engagement_monthly_stats_id)
                ->update(['status' => 'Paid']);

            if ($walletBalance > 0) {
                $wallet->update(['balance' => 0]);

                Payout::create([
                    'engagement_monthly_stats_id' => $payoutInfo->engagement_monthly_stats_id,
                    'user_id' => $user->id,
                    'level' => $payoutInfo->level,
                    'amount' => $walletBalance,
                    'total_engagement' => 0.00,
                    'month' => $payoutInfo->month,
                    'currency' => $wallet->currency ?? 'NGN',
                    'status' => 'Paid',
                    'type' => 'Bonus',
                ]);
            }

            $this->audit->log('payout.fund_transfer', $payoutInfo, [
                'amount' => $transferAmount,
                'user_id' => $user->id,
            ]);

            return $fundTransferResponse;
        });
    }
}
