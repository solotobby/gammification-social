<?php

namespace App\Services\Admin;

use App\Mail\GeneralMail;
use App\Models\EngagementMonthlyStat;
use App\Models\EngagementPayoutComponent;
use App\Models\FremiumEngagementStat;
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
use Illuminate\Support\Facades\Auth;
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

        $members = EngagementMonthlyStat::with(['user.wallet', 'payoutComponents'])
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

            if (! $member->amount_manual) {
                $member->update(['amount' => $payout]);
            } else {
                $payout = (float) $member->amount;
            }

            $userEngagements[] = $this->buildUserEngagementRow($member, $percentage, $payout);
        }

        return [
            'userEngagement' => $userEngagements,
            'totalEngagement' => $totalEngagement,
            'revenue' => $revenue,
            'memberCount' => $memberCount,
            'levelPool' => $levelPool,
            'poolLabel' => 'Level pool',
            'componentAnalytics' => $this->componentAnalytics($lastMonth, $level),
        ];
    }

    public function processBasic(string $lastMonth): array
    {
        $members = FremiumEngagementStat::with(['user.wallet', 'payoutComponents'])
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

            if (! $member->amount_manual) {
                $member->update(['amount' => $payout]);
            } else {
                $payout = (float) $member->amount;
            }

            $userEngagements[] = $this->buildUserEngagementRow($member, $percentage, $payout);
        }

        return [
            'userEngagement' => $userEngagements,
            'totalEngagement' => $totalEngagement,
            'revenue' => $fremiumPool,
            'memberCount' => $members->count(),
            'levelPool' => $fremiumPool,
            'poolLabel' => 'Freemium pool',
            'componentAnalytics' => $this->componentAnalytics($lastMonth, 'Basic'),
        ];
    }

    public function addComponent(
        string $engagementStatId,
        string $type,
        float $amount,
        ?string $note = null,
        string $currency = 'NGN'
    ): EngagementPayoutComponent {
        $stat = $this->assertEditableStat($engagementStatId);

        if (! in_array($type, ['revenue', 'bonus'], true)) {
            throw new \InvalidArgumentException('Invalid payout component type.');
        }

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }

        $component = EngagementPayoutComponent::create([
            'engagement_monthly_stats_id' => $stat->id,
            'user_id' => $stat->user_id,
            'level' => $stat->level,
            'month' => $stat->month,
            'type' => $type,
            'amount' => round($amount, 2),
            'currency' => strtoupper($currency),
            'note' => $note,
            'admin_id' => Auth::id(),
        ]);

        $this->audit->log('payout.component.created', $component, [
            'engagement_stat_id' => $stat->id,
            'user_id' => $stat->user_id,
            'type' => $type,
            'amount' => $component->amount,
            'currency' => $component->currency,
            'note' => $note,
        ]);

        return $component;
    }

    public function updateComponent(string $componentId, float $amount, ?string $note = null): EngagementPayoutComponent
    {
        $component = EngagementPayoutComponent::query()->findOrFail($componentId);

        if ($component->payout_id) {
            throw new \RuntimeException('Cannot edit a component that has already been queued.');
        }

        $this->assertEditableStat($component->engagement_monthly_stats_id);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }

        $before = $component->only(['amount', 'note', 'type']);

        $component->update([
            'amount' => round($amount, 2),
            'note' => $note,
            'admin_id' => Auth::id(),
        ]);

        $this->audit->log('payout.component.updated', $component, [
            'before' => $before,
            'after' => $component->only(['amount', 'note', 'type']),
            'user_id' => $component->user_id,
        ]);

        return $component->fresh();
    }

    public function deleteComponent(string $componentId): void
    {
        $component = EngagementPayoutComponent::query()->findOrFail($componentId);

        if ($component->payout_id) {
            throw new \RuntimeException('Cannot delete a component that has already been queued.');
        }

        $this->assertEditableStat($component->engagement_monthly_stats_id);

        $this->audit->log('payout.component.deleted', $component, [
            'engagement_stat_id' => $component->engagement_monthly_stats_id,
            'user_id' => $component->user_id,
            'type' => $component->type,
            'amount' => $component->amount,
            'currency' => $component->currency,
        ]);

        $component->delete();
    }

    public function updateEngagementPayoutAmount(string $engagementStatId, float $amount): EngagementMonthlyStat
    {
        $stat = $this->assertEditableStat($engagementStatId);

        if ($amount < 0) {
            throw new \InvalidArgumentException('Engagement payout cannot be negative.');
        }

        $before = (float) $stat->amount;

        $stat->update([
            'amount' => round($amount, 2),
            'amount_manual' => true,
        ]);

        $this->audit->log('payout.engagement_amount.updated', $stat, [
            'user_id' => $stat->user_id,
            'before' => $before,
            'after' => (float) $stat->amount,
            'month' => $stat->month,
            'level' => $stat->level,
        ]);

        return $stat->fresh();
    }

    public function componentAnalytics(string $month, ?string $level = null): array
    {
        $query = EngagementPayoutComponent::query()->where('month', $month);

        if ($level) {
            $query->where('level', $level);
        }

        return [
            'revenue_total' => (float) (clone $query)->where('type', 'revenue')->sum('amount'),
            'bonus_total' => (float) (clone $query)->where('type', 'bonus')->sum('amount'),
            'revenue_count' => (clone $query)->where('type', 'revenue')->count(),
            'bonus_count' => (clone $query)->where('type', 'bonus')->count(),
            'members_with_adjustments' => (clone $query)->distinct('user_id')->count('user_id'),
        ];
    }

    public function queuePayout(string $engagementStatId): Payout
    {
        $engagementStat = EngagementMonthlyStat::query()
            ->with(['user', 'payoutComponents'])
            ->findOrFail($engagementStatId);

        if ($engagementStat->status !== 'Pending') {
            throw new \RuntimeException('This payout has already been queued or paid.');
        }

        $breakdown = $this->payoutBreakdown($engagementStat);
        $totalNgn = $breakdown['total_ngn'];

        if ($totalNgn <= 0) {
            throw new \RuntimeException('Total payout must be greater than zero.');
        }

        $payout = Payout::create([
            'engagement_monthly_stats_id' => $engagementStatId,
            'user_id' => $engagementStat->user_id,
            'level' => $engagementStat->level,
            'amount' => $totalNgn,
            'total_engagement' => $engagementStat->points,
            'month' => $engagementStat->month,
            'currency' => 'NGN',
            'status' => 'Queued',
            'type' => $engagementStat->level === 'Basic' ? 'Freemium' : 'Premium',
        ]);

        EngagementPayoutComponent::query()
            ->where('engagement_monthly_stats_id', $engagementStatId)
            ->whereNull('payout_id')
            ->update(['payout_id' => $payout->id]);

        $engagementStat->update(['status' => 'Queued']);

        $this->audit->log('payout.queued', $payout, [
            'user_id' => $payout->user_id,
            'amount' => $payout->amount,
            'breakdown' => $breakdown,
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

    protected function buildUserEngagementRow(EngagementMonthlyStat $member, float $percentage, float $engagementPayout): array
    {
        $user = $member->user;
        $breakdown = $this->payoutBreakdown($member);

        $components = $member->payoutComponents
            ->whereNull('payout_id')
            ->values()
            ->map(fn (EngagementPayoutComponent $c) => [
                'id' => $c->id,
                'type' => $c->type,
                'amount' => (float) $c->amount,
                'currency' => $c->currency,
                'note' => $c->note,
            ])
            ->all();

        return [
            'id' => $member->id,
            'user_id' => $member->user_id,
            'name' => $user->name ?? 'N/A',
            'email' => $user->email ?? 'N/A',
            'engagement' => $member->points ?? 0,
            'userPercentage' => $percentage,
            'userPayout' => $engagementPayout,
            'revenuePayout' => $breakdown['revenue_ngn'],
            'bonusPayout' => $breakdown['bonus_ngn'],
            'totalPayoutNgn' => $breakdown['total_ngn'],
            'userWallet' => $user->wallet->currency ?? 'USD',
            'status' => $member->status,
            'components' => $components,
        ];
    }

    protected function payoutBreakdown(EngagementMonthlyStat $stat): array
    {
        $engagementNgn = (float) convertToBaseCurrency((float) $stat->amount, 'NGN');

        $pendingComponents = $stat->relationLoaded('payoutComponents')
            ? $stat->payoutComponents->whereNull('payout_id')
            : EngagementPayoutComponent::query()
                ->where('engagement_monthly_stats_id', $stat->id)
                ->whereNull('payout_id')
                ->get();

        $revenueNgn = (float) $pendingComponents->where('type', 'revenue')->sum('amount');
        $bonusNgn = (float) $pendingComponents->where('type', 'bonus')->sum('amount');

        return [
            'engagement_usd' => (float) $stat->amount,
            'engagement_ngn' => $engagementNgn,
            'revenue_ngn' => $revenueNgn,
            'bonus_ngn' => $bonusNgn,
            'total_ngn' => round($engagementNgn + $revenueNgn + $bonusNgn, 2),
        ];
    }

    protected function assertEditableStat(string $engagementStatId): EngagementMonthlyStat
    {
        $stat = EngagementMonthlyStat::query()->findOrFail($engagementStatId);

        if ($stat->status !== 'Pending') {
            throw new \RuntimeException('Payout adjustments are only allowed while status is Pending.');
        }

        return $stat;
    }
}
