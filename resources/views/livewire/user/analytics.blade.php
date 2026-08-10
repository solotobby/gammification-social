<div class="pk-app">
    @include('livewire.user.partials.pk-app-ui')

    <div class="pk-app-hero">
        <div class="pk-app-hero-inner">
            <span class="pk-app-kicker">Performance</span>
            <h1>Analytics · {{ $month }}</h1>
            <p>Track posts, engagement, and estimated earnings for this month. Payouts are validated and processed at month end.</p>
        </div>
    </div>

    <div class="pk-alert pk-alert--info">
        Every <strong>1,000 engagements</strong> can put
        <strong>{{ getCurrencyCode() }}{{ number_format(convertToBaseCurrency(1, auth()->user()->wallet->currency), 2) }}</strong>
        in your wallet — keep creating and engaging.
    </div>

    <div class="pk-stat-grid">
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:#EEF2FF;color:#4F46E5;"><i class="fa fa-file-text"></i></div>
            <p class="pk-stat-card-value">{{ number_format($postsCount) }}</p>
            <p class="pk-stat-card-label">Posts this month</p>
        </article>
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:#ECFDF5;color:#059669;"><i class="fa fa-eye"></i></div>
            <p class="pk-stat-card-value">{{ number_format($totalViews) }}</p>
            <p class="pk-stat-card-label">Total views</p>
        </article>
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:#FEF3C7;color:#D97706;"><i class="fa fa-thumbs-up"></i></div>
            <p class="pk-stat-card-value">{{ number_format($totalLikes) }}</p>
            <p class="pk-stat-card-label">Likes</p>
        </article>
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:var(--pk-violet-soft);color:var(--pk-violet);"><i class="fa fa-comments"></i></div>
            <p class="pk-stat-card-value">{{ number_format($totalComments) }}</p>
            <p class="pk-stat-card-label">Comments</p>
        </article>
    </div>

    <div class="pk-panel">
        <div class="pk-panel-head"><h2>Monetized engagement</h2></div>
        <div class="pk-panel-body">
            <div class="pk-stat-grid" style="margin-bottom:0">
                <article class="pk-stat-card">
                    <p class="pk-stat-card-value">{{ number_format($monetizedViews) }}</p>
                    <p class="pk-stat-card-label">Monetized views</p>
                </article>
                <article class="pk-stat-card">
                    <p class="pk-stat-card-value">{{ number_format($monetizedLikes) }}</p>
                    <p class="pk-stat-card-label">Monetized likes</p>
                </article>
                <article class="pk-stat-card">
                    <p class="pk-stat-card-value">{{ number_format($monetizedComments) }}</p>
                    <p class="pk-stat-card-label">Monetized comments</p>
                </article>
                <article class="pk-stat-card">
                    <p class="pk-stat-card-value">{{ number_format($totalEngagement) }}</p>
                    <p class="pk-stat-card-label">Total engagement</p>
                </article>
            </div>
        </div>
    </div>

    <div class="pk-panel">
        <div class="pk-panel-head"><h2>Estimated earnings</h2></div>
        <div class="pk-panel-body">
            <p class="pk-stat-card-value" style="font-size:2rem;margin-bottom:8px;">
                {{ getCurrencyCode() }}{{ number_format(convertToBaseCurrency($estimatedEarnings, auth()->user()->wallet->currency), 2) }}
            </p>
            <p class="pk-hint" style="margin:0">
                Estimate only — final payout is calculated after validation of real engagement at month end.
            </p>
        </div>
    </div>

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
