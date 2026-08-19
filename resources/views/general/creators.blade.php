@extends('general.master.apple')

@section('title', 'Creators · Payhankey | Why Creators Choose Payhankey')
@section('meta_description', 'Why creators choose Payhankey: monetize content without follower gates, build communities, unlock multiple income streams, use AI tools and get paid locally across Africa.')
@section('body_class', 'page-landing-apple page-creators')

@section('apple_content')

{{-- HERO --}}
<section class="apl-creators-hero">
  <div class="apl-creators-hero__inner">
    <div class="apl-crumbs reveal"><a href="{{ url('/') }}">Home</a> / <span>Creators</span></div>
    <p class="apl-creators-hero__eyebrow reveal">Built for creators</p>
    <h1 class="reveal">Create. Grow. Get Paid.<br>Own Your Creator Business.</h1>
    <p class="apl-creators-hero__lead reveal">
      Payhankey helps African creators turn content into income — with AI tools, communities, multiple earning streams and local payouts. No 10K-follower gate. No vanity metrics.
    </p>
    <div class="apl-creators-hero__cta reveal">
      <a class="apl-btn apl-btn--fill" href="{{ url('/register') }}">Start Free</a>
      <a class="apl-btn apl-btn--ghost-dark" href="{{ route('earn') }}">See how you earn</a>
    </div>
    <div class="apl-creators-hero__proof reveal" aria-label="Creator proof points">
      <div><b>32,000+</b><span>creators</span></div>
      <div><b>1,700+</b><span>creators paid</span></div>
      <div><b>6+</b><span>African markets</span></div>
    </div>
  </div>
</section>

{{-- BENEFITS --}}
<section class="apl-showcase apl-showcase--light" id="benefits">
  <p class="apl-showcase__eyebrow reveal">Why Payhankey</p>
  <h2 class="apl-journey-heading reveal">Why Creators<br>Choose Payhankey.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">Most platforms ask you to grow first and monetize later. Payhankey is built so creators can publish, build audiences and earn from day one.</p>

  <div class="apl-wrap apl-grid-3" style="margin-top:clamp(36px,5vw,52px)">
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
      <h3>Earn without follower gates</h3>
      <p>Eligible engagement can contribute to earnings — you don’t need thousands of followers before your work has value.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
      <h3>Own your audience</h3>
      <p>Turn followers into members with communities — public, private, paid or request-to-join — and keep the relationship.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div>
      <h3>Local-ready payouts</h3>
      <p>Cash out via PayPal, USDT or local bank options designed for creators across African markets.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
      <h3>AI that helps you grow</h3>
      <p>Use AI-powered tools for discovery, content support and smarter growth — so you spend more time creating.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
      <h3>Clear earnings dashboard</h3>
      <p>See what you earn, which posts perform and how your balance grows — transparent by design.</p>
    </article>
    <article class="apl-feature-card reveal">
      <div class="apl-feature-card__icon apl-feature-card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
      <h3>Built for Africa</h3>
      <p>Creator tools, communities and payouts shaped for African creators — not retrofitted from elsewhere.</p>
    </article>
  </div>
</section>

{{-- CREATOR JOURNEY --}}
<section class="apl-showcase apl-showcase--soft" id="journey">
  <p class="apl-showcase__eyebrow reveal">The Creator Journey</p>
  <h2 class="apl-journey-heading reveal">From Your First Post<br>to a Real Creator Business.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">A clear path from creating to monetizing — designed so you always know what to do next.</p>

  <div class="apl-steps">
    <div class="apl-step reveal">
      <div class="apl-step__num">1</div>
      <h3>Create your profile</h3>
      <p>Sign up free, set up your creator profile and show the world what you create.</p>
    </div>
    <div class="apl-step reveal">
      <div class="apl-step__num">2</div>
      <h3>Publish &amp; get discovered</h3>
      <p>Share posts, photos or Payhankey Rolls. Smart discovery helps the right audience find you.</p>
    </div>
    <div class="apl-step reveal">
      <div class="apl-step__num">3</div>
      <h3>Build community</h3>
      <p>Convert attention into membership with public, private or paid communities.</p>
    </div>
    <div class="apl-step reveal">
      <div class="apl-step__num">4</div>
      <h3>Earn &amp; scale</h3>
      <p>Stack income from engagement, memberships, referrals and more — then withdraw on schedule.</p>
    </div>
  </div>
</section>

{{-- INCOME STREAMS --}}
<section class="apl-showcase apl-showcase--light" id="income">
  <p class="apl-showcase__eyebrow reveal">Monetization</p>
  <h2 class="apl-journey-heading reveal">Multiple Ways<br>to Make Money.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">Don’t rely on one algorithm. Payhankey gives creators several income paths that can work together.</p>

  <div class="apl-creators-income">
    <article class="apl-creators-income__card reveal">
      <span class="apl-creators-income__tag">Engagement</span>
      <h3>Content rewards</h3>
      <p>Eligible likes, comments and views on your posts and Rolls can contribute to earnings on monetized plans.</p>
    </article>
    <article class="apl-creators-income__card reveal">
      <span class="apl-creators-income__tag">Recurring</span>
      <h3>Community memberships</h3>
      <p>Charge subscriptions or one-off access for your community — turn fans into paying members.</p>
    </article>
    <article class="apl-creators-income__card reveal">
      <span class="apl-creators-income__tag">Growth</span>
      <h3>Referral commissions</h3>
      <p>Share your invite code. When creators you refer join and grow, you can earn affiliate commissions.</p>
    </article>
    <article class="apl-creators-income__card reveal">
      <span class="apl-creators-income__tag">Video</span>
      <h3>Payhankey Rolls</h3>
      <p>Publish short-form vertical video and reach new audiences through swipe discovery and recommendations.</p>
    </article>
    <article class="apl-creators-income__card reveal">
      <span class="apl-creators-income__tag">Upgrades</span>
      <h3>Creator &amp; Influencer plans</h3>
      <p>Start free, then unlock stronger monetization and visibility when you’re ready to grow faster.</p>
    </article>
    <article class="apl-creators-income__card reveal">
      <span class="apl-creators-income__tag">Future</span>
      <h3>Brand opportunities</h3>
      <p>As the platform grows, creators gain more paths to partnerships and sponsored opportunities.</p>
    </article>
  </div>
</section>

{{-- COMMUNITIES --}}
<section class="apl-section apl-section--soft" id="communities">
  <div class="apl-wrap apl-split">
    <div class="reveal">
      <p class="apl-showcase__eyebrow" style="text-align:left">Communities</p>
      <h2 class="apl-creators-h2">Your audience shouldn’t live on rented land.</h2>
      <p class="apl-creators-copy">Followers on big platforms can disappear overnight. On Payhankey, you build communities you control — spaces where members return, engage and support your work.</p>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg></div>
        <div><h4>Public communities</h4><p>Open spaces to grow reach and welcome new members quickly.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
        <div><h4>Private &amp; request-to-join</h4><p>Curate who gets in — perfect for exclusive groups and high-trust circles.</p></div>
      </div>
      <div class="apl-benefit">
        <div class="apl-benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        <div><h4>Paid memberships</h4><p>Monetize access with subscriptions or one-time fees and earn recurring income.</p></div>
      </div>
      <div class="apl-showcase__links" style="margin-top:8px;justify-content:flex-start">
        <a class="apl-link" href="{{ url('/communities') }}">Explore communities <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
      </div>
    </div>
    <div class="apl-creators-panel reveal" aria-hidden="true">
      <div class="apl-creators-panel__bar">
        <span></span><span></span><span></span>
      </div>
      <div class="apl-creators-panel__title">Design Collective</div>
      <div class="apl-creators-panel__meta">Paid · Monthly · 248 members</div>
      <div class="apl-creators-panel__stats">
        <div><b>₦185K</b><span>This month</span></div>
        <div><b>92%</b><span>Retention</span></div>
        <div><b>36</b><span>New joins</span></div>
      </div>
      <div class="apl-creators-panel__feed">
        <div class="apl-creators-panel__post">
          <div class="apl-creators-panel__av">A</div>
          <div><strong>New member brief</strong><p>Welcome pack + this week’s challenge is live.</p></div>
        </div>
        <div class="apl-creators-panel__post">
          <div class="apl-creators-panel__av apl-creators-panel__av--mint">T</div>
          <div><strong>Member tip</strong><p>Shared a resource that got 48 reactions.</p></div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- AI FEATURES --}}
<section class="apl-showcase apl-showcase--dark" id="ai">
  <p class="apl-showcase__eyebrow reveal">AI Tools</p>
  <h2 class="apl-journey-heading reveal">Create Faster.<br>Grow Smarter.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">AI on Payhankey is built to support creators — helping your content get discovered and your creator business move faster.</p>

  <div class="apl-wrap apl-creators-ai">
    <article class="apl-creators-ai__card reveal">
      <h3>AI Creator Assistant</h3>
      <p>Get practical support for growing your presence, shaping content ideas and moving faster as a creator.</p>
    </article>
    <article class="apl-creators-ai__card reveal">
      <h3>Smart discovery</h3>
      <p>Recommendation systems help quality posts and Rolls reach people who are more likely to engage.</p>
    </article>
    <article class="apl-creators-ai__card reveal">
      <h3>Performance insight</h3>
      <p>Understand what’s working with clear analytics — so you double down on content that earns attention and income.</p>
    </article>
  </div>

  <div class="apl-showcase__links reveal" style="margin-top:2rem">
    <a class="apl-link" href="{{ route('ai') }}">Explore AI Tools <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
  </div>
</section>

{{-- CREATOR STORIES --}}
<section class="apl-showcase apl-showcase--soft" id="stories">
  <p class="apl-showcase__eyebrow reveal">Creator stories</p>
  <h2 class="apl-journey-heading reveal">Creators Building<br>Real Income.</h2>
  <p class="apl-showcase__lead apl-journey-lead reveal">From first posts to paid communities — creators across Africa are using Payhankey to grow digital businesses.</p>

  <div class="apl-creators-stories">
    <figure class="apl-creators-story reveal">
      <blockquote>“I stopped waiting for follower milestones. On Payhankey I publish, engage and actually see earnings move.”</blockquote>
      <figcaption>
        <div class="apl-creators-story__av" aria-hidden="true">AO</div>
        <div>
          <strong>Adaora O.</strong>
          <span>Lifestyle creator · Lagos</span>
        </div>
      </figcaption>
    </figure>
    <figure class="apl-creators-story reveal">
      <blockquote>“My paid community became a second income stream. Members show up every week — and so do the payouts.”</blockquote>
      <figcaption>
        <div class="apl-creators-story__av apl-creators-story__av--mint" aria-hidden="true">KM</div>
        <div>
          <strong>Kwame M.</strong>
          <span>Educator · Accra</span>
        </div>
      </figcaption>
    </figure>
    <figure class="apl-creators-story reveal">
      <blockquote>“Rolls helped new people find my work. The dashboard makes it clear which content is worth doubling down on.”</blockquote>
      <figcaption>
        <div class="apl-creators-story__av apl-creators-story__av--rose" aria-hidden="true">SN</div>
        <div>
          <strong>Sade N.</strong>
          <span>Video creator · Nairobi</span>
        </div>
      </figcaption>
    </figure>
  </div>
</section>

{{-- CTA --}}
@include('general.partials.apl-close-cta', [
  'eyebrow' => 'Join the creators',
  'title' => 'Your audience is waiting. Start building today.',
  'lead' => 'Create free, publish your first post, and take the first step toward a sustainable creator business on Payhankey.',
  'primaryLabel' => 'Start Free',
  'secondaryLabel' => 'Login',
])

@endsection
