@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-badge--success { background: rgba(16,185,129,.12); color: #067647; }
        .dash-badge--warn { background: rgba(245,158,11,.14); color: #b54708; }
        .dash-badge--danger { background: rgba(220,53,69,.12); color: #b42318; }
        .dash-badge--flw { background: rgba(251,146,60,.14); color: #c2410c; }
        .dash-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start; }
        @media (max-width: 960px) { .dash-grid-2 { grid-template-columns: 1fr; } }
        .dash-dl {
            display: grid;
            grid-template-columns: minmax(140px, 36%) 1fr;
            gap: .75rem 1rem;
            margin: 0;
            font-size: .875rem;
        }
        .dash-dl dt { margin: 0; font-weight: 600; color: var(--dash-muted); }
        .dash-dl dd { margin: 0; }
    </style>
@endsection

@section('content')
@php
    $isCommunity = ($kind ?? '') === 'community';
    $title = $isCommunity
        ? ($subscription->community?->name ?? 'Community subscription')
        : (($plan->level?->name ?? $plan->name ?? 'Level').' plan');
    $member = $isCommunity ? $subscription->user : $plan->user;
    $paymentStatus = $payment?->status;
    $paymentBadge = $paymentStatus === 'successful' ? 'dash-badge--success'
        : (in_array($paymentStatus, ['initiated', 'processing'], true) ? 'dash-badge--warn' : 'dash-badge--danger');
@endphp

<div class="content p-0">
    <div class="dash">
        <header class="dash-header">
            <div>
                <h1>{{ $title }}</h1>
                <p>
                    {{ $isCommunity ? 'Community subscription' : 'Level payment plan' }}
                    · Flutterwave
                    · {{ $member?->name ?? 'Unknown member' }}
                </p>
            </div>
            <a href="{{ route('admin.flutterwave.index', ['tab' => 'subscriptions']) }}" class="dash-btn dash-btn--ghost">
                <i class="fa fa-arrow-left"></i> Back to subscriptions
            </a>
        </header>

        <section class="dash-section">
            <div class="dash-grid dash-grid--4">
                <div class="dash-kpi">
                    <span class="dash-kpi__label">Status</span>
                    <div class="dash-kpi__value" style="font-size:1.15rem">
                        @if ($isCommunity)
                            {{ $subscription->status }}
                        @else
                            {{ $userLevel?->status ?? $plan->status ?? '—' }}
                        @endif
                    </div>
                </div>
                <div class="dash-kpi">
                    <span class="dash-kpi__label">Payment attached</span>
                    <div class="dash-kpi__value" style="font-size:1.15rem">
                        @if ($payment)
                            <span class="dash-badge {{ $paymentBadge }}">{{ $payment->status }}</span>
                        @else
                            <span class="dash-badge dash-badge--warn">No payment linked</span>
                        @endif
                    </div>
                    @if ($payment)
                        <div class="dash-muted">{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</div>
                    @endif
                </div>
                <div class="dash-kpi">
                    <span class="dash-kpi__label">Next payment</span>
                    <div class="dash-kpi__value" style="font-size:1.15rem">{{ $nextPaymentLabel }}</div>
                    @if ($nextPaymentAt)
                        <div class="dash-muted">
                            @if ($nextPaymentAt->isPast())
                                Overdue
                            @else
                                {{ $nextPaymentAt->diffForHumans() }}
                            @endif
                        </div>
                    @endif
                </div>
                <div class="dash-kpi">
                    <span class="dash-kpi__label">Gateway</span>
                    <div class="dash-kpi__value" style="font-size:1.15rem">Flutterwave</div>
                </div>
            </div>
        </section>

        <section class="dash-section dash-grid-2">
            <div class="dash-card">
                <div class="dash-card__head"><h2 class="dash-card__title">Subscription details</h2></div>
                <div class="dash-card__body">
                    <dl class="dash-dl">
                        <dt>Member</dt>
                        <dd>
                            {{ $member?->name ?? '—' }}
                            <div class="dash-muted" style="font-size:.8rem">{{ $member?->email }}</div>
                            @if ($member)
                                <a href="{{ route('admin.users.show', $member) }}" class="dash-link">View profile</a>
                            @endif
                        </dd>

                        @if ($isCommunity)
                            <dt>Community</dt>
                            <dd>
                                {{ $subscription->community?->name ?? '—' }}
                                @if ($subscription->community)
                                    <div><a href="{{ route('admin.communities.show', $subscription->community) }}" class="dash-link">Open community</a></div>
                                @endif
                            </dd>
                            <dt>Billing</dt>
                            <dd>
                                {{ $subscription->billing_type }}
                                @if ($subscription->billing_interval)
                                    · {{ $subscription->billing_interval }}
                                @endif
                            </dd>
                            <dt>Amount</dt>
                            <dd>{{ strtoupper($subscription->community?->currency ?? '') }} {{ number_format((float) $subscription->amount, 2) }}</dd>
                            <dt>Platform fee</dt>
                            <dd>{{ number_format((float) $subscription->platform_fee, 2) }}</dd>
                            <dt>Creator amount</dt>
                            <dd>{{ number_format((float) $subscription->creator_amount, 2) }}</dd>
                            <dt>Starts</dt>
                            <dd>{{ $subscription->starts_at?->format('M j, Y g:i A') ?: '—' }}</dd>
                            <dt>Expires / next due</dt>
                            <dd>{{ $subscription->expires_at?->format('M j, Y g:i A') ?: '—' }}</dd>
                            <dt>Gateway ref</dt>
                            <dd>{{ $subscription->gateway_reference ?: '—' }}</dd>
                        @else
                            <dt>Level</dt>
                            <dd>{{ $plan->level?->name ?? $plan->name ?? '—' }}</dd>
                            <dt>Plan amount</dt>
                            <dd>{{ $plan->currency }} {{ number_format((float) $plan->amount, 2) }}</dd>
                            <dt>Interval</dt>
                            <dd>{{ $plan->interval ?: '—' }}</dd>
                            <dt>Flutterwave plan ID</dt>
                            <dd>{{ $plan->payment_plan_id ?: '—' }}</dd>
                            <dt>User level status</dt>
                            <dd>{{ $userLevel?->status ?? '—' }}</dd>
                            <dt>Next payment date</dt>
                            <dd>{{ $userLevel?->next_payment_date?->format('M j, Y g:i A') ?: '—' }}</dd>
                            <dt>Subscription code</dt>
                            <dd>{{ $userLevel?->subscription_code ?: '—' }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card__head"><h2 class="dash-card__title">Attached payment</h2></div>
                <div class="dash-card__body">
                    @if ($payment)
                        <dl class="dash-dl">
                            <dt>Reference</dt>
                            <dd>{{ $payment->ref }}</dd>
                            <dt>Status</dt>
                            <dd><span class="dash-badge {{ $paymentBadge }}">{{ $payment->status }}</span></dd>
                            <dt>Amount</dt>
                            <dd>{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</dd>
                            <dt>Type</dt>
                            <dd>{{ $payment->type }}</dd>
                            <dt>Action</dt>
                            <dd>{{ $payment->action ?: '—' }}</dd>
                            <dt>Paid / created</dt>
                            <dd>{{ $payment->created_at?->format('M j, Y g:i A') }}</dd>
                            <dt>Description</dt>
                            <dd>{{ $payment->description ?: '—' }}</dd>
                        </dl>
                    @else
                        <div class="dash-empty">No Flutterwave payment is linked to this subscription yet.</div>
                    @endif
                </div>
            </div>
        </section>

        <div class="dash-card">
            <div class="dash-card__head">
                <div>
                    <h2 class="dash-card__title">Related Flutterwave payments</h2>
                    <p class="dash-muted" style="margin:.25rem 0 0">Recent matching transactions for this member</p>
                </div>
            </div>
            <div class="dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>When</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($relatedPayments as $tx)
                                @php
                                    $txBadge = $tx->status === 'successful' ? 'dash-badge--success'
                                        : (in_array($tx->status, ['initiated', 'processing'], true) ? 'dash-badge--warn' : 'dash-badge--danger');
                                @endphp
                                <tr>
                                    <td>{{ $tx->ref }}</td>
                                    <td><span class="dash-badge dash-badge--flw">{{ $tx->type }}</span></td>
                                    <td class="dash-num">{{ $tx->currency }} {{ number_format((float) $tx->amount, 2) }}</td>
                                    <td><span class="dash-badge {{ $txBadge }}">{{ $tx->status }}</span></td>
                                    <td class="dash-muted">{{ $tx->created_at?->format('M j, Y g:i A') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5"><div class="dash-empty">No related payments found.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
