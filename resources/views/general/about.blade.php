@extends('general.master.apple')

@section('title', 'About Payhankey | Building Africa\'s Creator Economy')
@section('meta_description', 'Payhankey is an AI-powered creator monetization platform built to help African creators turn content, audiences and communities into sustainable digital businesses.')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'About us',
    'title' => 'Building the Future of Africa\'s Creator Economy',
    'lead' => 'Payhankey is an AI-powered creator monetization platform built to help African creators turn content, audiences and communities into sustainable digital businesses.',
])

{{-- STORY --}}
<section class="apl-section apl-section--white">
  <div class="apl-wrap apl-split">
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">Our story</p>
      <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:700;letter-spacing:-.03em;margin-bottom:16px">From Freebyz to Payhankey</h2>
      <p style="color:var(--ink-soft);margin-bottom:16px;line-height:1.6">Across Africa, creators publish every day — but too many platforms still lock monetization behind unreachable thresholds. Payhankey was built to change that: grow an audience, launch communities, earn recurring income and manage a creator business from one place.</p>
      <p style="color:var(--ink-soft);line-height:1.6">Payhankey launched in 2024 as a Freebyz product and was rebranded in January 2025 under Payhankey Limited — continuing the Freebyz Technologies Ltd mission of building practical, Africa-first digital products.</p>
    </div>
    <div class="apl-about-timeline reveal">
      <div class="apl-about-timeline__item">
        <span class="apl-about-timeline__year">2024</span>
        <p>Launched as a Freebyz product</p>
      </div>
      <div class="apl-about-timeline__item">
        <span class="apl-about-timeline__year">Jan 2025</span>
        <p>Rebranded under Payhankey Limited</p>
      </div>
      <div class="apl-about-timeline__stats">
        <div><b>32,000+</b><span>Users</span></div>
        <div><b>1,700+</b><span>Creators paid</span></div>
        <div><b>6+</b><span>African markets</span></div>
      </div>
    </div>
  </div>
</section>

{{-- MISSION / VISION --}}
<section class="apl-section apl-section--soft">
  <div class="apl-wrap apl-mv-grid">
    <article class="apl-mv-card reveal">
      <div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div>
      <h3 style="font-size:1.2rem;font-weight:700;margin:16px 0 8px">Our mission</h3>
      <p style="color:var(--ink-soft);line-height:1.55">To help African creators turn content, audiences and communities into sustainable digital businesses — with AI tools, multiple income streams and local payouts.</p>
    </article>
    <article class="apl-mv-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/></svg></div>
      <h3 style="font-size:1.2rem;font-weight:700;margin:16px 0 8px">Our vision</h3>
      <p style="color:var(--ink-soft);line-height:1.55">To build the future of Africa's creator economy — where creativity is rewarded, communities create recurring income, and creators own their businesses.</p>
    </article>
  </div>
</section>

{{-- VALUES --}}
<section class="apl-section apl-section--white">
  <div class="apl-wrap">
    <div style="text-align:center;margin-bottom:40px" class="reveal">
      <p class="apl-showcase__eyebrow">What we stand for</p>
      <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:700;letter-spacing:-.03em">Our values</h2>
    </div>
    <div class="apl-grid-3">
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h3>Fairness first</h3><p>Clear terms for creators. Effort and eligibility — not gatekeeping — should shape opportunity.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h3>Trust &amp; transparency</h3><p>Earnings stay visible in your dashboard, with supported payouts through methods you can trust.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/></svg></div><h3>Built for Africa</h3><p>Local currency payouts, communities and tools designed for creators across African markets.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><h3>Community-powered</h3><p>Audiences become members. Creators grow faster when they own the relationship.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div><h3>Relentlessly simple</h3><p>If a feature doesn't help creators grow, monetize or manage their business, it doesn't ship.</p></article>
      <article class="apl-feature-card reveal"><div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><h3>Long-term focus</h3><p>We're building lasting creator businesses — not vanity metrics or one-off wins.</p></article>
    </div>
  </div>
</section>

{{-- FOUNDERS --}}
<section class="apl-section apl-section--soft">
  <div class="apl-wrap">
    <div style="text-align:center;margin-bottom:40px" class="reveal">
      <p class="apl-showcase__eyebrow">Leadership</p>
      <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:700;letter-spacing:-.03em">Meet the founders</h2>
    </div>
    <div class="apl-founders">
      <article class="apl-founder reveal">
        <div class="apl-founder__avatar" aria-hidden="true">SF</div>
        <div>
          <h3>Dr. Samuel Farohunbi</h3>
          <p class="apl-founder__role">Co-Founder &amp; CEO</p>
          <p>Leads strategy, partnerships and growth at Freebyz Technologies Ltd and Payhankey. With a PhD in Biochemistry and deep experience in AI, product strategy and digital innovation, he co-founded Myhotjobz in 2018 — the platform that later evolved into Freebyz — and continues to build Africa-first technology with lasting economic impact.</p>
        </div>
      </article>
      <article class="apl-founder reveal">
        <div class="apl-founder__avatar apl-founder__avatar--mint" aria-hidden="true">OS</div>
        <div>
          <h3>Oluwatobi Solomon</h3>
          <p class="apl-founder__role">Co-Founder &amp; CTO</p>
          <p>Leads technology, engineering and platform infrastructure for Payhankey and the Freebyz ecosystem. With degrees in Computer Science from the University of Ilorin and the University of Lincoln, and experience across software engineering, AI and cloud systems, he builds the secure, scalable products creators rely on every day.</p>
        </div>
      </article>
    </div>
  </div>
</section>

@include('general.partials.apl-close-cta')
@endsection
