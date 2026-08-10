<div class="pk-app">
    @include('livewire.user.partials.pk-app-ui')

    <div class="pk-app-hero">
        <div class="pk-app-hero-inner">
            <span class="pk-app-kicker">Earn more</span>
            <h1>How to earn on Payhankey</h1>
            <p>Four proven ways to grow your wallet — from posting and engaging to referrals and promotion.</p>
        </div>
    </div>

    <div class="pk-steps" style="margin-bottom:20px;">
        <div class="pk-step">
            <span class="pk-step-num">1</span>
            <div>
                <h3>Content & engagement</h3>
                <p>Post text, images, and video. As a Creator or Influencer, earn from views, likes, and comments. Basic accounts see estimates; upgrade to monetize.</p>
            </div>
        </div>
        <div class="pk-step">
            <span class="pk-step-num">2</span>
            <div>
                <h3>Promoter earnings</h3>
                <p>Create viral review videos about Payhankey on Instagram or TikTok. Tag <strong>@payhankeyofficial</strong> — earn up to $20 when videos hit 20,000+ views.</p>
            </div>
        </div>
        <div class="pk-step">
            <span class="pk-step-num">3</span>
            <div>
                <h3>Signup bonus</h3>
                <p>Every new member receives a welcome bonus on registration — start earning from day one.</p>
            </div>
        </div>
        <div class="pk-step">
            <span class="pk-step-num">4</span>
            <div>
                <h3>Referral bonus</h3>
                <p>Influencers earn up to $0.75 per qualifying influencer referral. Copy your link from your <a href="{{ url('profile/' . auth()->user()->username) }}">profile</a> and share everywhere.</p>
            </div>
        </div>
    </div>

    <div class="pk-panel">
        <div class="pk-panel-body" style="display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;align-items:center;">
            <p class="pk-hint" style="margin:0">Ready to unlock monetization?</p>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ url('upgrade') }}" class="pk-btn pk-btn--primary">Upgrade now</a>
                <a href="{{ url('referral/list') }}" class="pk-btn pk-btn--ghost">My referrals</a>
            </div>
        </div>
    </div>

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
