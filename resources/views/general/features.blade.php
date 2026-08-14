@extends('general.master.apple')

@section('title', 'Features · Payhankey')
@section('meta_description', 'Explore Payhankey features: timeline monetization, communities, video rolls, wallets, referrals, analytics, and monthly payouts.')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Features',
    'eyebrow' => 'Platform features',
    'title' => 'Everything you need to create, connect &amp; cash out',
    'lead' => 'Built for creators who want real money from real engagement — not vanity metrics.',
])

<section class="apl-section apl-section--white">
  <div class="apl-wrap">
    <div class="apl-grid-3">
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><h3>Live earnings dashboard</h3><p>Watch your balance update as engagement rolls in. See exactly which posts drive income.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h3>Communities</h3><p>Launch public, private, or paid communities. Charge subscriptions or one-off access fees.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div><h3>Video rolls</h3><p>Short-form vertical video with swipe discovery — TikTok energy, Payhankey payouts.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg></div><h3>Flexible withdrawals</h3><p>Cash out from $1 via PayPal, USDT, or local bank — paid on the 2nd of every month.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/></svg></div><h3>Referral commissions</h3><p>Share your code. When friends join and post, you earn affiliate commissions on top of content income.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg></div><h3>Post analytics</h3><p>Per-post views, likes, comments, and estimated earnings — know what's hitting before you double down.</p></article>
    </div>
  </div>
</section>

<section class="apl-section apl-section--soft">
  <div class="apl-wrap apl-split">
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">Social by design</p>
      <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:700;letter-spacing:-.03em;margin-bottom:16px">Built for how you already scroll</h2>
      <p style="color:var(--ink-soft);margin-bottom:24px;line-height:1.55">Timeline reactions, comments, hashtags, profiles, and engagement — minus the "10K followers before you earn" nonsense.</p>
      <div class="apl-benefit"><div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/></svg></div><div><h4>Reactions that pay</h4><p>Likes, comments, and views on your posts can all contribute to earnings on paid tiers.</p></div></div>
      <div class="apl-benefit"><div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div><div><h4>Multi-format posts</h4><p>Text, images, video, quizzes, and teasers — post what fits your vibe.</p></div></div>
      <div class="apl-benefit"><div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div><div><h4>Always-on discovery</h4><p>Recommendations help quality content find the right audience faster.</p></div></div>
    </div>
    <div class="apl-dark-card reveal">
      <p class="apl-showcase__eyebrow">Levels</p>
      <h2>Upgrade when you're ready</h2>
      <p>Start free on Basic. Unlock monetization with Creator ($1) or Influencer ($5) activation.</p>
      <div class="apl-chips">
        <div class="apl-chip"><b>Basic</b><span>Free</span></div>
        <div class="apl-chip"><b>Creator</b><span>$1</span></div>
        <div class="apl-chip"><b>Influencer</b><span>$5</span></div>
      </div>
      <a class="apl-btn apl-btn--fill" style="margin-top:24px;display:inline-flex" href="{{ url('/register') }}">Start free today</a>
    </div>
  </div>
</section>

@include('general.partials.apl-close-cta')
@endsection
