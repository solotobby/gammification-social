<?php

namespace App\Services\Admin;

use App\Models\EngagementMonthlyStat;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawals;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminFinanceService
{
    public function dashboardStats(): array
    {
        $walletTotals = Wallet::query()
            ->selectRaw('currency, COUNT(*) as wallets, COALESCE(SUM(balance),0) as main, COALESCE(SUM(referral_balance),0) as referral, COALESCE(SUM(promoter_balance),0) as promoter')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        return [
            'walletTotals' => $walletTotals,
            'queued_withdrawals' => Withdrawals::query()->where('status', 'Queued')->count(),
            'queued_withdrawal_amount' => (float) Withdrawals::query()->where('status', 'Queued')->sum('amount'),
            'paid_withdrawals_month' => (float) Withdrawals::query()
                ->where('status', 'Paid')
                ->where('updated_at', '>=', now()->startOfMonth())
                ->sum('amount'),
            'queued_payouts' => Payout::query()->where('status', 'Queued')->count(),
            'queued_payout_amount' => (float) Payout::query()->where('status', 'Queued')->sum('amount'),
            'pending_engagement_stats' => EngagementMonthlyStat::query()->where('status', 'Pending')->count(),
            'negative_wallets' => Wallet::query()
                ->where(function ($q) {
                    $q->where('balance', '<', 0)
                        ->orWhere('referral_balance', '<', 0)
                        ->orWhere('promoter_balance', '<', 0);
                })
                ->count(),
        ];
    }

    public function searchLedger(
        ?string $search = null,
        ?string $type = null,
        ?string $status = null,
        ?string $action = null
    ): LengthAwarePaginator {
        $query = Transaction::query()
            ->with('user:id,name,username,email')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ref', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('user_id', $search)
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($action) {
            $query->where('action', $action);
        }

        return $query->paginate(25)->withQueryString();
    }

    public function anomalousWallets(): Collection
    {
        return Wallet::query()
            ->with('user:id,name,username,email,status')
            ->where(function ($q) {
                $q->where('balance', '<', 0)
                    ->orWhere('referral_balance', '<', 0)
                    ->orWhere('promoter_balance', '<', 0)
                    ->orWhere('balance', '>', 10000)
                    ->orWhere('referral_balance', '>', 5000)
                    ->orWhere('promoter_balance', '>', 5000);
            })
            ->orderByDesc('balance')
            ->limit(50)
            ->get();
    }

    public function reconciliation(): array
    {
        $queuedWithdrawals = Withdrawals::query()
            ->with('user:id,name,username,email')
            ->where('status', 'Queued')
            ->latest()
            ->limit(20)
            ->get();

        $queuedPayouts = Payout::query()
            ->with('user:id,name,username,email')
            ->where('status', 'Queued')
            ->latest()
            ->limit(20)
            ->get();

        $pendingEngagement = EngagementMonthlyStat::query()
            ->with('user:id,name,username,email')
            ->whereIn('status', ['Pending', 'Queued'])
            ->orderByDesc('month')
            ->limit(20)
            ->get();

        $withdrawalByWallet = Withdrawals::query()
            ->selectRaw('wallet_type, status, COUNT(*) as total, COALESCE(SUM(amount),0) as amount')
            ->groupBy('wallet_type', 'status')
            ->orderBy('wallet_type')
            ->get();

        $payoutByStatus = Payout::query()
            ->selectRaw('status, COUNT(*) as total, COALESCE(SUM(amount),0) as amount')
            ->groupBy('status')
            ->get();

        return [
            'queuedWithdrawals' => $queuedWithdrawals,
            'queuedPayouts' => $queuedPayouts,
            'pendingEngagement' => $pendingEngagement,
            'withdrawalByWallet' => $withdrawalByWallet,
            'payoutByStatus' => $payoutByStatus,
        ];
    }

    public function transactionTypes(): Collection
    {
        return Transaction::query()
            ->whereNotNull('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
    }
}
