@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-badge--kora { background: rgba(99,102,241,.12); color: #4338ca; }
        .dash-badge--success { background: rgba(16,185,129,.12); color: #067647; }
        .dash-badge--warn { background: rgba(245,158,11,.14); color: #b54708; }
        .dash-badge--danger { background: rgba(220,53,69,.12); color: #b42318; }
        .dash-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start; }
        @media (max-width: 960px) { .dash-grid-2 { grid-template-columns: 1fr; } }
        .dash-form { display: grid; gap: 1rem; }
        .dash-form__hint { margin: 0 0 .5rem; color: var(--dash-muted); font-size: .875rem; line-height: 1.5; }

        /* Kora Pay metrics */
        .kora-metrics {
            display: grid;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .kora-hero {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 1.5rem 1.75rem;
            background:
                radial-gradient(circle at 85% 15%, rgba(52, 211, 153, .22) 0%, transparent 42%),
                radial-gradient(circle at 10% 90%, rgba(99, 102, 241, .35) 0%, transparent 45%),
                linear-gradient(135deg, #0b1220 0%, #151b33 42%, #1e1b4b 100%);
            border: 1px solid rgba(255, 255, 255, .08);
            box-shadow:
                0 1px 0 rgba(255, 255, 255, .06) inset,
                0 24px 48px -12px rgba(15, 23, 42, .45);
        }

        .kora-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .03) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: linear-gradient(180deg, rgba(0, 0, 0, .5) 0%, transparent 85%);
            pointer-events: none;
        }

        .kora-hero__glow {
            position: absolute;
            top: -40%;
            right: -8%;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, .18) 0%, transparent 70%);
            pointer-events: none;
        }

        .kora-hero__top,
        .kora-hero__main,
        .kora-hero__error {
            position: relative;
            z-index: 1;
        }

        .kora-hero__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: 1.35rem;
        }

        .kora-hero__badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .35rem .75rem;
            border-radius: 999px;
            background: rgba(16, 185, 129, .12);
            border: 1px solid rgba(52, 211, 153, .25);
            color: #6ee7b7;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .kora-pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 0 0 rgba(52, 211, 153, .6);
            animation: kora-pulse 2s ease-out infinite;
        }

        @keyframes kora-pulse {
            0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, .55); }
            70% { box-shadow: 0 0 0 8px rgba(52, 211, 153, 0); }
            100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
        }

        .kora-hero__tag {
            font-size: .75rem;
            font-weight: 600;
            color: rgba(226, 232, 240, .55);
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .kora-hero__main {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 1.25rem;
            align-items: end;
        }

        @media (max-width: 720px) {
            .kora-hero__main { grid-template-columns: 1fr; }
        }

        .kora-hero__label {
            display: block;
            font-size: .8125rem;
            font-weight: 600;
            color: rgba(226, 232, 240, .65);
            letter-spacing: .02em;
            margin-bottom: .5rem;
        }

        .kora-hero__amount {
            font-size: clamp(2rem, 5vw, 2.75rem);
            font-weight: 700;
            letter-spacing: -.04em;
            line-height: 1.05;
            color: #f8fafc;
            font-variant-numeric: tabular-nums;
            text-shadow: 0 2px 24px rgba(52, 211, 153, .25);
        }

        .kora-hero__sub {
            display: inline-block;
            margin-top: .65rem;
            font-size: .8125rem;
            color: rgba(148, 163, 184, .9);
        }

        .kora-hero__chips {
            display: grid;
            gap: .75rem;
        }

        .kora-chip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .9rem 1.1rem;
            border-radius: 14px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .08);
            backdrop-filter: blur(8px);
        }

        .kora-chip__label {
            display: flex;
            align-items: center;
            gap: .55rem;
            font-size: .8125rem;
            font-weight: 600;
            color: rgba(226, 232, 240, .75);
        }

        .kora-chip__label i {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: .75rem;
        }

        .kora-chip--pending .kora-chip__label i {
            background: rgba(245, 158, 11, .15);
            color: #fbbf24;
        }

        .kora-chip__value {
            font-size: 1.125rem;
            font-weight: 700;
            color: #f8fafc;
            font-variant-numeric: tabular-nums;
            letter-spacing: -.02em;
        }

        .kora-hero__error {
            padding: 1rem 1.15rem;
            border-radius: 14px;
            background: rgba(239, 68, 68, .12);
            border: 1px solid rgba(248, 113, 113, .25);
            color: #fecaca;
            font-size: .875rem;
            font-weight: 500;
        }

        .kora-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        @media (max-width: 900px) {
            .kora-stats { grid-template-columns: 1fr; }
        }

        .kora-stat {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.15rem 1.25rem;
            border-radius: 16px;
            background: var(--dash-surface);
            border: 1px solid var(--dash-border);
            box-shadow: var(--dash-shadow);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .kora-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px -8px rgba(15, 23, 42, .12);
        }

        .kora-stat::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }

        .kora-stat--fund::after { background: linear-gradient(90deg, #10b981, #34d399); }
        .kora-stat--pending::after { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .kora-stat--count::after { background: linear-gradient(90deg, #6366f1, #818cf8); }

        .kora-stat--fund:hover { border-color: rgba(16, 185, 129, .35); }
        .kora-stat--pending:hover { border-color: rgba(245, 158, 11, .35); }
        .kora-stat--count:hover { border-color: rgba(99, 102, 241, .35); }

        .kora-stat__icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1rem;
        }

        .kora-stat--fund .kora-stat__icon {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
        }

        .kora-stat--pending .kora-stat__icon {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            color: #d97706;
        }

        .kora-stat--count .kora-stat__icon {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            color: #4f46e5;
        }

        .kora-stat__label {
            display: block;
            font-size: .6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--dash-muted);
            margin-bottom: .35rem;
        }

        .kora-stat__value {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -.03em;
            line-height: 1.15;
            font-variant-numeric: tabular-nums;
            color: var(--dash-text);
        }

        .kora-stat__hint {
            display: block;
            margin-top: .35rem;
            font-size: .8125rem;
            color: var(--dash-muted);
        }
    </style>
@endsection

@section('content')
@php
    $formatNgn = fn (float $amount): string => '₦'.number_format($amount, 0);
@endphp
<div class="content p-0"><div class="dash">
    <header class="dash-header">
        <div>
            <h1>Kora Pay</h1>
            <p>NGN merchant balance — fund your Korapay account for NGN payouts and disbursements · {{ $dateRange->label() }}</p>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
            <a href="{{ route('admin.finance.index') }}" class="dash-btn dash-btn--ghost">Wallets & earnings</a>
            <a href="{{ route('admin.payouts.current') }}" class="dash-btn dash-btn--ghost">Current payouts</a>
        </div>
    </header>

    @if (session('success'))
        <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="dash-alert dash-alert--success">{{ session('info') }}</div>
    @endif
    @if (session('error'))
        <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
    @endif

    <section class="kora-metrics">
        <div class="kora-hero">
            <div class="kora-hero__glow"></div>

            <div class="kora-hero__top">
                <span class="kora-hero__badge"><span class="kora-pulse"></span> Live balance</span>
                <span class="kora-hero__tag">Korapay · NGN</span>
            </div>

            @if (! $balances['ok'])
                <div class="kora-hero__error">
                    <i class="fa fa-circle-exclamation"></i>
                    {{ $balances['error'] ?? 'Unable to load balances.' }}
                </div>
            @elseif ($balances['currencies']->isEmpty())
                <div class="kora-hero__error">No NGN balance data returned from Korapay.</div>
            @else
                @php $ngn = $balances['currencies']->first(); @endphp
                <div class="kora-hero__main">
                    <div class="kora-hero__primary">
                        <span class="kora-hero__label">Available to spend</span>
                        <div class="kora-hero__amount">{{ $formatNgn($ngn['available']) }}</div>
                        <span class="kora-hero__sub">Ready for NGN payouts & disbursements</span>
                    </div>
                    <div class="kora-hero__chips">
                        <div class="kora-chip kora-chip--pending">
                            <span class="kora-chip__label">
                                <i class="fa fa-clock"></i>
                                Pending settlement
                            </span>
                            <span class="kora-chip__value">{{ $formatNgn($ngn['pending']) }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="kora-stats">
            <div class="kora-stat kora-stat--fund">
                <div class="kora-stat__icon"><i class="fa fa-arrow-trend-up"></i></div>
                <div>
                    <span class="kora-stat__label">Funded · {{ $dateRange->label() }}</span>
                    <div class="kora-stat__value">{{ $formatNgn($stats['ngnInRange']) }}</div>
                    <span class="kora-stat__hint">{{ number_format($stats['successfulInRange']) }} successful deposit{{ $stats['successfulInRange'] === 1 ? '' : 's' }}</span>
                </div>
            </div>
            <div class="kora-stat kora-stat--pending">
                <div class="kora-stat__icon"><i class="fa fa-hourglass-half"></i></div>
                <div>
                    <span class="kora-stat__label">Pending deposits</span>
                    <div class="kora-stat__value">{{ number_format($stats['pendingInRange']) }}</div>
                    <span class="kora-stat__hint">Awaiting Korapay confirmation</span>
                </div>
            </div>
            <div class="kora-stat kora-stat--count">
                <div class="kora-stat__icon"><i class="fa fa-receipt"></i></div>
                <div>
                    <span class="kora-stat__label">Total attempts</span>
                    <div class="kora-stat__value">{{ number_format($stats['countInRange']) }}</div>
                    <span class="kora-stat__hint">Deposits in selected period</span>
                </div>
            </div>
        </div>
    </section>

    <section class="dash-section dash-grid-2">
        <div class="dash-card">
            <div class="dash-card__head">
                <h2 class="dash-card__title">Deposit cash into Kora Pay</h2>
            </div>
            <div class="dash-card__body">
                <p class="dash-form__hint">
                    Deposit NGN into your Korapay merchant balance via card or bank transfer.
                    Funds are used for NGN platform payouts — they are not credited to any user wallet.
                </p>
                <form method="POST" action="{{ route('admin.korapay.deposit') }}" class="dash-form"
                    onsubmit="return confirm('Proceed to Korapay checkout to fund your NGN merchant balance?');">
                    @csrf
                    <div class="dash-field">
                        <label for="kora-amount">Amount (NGN)</label>
                        <input type="number" id="kora-amount" name="amount" class="dash-input"
                            min="100" step="1" required placeholder="e.g. 50000"
                            value="{{ old('amount') }}">
                        <span class="dash-form__hint">Minimum ₦100</span>
                    </div>
                    <div class="dash-field">
                        <label for="kora-note">Note (optional)</label>
                        <input type="text" id="kora-note" name="note" class="dash-input" maxlength="255"
                            placeholder="e.g. Monthly payout float" value="{{ old('note') }}">
                    </div>
                    <div class="dash-field">
                        <label for="kora-validation-code">Validation code</label>
                        <input type="text" id="kora-validation-code" name="validationCode" class="dash-input"
                            required placeholder="Enter validation code">
                    </div>
                    <button type="submit" class="dash-btn dash-btn--primary">
                        <i class="fa fa-arrow-up-right-from-square"></i> Continue to Korapay checkout
                    </button>
                </form>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card__head">
                <h2 class="dash-card__title">How it works</h2>
            </div>
            <div class="dash-card__body">
                <ol class="dash-muted" style="margin:0;padding-left:1.15rem;line-height:1.65">
                    <li>Enter the NGN amount and your validation code, then continue to Korapay.</li>
                    <li>Pay with card, bank transfer, or pay-with-bank on Korapay checkout.</li>
                    <li>Korapay credits your NGN merchant available balance once payment succeeds.</li>
                    <li>Use that balance as the funding source for NGN user payouts and disbursements.</li>
                </ol>
                <p class="dash-form__hint" style="margin-top:1rem">
                    Deposits appear below and in the finance ledger with type <code>korapay_funding</code>.
                </p>
            </div>
        </div>
    </section>

    @include('admin.partials.date-range-filter', [
        'routeName' => 'admin.korapay.index',
        'extraQuery' => ['status' => $status],
    ])

    <section class="dash-section dash-card">
        <div class="dash-card__head">
            <h2 class="dash-card__title">Deposit history</h2>
            <form method="get" action="{{ route('admin.korapay.index') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
                @foreach ($dateRange->queryParams() as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="status" class="dash-select" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    @foreach ($statusLabels as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="dash-card__body dash-card__body--flush">
            <div class="dash-table-wrap">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Initiated by</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deposits as $deposit)
                            @php
                                $badgeClass = match ($deposit->status) {
                                    'successful' => 'dash-badge--success',
                                    'failed', 'cancelled', 'flagged' => 'dash-badge--danger',
                                    'processing' => 'dash-badge--warn',
                                    default => 'dash-badge--kora',
                                };
                            @endphp
                            <tr>
                                <td>{{ $deposit->created_at->format('M j, Y g:i A') }}</td>
                                <td><code>{{ $deposit->ref }}</code></td>
                                <td>
                                    @if ($deposit->user)
                                        <a href="{{ route('admin.users.show', $deposit->user) }}" class="dash-link">
                                            {{ $deposit->user->username ?? $deposit->user->name }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $formatNgn((float) $deposit->amount) }}</td>
                                <td><span class="dash-badge {{ $badgeClass }}">{{ $statusLabels[$deposit->status] ?? ucfirst($deposit->status) }}</span></td>
                                <td>{{ $deposit->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="dash-muted" style="text-align:center;padding:2rem">No deposits in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($deposits->hasPages())
                <div class="dash-card__foot">{{ $deposits->links() }}</div>
            @endif
        </div>
    </section>
</div></div>
@endsection
