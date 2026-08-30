<div class="pk-app">
    @include('livewire.user.partials.pk-app-ui')

    <div class="pk-app-hero">
        <div class="pk-app-hero-inner">
            <span class="pk-app-kicker">Wallet · {{ userLevel() }}</span>
            <h1>Your earnings & PayKoin</h1>
            <p>Fiat balances, PayKoin for gifts, and payout schedule in one place.</p>
        </div>
    </div>

    @include('livewire.user.partials.paykoin-wallet')

    @foreach (['status_refresh' => 'success', 'status' => 'success', 'status_error' => 'error'] as $key => $type)
        @if (session()->has($key))
            <div class="pk-alert pk-alert--{{ $type }}">{{ session($key) }}</div>
        @endif
    @endforeach

    <div class="pk-stat-grid">
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:#EEF2FF;color:#4F46E5;"><i class="fa fa-wallet"></i></div>
            <p class="pk-stat-card-value">{{ getCurrencyCode() }}{{ number_format((float) $wallets->balance, 2) }}</p>
            <p class="pk-stat-card-label">Main balance <small>(content monetization)</small></p>
        </article>
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:var(--pk-mint-soft);color:var(--pk-mint);"><i class="fa fa-users"></i></div>
            <p class="pk-stat-card-value">{{ getCurrencyCode() }}{{ number_format((float) $wallets->referral_balance, 2) }}</p>
            <p class="pk-stat-card-label">Referral balance</p>
        </article>
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:#FEF3C7;color:#D97706;"><i class="fa fa-bullhorn"></i></div>
            <p class="pk-stat-card-value">{{ getCurrencyCode() }}{{ number_format((float) $wallets->promoter_balance, 2) }}</p>
            <p class="pk-stat-card-label">Promotion balance</p>
        </article>
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:var(--pk-violet-soft);color:var(--pk-violet);"><i class="fa fa-arrow-down"></i></div>
            <p class="pk-stat-card-value">{{ getCurrencyCode() }}{{ number_format((float) $paidWithdrawals, 2) }}</p>
            <p class="pk-stat-card-label">Total withdrawn</p>
        </article>
    </div>

    @if (userLevel() == 'Basic')
        @include('layouts.upgrade')
    @else
        <div class="pk-panel">
            <div class="pk-panel-head">
                <h2>Subscription & payout</h2>
                <button type="button" class="pk-btn pk-btn--ghost" wire:click="refresh">
                    <i class="fa fa-refresh"></i> Refresh
                </button>
            </div>
            <div class="pk-panel-body">
                @if ($subscription)
                    <ul class="pk-detail-list">
                        <li><span>Current plan</span><b>{{ userLevel() }}</b></li>
                        <li><span>Started</span><b>{{ \Carbon\Carbon::parse($subscription->start_date ?? $subscription->created_at)->format('F j, Y') }}</b></li>
                        <li><span>Plan renews</span><b>{{ $subscription->next_payment_date?->format('F j, Y') }}</b></li>
                        <li><span>Next payout date</span><b>{{ now()->addMonth()->day(2)->format('F j, Y') }}</b></li>
                        <li><span>Payout currency</span><b>{{ $payouts->currency ?? $wallets->currency ?? 'NGN' }}</b></li>
                        <li><span>Bonus balance</span><b>{{ getCurrencyCode() }}{{ number_format((float) $wallets->balance, 2) }}</b></li>
                        <li><span>Payout status</span><b>{{ $payouts->status ?? 'Pending' }}</b></li>
                    </ul>
                    <div class="pk-alert pk-alert--info" style="margin-top:16px;margin-bottom:0">
                        Payouts are calculated on the <strong>1st</strong> of each month and processed by the <strong>2nd</strong>.
                        Ensure your <a href="{{ url('bank/information') }}">payout details</a> are correct.
                    </div>
                @else
                    <div class="pk-empty">
                        <h3>No active subscription</h3>
                        <p><a href="{{ url('upgrade') }}">Upgrade your account</a> to unlock monetization and payouts.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="pk-panel">
            <div class="pk-panel-head"><h2>Quick links</h2></div>
            <div class="pk-panel-body" style="display:flex;flex-wrap:wrap;gap:10px;">
                <a href="{{ url('bank/information') }}" class="pk-btn pk-btn--ghost"><i class="fa fa-university"></i> Bank information</a>
                <a href="{{ url('analytics') }}" class="pk-btn pk-btn--ghost"><i class="fa fa-line-chart"></i> Analytics</a>
                <a href="{{ url('referral/list') }}" class="pk-btn pk-btn--ghost"><i class="fa fa-user-plus"></i> Referrals</a>
                <a href="{{ url('transaction/list') }}" class="pk-btn pk-btn--ghost"><i class="fa fa-list"></i> Transactions</a>
            </div>
        </div>
    @endif

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
