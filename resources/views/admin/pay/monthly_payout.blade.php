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
        .dash-num { font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }

        .dash-split {
            display: grid;
            gap: 0.75rem;
        }

        .dash-split__item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.875rem 1rem;
            border: 1px solid var(--dash-border);
            border-radius: 12px;
            background: #fafafa;
        }

        .dash-split__title { margin: 0; font-size: 0.875rem; font-weight: 600; }
        .dash-split__desc { margin: 0.25rem 0 0; font-size: 0.8125rem; color: var(--dash-muted); }

        @media (max-width: 1200px) {
            .dash-grid--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .dash-grid--4, .dash-grid--2 { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    @php
        $totals = collect($results);
        $sumRevenue = $totals->sum('totalRev');
        $sumLevelPool = $totals->sum('levelPool');
        $sumMembers = $totals->sum('memberCount');
        $sumEngagement = $totals->sum('totalEngagement');
        $periodLabel = \Carbon\Carbon::parse($startMonth)->format('j M') . ' – ' . \Carbon\Carbon::parse($endMonth)->format('j M Y');
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Pro-rata payouts</h1>
                    <p>Monthly revenue split · {{ $periodLabel }}</p>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> Dashboard
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
                        <span class="dash-kpi__label">Total revenue</span>
                        <div class="dash-kpi__value">₦{{ number_format(convertToBaseCurrency($sumRevenue, 'NGN'), 2) }}</div>
                        <div class="dash-kpi__hint">Creator + Influencer tiers</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Level pool (50%)</span>
                        <div class="dash-kpi__value">₦{{ number_format(convertToBaseCurrency($sumLevelPool, 'NGN'), 2) }}</div>
                        <div class="dash-kpi__hint">Shared pro-rata by engagement</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Active members</span>
                        <div class="dash-kpi__value">{{ number_format($sumMembers) }}</div>
                        <div class="dash-kpi__hint">Across paid levels</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Total engagement</span>
                        <div class="dash-kpi__value">{{ number_format($sumEngagement) }}</div>
                        <div class="dash-kpi__hint">Views + likes + comments</div>
                    </div>
                </div>
            </section>

            <div class="dash-card dash-section">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Level breakdown</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">Revenue allocation by subscription tier</p>
                    </div>
                </div>

                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>Total rev.</th>
                                    <th>Platform (30%)</th>
                                    <th>Level pool (50%)</th>
                                    <th>Savings (10%)</th>
                                    <th>Freemium (10%)</th>
                                    <th>Engagement</th>
                                    <th>Members</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($results as $row)
                                    <tr>
                                        <td><span class="dash-badge dash-badge--indigo">{{ $row['level'] }}</span></td>
                                        <td class="dash-num">₦{{ number_format(convertToBaseCurrency($row['totalRev'], 'NGN'), 2) }}</td>
                                        <td class="dash-num">₦{{ number_format(convertToBaseCurrency($row['platformRev'], 'NGN'), 2) }}</td>
                                        <td class="dash-num">₦{{ number_format(convertToBaseCurrency($row['levelPool'], 'NGN'), 2) }}</td>
                                        <td class="dash-num">₦{{ number_format(convertToBaseCurrency($row['savingsPool'], 'NGN'), 2) }}</td>
                                        <td class="dash-num">₦{{ number_format(convertToBaseCurrency($row['fremiumPool'], 'NGN'), 2) }}</td>
                                        <td class="dash-num">{{ number_format($row['totalEngagement']) }}</td>
                                        <td class="dash-num">{{ number_format($row['memberCount']) }}</td>
                                        <td>
                                            <a href="{{ route('admin.payouts.monthly.users', $row['level']) }}" class="dash-btn dash-btn--primary" style="padding:0.5rem 0.75rem;">
                                                View users
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="dash-empty">No payout data available for this month.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <section class="dash-section dash-grid dash-grid--2">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Revenue split guide</h2>
                    </div>
                    <div class="dash-card__body">
                        <div class="dash-split">
                            <div class="dash-split__item">
                                <div>
                                    <p class="dash-split__title">Total revenue</p>
                                    <p class="dash-split__desc">Combined subscription revenue from each paid level.</p>
                                </div>
                                <span class="dash-badge dash-badge--indigo">100%</span>
                            </div>
                            <div class="dash-split__item">
                                <div>
                                    <p class="dash-split__title">Level pool</p>
                                    <p class="dash-split__desc">Shared among level members on a pro-rata basis by engagement.</p>
                                </div>
                                <span class="dash-badge dash-badge--emerald">50%</span>
                            </div>
                            <div class="dash-split__item">
                                <div>
                                    <p class="dash-split__title">Platform revenue</p>
                                    <p class="dash-split__desc">Platform operating allocation.</p>
                                </div>
                                <span class="dash-badge dash-badge--gray">30%</span>
                            </div>
                            <div class="dash-split__item">
                                <div>
                                    <p class="dash-split__title">Platform savings</p>
                                    <p class="dash-split__desc">Reserved savings allocation.</p>
                                </div>
                                <span class="dash-badge dash-badge--gray">10%</span>
                            </div>
                            <div class="dash-split__item">
                                <div>
                                    <p class="dash-split__title">Freemium pool</p>
                                    <p class="dash-split__desc">10% from Creator and Influencer levels, shared among Basic users pro-rata.</p>
                                </div>
                                <span class="dash-badge dash-badge--amber">10%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Engagement scoring</h2>
                    </div>
                    <div class="dash-card__body">
                        <p class="dash-muted" style="margin:0 0 1rem;">
                            Total engagement is the sum of views, likes, and comments from all active members in a level during the period.
                            Each member's payout from the level pool is:
                        </p>
                        <p style="margin:0; font-size:0.875rem; font-weight:600;">
                            (member engagement ÷ total level engagement) × level pool
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
