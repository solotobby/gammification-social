@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .dash-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        .dash-kpi {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1.25rem;
            background: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            box-shadow: var(--dash-shadow);
            height: 100%;
        }

        .dash-kpi__label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dash-muted);
        }

        .dash-kpi__value {
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .dash-kpi__hint { font-size: 0.8125rem; color: var(--dash-muted); }
        .dash-num { font-weight: 600; font-variant-numeric: tabular-nums; }

        .dash-dl {
            display: grid;
            grid-template-columns: minmax(140px, 42%) 1fr;
            gap: 0.75rem 1rem;
            margin: 0;
            font-size: 0.875rem;
        }

        .dash-dl dt { margin: 0; font-weight: 600; color: var(--dash-muted); }
        .dash-dl dd { margin: 0; }

        @media (max-width: 1200px) {
            .dash-grid--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .dash-grid--4, .dash-grid--2 { grid-template-columns: 1fr; }
            .dash-dl { grid-template-columns: 1fr; gap: 0.25rem; }
        }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>{{ $level }} payouts</h1>
                    <p>Pro-rata member breakdown · {{ $month }}</p>
                </div>
                <a href="{{ route('admin.payouts.pro-rata') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> Pro-rata overview
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Members</span>
                        <div class="dash-kpi__value">{{ number_format($memberCount) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Total engagement</span>
                        <div class="dash-kpi__value">{{ number_format($totalEngagement) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Tier pool (50%)</span>
                        <div class="dash-kpi__value">₦{{ number_format(convertToBaseCurrency($tierPool, 'NGN'), 2) }}</div>
                        <div class="dash-kpi__hint">Distributed pro-rata</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Total revenue</span>
                        <div class="dash-kpi__value">₦{{ number_format(convertToBaseCurrency($totalRevenue, 'NGN'), 2) }}</div>
                    </div>
                </div>
            </section>

            <section class="dash-section dash-grid dash-grid--2">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Pool allocation</h2>
                    </div>
                    <div class="dash-card__body">
                        <dl class="dash-dl">
                            <dt>Platform cut</dt>
                            <dd class="dash-num">₦{{ number_format(convertToBaseCurrency($platformPool, 'NGN'), 2) }} <span class="dash-muted">(30%)</span></dd>
                            <dt>Tier pool</dt>
                            <dd class="dash-num">₦{{ number_format(convertToBaseCurrency($tierPool, 'NGN'), 2) }} <span class="dash-muted">(50%)</span></dd>
                            <dt>Savings pool</dt>
                            <dd class="dash-num">₦{{ number_format(convertToBaseCurrency($savingsPool, 'NGN'), 2) }} <span class="dash-muted">(10%)</span></dd>
                            <dt>Freemium pool</dt>
                            <dd class="dash-num">₦{{ number_format(convertToBaseCurrency($fremiumPool, 'NGN'), 2) }} <span class="dash-muted">(10%)</span></dd>
                        </dl>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Process engagement</h2>
                    </div>
                    <div class="dash-card__body">
                        <p class="dash-muted" style="margin:0 0 1rem;">
                            Aggregate daily engagement stats into monthly records before final payout processing.
                        </p>
                        <form method="POST" action="{{ route('admin.payouts.process-level', $level) }}"
                            onsubmit="return confirm('Process monthly engagement stats for {{ $level }}?');">
                            @csrf
                            <button type="submit" class="dash-btn dash-btn--primary">
                                <i class="fa fa-calculator"></i> Process engagement stats
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Member payouts</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            {{ count($users) }} member{{ count($users) === 1 ? '' : 's' }} · tier pool ₦{{ number_format(convertToBaseCurrency($tierPool, 'NGN'), 2) }}
                        </p>
                    </div>
                </div>

                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Engagement</th>
                                    <th>Share</th>
                                    <th>Payout</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user['name'] }}</td>
                                        <td class="dash-num">{{ number_format($user['engagement']) }}</td>
                                        <td>
                                            <span class="dash-badge dash-badge--indigo">{{ $user['percentage'] }}%</span>
                                        </td>
                                        <td class="dash-num">₦{{ number_format(convertToBaseCurrency($user['payout'], 'NGN'), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
