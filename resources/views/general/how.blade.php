@extends('general.master.apple')

@section('title', 'How it works · Payhankey')
@section('meta_description', 'From your first post to your first community — see how Payhankey helps creators grow audiences, launch communities, earn recurring income, and build lasting digital businesses.')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'How it works',
    'eyebrow' => 'The Creator Journey',
    'title' => 'From Your First Post to Your First Community.',
    'lead' => 'Payhankey is more than a place to post content. It\'s where creators grow audiences, launch communities, earn recurring income, and build lasting digital businesses.',
])

<section class="apl-showcase apl-showcase--soft">
  <div class="apl-steps">
    <div class="apl-step reveal">
      <div class="apl-step__num">1</div>
      <h3>Create Your Creator Profile</h3>
      <p>Create your free account, personalize your profile and tell the world what you create.</p>
    </div>
    <div class="apl-step reveal">
      <div class="apl-step__num">2</div>
      <h3>Publish &amp; Get Discovered</h3>
      <p>Share posts, photos or Payhankey Rolls. Our recommendation engine helps the right audience discover your content.</p>
    </div>
    <div class="apl-step reveal">
      <div class="apl-step__num">3</div>
      <h3>Build Your Community</h3>
      <p>Turn followers into loyal members with public, membership, private or request-to-join communities.</p>
    </div>
    <div class="apl-step reveal">
      <div class="apl-step__num">4</div>
      <h3>Earn &amp; Grow</h3>
      <p>Monetize through creator rewards, memberships, referrals and future brand partnerships while tracking everything from one dashboard.</p>
    </div>
  </div>
</section>

<section class="apl-section apl-section--white">
  <div class="apl-wrap apl-split">
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">Where earnings come from</p>
      <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:700;letter-spacing:-.03em;margin-bottom:16px">Five ways to grow your balance</h2>
      <p style="color:var(--ink-soft);margin-bottom:24px">Your income isn't tied to one thing. Payhankey stacks multiple earning streams.</p>
      <div class="apl-benefit"><div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/></svg></div><div><h4>Signup bonus</h4><p>Get up to $2 the moment you join — before you've even posted.</p></div></div>
      <div class="apl-benefit"><div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/></svg></div><div><h4>Engagement earnings</h4><p>Every like, comment and view on your posts adds to your balance automatically.</p></div></div>
      <div class="apl-benefit"><div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div><h4>Referral commissions</h4><p>Invite friends with your code and earn when they join and post.</p></div></div>
      <div class="apl-benefit"><div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div><div><h4>Promotional payouts</h4><p>Make a viral video about Payhankey, tag us, and earn up to $10 per 1,000 views.</p></div></div>
    </div>
    <div class="apl-dark-card reveal">
      <p class="apl-showcase__eyebrow">Account levels</p>
      <h2>Upgrade to earn more per post</h2>
      <p style="margin-bottom:20px">Basic accounts can post and earn bonuses. To monetize posts, activate Creator or Influencer.</p>
      <div style="background:rgba(255,255,255,.1);border-radius:16px;padding:18px 20px;margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;font-weight:700;color:#fff">Creator <span style="color:var(--mint)">$1</span></div>
        <p style="font-size:.88rem;color:rgba(255,255,255,.7);margin-top:6px">Blue tick · earn up to $2 per post · images &amp; video.</p>
      </div>
      <div style="background:rgba(255,255,255,.1);border-radius:16px;padding:18px 20px">
        <div style="display:flex;justify-content:space-between;font-weight:700;color:#fff">Influencer <span style="color:var(--mint)">$5</span></div>
        <p style="font-size:.88rem;color:rgba(255,255,255,.7);margin-top:6px">Blue tick + ring · earn up to $5 per post · top placement.</p>
      </div>
    </div>
  </div>
</section>

@include('general.partials.apl-close-cta')
@endsection
