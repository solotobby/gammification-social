@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dash-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

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

        .dash-kpi__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dash-kpi__icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1rem;
        }

        .dash-kpi__icon--sky { background: #f0f9ff; color: #0284c7; }
        .dash-kpi__icon--amber { background: #fffbeb; color: #d97706; }
        .dash-kpi__icon--emerald { background: #ecfdf5; color: #059669; }
        .dash-kpi__icon--indigo { background: #eef2ff; color: #4f46e5; }

        .dash-kpi__label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dash-muted);
        }

        .dash-kpi__value {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .dash-kpi__hint {
            font-size: 0.8125rem;
            color: var(--dash-muted);
        }

        .dash-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .dash-chart {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .dash-num {
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        .dash-note {
            margin: 0;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid var(--dash-border);
            color: var(--dash-muted);
            font-size: 0.8125rem;
            line-height: 1.5;
        }

        @media (max-width: 960px) {
            .dash-grid--2, .dash-grid--4 { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    @php
        $currency = $user->wallet?->currency ?? 'NGN';
        $currencySymbol = getCurrencyCode($currency);
        $hasDaily = ($dailyEngagements->total() ?? 0) > 0;
        $hasMonthly = $monthlyStats->isNotEmpty();
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Engagement</h1>
                    <p>{{ $user->name }} · {{ $user->email }}</p>
                    <div class="dash-meta">
                        <span class="dash-badge dash-badge--indigo">{{ $planName }}</span>
                        @if (in_array($planName, ['Creator', 'Influencer'], true))
                            <span class="dash-badge dash-badge--emerald">Payout eligible</span>
                        @else
                            <span class="dash-badge dash-badge--gray">Basic tier</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $user) }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> User profile
                </a>
            </header>

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Total views</span>
                            <span class="dash-kpi__icon dash-kpi__icon--sky"><i class="fa fa-eye"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($totals->views ?? 0) }}</div>
                        <div class="dash-kpi__hint">All recorded daily stats</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Total likes</span>
                            <span class="dash-kpi__icon dash-kpi__icon--amber"><i class="fa fa-heart"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($totals->likes ?? 0) }}</div>
                        <div class="dash-kpi__hint">Across payout periods</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Total comments</span>
                            <span class="dash-kpi__icon dash-kpi__icon--emerald"><i class="fa fa-comment"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($totals->comments ?? 0) }}</div>
                        <div class="dash-kpi__hint">Across payout periods</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Engagement points</span>
                            <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-chart-line"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($totals->points ?? 0) }}</div>
                        <div class="dash-kpi__hint">
                            {{ number_format($totals->active_days ?? 0) }} active days
                            @if (($monthlyTotals->amount ?? 0) > 0)
                                · {{ $currencySymbol }}{{ number_format((float) $monthlyTotals->amount, 2) }} earned
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <section class="dash-section dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Daily trend</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            Last 30 days · {{ number_format($chart['total'] ?? 0) }} points
                        </p>
                    </div>
                </div>
                <div class="dash-card__body">
                    @if (($chart['total'] ?? 0) > 0)
                        <div class="dash-chart">
                            <canvas id="engagement-trend-chart" role="img" aria-label="Daily engagement trend"></canvas>
                        </div>
                    @else
                        <div class="dash-empty">No daily engagement in the last 30 days.</div>
                    @endif
                </div>
            </section>

            <section class="dash-section dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Daily breakdown</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            {{ number_format($dailyEngagements->total()) }} records · showing {{ $dailyEngagements->firstItem() ?? 0 }}–{{ $dailyEngagements->lastItem() ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="dash-card__body--flush">
                    @if ($hasDaily)
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Level</th>
                                        <th>Views</th>
                                        <th>Likes</th>
                                        <th>Comments</th>
                                        <th>Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dailyEngagements as $engagement)
                                        <tr>
                                            <td class="dash-muted">
                                                {{ $engagement->date?->format('M j, Y') }}
                                                <span style="display:block; font-size:0.75rem;">
                                                    {{ $engagement->date?->format('l') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="dash-badge dash-badge--indigo">{{ $engagement->level }}</span>
                                            </td>
                                            <td class="dash-num">{{ number_format($engagement->views) }}</td>
                                            <td class="dash-num">{{ number_format($engagement->likes) }}</td>
                                            <td class="dash-num">{{ number_format($engagement->comments) }}</td>
                                            <td class="dash-num">{{ number_format($engagement->points) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($dailyEngagements->hasPages())
                            <div class="dash-pagination">
                                {{ $dailyEngagements->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @else
                        <div class="dash-empty">
                            No daily engagement stats recorded for this user yet.
                        </div>
                    @endif
                </div>
            </section>

            <section class="dash-section dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Monthly summaries</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            Processed monthly stats used for pro-rata payouts
                        </p>
                    </div>
                </div>

                <div class="dash-card__body--flush">
                    @if ($hasMonthly)
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Level</th>
                                        <th>Views</th>
                                        <th>Likes</th>
                                        <th>Comments</th>
                                        <th>Points</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monthlyStats as $stat)
                                        @php
                                            $statusClass = match ($stat->status) {
                                                'Paid' => 'dash-badge--emerald',
                                                'Queued' => 'dash-badge--amber',
                                                default => 'dash-badge--gray',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="dash-muted">{{ $stat->month }}</td>
                                            <td>
                                                <span class="dash-badge dash-badge--indigo">{{ $stat->level }}</span>
                                            </td>
                                            <td class="dash-num">{{ number_format($stat->views) }}</td>
                                            <td class="dash-num">{{ number_format($stat->likes) }}</td>
                                            <td class="dash-num">{{ number_format($stat->comments) }}</td>
                                            <td class="dash-num">{{ number_format($stat->points) }}</td>
                                            <td class="dash-num">
                                                {{ $currencySymbol }}{{ number_format((float) $stat->amount, 2) }}
                                            </td>
                                            <td>
                                                <span class="dash-badge {{ $statusClass }}">{{ $stat->status }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="dash-empty">
                            No monthly engagement summaries yet.
                        </div>
                    @endif
                </div>
            </section>

            @unless ($hasDaily || $hasMonthly)
                <section class="dash-section">
                    <p class="dash-note">
                        Daily and monthly stats are generated by the scheduled engagement jobs for Creator and Influencer accounts.
                        If this user recently upgraded, stats will appear after the next daily run and monthly payout cycle.
                    </p>
                </section>
            @endunless
        </div>
    </div>
@endsection

@if (($chart['total'] ?? 0) > 0)
    @section('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                var labels = @json($chart['labels'] ?? []);
                var views = @json($chart['views'] ?? []);
                var likes = @json($chart['likes'] ?? []);
                var comments = @json($chart['comments'] ?? []);

                function initChart() {
                    if (typeof Chart === 'undefined') {
                        return;
                    }

                    var canvas = document.getElementById('engagement-trend-chart');
                    if (!canvas) {
                        return;
                    }

                    new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Views',
                                    data: views,
                                    backgroundColor: 'rgba(14, 165, 233, 0.9)',
                                    borderRadius: 3,
                                    maxBarThickness: 18,
                                    stack: 'engagement',
                                },
                                {
                                    label: 'Likes',
                                    data: likes,
                                    backgroundColor: 'rgba(245, 158, 11, 0.9)',
                                    borderRadius: 3,
                                    maxBarThickness: 18,
                                    stack: 'engagement',
                                },
                                {
                                    label: 'Comments',
                                    data: comments,
                                    backgroundColor: 'rgba(16, 185, 129, 0.9)',
                                    borderRadius: 3,
                                    maxBarThickness: 18,
                                    stack: 'engagement',
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { intersect: false, mode: 'index' },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 10,
                                        boxHeight: 10,
                                        color: '#64748b',
                                        font: { size: 11 },
                                        padding: 14,
                                    },
                                },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 10,
                                    cornerRadius: 8,
                                },
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    grid: { display: false },
                                    ticks: {
                                        color: '#64748b',
                                        maxTicksLimit: 8,
                                        font: { size: 11 },
                                    },
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9' },
                                    ticks: {
                                        color: '#64748b',
                                        precision: 0,
                                        font: { size: 11 },
                                    },
                                },
                            },
                        },
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function () {
                        window.requestAnimationFrame(initChart);
                    });
                } else {
                    window.requestAnimationFrame(initChart);
                }
            })();
        </script>
    @endsection
@endif
