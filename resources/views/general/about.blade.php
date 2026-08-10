@extends('general.master.apple')

@section('title', 'About · Payhankey')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'About us',
    'eyebrow' => 'About Payhankey',
    'title' => 'We\'re removing every barrier to creator earning',
    'lead' => 'Payhankey is the social monetization platform built for African creators and influencers — and anyone, anywhere, who deserves to be paid for what they create.',
])

<section class="apl-section apl-section--white">
  <div class="apl-wrap apl-split">
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">Our story</p>
      <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:700;letter-spacing:-.03em;margin-bottom:16px">Built so talent never goes unpaid</h2>
      <p style="color:var(--ink-soft);margin-bottom:16px;line-height:1.6">Across Africa and beyond, millions of people create brilliant content every day — and earn nothing for it. Traditional platforms lock monetization behind huge follower counts, watch hours and approval queues that most creators never clear.</p>
      <p style="color:var(--ink-soft);line-height:1.6">Payhankey was created to change that. We pay creators directly for the engagement their content earns, from the very first post. Payhankey is a product of Freebyz Technologies Ltd.</p>
    </div>
    <div class="apl-dark-card reveal">
      <p class="apl-showcase__eyebrow">Our 2030 mission</p>
      <div style="font-family:var(--font-display);font-size:clamp(2.4rem,5vw,3.4rem);font-weight:800;letter-spacing:-.03em;margin:12px 0">30M<span style="color:var(--mint)">+</span></div>
      <p>creators and influencers across Africa empowered to monetize their content by 2030.</p>
      <div class="apl-chips">
        <div class="apl-chip"><b>{{ config('payhankey.stats.creators', '120K+') }}</b><span>members</span></div>
        <div class="apl-chip"><b>{{ config('payhankey.stats.countries', '40+') }}</b><span>countries</span></div>
        <div class="apl-chip"><b>{{ config('payhankey.stats.paid_out_usd', '$486K+') }}</b><span>paid out</span></div>
      </div>
    </div>
  </div>
</section>

<section class="apl-section apl-section--soft">
  <div class="apl-wrap apl-mv-grid">
    <article class="apl-mv-card reveal">
      <div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div>
      <h3 style="font-size:1.2rem;font-weight:700;margin:16px 0 8px">Our mission</h3>
      <p style="color:var(--ink-soft);line-height:1.55">To remove every monetization barrier for creators globally, so that anyone with something to share can earn from it — fairly, transparently and from day one.</p>
    </article>
    <article class="apl-mv-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/></svg></div>
      <h3 style="font-size:1.2rem;font-weight:700;margin:16px 0 8px">Our vision</h3>
      <p style="color:var(--ink-soft);line-height:1.55">A world where creativity is rewarded everywhere it happens. We're building the platform that pays the next generation of African and global creators.</p>
    </article>
  </div>
</section>

<section class="apl-section apl-section--white">
  <div class="apl-wrap">
    <div style="text-align:center;margin-bottom:40px" class="reveal">
      <p class="apl-showcase__eyebrow">What we stand for</p>
      <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:700;letter-spacing:-.03em">Our values</h2>
    </div>
    <div class="apl-grid-3">
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h3>Fairness first</h3><p>Everyone earns on the same clear terms. No hidden rules — your effort decides your earnings.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h3>Trust &amp; transparency</h3><p>Every cent is tracked in your dashboard, and payouts arrive on schedule.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/></svg></div><h3>Built for access</h3><p>Low minimums, local currencies and familiar payout methods mean earning is open to everyone.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><h3>Community-powered</h3><p>Creators grow faster together. Referrals and engagement reward the whole community.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div><h3>Relentlessly simple</h3><p>If a feature doesn't make earning easier, it doesn't ship. Clarity beats complexity.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><h3>Long-term focus</h3><p>We're here to build lasting income for creators — not quick wins.</p></article>
    </div>
  </div>
</section>

<section class="apl-section apl-section--soft">
  <div class="apl-wrap">
    <div class="apl-stat-band reveal">
      <p class="apl-showcase__eyebrow" style="color:#a79fff">Why creators trust us</p>
      <h2>A promise we keep every month</h2>
      <p>Payouts are processed on the 2nd of every month, with a minimum of just $1. Your data stays private, your earnings stay visible.</p>
      <a class="apl-btn apl-btn--fill" style="margin-top:24px;display:inline-flex;background:#fff;color:var(--ink)" href="{{ url('/register') }}">Join Payhankey</a>
    </div>
  </div>
</section>
@endsection
