<?php

namespace App\Services\Admin;

use App\Models\CommunitySubscription;
use App\Models\Transaction;
use App\Models\UserPaymentPlan;
use App\Models\Wallet;
use App\Support\AdminDateRange;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AdminFlutterwaveService
{
    public function fetchBalances(): array
    {
        $baseUrl = rtrim((string) config('services.env.flutterwave_base_url'), '/');
        $secret = (string) config('services.env.flutterwave_secret_key');

        if ($baseUrl === '' || $secret === '') {
            return [
                'ok' => false,
                'error' => 'Flutterwave API credentials are not configured.',
                'currencies' => collect(),
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$secret,
            ])->timeout(20)->get("{$baseUrl}/balances");

            if (! $response->successful() || $response->json('status') !== 'success') {
                return [
                    'ok' => false,
                    'error' => $response->json('message') ?? 'Unable to fetch Flutterwave balances.',
                    'currencies' => collect(),
                ];
            }

            $rows = collect($response->json('data') ?? [])
                ->map(fn ($row) => [
                    'code' => strtoupper((string) ($row['currency'] ?? '')),
                    'available' => (float) ($row['available_balance'] ?? 0),
                    'ledger' => (float) ($row['ledger_balance'] ?? 0),
                ])
                ->filter(fn ($row) => $row['code'] !== '')
                ->sortByDesc('available')
                ->values();

            return [
                'ok' => true,
                'error' => null,
                'currencies' => $rows,
            ];
        } catch (\Throwable $e) {
            report($e);

            return [
                'ok' => false,
                'error' => 'Flutterwave balance request failed.',
                'currencies' => collect(),
            ];
        }
    }

    public function flowStats(AdminDateRange $dateRange): array
    {
        $base = Transaction::query()
            ->where('provider', 'flutterwave')
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end]);

        $successful = (clone $base)->where('status', 'successful');
        $inflow = (clone $successful)->where(function ($q) {
            $q->where('action', 'Credit')->orWhereNull('action')->orWhere('action', '');
        });
        $outflow = (clone $successful)->where('action', 'Debit');

        $inflowByCurrency = (clone $inflow)
            ->selectRaw('currency, COUNT(*) as total, SUM(amount) as amount')
            ->groupBy('currency')
            ->get();

        $outflowByCurrency = (clone $outflow)
            ->selectRaw('currency, COUNT(*) as total, SUM(amount) as amount')
            ->groupBy('currency')
            ->get();

        return [
            'txCount' => (clone $base)->count(),
            'successfulCount' => (clone $successful)->count(),
            'pendingCount' => (clone $base)->whereIn('status', ['initiated', 'processing'])->count(),
            'failedCount' => (clone $base)->whereIn('status', ['failed', 'cancelled', 'flagged'])->count(),
            'inflowCount' => (clone $inflow)->count(),
            'inflowAmount' => (float) (clone $inflow)->sum('amount'),
            'outflowCount' => (clone $outflow)->count(),
            'outflowAmount' => (float) (clone $outflow)->sum('amount'),
            'inflowByCurrency' => $inflowByCurrency,
            'outflowByCurrency' => $outflowByCurrency,
            'subscriptionTxCount' => (clone $successful)->where('type', 'subscription_upgrade')->count(),
            'communityTxCount' => (clone $successful)
                ->where(function ($q) {
                    $q->where('type', 'like', 'community_%');
                })
                ->count(),
        ];
    }

    public function fetchTransfers(AdminDateRange $dateRange): array
    {
        $baseUrl = rtrim((string) config('services.env.flutterwave_base_url'), '/');
        $secret = (string) config('services.env.flutterwave_secret_key');

        if ($baseUrl === '' || $secret === '') {
            return [
                'ok' => false,
                'error' => 'Flutterwave API credentials are not configured.',
                'items' => collect(),
                'totalAmount' => 0.0,
                'count' => 0,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$secret,
            ])->timeout(25)->get("{$baseUrl}/transfers", [
                'from' => $dateRange->start->toDateString(),
                'to' => $dateRange->end->toDateString(),
                'page' => 1,
            ]);

            if (! $response->successful() || $response->json('status') !== 'success') {
                return [
                    'ok' => false,
                    'error' => $response->json('message') ?? 'Unable to fetch Flutterwave transfers.',
                    'items' => collect(),
                    'totalAmount' => 0.0,
                    'count' => 0,
                ];
            }

            $items = collect($response->json('data') ?? [])
                ->map(fn ($row) => [
                    'id' => $row['id'] ?? null,
                    'reference' => $row['reference'] ?? ($row['id'] ?? '—'),
                    'amount' => (float) ($row['amount'] ?? 0),
                    'currency' => strtoupper((string) ($row['currency'] ?? '')),
                    'status' => $row['status'] ?? 'unknown',
                    'narration' => $row['narration'] ?? ($row['meta']['narration'] ?? null),
                    'created_at' => $row['created_at'] ?? ($row['complete_message'] ?? null),
                    'account_name' => data_get($row, 'account_name')
                        ?? data_get($row, 'full_name')
                        ?? data_get($row, 'bank_name'),
                ]);

            $successful = $items->filter(fn ($row) => strtolower((string) $row['status']) === 'successful');

            return [
                'ok' => true,
                'error' => null,
                'items' => $items->take(50)->values(),
                'totalAmount' => (float) $successful->sum('amount'),
                'count' => $items->count(),
            ];
        } catch (\Throwable $e) {
            report($e);

            return [
                'ok' => false,
                'error' => 'Flutterwave transfers request failed.',
                'items' => collect(),
                'totalAmount' => 0.0,
                'count' => 0,
            ];
        }
    }

    public function recentTransactions(AdminDateRange $dateRange, int $limit = 10): Collection
    {
        return Transaction::query()
            ->with('user:id,name,username,email')
            ->where('provider', 'flutterwave')
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function transactionHistory(
        AdminDateRange $dateRange,
        ?string $status = null,
        ?string $type = null,
        ?string $search = null,
        ?string $flow = null,
    ): LengthAwarePaginator {
        $query = Transaction::query()
            ->with('user:id,name,username,email')
            ->where('provider', 'flutterwave')
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            if ($type === 'community') {
                $query->where('type', 'like', 'community_%');
            } else {
                $query->where('type', $type);
            }
        }

        if ($flow === 'in') {
            $query->where(function ($q) {
                $q->where('action', 'Credit')->orWhereNull('action')->orWhere('action', '');
            });
        } elseif ($flow === 'out') {
            $query->where('action', 'Debit');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ref', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(25)->withQueryString();
    }

    public function subscriptionHistory(
        AdminDateRange $dateRange,
        ?string $status = null,
        ?string $search = null,
    ): LengthAwarePaginator {
        $query = CommunitySubscription::query()
            ->with([
                'user:id,name,username,email',
                'community:id,name,slug,currency',
            ])
            ->where('gateway', 'flutterwave')
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('gateway_reference', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    })
                    ->orWhereHas('community', function ($communityQuery) use ($search) {
                        $communityQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(25)->withQueryString();
    }

    public function levelSubscriptionPlans(AdminDateRange $dateRange): Collection
    {
        return UserPaymentPlan::query()
            ->with(['user:id,name,username,email', 'level:id,name'])
            ->where('payment_gateway', 'flutterwave')
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->latest()
            ->limit(50)
            ->get();
    }

    public function platformWalletTotals(): Collection
    {
        return Wallet::query()
            ->selectRaw('currency, COUNT(*) as wallets, SUM(balance) as main, SUM(referral_balance) as referral, SUM(promoter_balance) as promoter')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();
    }

    public function statusLabels(): array
    {
        return [
            'successful' => 'Successful',
            'initiated' => 'Initiated',
            'processing' => 'Processing',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'flagged' => 'Flagged',
        ];
    }

    public function subscriptionStatusLabels(): array
    {
        return [
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            'pending' => 'Pending',
        ];
    }

    public function typeLabels(): array
    {
        return [
            'subscription_upgrade' => 'Level subscription',
            'community' => 'Community payment',
        ];
    }
}
