@extends('general.master.body')

@section('content')
  <section class="pagehero">
  <div class="wrap pagehero__inner">
    <div class="crumbs"><a href="{{ url('/') }}">Home</a> / <span>About us</span></div>
    <span class="eyebrow">About Payhankey</span>
    <h1>We're removing every barrier to creator earning</h1>
    <p>Payhankey is the social monetization platform built for African creators and influencers — and anyone, anywhere, who deserves to be paid for what they create.</p>
  </div>
</section>
<section class="section">
  <div class="wrap split">
    <div class="reveal">
      <span class="eyebrow">Our story</span>
      <h2>Built so talent never goes unpaid</h2>
      <p class="lead" style="margin:18px 0 16px">Across Africa and beyond, millions of people create brilliant content every day — and earn nothing for it. Traditional platforms lock monetization behind huge follower counts, watch hours and approval queues that most creators never clear.</p>
      <p style="color:var(--ink-soft)">Payhankey was created to change that. We pay creators directly for the engagement their content earns, from the very first post. No gatekeeping, no impossible thresholds — just a fair, transparent way to turn everyday creativity into real income. Payhankey is a product of Freebyz Technologies Ltd, a software company building tech and AI-powered solutions across the Fintech, Edutech and Adtech ecosystems.</p>
    </div>
    <div class="media-card reveal">
      <span class="eyebrow" style="color:#C9C2FF">Our 2030 mission</span>
      <div class="device__amount" style="color:#fff;font-size:3.4rem;margin:14px 0 6px">30M<span style="color:#7EE8C4">+</span></div>
      <p style="color:rgba(255,255,255,.82)">creators and influencers across Africa empowered to monetize their content by 2030.</p>
      <div class="device__row" style="margin-top:26px">
        <div class="device__chip" style="background:rgba(255,255,255,.12)"><b style="color:#fff">120K+</b><span style="color:rgba(255,255,255,.7)">members</span></div>
        <div class="device__chip" style="background:rgba(255,255,255,.12)"><b style="color:#fff">40+</b><span style="color:rgba(255,255,255,.7)">countries</span></div>
        <div class="device__chip" style="background:rgba(255,255,255,.12)"><b style="color:#fff">$486K+</b><span style="color:rgba(255,255,255,.7)">paid out</span></div>
      </div>
    </div>
  </div>
</section>

<section class="section--tight">
  <div class="wrap">
    <div class="mv-grid">
      <div class="mv-card reveal"><div class="card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div><h3>Our mission</h3><p>To remove every monetization barrier for creators globally, so that anyone with something to share can earn from it — fairly, transparently and from day one.</p></div>
      <div class="mv-card reveal"><div class="card__icon card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91 0z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg></div><h3>Our vision</h3><p>A world where creativity is rewarded everywhere it happens. We're building the platform that pays the next generation of African and global creators.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head center reveal">
      <span class="eyebrow eyebrow--center">What we stand for</span>
      <h2>Our values</h2>
    </div>
    <div class="grid-3">
      <div class="card reveal"><div class="card__icon card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg></div><h3>Fairness first</h3><p>Everyone earns on the same clear terms. No hidden rules, no favourites — your effort decides your earnings.</p></div>
      <div class="card reveal"><div class="card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h3>Trust &amp; transparency</h3><p>Every cent is tracked in your dashboard, and payouts arrive on schedule. We say what we'll do, then do it.</p></div>
      <div class="card reveal"><div class="card__icon card__icon--gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/></svg></div><h3>Built for access</h3><p>Low minimums, local currencies and familiar payout methods mean earning is open to everyone.</p></div>
      <div class="card reveal"><div class="card__icon card__icon--rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h3>Community-powered</h3><p>Creators grow faster together. Referrals and engagement reward the whole community, not just a few.</p></div>
      <div class="card reveal"><div class="card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div><h3>Relentlessly simple</h3><p>If a feature doesn't make earning easier, it doesn't ship. Clarity beats complexity, always.</p></div>
      <div class="card reveal"><div class="card__icon card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><h3>Long-term focus</h3><p>We're here to build lasting income for creators — not quick wins. Your growth is our growth.</p></div>
    </div>
  </div>
</section>

<section class="section--tight">
  <div class="wrap">
    <div class="statband reveal" style="text-align:center">
      <div style="position:relative;z-index:2;max-width:680px;margin-inline:auto">
        <span class="eyebrow eyebrow--center" style="color:#C9C2FF">Why creators trust us</span>
        <h2 style="color:#fff;margin:14px 0 14px">A promise we keep every month</h2>
        <p style="color:rgba(255,255,255,.82)">Payouts are processed on the 2nd of every month, with a minimum of just $1. Your data stays private, your earnings stay visible, and your money reaches you through PayPal, USDT or your local bank. That consistency is how trust is earned — one payout at a time.</p>
        <a class="btn btn--white" style="margin-top:24px" href="{{ url('/register') }}">Join Payhankey <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
      </div>
    </div>
  </div>
</section>
@endsection