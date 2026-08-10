@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }

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
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .dash-num { font-weight: 600; font-variant-numeric: tabular-nums; }
        .dash-code { font-family: ui-monospace, monospace; font-size: 0.8125rem; }

        @media (max-width: 960px) {
            .dash-grid--3 { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    @php
        $withPlan = $levels->filter(fn ($level) => $level->planId)->count();
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Level management</h1>
                    <p>Subscription tiers, bonuses, and Paystack plan configuration</p>
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
                <div class="dash-grid dash-grid--3">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Total levels</span>
                        <div class="dash-kpi__value">{{ number_format($levels->count()) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Paystack plans</span>
                        <div class="dash-kpi__value">{{ number_format($withPlan) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Needs setup</span>
                        <div class="dash-kpi__value">{{ number_format(max(0, $levels->count() - $withPlan)) }}</div>
                    </div>
                </div>
            </section>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Subscription levels</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">Earnings shown per 1k views / likes / comments</p>
                    </div>
                </div>

                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>Price (USD)</th>
                                    <th>Reg. bonus</th>
                                    <th>Ref. bonus</th>
                                    <th>Min. withdrawal</th>
                                    <th>Per 1k views</th>
                                    <th>Per 1k likes</th>
                                    <th>Per 1k comments</th>
                                    <th>Paystack</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($levels as $level)
                                    <tr>
                                        <td>
                                            <span class="dash-badge dash-badge--indigo">{{ $level->name }}</span>
                                        </td>
                                        <td class="dash-num">${{ number_format((float) $level->amount, 2) }}</td>
                                        <td class="dash-num">${{ number_format((float) $level->reg_bonus, 2) }}</td>
                                        <td class="dash-num">${{ number_format((float) $level->ref_bonus, 2) }}</td>
                                        <td class="dash-num">${{ number_format((float) $level->min_withdrawal, 2) }}</td>
                                        <td class="dash-num">${{ number_format((float) $level->earning_per_view, 2) }}</td>
                                        <td class="dash-num">${{ number_format((float) $level->earning_per_like, 2) }}</td>
                                        <td class="dash-num">${{ number_format((float) $level->earning_per_comment, 2) }}</td>
                                        <td>
                                            @if ($level->planId)
                                                <span class="dash-code">{{ $level->planId->plan_code }}</span>
                                                <span class="dash-muted" style="display:block; font-size:0.75rem; margin-top:0.25rem;">
                                                    {{ $level->planId->provider }} · {{ strtoupper($level->planId->currency) }}
                                                </span>
                                            @elseif ($level->name === 'Basic')
                                                <span class="dash-muted">Not required</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.levels.generate-plan', $level) }}"
                                                    onsubmit="return confirm('Generate Paystack plan for {{ $level->name }}?');">
                                                    @csrf
                                                    <button type="submit" class="dash-btn dash-btn--primary" style="padding:0.5rem 0.75rem;">
                                                        Generate plan
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="dash-empty">No subscription levels found.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
