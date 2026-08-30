<?php

namespace App\Services\Admin;

use App\Models\PaykoinTransaction;
use App\Models\PostGift;
use App\Models\Wallet;
use App\Support\AdminDateRange;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminPayKoinService
{
    public function dashboardAnalytics(AdminDateRange $dateRange): array
    {
        $rangeTx = PaykoinTransaction::query()
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end]);

        $topupsInRange = (int) (clone $rangeTx)->where('type', 'topup')->sum('pk_amount');
        $giftsSentInRange = abs((int) (clone $rangeTx)->where('type', 'gift_sent')->sum('pk_amount'));
        $giftsReceivedInRange = (int) (clone $rangeTx)->where('type', 'gift_received')->sum('pk_amount');
        $convertedInRange = abs((int) (clone $rangeTx)->where('type', 'convert')->sum('pk_amount'));
        $topupFiatNgn = (float) (clone $rangeTx)->where('type', 'topup')->where('currency', 'NGN')->sum('fiat_amount');
        $topupFiatUsd = (float) (clone $rangeTx)->where('type', 'topup')->where('currency', 'USD')->sum('fiat_amount');
        $convertFiatNgn = (float) (clone $rangeTx)->where('type', 'convert')->where('currency', 'NGN')->sum('fiat_amount');
        $convertFiatUsd = (float) (clone $rangeTx)->where('type', 'convert')->where('currency', 'USD')->sum('fiat_amount');

        $giftsInRange = PostGift::query()
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->count();

        $platform = $this->platformTotals();

        return [
            'topupsInRange' => $topupsInRange,
            'giftsSentInRange' => $giftsSentInRange,
            'giftsReceivedInRange' => $giftsReceivedInRange,
            'convertedInRange' => $convertedInRange,
            'giftsCountInRange' => $giftsInRange,
            'topupFiatNgn' => $topupFiatNgn,
            'topupFiatUsd' => $topupFiatUsd,
            'convertFiatNgn' => $convertFiatNgn,
            'convertFiatUsd' => $convertFiatUsd,
            'totalSpendable' => $platform['total_spendable'],
            'totalEarned' => $platform['total_earned'],
            'walletsWithPk' => $platform['wallets_with_pk'],
            'totalGiftsAllTime' => $platform['total_gifts'],
            'volumeChart' => $this->volumeChart($dateRange),
            'recentTransactions' => PaykoinTransaction::query()
                ->with('user:id,name,username,email')
                ->latest()
                ->limit(8)
                ->get(),
            'recentGifts' => PostGift::query()
                ->with(['sender:id,name,username', 'recipient:id,name,username'])
                ->latest()
                ->limit(8)
                ->get(),
        ];
    }

    public function platformTotals(): array
    {
        $walletTotals = Wallet::query()
            ->selectRaw('COALESCE(SUM(paykoin_spendable), 0) as spendable')
            ->selectRaw('COALESCE(SUM(paykoin_earned), 0) as earned')
            ->selectRaw('COUNT(CASE WHEN paykoin_spendable > 0 OR paykoin_earned > 0 THEN 1 END) as wallets_with_pk')
            ->first();

        return [
            'total_spendable' => (int) ($walletTotals->spendable ?? 0),
            'total_earned' => (int) ($walletTotals->earned ?? 0),
            'wallets_with_pk' => (int) ($walletTotals->wallets_with_pk ?? 0),
            'total_gifts' => PostGift::query()->count(),
            'total_topups_pk' => (int) PaykoinTransaction::query()->where('type', 'topup')->sum('pk_amount'),
            'total_converted_pk' => abs((int) PaykoinTransaction::query()->where('type', 'convert')->sum('pk_amount')),
        ];
    }

    public function managementStats(AdminDateRange $dateRange): array
    {
        return array_merge(
            $this->platformTotals(),
            $this->dashboardAnalytics($dateRange),
            [
                'byType' => PaykoinTransaction::query()
                    ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                    ->selectRaw('type, COUNT(*) as total, COALESCE(SUM(ABS(pk_amount)), 0) as pk_volume')
                    ->groupBy('type')
                    ->orderByDesc('total')
                    ->get(),
                'byCurrency' => PaykoinTransaction::query()
                    ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                    ->whereNotNull('fiat_amount')
                    ->selectRaw('currency, type, COUNT(*) as total, COALESCE(SUM(fiat_amount), 0) as fiat_volume')
                    ->groupBy('currency', 'type')
                    ->orderBy('currency')
                    ->get(),
                'giftsByArtifact' => PostGift::query()
                    ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                    ->selectRaw('artifact_id, COUNT(*) as total, COALESCE(SUM(pk_amount), 0) as pk_volume')
                    ->groupBy('artifact_id')
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get(),
            ]
        );
    }

    public function searchTransactions(
        AdminDateRange $dateRange,
        ?string $search = null,
        ?string $type = null,
    ): LengthAwarePaginator {
        $query = PaykoinTransaction::query()
            ->with('user:id,name,username,email')
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->latest();

        if ($type) {
            $query->where('type', $type);
        }

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

        return $query->paginate(25)->withQueryString();
    }

    public function searchGifts(
        AdminDateRange $dateRange,
        ?string $search = null,
    ): LengthAwarePaginator {
        $query = PostGift::query()
            ->with(['sender:id,name,username,email', 'recipient:id,name,username,email'])
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ref', 'like', "%{$search}%")
                    ->orWhere('artifact_id', 'like', "%{$search}%")
                    ->orWhereHas('sender', fn ($user) => $user->where('username', 'like', "%{$search}%"))
                    ->orWhereHas('recipient', fn ($user) => $user->where('username', 'like', "%{$search}%"));
            });
        }

        return $query->paginate(25)->withQueryString();
    }

    public function topWallets(int $limit = 20): Collection
    {
        return Wallet::query()
            ->with('user:id,name,username,email,status')
            ->where(function ($q) {
                $q->where('paykoin_spendable', '>', 0)
                    ->orWhere('paykoin_earned', '>', 0);
            })
            ->orderByRaw('(paykoin_spendable + paykoin_earned) DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array{labels: array<int, string>, topups: array<int, int>, gifts: array<int, int>, converts: array<int, int>}
     */
    public function volumeChart(AdminDateRange $dateRange): array
    {
        $days = min($dateRange->days(), 60);
        $start = $dateRange->end->copy()->subDays($days - 1)->startOfDay();

        $labels = [];
        $topups = [];
        $gifts = [];
        $converts = [];

        $rows = PaykoinTransaction::query()
            ->whereBetween('created_at', [$start, $dateRange->end])
            ->selectRaw('DATE(created_at) as day, type, COALESCE(SUM(ABS(pk_amount)), 0) as volume')
            ->groupBy('day', 'type')
            ->orderBy('day')
            ->get()
            ->groupBy('day');

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i)->toDateString();
            $labels[] = $start->copy()->addDays($i)->format('M j');
            $dayRows = $rows->get($day, collect());

            $topups[] = (int) ($dayRows->firstWhere('type', 'topup')?->volume ?? 0);
            $gifts[] = (int) ($dayRows->firstWhere('type', 'gift_sent')?->volume ?? 0);
            $converts[] = (int) ($dayRows->firstWhere('type', 'convert')?->volume ?? 0);
        }

        return compact('labels', 'topups', 'gifts', 'converts');
    }

    public function transactionTypes(): array
    {
        return [
            'topup' => 'Top-ups',
            'gift_sent' => 'Gifts sent',
            'gift_received' => 'Gifts received',
            'convert' => 'Converted',
        ];
    }

    public function artifactLabel(string $artifactId): string
    {
        foreach (config('payhankey.paykoin.gift_artifacts', []) as $artifact) {
            if (($artifact['id'] ?? '') === $artifactId) {
                return ($artifact['emoji'] ?? '').' '.($artifact['name'] ?? $artifactId);
            }
        }

        return $artifactId;
    }
}
