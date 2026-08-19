@extends('general.master.apple')

@php
    $faqs = [
        [
            'q' => 'How do I earn money on Payhankey?',
            'a' => 'Creators can earn through eligible content monetization, creator and influencer plan rewards, paid community memberships, referral commissions and other monetization programs available on Payhankey. Available options depend on your plan and eligibility.',
        ],
        [
            'q' => 'Do I need thousands of followers to start earning?',
            'a' => 'No. Payhankey is designed so creators can monetize without the large follower thresholds used by many traditional platforms. Some features may still have their own eligibility rules.',
        ],
        [
            'q' => 'What is the difference between Creator and Influencer plans?',
            'a' => 'The Creator plan ($1/month) unlocks monetization features and a verified creator badge. The Influencer plan ($5/month) adds stronger visibility and higher earning potential per eligible post, with a premium verified badge.',
        ],
        [
            'q' => 'How do community subscriptions work?',
            'a' => 'Creators can launch paid communities, set a membership price (one-off or recurring) and earn from members who join. Platform fees on eligible community earnings are typically 1–2%, subject to applicable terms.',
        ],
        [
            'q' => 'How do referrals pay?',
            'a' => 'Share your invite code or link. When people you refer join and become active creators on Payhankey, you can earn referral commissions according to the current referral program rules.',
        ],
        [
            'q' => 'How and when can I withdraw?',
            'a' => 'Withdrawals are available through supported methods such as PayPal, USDT and local bank transfer, depending on your country. Payouts are processed on the 2nd of every month, subject to eligibility and account verification.',
        ],
        [
            'q' => 'Is Payhankey free to join?',
            'a' => 'Yes. Creating a Basic account is free. You can upgrade to Creator ($1/month) or Influencer ($5/month) when you want more monetization and visibility.',
        ],
        [
            'q' => 'Where can I track my earnings?',
            'a' => 'Your dashboard shows your balance, engagement activity and available earnings so you can see what is driving income and when you are ready to withdraw.',
        ],
    ];
@endphp

@section('title', 'How to Earn on Payhankey | Creator Monetization for Africa')
@section('meta_description', 'Learn how to earn on Payhankey: content rewards, Creator and Influencer plans, paid communities, referrals and withdrawals via PayPal, USDT and local bank. Start free.')
@section('body_class', 'page-landing-apple page-earn')

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faqs)->map(fn ($item) => [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $item['a'],
        ],
    ])->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'How to Earn on Payhankey',
    'description' => 'Learn how creators earn on Payhankey through content monetization, subscriptions, communities, referrals and withdrawals.',
    'url' => url('/earn'),
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
    <div class="apl-crumbs reveal"><a href="{{ url('/') }}">Home</a> / <span>Earn</span></div>
    <p class="apl-creators-hero__eyebrow reveal">Creator monetization</p>
    <h1 class="reveal">How to Earn Money<br>on Payhankey</h1>
    <p class="apl-creators-hero__lead reveal">
      Turn posts, communities and referrals into real income. Payhankey helps African creators monetize content, grow memberships and withdraw through local-ready payout methods — without waiting for 10K followers.
    </p>
    <div class="apl-creators-hero__cta reveal">
      <a class="apl-btn apl-btn--fill" href="{{ url('/register') }}">Start Earning Free</a>
      <a class="apl-btn apl-btn--ghost-dark" href="#ways">See ways to earn</a>
    </div>
    <div class="apl-creators-hero__proof reveal">
      <div><b>32,000+</b><span>creators</span></div>
      <div><b>1,700+</b><span>creators paid</span></div>
      <div><b>From $1</b><span>min withdrawal</span></div>
    </div>
  </div>
</section>

{{-- WAYS TO EARN --}}
<section class="apl-showcase apl-showcase--light" id="ways">
  <p class="apl-showcase__eyebrow reveal">Ways to earn</p>
  <h2 class="apl-journey-heading reveal">Multiple Income Paths.<br>One Creator Dashboard.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">Don’t depend on a single algorithm. Stack earnings from content, memberships, referrals and plan rewards.</p>

  <div class="apl-wrap apl-grid-3" style="margin-top:clamp(36px,5vw,52px)">
    <article class="apl-feature-card reveal" id="content-rewards">
      <div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
      <h3>Content rewards</h3>
      <p>Eligible likes, comments and views on your posts and Rolls can contribute to earnings on Creator and Influencer plans.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
      <h3>Community memberships</h3>
      <p>Charge one-off or recurring fees for access to your public, private or paid community.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg></div>
      <h3>Referral commissions</h3>
      <p>Invite creators with your code and earn when they join and grow on Payhankey.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
      <h3>Payhankey Rolls</h3>
      <p>Publish short-form video, reach new audiences through discovery and monetize eligible engagement.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
      <h3>Creator plan rewards</h3>
      <p>Upgrade for $1/month to unlock monetization features and earn more from eligible posts.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15 8.5 22 9.3 17 14.1 18.2 21 12 17.8 5.8 21 7 14.1 2 9.3 9 8.5 12 2"/></svg></div>
      <h3>Influencer plan rewards</h3>
      <p>Upgrade for $5/month for higher visibility and stronger earning potential on eligible content.</p>
    </article>
  </div>
</section>

{{-- SUBSCRIPTIONS OVERVIEW --}}
<section class="apl-showcase apl-showcase--soft" id="subscriptions">
  <p class="apl-showcase__eyebrow reveal">Subscriptions</p>
  <h2 class="apl-journey-heading reveal">Start Free.<br>Upgrade When You’re Ready.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">Basic is free forever. Creator and Influencer unlock deeper monetization and visibility.</p>

  <div class="apl-tiers">
    <div class="apl-tier reveal">
      <div class="apl-tier__name">Basic</div>
      <div class="apl-tier__price">Free</div>
      <p class="apl-tier__blurb">Create, explore communities and learn the platform.</p>
      <ul>
        <li>Unlimited posts &amp; quizzes</li>
        <li>Payhankey Rolls</li>
        <li>Join communities</li>
        <li>Dashboard access</li>
      </ul>
      <a class="apl-btn apl-btn--ghost-dark" href="{{ url('/register') }}">Create free account</a>
    </div>
    <div class="apl-tier apl-tier--featured reveal" id="creator">
      <div class="apl-tier__badge">Most popular</div>
      <div class="apl-tier__name">Creator</div>
      <div class="apl-tier__price">$1 <small>/ month</small></div>
      <p class="apl-tier__blurb">Monetize posts and grow as a verified creator.</p>
      <ul>
        <li>Verified creator badge</li>
        <li>Eligible post monetization</li>
        <li>Earn up to $2 per post</li>
        <li>Images &amp; video support</li>
      </ul>
      <a class="apl-btn apl-btn--fill" href="{{ url('/register') }}">Start Creator</a>
    </div>
    <div class="apl-tier reveal" id="influencer">
      <div class="apl-tier__name">Influencer</div>
      <div class="apl-tier__price">$5 <small>/ month</small></div>
      <p class="apl-tier__blurb">Higher visibility and stronger earning potential.</p>
      <ul>
        <li>Premium verified badge</li>
        <li>Priority placement</li>
        <li>Earn up to $5 per post</li>
        <li>Advanced creator tools</li>
      </ul>
      <a class="apl-btn apl-btn--ghost-dark" href="{{ url('/register') }}">Go Influencer</a>
    </div>
  </div>
</section>

{{-- COMMUNITIES --}}
<section class="apl-section apl-section--white" id="communities">
  <div class="apl-wrap apl-split">
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">Communities</p>
      <h2 class="apl-creators-h2">Earn recurring income from members</h2>
      <p class="apl-creators-copy">Communities let you own the relationship with your audience. Charge for access, deliver exclusive value and build monthly membership revenue.</p>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg></div>
        <div><h4>Public communities</h4><p>Grow reach fast, then convert engaged members into paid subscribers.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        <div><h4>Paid memberships</h4><p>Set one-off or subscription pricing and earn when members join and renew.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
        <div><h4>Private &amp; approval groups</h4><p>Curate membership for exclusive coaching, premium drops or high-trust circles.</p></div>
      </div>
      <div class="apl-showcase__links" style="margin-top:8px;justify-content:flex-start">
        <a class="apl-link" href="{{ route('communities') }}">Browse communities <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
      </div>
    </div>
    <div class="apl-dark-card reveal">
      <p class="apl-showcase__eyebrow">Community earnings</p>
      <h2>Memberships that pay monthly</h2>
      <p>Launch a paid community, set your price and keep earning as members stay engaged.</p>
      <div class="apl-chips">
        <div class="apl-chip"><b>1–2%</b><span>platform fee</span></div>
        <div class="apl-chip"><b>One-off</b><span>or subscription</span></div>
        <div class="apl-chip"><b>Local</b><span>ready payouts</span></div>
      </div>
      <a class="apl-btn apl-btn--fill" style="margin-top:24px;display:inline-flex" href="{{ url('/register') }}">Create your community</a>
    </div>
  </div>
</section>

{{-- REFERRAL --}}
<section class="apl-showcase apl-showcase--soft" id="referral">
  <p class="apl-showcase__eyebrow reveal">Referral</p>
  <h2 class="apl-journey-heading reveal">Earn When You<br>Invite Creators.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">Your network is an income stream. Share your invite code and earn commissions when referred creators join and grow.</p>

  <div class="apl-steps">
    <div class="apl-step reveal">
      <div class="apl-step__num">1</div>
      <h3>Get your code</h3>
      <p>Find your unique referral link or code inside your Payhankey account.</p>
    </div>
    <div class="apl-step reveal">
      <div class="apl-step__num">2</div>
      <h3>Invite creators</h3>
      <p>Share it with friends, followers and fellow creators across Africa.</p>
    </div>
    <div class="apl-step reveal">
      <div class="apl-step__num">3</div>
      <h3>They join &amp; create</h3>
      <p>When they sign up and become active, your referral activity starts counting.</p>
    </div>
    <div class="apl-step reveal">
      <div class="apl-step__num">4</div>
      <h3>Earn commissions</h3>
      <p>Receive referral earnings according to the current program rules — tracked in your dashboard.</p>
    </div>
  </div>
</section>

{{-- CREATOR PLAN --}}
<section class="apl-section apl-section--white">
  <div class="apl-wrap apl-split">
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">Creator plan</p>
      <h2 class="apl-creators-h2">Creator — $1/month</h2>
      <p class="apl-creators-copy">Built for creators ready to monetize consistently. Unlock eligible post earnings, a verified badge and the tools to grow a real creator income.</p>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
        <div><h4>Verified creator badge</h4><p>Stand out in the feed with a trusted creator mark.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        <div><h4>Earn up to $2 per post</h4><p>Eligible engagement on your content can contribute to your balance.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg></div>
        <div><h4>Images &amp; video monetization</h4><p>Post the formats your audience loves — including Rolls.</p></div>
      </div>
    </div>
    <div class="apl-creators-panel reveal" aria-hidden="true">
      <div class="apl-creators-panel__bar"><span></span><span></span><span></span></div>
      <div class="apl-creators-panel__title">Creator dashboard</div>
      <div class="apl-creators-panel__meta">Plan · Creator · $1/mo</div>
      <div class="apl-creators-panel__stats">
        <div><b>$128</b><span>This month</span></div>
        <div><b>42</b><span>Posts</span></div>
        <div><b>$2</b><span>Max / post</span></div>
      </div>
      <div class="apl-creators-panel__feed">
        <div class="apl-creators-panel__post">
          <div class="apl-creators-panel__av">C</div>
          <div><strong>Post earnings updated</strong><p>Engagement rewards added to your wallet.</p></div>
        </div>
        <div class="apl-creators-panel__post">
          <div class="apl-creators-panel__av apl-creators-panel__av--mint">R</div>
          <div><strong>Roll discovered</strong><p>New viewers found your latest short video.</p></div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- INFLUENCER PLAN --}}
<section class="apl-section apl-section--soft">
  <div class="apl-wrap apl-split">
    <div class="apl-dark-card reveal" style="order:0">
      <p class="apl-showcase__eyebrow">Influencer plan</p>
      <h2>Built for scale</h2>
      <p>For creators who want maximum visibility and higher earning ceilings on eligible content.</p>
      <div class="apl-chips">
        <div class="apl-chip"><b>$5</b><span>per month</span></div>
        <div class="apl-chip"><b>$5</b><span>max / post</span></div>
        <div class="apl-chip"><b>Top</b><span>placement</span></div>
      </div>
      <a class="apl-btn apl-btn--fill" style="margin-top:24px;display:inline-flex" href="{{ url('/register') }}">Upgrade to Influencer</a>
    </div>
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">Influencer plan</p>
      <h2 class="apl-creators-h2">Influencer — $5/month</h2>
      <p class="apl-creators-copy">Get premium placement, a stronger verified identity and higher earning potential as your audience grows.</p>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div>
        <div><h4>Premium verified badge</h4><p>A higher-signal identity that builds trust with new audiences.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg></div>
        <div><h4>Priority visibility</h4><p>Stronger placement so more people discover your posts and Rolls.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        <div><h4>Earn up to $5 per post</h4><p>Higher earning potential on eligible engagement and content activity.</p></div>
      </div>
    </div>
  </div>
</section>

{{-- WITHDRAWALS --}}
<section class="apl-showcase apl-showcase--light" id="withdrawals">
  <p class="apl-showcase__eyebrow reveal">Withdrawals</p>
  <h2 class="apl-journey-heading reveal">Get Paid Your Way.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">Cash out through methods built for creators across African markets — with transparent monthly processing.</p>

  <div class="apl-wrap apl-grid-3" style="margin-top:clamp(36px,5vw,52px)">
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
      <h3>Local bank</h3>
      <p>Withdraw to supported local bank accounts where available in your country.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
      <h3>PayPal</h3>
      <p>Send earnings to your PayPal balance when the method is enabled for your account.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg></div>
      <h3>USDT</h3>
      <p>Withdraw in USDT where supported — useful for cross-border creator payouts.</p>
    </article>
  </div>

  <div class="apl-wrap" style="margin-top:28px">
    <div class="apl-earn-note reveal">
      <strong>Payout schedule:</strong> Eligible withdrawals are processed on the <b>2nd of every month</b>. Minimum withdrawal starts from <b>$1</b>, subject to method availability and account checks.
    </div>
  </div>
</section>

{{-- FAQ --}}
<section class="apl-showcase apl-showcase--soft apl-showcase--to-close" id="faq">
  <p class="apl-showcase__eyebrow reveal">FAQ</p>
  <h2 class="apl-journey-heading reveal">Earning Questions,<br>Answered.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">Clear answers about monetization, plans, communities, referrals and withdrawals.</p>

  <div class="apl-faq faq reveal">
    @foreach ($faqs as $item)
      <div class="faq__item">
        <button class="faq__q" type="button">{{ $item['q'] }}<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>
        <div class="faq__a"><div class="faq__a-inner">{{ $item['a'] }}</div></div>
      </div>
    @endforeach
  </div>

  <div class="apl-showcase__links reveal" style="margin-top:2rem">
    <a class="apl-link" href="{{ route('help') }}">Browse the Help Center <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
  </div>
</section>

{{-- CTA --}}
@include('general.partials.apl-close-cta', [
  'eyebrow' => 'Start earning',
  'title' => 'Post once. Get paid. Build your creator income.',
  'lead' => 'Create a free Payhankey account, publish your first post and unlock monetization when you’re ready.',
  'primaryLabel' => 'Start Free',
  'secondaryLabel' => 'Login',
])

@endsection
