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
        ?string $currency = null,
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

        if ($currency) {
            $query->where('currency', strtoupper($currency));
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

    public function transactionFilterOptions(): array
    {
        $currencies = Transaction::query()
            ->where('provider', 'flutterwave')
            ->whereNotNull('currency')
            ->distinct()
            ->orderBy('currency')
            ->pluck('currency')
            ->filter()
            ->values()
            ->all();

        $types = Transaction::query()
            ->where('provider', 'flutterwave')
            ->whereNotNull('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->filter()
            ->values()
            ->all();

        return [
            'currencies' => $currencies,
            'types' => $types,
        ];
    }

    public function subscriptionHistory(
        AdminDateRange $dateRange,
        ?string $status = null,
        ?string $search = null,
        ?string $billingType = null,
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

        if ($billingType) {
            $query->where('billing_type', $billingType);
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

        $paginator = $query->paginate(25)->withQueryString();
        $this->attachCommunityPayments($paginator->getCollection());

        return $paginator;
    }

    public function levelSubscriptionPlans(
        AdminDateRange $dateRange,
        ?string $search = null,
        ?string $status = null,
        ?string $paymentFilter = null,
    ): LengthAwarePaginator {
        $query = UserPaymentPlan::query()
            ->with(['user:id,name,username,email', 'level:id,name'])
            ->where('payment_gateway', 'flutterwave')
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_plan_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    })
                    ->orWhereHas('level', function ($levelQuery) use ($search) {
                        $levelQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (in_array($paymentFilter, ['with', 'without'], true)) {
            $paidUserIds = Transaction::query()
                ->where('provider', 'flutterwave')
                ->where('type', 'subscription_upgrade')
                ->where('status', 'successful')
                ->select('user_id');

            if ($paymentFilter === 'with') {
                $query->whereIn('user_id', $paidUserIds);
            } else {
                $query->whereNotIn('user_id', $paidUserIds);
            }
        }

        $paginator = $query->paginate(25, ['*'], 'level_page')->withQueryString();
        $this->attachLevelPlanContext($paginator->getCollection());

        return $paginator;
    }

    public function communitySubscriptionDetail(string $id): array
    {
        $subscription = CommunitySubscription::query()
            ->with([
                'user:id,name,username,email',
                'community:id,name,slug,currency,owner_id',
            ])
            ->where('gateway', 'flutterwave')
            ->findOrFail($id);

        $payment = $this->findPaymentForReference($subscription->gateway_reference);
        $relatedPayments = Transaction::query()
            ->with('user:id,name,username,email')
            ->where('provider', 'flutterwave')
            ->where('user_id', $subscription->user_id)
            ->where(function ($q) use ($subscription) {
                $q->where('type', 'like', 'community_%');
                if ($subscription->gateway_reference) {
                    $q->orWhere('ref', $subscription->gateway_reference);
                }
            })
            ->latest()
            ->limit(20)
            ->get();

        return [
            'kind' => 'community',
            'subscription' => $subscription,
            'payment' => $payment,
            'relatedPayments' => $relatedPayments,
            'nextPaymentAt' => $subscription->isRecurring() ? $subscription->expires_at : null,
            'nextPaymentLabel' => $subscription->isOneOff()
                ? 'One-off — no renewal'
                : ($subscription->expires_at
                    ? $subscription->expires_at->format('M j, Y g:i A')
                    : 'Not scheduled'),
        ];
    }

    public function levelPlanDetail(string $id): array
    {
        $plan = UserPaymentPlan::query()
            ->with(['user:id,name,username,email', 'level:id,name'])
            ->where('payment_gateway', 'flutterwave')
            ->findOrFail($id);

        $userLevel = \App\Models\UserLevel::query()
            ->with('level:id,name')
            ->where('user_id', $plan->user_id)
            ->when($plan->level_id, fn ($q) => $q->where('level_id', $plan->level_id))
            ->latest()
            ->first();

        $relatedPayments = Transaction::query()
            ->with('user:id,name,username,email')
            ->where('provider', 'flutterwave')
            ->where('user_id', $plan->user_id)
            ->where('type', 'subscription_upgrade')
            ->latest()
            ->limit(20)
            ->get();

        $payment = $relatedPayments->firstWhere('status', 'successful')
            ?? $relatedPayments->first();

        return [
            'kind' => 'level',
            'plan' => $plan,
            'userLevel' => $userLevel,
            'payment' => $payment,
            'relatedPayments' => $relatedPayments,
            'nextPaymentAt' => $userLevel?->next_payment_date,
            'nextPaymentLabel' => $userLevel?->next_payment_date
                ? $userLevel->next_payment_date->format('M j, Y g:i A')
                : 'Not scheduled',
        ];
    }

    protected function attachCommunityPayments(Collection $subscriptions): void
    {
        $refs = $subscriptions->pluck('gateway_reference')->filter()->unique()->values();

        if ($refs->isEmpty()) {
            $subscriptions->each(function ($sub) {
                $sub->setAttribute('attached_payment', null);
                $sub->setAttribute('next_payment_label', $this->communityNextPaymentLabel($sub));
            });

            return;
        }

        $payments = Transaction::query()
            ->where('provider', 'flutterwave')
            ->whereIn('ref', $refs->all())
            ->get()
            ->keyBy('ref');

        $subscriptions->each(function ($sub) use ($payments) {
            $payment = $sub->gateway_reference
                ? ($payments->get($sub->gateway_reference) ?? null)
                : null;
            $sub->setAttribute('attached_payment', $payment);
            $sub->setAttribute('next_payment_label', $this->communityNextPaymentLabel($sub));
        });
    }

    protected function attachLevelPlanContext(Collection $plans): void
    {
        if ($plans->isEmpty()) {
            return;
        }

        $userIds = $plans->pluck('user_id')->unique()->values();
        $levelIds = $plans->pluck('level_id')->filter()->unique()->values();

        $userLevels = \App\Models\UserLevel::query()
            ->whereIn('user_id', $userIds)
            ->when($levelIds->isNotEmpty(), fn ($q) => $q->whereIn('level_id', $levelIds))
            ->latest()
            ->get()
            ->groupBy(fn ($row) => $row->user_id.'|'.$row->level_id);

        $payments = Transaction::query()
            ->where('provider', 'flutterwave')
            ->where('type', 'subscription_upgrade')
            ->whereIn('user_id', $userIds)
            ->where('status', 'successful')
            ->latest()
            ->get()
            ->groupBy('user_id');

        $plans->each(function ($plan) use ($userLevels, $payments) {
            $key = $plan->user_id.'|'.$plan->level_id;
            $userLevel = $userLevels->get($key)?->first()
                ?? $userLevels->flatten(1)->firstWhere('user_id', $plan->user_id);
            $payment = $payments->get($plan->user_id)?->first();

            $plan->setAttribute('user_level', $userLevel);
            $plan->setAttribute('attached_payment', $payment);
            $plan->setAttribute(
                'next_payment_label',
                $userLevel?->next_payment_date
                    ? $userLevel->next_payment_date->format('M j, Y')
                    : 'Not scheduled'
            );
        });
    }

    protected function communityNextPaymentLabel(CommunitySubscription $sub): string
    {
        if ($sub->isOneOff()) {
            return 'One-off — no renewal';
        }

        if (! $sub->expires_at) {
            return 'Not scheduled';
        }

        return $sub->expires_at->format('M j, Y');
    }

    protected function findPaymentForReference(?string $reference): ?Transaction
    {
        if (! $reference) {
            return null;
        }

        return Transaction::query()
            ->with('user:id,name,username,email')
            ->where('provider', 'flutterwave')
            ->where('ref', $reference)
            ->first();
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
            'community_subscription' => 'Community subscription',
            'community_one_off' => 'Community one-off',
        ];
    }
}
