<div class="pk-app">
    @include('livewire.user.partials.pk-app-ui')

    <div class="pk-app-hero">
        <div class="pk-app-hero-inner">
            <span class="pk-app-kicker">Platform guide</span>
            <h1>How Payhankey works</h1>
            <p>Understand account levels, monetization, referrals, and how payouts are calculated.</p>
        </div>
    </div>

    <div class="pk-panel">
        <div class="pk-panel-body pk-prose">
            <h3>What is Payhankey?</h3>
            <p>Payhankey is a social monetization platform for creators and influencers. Post content, earn from engagement, grow your audience, and get paid monthly — with a mission to support millions of creators across Africa and beyond.</p>

            <h3>Account levels</h3>
            <div class="pk-steps">
                <div class="pk-step">
                    <span class="pk-step-num">B</span>
                    <div>
                        <h3>Basic</h3>
                        <p>Free for all new users. Text posts (160 chars), signup bonus, follower growth — posts show estimated earnings but are not monetized until you upgrade.</p>
                    </div>
                </div>
                <div class="pk-step">
                    <span class="pk-step-num">C</span>
                    <div>
                        <h3>Creator</h3>
                        <p>Monetize posts with images and longer text. Blue verified tick. Monthly subscription from ~$1. Earn from views, likes, and comments on every post.</p>
                    </div>
                </div>
                <div class="pk-step">
                    <span class="pk-step-num">I</span>
                    <div>
                        <h3>Influencer</h3>
                        <p>Higher earning rates and referral bonuses for influencer referrals. Premium subscription from ~$5. Best for creators scaling income and reach.</p>
                    </div>
                </div>
            </div>

            <h3>How payouts work</h3>
            <p>Engagement is tracked throughout the month. Earnings are validated and payouts are calculated on the <strong>1st</strong> of the following month, then sent by the <strong>2nd</strong> to your configured payout method.</p>

            <h3>Referrals</h3>
            <p>Influencers earn referral bonuses for qualifying referrals. All users benefit when referrals follow and engage with their content. Invite 500 friends in a month to unlock free monetization access.</p>

            <div style="margin-top:20px;display:flex;flex-wrap:wrap;gap:10px;">
                <a href="{{ url('how/to/earn') }}" class="pk-btn pk-btn--primary">How to earn</a>
                <a href="{{ url('upgrade') }}" class="pk-btn pk-btn--ghost">Upgrade account</a>
            </div>
        </div>
    </div>

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
