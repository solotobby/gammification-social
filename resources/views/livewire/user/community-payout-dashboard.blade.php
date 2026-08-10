@php
    $feePercent = $stats['fee_percent'];
    $memberCharge = $community->member_charge;
    $platformCut = $community->platform_fee_amount;
    $creatorPayout = $community->creator_payout;
    $billingLabel = $community->billing_label;
    $priceSuffix = $community->price_suffix;
@endphp

<div class="pk-earnings-tab">

    {{-- Current pricing — mirrors paid settings fee preview --}}
    <div class="pk-card pk-settings-section" style="margin-bottom:16px">
        <h3>Your pricing</h3>
        <div class="pk-sub" style="margin-bottom:14px">
            What members pay and what you receive per
            {{ $community->billing_type === 'one_off' ? 'join' : 'billing cycle' }}.
        </div>

        <div class="pk-about-row d-flex flex-column flex-sm-row gap-1 gap-sm-3">
            <span class="pk-lbl flex-sm-shrink-0" style="min-width:130px">Billing</span>
            <span>{{ $billingLabel ?? 'Paid community' }}</span>
        </div>
        <div class="pk-about-row d-flex flex-column flex-sm-row gap-1 gap-sm-3">
            <span class="pk-lbl flex-sm-shrink-0" style="min-width:130px">Fee payer</span>
            <span>
                {{ $community->fee_payer === 'members'
                    ? 'Members cover the ' . $feePercent . '% platform fee'
                    : 'You cover the ' . $feePercent . '% platform fee' }}
            </span>
        </div>
        <div class="pk-about-row d-flex flex-column flex-sm-row gap-1 gap-sm-3">
            <span class="pk-lbl flex-sm-shrink-0" style="min-width:130px">Sticker price</span>
            <span>{{ $this->formatMoney((float) $community->monthly_fee) }}{{ $priceSuffix }}</span>
        </div>

        @if ($memberCharge)
            <div class="pk-fee-preview" style="margin-top:14px">
                <div class="pk-fp-row">
                    <span>Members pay</span>
                    <b>{{ $this->formatMoney($memberCharge) }}{{ $priceSuffix }}</b>
                </div>
                <div class="pk-fp-row">
                    <span>{{ config('app.name') }} fee ({{ $feePercent }}%)</span>
                    <b>{{ $this->formatMoney($platformCut) }}</b>
                </div>
                <div class="pk-fp-row pk-fp-total">
                    <span>You receive{{ $priceSuffix ? ', per member' . $priceSuffix : '' }}</span>
                    <b>{{ $this->formatMoney($creatorPayout) }}{{ $priceSuffix }}</b>
                </div>
            </div>
        @endif
    </div>

    {{-- Period filters --}}
    <div class="pk-earn-filters d-flex flex-wrap gap-2" style="margin-bottom:16px">
        @foreach (['all' => 'All time', '7d' => 'Last 7 days', '30d' => 'Last 30 days', '90d' => 'Last 90 days'] as $key => $label)
            <button type="button"
                class="pk-f-chip @if ($period === $key) pk-sel @endif"
                wire:click="$set('period', '{{ $key }}')">{{ $label }}</button>
        @endforeach
    </div>

    {{-- Summary stats --}}
    <div class="row g-3" style="margin-bottom:16px">
        <div class="col-sm-6 col-xl-3">
            <div class="pk-card pk-stat-card">
                <div class="pk-stat-lbl">Total collected</div>
                <div class="pk-stat-val">{{ $this->formatMoney($stats['gross']) }}</div>
                <div class="pk-stat-sub">{{ $stats['count'] }} payment{{ $stats['count'] === 1 ? '' : 's' }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="pk-card pk-stat-card">
                <div class="pk-stat-lbl">Platform fee ({{ $feePercent }}%)</div>
                <div class="pk-stat-val pk-stat-warn">{{ $this->formatMoney($stats['platform']) }}</div>
                <div class="pk-stat-sub">Remitted to {{ config('app.name') }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="pk-card pk-stat-card">
                <div class="pk-stat-lbl">Your earnings</div>
                <div class="pk-stat-val pk-stat-good">{{ $this->formatMoney($stats['creator']) }}</div>
                <div class="pk-stat-sub">Net after platform fee</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="pk-card pk-stat-card">
                <div class="pk-stat-lbl">Active subscribers</div>
                <div class="pk-stat-val">{{ number_format($stats['active_members']) }}</div>
                <div class="pk-stat-sub">Currently paying members</div>
            </div>
        </div>
    </div>

    {{-- Payment history --}}
    <div class="pk-card pk-settings-section" style="margin-bottom:16px">
        <h3>Payment history</h3>
        <div class="pk-sub" style="margin-bottom:6px">
            Every successful join or subscription payment for this community.
        </div>

        @forelse ($payments as $sub)
            @php
                $role = $paymentRoles[$sub->user_id] ?? 'member';
                $typeLabel = $sub->billing_type === 'one_off'
                    ? 'One-off'
                    : ucfirst($sub->billing_interval ?? 'subscription');
            @endphp
            <div class="pk-member-row d-flex flex-wrap align-items-center gap-2"
                wire:key="payment-{{ $sub->id }}">
                <div class="pk-ph-av" style="background:{{ $community->color }}">
                    {{ mb_strtoupper(mb_substr($sub->user->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-grow-1" style="min-width:160px">
                    <div class="pk-member-info">
                        <div class="pk-n">
                            {{ $sub->user->name ?? 'Unknown' }}
                            <span class="pk-role-badge @if ($role === 'member') pk-member-role @endif">
                                {{ ucfirst($role) }}
                            </span>
                        </div>
                        <div class="pk-h">
                            {{ $sub->user->username ?? $sub->user->email ?? '' }}
                            · {{ $typeLabel }}
                            · {{ ucfirst($sub->status) }}
                        </div>
                    </div>
                </div>
                <div class="pk-pay-col text-sm-end">
                    <div class="pk-pay-amt pk-stat-good">{{ $this->formatMoney((float) $sub->creator_amount) }}</div>
                    <div class="pk-pay-meta">
                        Gross {{ $this->formatMoney((float) $sub->amount) }}
                        · Fee {{ $this->formatMoney((float) $sub->platform_fee) }}
                    </div>
                    <div class="pk-pay-meta">
                        {{ $sub->starts_at?->format('M j, Y g:i A') ?? '—' }}
                    </div>
                </div>
            </div>
        @empty
            <div class="pk-sub" style="margin-bottom:0;margin-top:8px">
                No payments recorded yet. They will appear here when members subscribe or pay to join.
            </div>
        @endforelse

        @if ($payments->hasMorePages())
            <div class="pk-load-more-row" style="margin-top:14px">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- Active paying members (subscriptions + community_users roles) --}}
    <div class="pk-card pk-settings-section">
        <h3>Paying members</h3>
        <div class="pk-sub" style="margin-bottom:6px">
            {{ number_format($activeSubscribers->count()) }} active
            {{ Str::plural('subscription', $activeSubscribers->count()) }}
            · roles from community membership
        </div>

        @forelse ($activeSubscribers as $sub)
            @php
                $role = $subscriberRoles[$sub->user_id] ?? 'member';
                $typeLabel = $sub->billing_type === 'one_off'
                    ? 'One-off · lifetime access'
                    : ucfirst($sub->billing_interval ?? 'subscription') . ' subscription';
                $renewLabel = $sub->expires_at
                    ? 'Renews ' . $sub->expires_at->format('M j, Y')
                    : 'No expiry';
            @endphp
            <div class="pk-member-row d-flex flex-wrap align-items-center gap-2"
                wire:key="subscriber-{{ $sub->id }}">
                <div class="pk-ph-av" style="background:{{ $community->color }}">
                    {{ mb_strtoupper(mb_substr($sub->user->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-grow-1" style="min-width:160px">
                    <div class="pk-member-info">
                        <div class="pk-n">
                            {{ $sub->user->name ?? 'Unknown' }}
                            <span class="pk-role-badge @if ($role === 'member') pk-member-role @endif">
                                {{ ucfirst($role) }}
                            </span>
                        </div>
                        <div class="pk-h">
                            {{ $typeLabel }} · {{ $renewLabel }}
                        </div>
                    </div>
                </div>
                <div class="pk-pay-col text-sm-end">
                    <div class="pk-pay-amt">{{ $this->formatMoney((float) $sub->amount) }}{{ $priceSuffix }}</div>
                    <div class="pk-pay-meta">You get {{ $this->formatMoney((float) $sub->creator_amount) }}</div>
                    <span class="pk-sub-status pk-sub-active">Active</span>
                </div>
            </div>
        @empty
            <div class="pk-sub" style="margin-bottom:0;margin-top:8px">
                No active paying members yet.
            </div>
        @endforelse
    </div>

</div>
