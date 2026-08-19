@extends('general.master.apple')

@section('title', 'AI Tools for Creators · Payhankey')
@section('meta_description', 'Payhankey AI tools for African creators: AI assistant, captions, analytics, content recommendations and AI-powered video discovery for Payhankey Rolls.')
@section('body_class', 'page-landing-apple page-ai')

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'AI Tools for Creators · Payhankey',
    'description' => 'AI assistant, captions, analytics, recommendations and video tools built to help creators grow and monetize on Payhankey.',
    'url' => url('/ai'),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Payhankey',
        'url' => url('/'),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('apple_content')

{{-- HERO --}}
<section class="apl-creators-hero">
  <div class="apl-creators-hero__inner">
    <div class="apl-crumbs reveal"><a href="{{ url('/') }}">Home</a> / <span>AI Tools</span></div>
    <p class="apl-creators-hero__eyebrow reveal">AI for creators</p>
    <h1 class="reveal">Create Faster.<br>Grow Smarter. Earn More.</h1>
    <p class="apl-creators-hero__lead reveal">
      Payhankey AI helps African creators write better captions, understand performance, get discovered and grow a sustainable creator business — without needing a full content team.
    </p>
    <div class="apl-creators-hero__cta reveal">
      <a class="apl-btn apl-btn--fill" href="{{ url('/register') }}">Try AI Tools Free</a>
      <a class="apl-btn apl-btn--ghost-dark" href="#assistant">Explore AI features</a>
    </div>
  </div>
</section>

{{-- AI ASSISTANT --}}
<section class="apl-section apl-section--white" id="assistant">
  <div class="apl-wrap apl-split">
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">AI Assistant</p>
      <h2 class="apl-creators-h2">Your always-on creator co-pilot</h2>
      <p class="apl-creators-copy">The AI Creator Assistant helps you move faster — from idea to post — with practical guidance tailored to growing an audience and monetizing on Payhankey.</p>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
        <div><h4>Ask anything creator-related</h4><p>Get help with content ideas, growth habits, community strategy and monetization next steps.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
        <div><h4>Faster creative decisions</h4><p>Spend less time stuck and more time publishing posts, Rolls and community updates.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div><h4>Built for African creators</h4><p>Guidance shaped around local audiences, communities and real monetization paths on Payhankey.</p></div>
      </div>
    </div>
    <div class="apl-ai-panel reveal" aria-hidden="true">
      <div class="apl-ai-panel__head">
        <span class="apl-ai-panel__dot"></span>
        AI Creator Assistant
      </div>
      <div class="apl-ai-panel__chat">
        <div class="apl-ai-bubble apl-ai-bubble--user">Give me 3 post ideas for lifestyle creators in Lagos this weekend.</div>
        <div class="apl-ai-bubble apl-ai-bubble--bot">
          1) “Saturday market finds under ₦5k” photo carousel<br>
          2) A Roll: “3 outfits for rainy Lagos evenings”<br>
          3) Community poll: “Brunch spot of the week?”
        </div>
        <div class="apl-ai-bubble apl-ai-bubble--user">Rewrite the second one as a short caption.</div>
        <div class="apl-ai-bubble apl-ai-bubble--bot">Rainy evenings deserve better outfits. 3 looks I’d actually wear in Lagos this week ☔✨ #LagosStyle</div>
      </div>
    </div>
  </div>
</section>

{{-- AI CAPTIONS --}}
<section class="apl-showcase apl-showcase--soft" id="captions">
  <p class="apl-showcase__eyebrow reveal">AI Captions</p>
  <h2 class="apl-journey-heading reveal">Captions That<br>Sound Like You.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">Generate hooks, hashtags and post copy that help your content travel further — then edit until it feels authentic.</p>

  <div class="apl-wrap apl-grid-3" style="margin-top:clamp(36px,5vw,52px)">
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></div>
      <h3>Caption drafts in seconds</h3>
      <p>Turn a rough idea into ready-to-edit captions for posts, Rolls and community updates.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h10M4 17h7"/></svg></div>
      <h3>Multiple tones</h3>
      <p>Try educational, witty, bold or soft storytelling styles until the voice fits your brand.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
      <h3>Discovery-friendly copy</h3>
      <p>Pair captions with hashtags and hooks designed to help the right audience find you.</p>
    </article>
  </div>
</section>

{{-- AI ANALYTICS --}}
<section class="apl-section apl-section--white" id="analytics">
  <div class="apl-wrap apl-split">
    <div class="apl-dark-card reveal">
      <p class="apl-showcase__eyebrow">AI Analytics</p>
      <h2>Know what is working</h2>
      <p>See which posts, Rolls and community updates drive engagement and earnings — then double down.</p>
      <div class="apl-chips">
        <div class="apl-chip"><b>Views</b><span>reach</span></div>
        <div class="apl-chip"><b>Likes</b><span>signals</span></div>
        <div class="apl-chip"><b>Earnings</b><span>impact</span></div>
      </div>
    </div>
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">AI Analytics</p>
      <h2 class="apl-creators-h2">Performance insight, not vanity metrics</h2>
      <p class="apl-creators-copy">AI Analytics helps you understand what your audience actually responds to — so you create with intention instead of guessing.</p>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
        <div><h4>Post-level clarity</h4><p>Track views, likes, comments and estimated earnings for individual posts.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg></div>
        <div><h4>Growth patterns</h4><p>Spot trends across formats — photos, text, quizzes and Rolls — over time.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        <div><h4>Monetization focus</h4><p>Connect content performance to income so your next post has a clearer purpose.</p></div>
      </div>
    </div>
  </div>
</section>

{{-- AI RECOMMENDATION --}}
<section class="apl-showcase apl-showcase--soft" id="recommendation">
  <p class="apl-showcase__eyebrow reveal">AI Recommendation</p>
  <h2 class="apl-journey-heading reveal">Get Discovered by<br>the Right Audience.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">Payhankey’s recommendation systems help quality content find people more likely to engage — so growth compounds.</p>

  <div class="apl-wrap apl-grid-3" style="margin-top:clamp(36px,5vw,52px)">
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15 15 0 0 1 0 20"/></svg></div>
      <h3>Smart feed discovery</h3>
      <p>Recommendations surface posts to people who are more likely to watch, react and follow.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
      <h3>Audience matching</h3>
      <p>Help your content reach creators and fans with similar interests across African markets.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
      <h3>Creator guidance</h3>
      <p>Get personalized suggestions on what to post next based on what is already working.</p>
    </article>
  </div>
</section>

{{-- AI VIDEO --}}
<section class="apl-section apl-section--white" id="video">
  <div class="apl-wrap apl-split">
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">AI Video</p>
      <h2 class="apl-creators-h2">Rolls, powered by smarter discovery</h2>
      <p class="apl-creators-copy">Payhankey Rolls are short-form vertical videos. AI helps the right viewers find them — so entertainment, education and creator stories travel further.</p>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
        <div><h4>Swipe discovery</h4><p>Vertical video built for mobile-first creator audiences.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div><h4>Watch-time signals</h4><p>Engagement and watch behavior help surface stronger Rolls to more people.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        <div><h4>Monetization ready</h4><p>Pair Rolls with eligible earning programs so views can contribute to income.</p></div>
      </div>
      <div class="apl-showcase__links" style="margin-top:8px;justify-content:flex-start">
        <a class="apl-link" href="{{ route('earn') }}">See how Rolls help you earn <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
      </div>
    </div>
    <div class="apl-ai-video reveal" aria-hidden="true">
      <div class="apl-ai-video__phone">
        <div class="apl-ai-video__screen">
          <div class="apl-ai-video__tag">AI recommended</div>
          <div class="apl-ai-video__title">3 creator habits that grew my community</div>
          <div class="apl-ai-video__meta">Rolls · 48s · For you</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ROADMAP --}}
<section class="apl-showcase apl-showcase--soft" id="roadmap">
  <p class="apl-showcase__eyebrow reveal">Roadmap</p>
  <h2 class="apl-journey-heading reveal">What’s Live.<br>What’s Next.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">We’re building AI that actually helps creators publish, grow and earn — not gimmicks.</p>

  <div class="apl-ai-roadmap">
    <article class="apl-ai-roadmap__item reveal">
      <span class="apl-ai-roadmap__status is-live">Live</span>
      <h3>AI Creator Assistant</h3>
      <p>Practical creator support for ideas, growth and monetization decisions.</p>
    </article>
    <article class="apl-ai-roadmap__item reveal">
      <span class="apl-ai-roadmap__status is-live">Live</span>
      <h3>AI Recommendations</h3>
      <p>Discovery systems that help quality posts and Rolls reach the right people.</p>
    </article>
    <article class="apl-ai-roadmap__item reveal">
      <span class="apl-ai-roadmap__status is-live">Live</span>
      <h3>Creator analytics</h3>
      <p>Clear performance views so you know what to create next.</p>
    </article>
    <article class="apl-ai-roadmap__item reveal">
      <span class="apl-ai-roadmap__status is-soon">Next</span>
      <h3>Deeper caption studio</h3>
      <p>Richer caption workflows with tone controls, hashtag packs and A/B variants.</p>
    </article>
    <article class="apl-ai-roadmap__item reveal">
      <span class="apl-ai-roadmap__status is-soon">Next</span>
      <h3>Smarter Roll insights</h3>
      <p>More precise video analytics and recommendation feedback for short-form creators.</p>
    </article>
    <article class="apl-ai-roadmap__item reveal">
      <span class="apl-ai-roadmap__status is-soon">Next</span>
      <h3>Community AI helpers</h3>
      <p>Assistants that help community owners welcome members, plan drops and grow retention.</p>
    </article>
  </div>
</section>

{{-- CTA --}}
@include('general.partials.apl-close-cta', [
  'eyebrow' => 'Create with AI',
  'title' => 'Let AI handle the busywork. You create.',
  'lead' => 'Start free on Payhankey and use AI tools to publish faster, get discovered and grow your creator income.',
  'primaryLabel' => 'Start Free',
  'secondaryLabel' => 'Login',
])

@endsection
