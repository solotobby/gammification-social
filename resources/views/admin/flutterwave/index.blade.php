@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-badge--success { background: rgba(16,185,129,.12); color: #067647; }
        .dash-badge--warn { background: rgba(245,158,11,.14); color: #b54708; }
        .dash-badge--danger { background: rgba(220,53,69,.12); color: #b42318; }
        .dash-badge--flw { background: rgba(251,146,60,.14); color: #c2410c; }
        .dash-tab-row { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; }
        .dash-tab-row .dash-tab { text-decoration:none; }
        .dash-tab-row .dash-tab.is-active { background: var(--dash-accent-soft); border-color: #c7d2fe; color: var(--dash-accent); }
        .dash-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start; }
        @media (max-width: 960px) { .dash-grid-2 { grid-template-columns: 1fr; } }
        .dash-filter-bar { display:flex; flex-wrap:wrap; gap:.5rem; align-items:center; margin-bottom:1rem; padding:0 1.25rem 1rem; }
        .dash-filter-bar .dash-input { flex: 1; min-width: 140px; }
        .dash-row-link { cursor: pointer; transition: background .12s; }
        .dash-row-link:hover { background: #f8fafc; }
        .dash-row-link a { color: inherit; text-decoration: none; }
        .dash-row-link a:hover { color: var(--dash-accent); text-decoration: underline; }

        .flw-hero {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 1.5rem 1.75rem;
            margin-bottom: 1rem;
            background:
                radial-gradient(circle at 88% 12%, rgba(251, 146, 60, .28) 0%, transparent 42%),
                radial-gradient(circle at 8% 88%, rgba(249, 115, 22, .28) 0%, transparent 45%),
                linear-gradient(135deg, #1c1917 0%, #292524 45%, #431407 100%);
            border: 1px solid rgba(255, 255, 255, .08);
            box-shadow: 0 24px 48px -12px rgba(15, 23, 42, .4);
        }
        .flw-hero__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: 1.1rem;
        }
        .flw-hero__badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .35rem .75rem;
            border-radius: 999px;
            background: rgba(251, 146, 60, .15);
            border: 1px solid rgba(251, 146, 60, .3);
            color: #fdba74;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .flw-pulse {
            width: 7px; height: 7px; border-radius: 50%;
            background: #fb923c;
            box-shadow: 0 0 0 0 rgba(251,146,60,.55);
            animation: flw-pulse 2s ease-out infinite;
        }
        @keyframes flw-pulse {
            0% { box-shadow: 0 0 0 0 rgba(251,146,60,.55); }
            70% { box-shadow: 0 0 0 8px rgba(251,146,60,0); }
            100% { box-shadow: 0 0 0 0 rgba(251,146,60,0); }
        }
        .flw-hero__tag { font-size:.75rem; font-weight:600; color:rgba(226,232,240,.55); letter-spacing:.06em; text-transform:uppercase; }
        .flw-hero__label { display:block; font-size:.8125rem; font-weight:600; color:rgba(226,232,240,.65); margin-bottom:.45rem; }
        .flw-hero__amount {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 700;
            letter-spacing: -.04em;
            color: #fff7ed;
            font-variant-numeric: tabular-nums;
        }
        .flw-hero__sub { margin-top:.5rem; font-size:.8125rem; color:rgba(253,186,116,.85); }
        .flw-hero__error {
            padding: 1rem 1.15rem;
            border-radius: 14px;
            background: rgba(239, 68, 68, .12);
            border: 1px solid rgba(248, 113, 113, .25);
            color: #fecaca;
            font-size: .875rem;
        }
        .flw-balance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: .75rem;
            margin-top: 1.15rem;
        }
        .flw-balance-chip {
            padding: .85rem 1rem;
            border-radius: 14px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.08);
        }
        .flw-balance-chip__code { font-size:.75rem; font-weight:700; color:#fdba74; letter-spacing:.06em; }
        .flw-balance-chip__avail { font-size:1.125rem; font-weight:700; color:#fff7ed; margin-top:.25rem; font-variant-numeric:tabular-nums; }
        .flw-balance-chip__ledger { font-size:.75rem; color:rgba(214,211,209,.75); margin-top:.2rem; }
        .dash-filter-bar { display:flex; flex-wrap:wrap; gap:.5rem; align-items:center; margin-bottom:1rem; padding:0 1.25rem 1rem; }
        .dash-filter-bar .dash-input { flex: 1; min-width: 140px; }
        .dash-row-link { cursor: pointer; transition: background .12s; }
        .dash-row-link:hover { background: #f8fafc; }
        .dash-row-link a { color: inherit; text-decoration: none; }
        .dash-row-link a:hover { color: var(--dash-accent); text-decoration: underline; }
    </style>
@endsection

@section('content')
@php
    $primaryBalance = ($balances['currencies'] ?? collect())->first();
    $extraQuery = array_filter([
        'tab' => $tab,
        'q' => $search ?: null,
        'status' => $status ?: null,
        'type' => $type ?: null,
        'flow' => $flow ?: null,
        'currency' => $currency ?: null,
        'billing' => $billingType ?: null,
        'lq' => $levelSearch ?: null,
        'payment' => $levelPayment ?: null,
        'lstatus' => $levelStatus ?: null,
    ]);
@endphp

<div class="content p-0">
    <div class="dash">
        <header class="dash-header">
            <div>
                <h1>Flutterwave</h1>
                <p>Merchant wallets, inflows, outflows, transactions & subscriptions · {{ $dateRange->label() }}</p>
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap">
                <a href="{{ route('admin.korapay.index') }}" class="dash-btn dash-btn--ghost">Kora Pay</a>
                <a href="{{ route('admin.finance.index') }}" class="dash-btn dash-btn--ghost">Wallets & earnings</a>
            </div>
        </header>

        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
        @endif

        @include('admin.partials.date-range-filter', [
            'routeName' => 'admin.flutterwave.index',
            'extraQuery' => $extraQuery,
        ])

        <div class="dash-tab-row">
            @foreach (['overview' => 'Overview', 'transactions' => 'Transactions', 'subscriptions' => 'Subscriptions', 'wallets' => 'Wallets'] as $tabId => $tabLabel)
                <a href="{{ route('admin.flutterwave.index', array_merge($dateRange->queryParams(), ['tab' => $tabId])) }}"
                   class="dash-tab @if($tab === $tabId) is-active @endif">{{ $tabLabel }}</a>
            @endforeach
        </div>

        <section class="dash-section">
            <div class="flw-hero">
                <div class="flw-hero__top">
                    <span class="flw-hero__badge"><span class="flw-pulse"></span> Live merchant balance</span>
                    <span class="flw-hero__tag">Flutterwave</span>
                </div>

                @if ($balances['ok'] ?? false)
                    <div>
                        <span class="flw-hero__label">Primary available</span>
                        @if ($primaryBalance)
                            <div class="flw-hero__amount">
                                {{ $primaryBalance['code'] }} {{ number_format($primaryBalance['available'], 2) }}
                            </div>
                            <div class="flw-hero__sub">
                                Ledger {{ number_format($primaryBalance['ledger'], 2) }}
                            </div>
                        @else
                            <div class="flw-hero__amount">—</div>
                        @endif
                    </div>

                    @if (($balances['currencies'] ?? collect())->count() > 1)
                        <div class="flw-balance-grid">
                            @foreach ($balances['currencies'] as $wallet)
                                <div class="flw-balance-chip">
                                    <div class="flw-balance-chip__code">{{ $wallet['code'] }}</div>
                                    <div class="flw-balance-chip__avail">{{ number_format($wallet['available'], 2) }}</div>
                                    <div class="flw-balance-chip__ledger">Ledger {{ number_format($wallet['ledger'], 2) }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="flw-hero__error">{{ $balances['error'] ?? 'Unable to load Flutterwave balances.' }}</div>
                @endif
            </div>

            <div class="dash-grid dash-grid--4">
                <div class="dash-kpi">
                    <span class="dash-kpi__label">Inflow (range)</span>
                    <div class="dash-kpi__value" style="color:#059669">+{{ number_format($stats['inflowCount']) }}</div>
                    <div class="dash-muted">{{ number_format($stats['inflowAmount'], 2) }} total units</div>
                </div>
                <div class="dash-kpi">
                    <span class="dash-kpi__label">Outflow / transfers</span>
                    <div class="dash-kpi__value" style="color:#b45309">{{ number_format(($transfers['count'] ?? 0) + ($stats['outflowCount'] ?? 0)) }}</div>
                    <div class="dash-muted">
                        API {{ number_format($transfers['totalAmount'] ?? 0, 2) }}
                        @if (($stats['outflowAmount'] ?? 0) > 0)
                            · ledger {{ number_format($stats['outflowAmount'], 2) }}
                        @endif
                    </div>
                </div>
                <div class="dash-kpi">
                    <span class="dash-kpi__label">Successful txs</span>
                    <div class="dash-kpi__value">{{ number_format($stats['successfulCount']) }}</div>
                    <div class="dash-muted">{{ number_format($stats['pendingCount']) }} pending · {{ number_format($stats['failedCount']) }} failed</div>
                </div>
                <div class="dash-kpi">
                    <span class="dash-kpi__label">Subscriptions</span>
                    <div class="dash-kpi__value">{{ number_format($stats['subscriptionTxCount'] + $stats['communityTxCount']) }}</div>
                    <div class="dash-muted">{{ number_format($stats['subscriptionTxCount']) }} level · {{ number_format($stats['communityTxCount']) }} community</div>
                </div>
            </div>
        </section>

        @if ($tab === 'overview')
            <section class="dash-section dash-grid-2">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Inflow by currency</h2>
                    </div>
                    <div class="dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead><tr><th>Currency</th><th>Count</th><th>Amount</th></tr></thead>
                                <tbody>
                                    @forelse ($stats['inflowByCurrency'] as $row)
                                        <tr>
                                            <td>{{ $row->currency ?: '—' }}</td>
                                            <td>{{ number_format($row->total) }}</td>
                                            <td class="dash-num">{{ number_format((float) $row->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3"><div class="dash-empty">No successful inflows in this range.</div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Flutterwave transfers (outflow)</h2>
                    </div>
                    <div class="dash-card__body--flush">
                        @if (! ($transfers['ok'] ?? false))
                            <div class="dash-empty" style="padding:1.25rem">{{ $transfers['error'] ?? 'Transfers unavailable.' }}</div>
                        @else
                            <div class="dash-table-wrap">
                                <table class="dash-table">
                                    <thead><tr><th>Reference</th><th>Amount</th><th>Status</th><th>When</th></tr></thead>
                                    <tbody>
                                        @forelse ($transfers['items'] as $item)
                                            <tr>
                                                <td>
                                                    <strong>{{ $item['reference'] }}</strong>
                                                    @if ($item['account_name'])
                                                        <div class="dash-muted" style="font-size:.75rem">{{ $item['account_name'] }}</div>
                                                    @endif
                                                </td>
                                                <td class="dash-num">{{ $item['currency'] }} {{ number_format($item['amount'], 2) }}</td>
                                                <td><span class="dash-badge dash-badge--flw">{{ $item['status'] }}</span></td>
                                                <td class="dash-muted">{{ $item['created_at'] ?: '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4"><div class="dash-empty">No transfers in this range.</div></td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Recent Flutterwave transactions</h2>
                        <p class="dash-muted" style="margin:.25rem 0 0">Latest ledger rows with provider = flutterwave</p>
                    </div>
                    <a href="{{ route('admin.flutterwave.index', array_merge($dateRange->queryParams(), ['tab' => 'transactions'])) }}" class="dash-link">View all</a>
                </div>
                <div class="dash-card__body--flush">
                    @include('admin.flutterwave.partials.transactions-table')
                </div>
            </div>
        @endif

        @if ($tab === 'transactions')
            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Transactions</h2>
                        <p class="dash-muted" style="margin:.25rem 0 0">Filter Flutterwave ledger by status, type, currency, and flow</p>
                    </div>
                </div>
                <form method="get" action="{{ route('admin.flutterwave.index') }}" class="dash-filter-bar">
                    @foreach ($dateRange->queryParams() as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="tab" value="transactions">
                    <input type="search" name="q" value="{{ $search }}" class="dash-input" placeholder="Search ref, user, email…">
                    <select name="status" class="dash-input" style="max-width:150px">
                        <option value="">All statuses</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="type" class="dash-input" style="max-width:180px">
                        <option value="">All types</option>
                        @foreach ($typeLabels as $value => $label)
                            <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                        @endforeach
                        @foreach (($filterOptions['types'] ?? []) as $txType)
                            @continue(array_key_exists($txType, $typeLabels) || $txType === 'community')
                            <option value="{{ $txType }}" @selected($type === $txType)>{{ $txType }}</option>
                        @endforeach
                    </select>
                    <select name="currency" class="dash-input" style="max-width:120px">
                        <option value="">All currencies</option>
                        @foreach (($filterOptions['currencies'] ?? []) as $code)
                            <option value="{{ $code }}" @selected(($currency ?? '') === $code)>{{ $code }}</option>
                        @endforeach
                    </select>
                    <select name="flow" class="dash-input" style="max-width:120px">
                        <option value="">All flow</option>
                        <option value="in" @selected($flow === 'in')>Inflow</option>
                        <option value="out" @selected($flow === 'out')>Outflow</option>
                    </select>
                    <button type="submit" class="dash-btn dash-btn--primary">Filter</button>
                    @if ($search || $status || $type || $flow || ($currency ?? ''))
                        <a href="{{ route('admin.flutterwave.index', array_merge($dateRange->queryParams(), ['tab' => 'transactions'])) }}" class="dash-btn dash-btn--ghost">Clear</a>
                    @endif
                </form>
                <div class="dash-card__body--flush">
                    @include('admin.flutterwave.partials.transactions-table')
                    @if ($transactions && $transactions->hasPages())
                        <div class="dash-pagination">{{ $transactions->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>
            </div>
        @endif

        @if ($tab === 'subscriptions')
            <div class="dash-card" style="margin-bottom:1rem">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Community subscriptions</h2>
                        <p class="dash-muted" style="margin:.25rem 0 0">Click a row for payment details and next due date</p>
                    </div>
                </div>
                <form method="get" action="{{ route('admin.flutterwave.index') }}" class="dash-filter-bar">
                    @foreach ($dateRange->queryParams() as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="tab" value="subscriptions">
                    @if ($levelSearch ?? '')<input type="hidden" name="lq" value="{{ $levelSearch }}">@endif
                    @if ($levelPayment ?? '')<input type="hidden" name="payment" value="{{ $levelPayment }}">@endif
                    @if ($levelStatus ?? '')<input type="hidden" name="lstatus" value="{{ $levelStatus }}">@endif
                    <input type="search" name="q" value="{{ $search }}" class="dash-input" placeholder="Search member, community, ref…">
                    <select name="status" class="dash-input" style="max-width:150px">
                        <option value="">All statuses</option>
                        @foreach ($subscriptionStatusLabels as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="billing" class="dash-input" style="max-width:160px">
                        <option value="">All billing</option>
                        <option value="subscription" @selected(($billingType ?? '') === 'subscription')>Recurring</option>
                        <option value="one_off" @selected(($billingType ?? '') === 'one_off')>One-off</option>
                    </select>
                    <button type="submit" class="dash-btn dash-btn--primary">Filter</button>
                    @if ($search || $status || ($billingType ?? ''))
                        <a href="{{ route('admin.flutterwave.index', array_merge($dateRange->queryParams(), ['tab' => 'subscriptions'])) }}" class="dash-btn dash-btn--ghost">Clear</a>
                    @endif
                </form>
                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Community</th>
                                    <th>Billing</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Next payment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subscriptions ?? [] as $sub)
                                    @php
                                        $subStatus = $sub->status ?? '—';
                                        $subBadge = $subStatus === 'active' ? 'dash-badge--success' : ($subStatus === 'expired' ? 'dash-badge--warn' : 'dash-badge--danger');
                                        $pay = $sub->attached_payment;
                                        $payBadge = $pay?->status === 'successful' ? 'dash-badge--success'
                                            : (in_array($pay?->status, ['initiated', 'processing'], true) ? 'dash-badge--warn' : 'dash-badge--danger');
                                        $detailUrl = route('admin.flutterwave.subscriptions.show', ['kind' => 'community', 'id' => $sub->id]);
                                    @endphp
                                    <tr class="dash-row-link" onclick="window.location='{{ $detailUrl }}'">
                                        <td>
                                            <a href="{{ $detailUrl }}"><strong>{{ $sub->user?->name ?? 'N/A' }}</strong></a>
                                            <div class="dash-muted" style="font-size:.75rem">{{ $sub->user?->email }}</div>
                                        </td>
                                        <td>{{ $sub->community?->name ?? '—' }}</td>
                                        <td>
                                            <span class="dash-badge dash-badge--flw">{{ $sub->billing_type }}</span>
                                            @if ($sub->billing_interval)
                                                <div class="dash-muted" style="font-size:.75rem">{{ $sub->billing_interval }}</div>
                                            @endif
                                        </td>
                                        <td class="dash-num">
                                            {{ strtoupper($sub->community?->currency ?? '') }} {{ number_format((float) $sub->amount, 2) }}
                                        </td>
                                        <td>
                                            @if ($pay)
                                                <span class="dash-badge {{ $payBadge }}">{{ $pay->status }}</span>
                                                <div class="dash-muted" style="font-size:.75rem">{{ $pay->ref }}</div>
                                            @else
                                                <span class="dash-badge dash-badge--warn">No payment</span>
                                            @endif
                                        </td>
                                        <td class="dash-muted">{{ $sub->next_payment_label }}</td>
                                        <td><span class="dash-badge {{ $subBadge }}">{{ $subStatus }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7"><div class="dash-empty">No Flutterwave community subscriptions in this range.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($subscriptions && $subscriptions->hasPages())
                        <div class="dash-pagination">{{ $subscriptions->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Level payment plans</h2>
                        <p class="dash-muted" style="margin:.25rem 0 0">Search members/plans and filter by payment attachment</p>
                    </div>
                </div>
                <form method="get" action="{{ route('admin.flutterwave.index') }}" class="dash-filter-bar">
                    @foreach ($dateRange->queryParams() as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="tab" value="subscriptions">
                    @if ($search)<input type="hidden" name="q" value="{{ $search }}">@endif
                    @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                    @if ($billingType ?? '')<input type="hidden" name="billing" value="{{ $billingType }}">@endif
                    <input type="search" name="lq" value="{{ $levelSearch ?? '' }}" class="dash-input" placeholder="Search member, email, level, plan ID…">
                    <select name="payment" class="dash-input" style="max-width:180px">
                        <option value="">All payment states</option>
                        <option value="with" @selected(($levelPayment ?? '') === 'with')>With payment</option>
                        <option value="without" @selected(($levelPayment ?? '') === 'without')>Without payment</option>
                    </select>
                    <select name="lstatus" class="dash-input" style="max-width:150px">
                        <option value="">All plan statuses</option>
                        <option value="active" @selected(($levelStatus ?? '') === 'active')>Active</option>
                        <option value="inactive" @selected(($levelStatus ?? '') === 'inactive')>Inactive</option>
                    </select>
                    <button type="submit" class="dash-btn dash-btn--primary">Filter</button>
                    @if (($levelSearch ?? '') || ($levelPayment ?? '') || ($levelStatus ?? ''))
                        <a href="{{ route('admin.flutterwave.index', array_merge($dateRange->queryParams(), array_filter([
                            'tab' => 'subscriptions',
                            'q' => $search ?: null,
                            'status' => $status ?: null,
                            'billing' => $billingType ?: null,
                        ]))) }}" class="dash-btn dash-btn--ghost">Clear</a>
                    @endif
                </form>
                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Level</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Next payment</th>
                                    <th>Plan ID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($levelPlans ?? [] as $plan)
                                    @php
                                        $pay = $plan->attached_payment;
                                        $payBadge = $pay?->status === 'successful' ? 'dash-badge--success'
                                            : (in_array($pay?->status, ['initiated', 'processing'], true) ? 'dash-badge--warn' : 'dash-badge--danger');
                                        $detailUrl = route('admin.flutterwave.subscriptions.show', ['kind' => 'level', 'id' => $plan->id]);
                                    @endphp
                                    <tr class="dash-row-link" onclick="window.location='{{ $detailUrl }}'">
                                        <td>
                                            <a href="{{ $detailUrl }}"><strong>{{ $plan->user?->name ?? 'N/A' }}</strong></a>
                                            <div class="dash-muted" style="font-size:.75rem">{{ $plan->user?->email }}</div>
                                        </td>
                                        <td>{{ $plan->level?->name ?? $plan->name ?? '—' }}</td>
                                        <td class="dash-num">{{ $plan->currency }} {{ number_format((float) $plan->amount, 2) }}</td>
                                        <td>
                                            @if ($pay)
                                                <span class="dash-badge {{ $payBadge }}">{{ $pay->status }}</span>
                                                <div class="dash-muted" style="font-size:.75rem">{{ $pay->ref }}</div>
                                            @else
                                                <span class="dash-badge dash-badge--warn">No payment</span>
                                            @endif
                                        </td>
                                        <td class="dash-muted">{{ $plan->next_payment_label }}</td>
                                        <td class="dash-muted" style="font-size:.8rem">{{ $plan->payment_plan_id ?: '—' }}</td>
                                        <td><span class="dash-badge dash-badge--flw">{{ $plan->user_level?->status ?? ($plan->status ?: '—') }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7"><div class="dash-empty">No Flutterwave level plans match your filters.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($levelPlans && method_exists($levelPlans, 'hasPages') && $levelPlans->hasPages())
                        <div class="dash-pagination">{{ $levelPlans->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>
            </div>
        @endif

        @if ($tab === 'wallets')
            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Platform wallet balances</h2>
                        <p class="dash-muted" style="margin:.25rem 0 0">Local Payhankey user wallets (not Flutterwave merchant float)</p>
                    </div>
                </div>
                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Currency</th>
                                    <th>Wallets</th>
                                    <th>Main</th>
                                    <th>Referral</th>
                                    <th>Promoter</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($walletTotals as $row)
                                    <tr>
                                        <td><strong>{{ $row->currency ?: '—' }}</strong></td>
                                        <td>{{ number_format($row->wallets) }}</td>
                                        <td class="dash-num">{{ number_format((float) $row->main, 2) }}</td>
                                        <td class="dash-num">{{ number_format((float) $row->referral, 2) }}</td>
                                        <td class="dash-num">{{ number_format((float) $row->promoter, 2) }}</td>
                                        <td class="dash-num"><strong>{{ number_format((float) $row->main + (float) $row->referral + (float) $row->promoter, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6"><div class="dash-empty">No wallets found.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
