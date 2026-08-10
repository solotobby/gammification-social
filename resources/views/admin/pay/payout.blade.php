@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
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

        @media (max-width: 1200px) {
            .dash-grid--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .dash-grid--4 { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    @php
        $influencerRevenue = $totalInfluncers * 7500;
        $creatorRevenue = $totalCreator * 1500;
        $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->format('F Y');
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Current payouts</h1>
                    <p>Subscription activity for {{ $monthLabel }}</p>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> Dashboard
                </a>
            </header>

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Influencers</span>
                        <div class="dash-kpi__value">{{ number_format($totalInfluncers) }}</div>
                        <div class="dash-kpi__hint">₦{{ number_format($influencerRevenue, 2) }} revenue</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Creators</span>
                        <div class="dash-kpi__value">{{ number_format($totalCreator) }}</div>
                        <div class="dash-kpi__hint">₦{{ number_format($creatorRevenue, 2) }} revenue</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Total subscriptions</span>
                        <div class="dash-kpi__value">{{ number_format($count) }}</div>
                        <div class="dash-kpi__hint">This month</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Total revenue</span>
                        <div class="dash-kpi__value">₦{{ number_format($totalRev, 2) }}</div>
                        <div class="dash-kpi__hint">Estimated from plan pricing</div>
                    </div>
                </div>
            </section>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Subscription records</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            {{ number_format($stats->total()) }} total · showing {{ $stats->firstItem() ?? 0 }}–{{ $stats->lastItem() ?? 0 }}
                        </p>
                    </div>
                    <a href="{{ route('admin.payouts.pro-rata') }}" class="dash-link">Pro-rata overview</a>
                </div>

                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Level</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Period</th>
                                    <th>Recorded</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stats as $stat)
                                    <tr>
                                        <td>{{ $stat->user?->name ?? '—' }}</td>
                                        <td><span class="dash-badge dash-badge--indigo">{{ $stat->plan_name }}</span></td>
                                        <td class="dash-num">{{ number_format((float) $stat->amount, 2) }}</td>
                                        <td class="dash-muted">{{ $stat->currency }}</td>
                                        <td class="dash-muted">
                                            {{ $stat->start_date ? \Carbon\Carbon::parse($stat->start_date)->format('M j, Y') : '—' }}
                                            –
                                            {{ $stat->end_date ? \Carbon\Carbon::parse($stat->end_date)->format('M j, Y') : '—' }}
                                        </td>
                                        <td class="dash-muted">
                                            {{ $stat->created_at?->format('M j, Y') }}
                                            <span style="display:block; font-size:0.75rem;">
                                                {{ $stat->created_at?->format('g:i A') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="dash-empty">No subscription records for this month.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($stats->hasPages())
                        <div class="dash-pagination">
                            {{ $stats->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
