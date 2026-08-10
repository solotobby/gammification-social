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

        .dash-num { font-weight: 600; font-variant-numeric: tabular-nums; }
        .dash-badge--amber { background: #fffbeb; color: #b45309; }

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
        $tabs = $levelTabs ?? ['Influencer', 'Creator', 'Basic'];
        $currentLevel = $level ?? 'Influencer';
        $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $lastmonth ?? now()->subMonth()->format('Y-m'))->format('F Y');
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Level payouts</h1>
                    <p>Pro-rata distribution · {{ $monthLabel }}</p>
                </div>
                <a href="{{ route('admin.payouts.pro-rata') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> Pro-rata overview
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif

            <div class="dash-tabs">
                @foreach ($tabs as $tab)
                    <a href="{{ route('admin.payouts.levels.show', $tab) }}"
                        class="dash-tab {{ $currentLevel === $tab ? 'is-active' : '' }}">
                        {{ $tab }}
                    </a>
                @endforeach
            </div>

            @if (($status ?? '') === 'error')
                <div class="dash-alert dash-alert--error">{{ $message ?? 'Unable to load payout data.' }}</div>
                @if ($currentLevel !== 'Basic')
                    <p class="dash-muted">
                        Run engagement processing from the
                        <a href="{{ route('admin.payouts.monthly.users', $currentLevel) }}" class="dash-link">monthly breakdown</a>
                        page first.
                    </p>
                @endif
            @else
                <section class="dash-section">
                    <div class="dash-grid dash-grid--4">
                        <div class="dash-kpi">
                            <span class="dash-kpi__label">Level</span>
                            <div class="dash-kpi__value">{{ $currentLevel }}</div>
                        </div>
                        <div class="dash-kpi">
                            <span class="dash-kpi__label">Members</span>
                            <div class="dash-kpi__value">{{ number_format($memberCount ?? 0) }}</div>
                        </div>
                        <div class="dash-kpi">
                            <span class="dash-kpi__label">Total engagement</span>
                            <div class="dash-kpi__value">{{ number_format($totalEngagement ?? 0) }}</div>
                        </div>
                        <div class="dash-kpi">
                            <span class="dash-kpi__label">{{ $poolLabel ?? 'Level pool' }}</span>
                            <div class="dash-kpi__value">₦{{ number_format(convertToBaseCurrency($levelPool ?? 0, 'NGN'), 2) }}</div>
                        </div>
                    </div>
                </section>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <div>
                            <h2 class="dash-card__title">{{ $currentLevel }} member payouts</h2>
                            <p class="dash-muted" style="margin:0.25rem 0 0;">
                                {{ is_countable($payouts) ? count($payouts) : 0 }} eligible member(s)
                            </p>
                        </div>
                        @if ($currentLevel !== 'Basic')
                            <a href="{{ route('admin.payouts.monthly.users', $currentLevel) }}" class="dash-link">Monthly breakdown</a>
                        @endif
                    </div>

                    <div class="dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Engagement</th>
                                        <th>Adj. engagement</th>
                                        <th>Share</th>
                                        <th>Currency</th>
                                        <th>Payout</th>
                                        <th>Est. earnings</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payouts ?? [] as $user)
                                        @php
                                            $engValue = ($user['engagement'] ?? 0) / 4;
                                            $payoutStatus = $user['status'] ?? 'Pending';
                                            $statusClass = $payoutStatus === 'Paid' ? 'dash-badge--gray' : ($payoutStatus === 'Queued' ? 'dash-badge--amber' : 'dash-badge--indigo');
                                        @endphp
                                        <tr>
                                            <td>{{ $user['name'] ?? 'N/A' }}</td>
                                            <td class="dash-num">{{ number_format($user['engagement'] ?? 0) }}</td>
                                            <td class="dash-num">{{ number_format($engValue) }}</td>
                                            <td><span class="dash-badge dash-badge--indigo">{{ $user['userPercentage'] ?? 0 }}%</span></td>
                                            <td class="dash-muted">{{ $user['userWallet'] ?? '—' }}</td>
                                            <td class="dash-num">₦{{ number_format(convertToBaseCurrency($user['userPayout'] ?? 0, 'NGN'), 2) }}</td>
                                            <td class="dash-num">₦{{ number_format(convertToBaseCurrency(engagementEarnings($engValue), 'NGN'), 2) }}</td>
                                            <td><span class="dash-badge {{ $statusClass }}">{{ $payoutStatus }}</span></td>
                                            <td>
                                                @if ($payoutStatus === 'Pending')
                                                    <form method="POST" action="{{ route('admin.payouts.queue', $user['id']) }}"
                                                        onsubmit="return confirm('Queue payout for {{ $user['name'] }}?');">
                                                        @csrf
                                                        <button type="submit" class="dash-btn dash-btn--primary" style="padding:0.5rem 0.75rem;">
                                                            Queue
                                                        </button>
                                                    </form>
                                                @elseif ($payoutStatus === 'Paid')
                                                    <span class="dash-muted">Processed</span>
                                                @else
                                                    <a href="{{ route('admin.payouts.show', $user['id']) }}" class="dash-btn dash-btn--ghost" style="padding:0.5rem 0.75rem;">
                                                        View
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9">
                                                <div class="dash-empty">No eligible users for payout this month.</div>
                                            </td>
                                        </tr>
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
