@extends('general.master.apple')

@section('title', 'Payhankey | Creator Monetization Platform for Africa')
@section('meta_description', 'Payhankey is an AI-powered creator monetization platform built for Africa. Create, grow, monetize content, build communities and earn through subscriptions and local payouts.')

@section('apple_content')

  <section class="apl-hero">
    <p class="apl-hero__kicker reveal">Payhankey</p>
    <h1 class="reveal">Turn Your Content Into<br>a Sustainable Business</h1>
    <p class="apl-hero__sub reveal">Join over 32,000 creators using AI-powered tools, creator communities and local currency payouts to grow their audience, monetize their content and build sustainable digital businesses.</p>
    <div class="apl-hero__links reveal">
      <a class="apl-link" href="{{ url('/register') }}">Start Building Free <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
      <a class="apl-link" href="{{ route('features') }}">Explore Creator Tools <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
    </div>

    <div class="apl-hero__visual reveal">
      <div class="apl-glow" aria-hidden="true"></div>
      <div class="apl-phone float float--slow">
        <div class="apl-phone__screen apl-phone__screen--dash">
          <div class="apl-phone__status"><span>9:41</span><span>5G</span></div>
          @include('general.partials.apl-dashboard-mock')
        </div>
      </div>
    </div>
  </section>

  {{-- PLATFORM --}}
  <section class="apl-showcase apl-showcase--dark" id="features">
    <p class="apl-showcase__eyebrow reveal">Creator business</p>
    <h2 class="reveal">More Than a Social Platform.<br>Build Your Creator Business.</h2>
    <p class="apl-showcase__lead reveal">Most social platforms help you publish content. Payhankey helps you grow an audience, build communities, earn recurring income, and manage your creator business, all from one platform.</p>
    <div class="apl-showcase__links reveal">
      <a class="apl-link apl-link--light" href="{{ route('features') }}">Explore features <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
    </div>
    <div class="apl-panel reveal">
      <div class="apl-panel__inner">
        <div>
          <div class="apl-panel__stat"><span data-count="32000" data-suffix="+">0</span></div>
          <div class="apl-panel__label">Creators</div>
        </div>
        <div>
          <div class="apl-panel__stat"><span data-count="1700" data-suffix="+">0</span></div>
          <div class="apl-panel__label">Creators Paid</div>
        </div>
        <div>
          <div class="apl-panel__stat"><span data-count="4">0</span></div>
          <div class="apl-panel__label">Community Types</div>
        </div>
        <div>
          <div class="apl-panel__stat"><span data-count="6">0</span></div>
          <div class="apl-panel__label">African Countries</div>
        </div>
        <div>
          <div class="apl-panel__stat"><span>Local</span></div>
          <div class="apl-panel__label">Currency Payouts</div>
        </div>
        <div>
          <div class="apl-panel__stat"><span>AI</span></div>
          <div class="apl-panel__label">Creator Tools</div>
        </div>
      </div>
    </div>
  </section>

  {{-- WHY SWITCH --}}
  <section class="apl-showcase apl-showcase--soft" id="why">
    <p class="apl-showcase__eyebrow reveal">The switch</p>
    <h2 class="apl-compare-heading reveal">Why creators are<br>switching to Payhankey</h2>
    <div class="apl-compare reveal">
      <div class="apl-compare__head" aria-hidden="true">
        <span class="apl-compare__label">Most Social Platforms</span>
        <span class="apl-compare__label apl-compare__label--pk">Payhankey</span>
      </div>
      <div class="apl-compare__rows">
        @foreach ([
          ['Followers first', 'Creator business first'],
          ['Limited monetization', 'Multiple income streams'],
          ['One audience', 'Communities you own'],
          ['Generic algorithms', 'AI creator assistant'],
          ['External tools needed', 'Everything in one platform'],
        ] as $pair)
          <div class="apl-compare__row">
            <div class="apl-compare__cell apl-compare__cell--them">
              <span class="apl-compare__mobile-label">Most Social Platforms</span>
              <span class="apl-compare__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
              </span>
              <span>{{ $pair[0] }}</span>
            </div>
            <div class="apl-compare__cell apl-compare__cell--us">
              <span class="apl-compare__mobile-label">Payhankey</span>
              <span class="apl-compare__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              </span>
              <span>{{ $pair[1] }}</span>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- CREATOR JOURNEY --}}
  <section class="apl-showcase apl-showcase--light" id="tools">
    <p class="apl-showcase__eyebrow reveal">Monetization made Simple</p>
    <h2 class="apl-journey-heading reveal">Everything You Need to<br>Build Your Creator Business.</h2>
    <p class="apl-showcase__lead apl-journey-lead reveal">From AI-powered growth to memberships, creator communities and local payouts, Payhankey gives you everything you need to turn your passion into a sustainable business.</p>
    <div class="apl-bento">
      <article class="apl-tile apl-tile--hero apl-tile--violet reveal">
        <div class="apl-tile__top">
          <span class="apl-tile__tag">AI</span>
          <span class="apl-tile__badge">Coming Soon</span>
        </div>
        <div class="apl-tile__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v2"/><path d="M12 19v2"/><path d="M3 12h2"/><path d="M19 12h2"/><circle cx="12" cy="12" r="4"/><path d="m5.6 5.6 1.4 1.4"/><path d="m16.9 16.9 1.5 1.5"/><path d="m5.6 18.4 1.4-1.4"/><path d="m16.9 7.1 1.5-1.5"/></svg>
        </div>
        <h3>AI Creator Assistant</h3>
        <p class="apl-tile__lead">Grow faster with AI.</p>
        <p>Generate captions, discover the best posting times, improve your content and receive personalized recommendations to reach more people.</p>
      </article>

      <article class="apl-tile apl-tile--half apl-tile--dark reveal">
        <span class="apl-tile__tag">Communities</span>
        <div class="apl-tile__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3>Creator Communities</h3>
        <p>Build public, membership, private or request-to-join communities and earn recurring income from your audience.</p>
        <a class="apl-tile__link" href="{{ route('features') }}">Learn More <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
      </article>

      <article class="apl-tile apl-tile--half reveal">
        <span class="apl-tile__tag">Earnings</span>
        <div class="apl-tile__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v10"/><path d="M9.5 9.5c.6-1 1.6-1.5 2.5-1.5 1.4 0 2.5.8 2.5 2s-1.1 2-2.5 2h-1c-1.4 0-2.5.8-2.5 2s1.1 2 2.5 2c.9 0 1.9-.5 2.5-1.5"/></svg>
        </div>
        <h3>Multiple Income Streams</h3>
        <p>Earn from creator rewards, memberships, referrals, community subscriptions and future brand partnerships.</p>
      </article>

      <article class="apl-tile reveal">
        <span class="apl-tile__tag">Analytics</span>
        <div class="apl-tile__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V9"/><path d="M10 19V5"/><path d="M16 19v-7"/><path d="M22 19V8"/></svg>
        </div>
        <h3>Creator Analytics</h3>
        <p class="apl-tile__lead">Know what works.</p>
        <p>Track growth, engagement, audience behaviour and estimated earnings.</p>
      </article>

      <article class="apl-tile reveal">
        <span class="apl-tile__tag">Payouts</span>
        <div class="apl-tile__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h2"/><path d="M12 15h2"/></svg>
        </div>
        <h3>Local Payouts</h3>
        <p>Withdraw earnings through local bank accounts, PayPal or USDT across supported African countries.</p>
      </article>

      <article class="apl-tile reveal">
        <span class="apl-tile__tag">Rolls</span>
        <div class="apl-tile__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="20" rx="3"/><circle cx="12" cy="17" r="1"/></svg>
        </div>
        <h3>Short Videos</h3>
        <p>Create engaging vertical videos, Payhankey Rolls, with AI-powered discovery and recommendations.</p>
      </article>
    </div>
  </section>

  {{-- CREATOR JOURNEY STEPS --}}
  <section class="apl-showcase apl-showcase--soft" id="how">
    <p class="apl-showcase__eyebrow reveal">The Creator Journey</p>
    <h2 class="apl-journey-heading reveal">From Your First Post<br>to Your First Community.</h2>
    <p class="apl-showcase__lead apl-journey-lead reveal">Payhankey is more than a place to post content. It's where creators grow audiences, launch communities, earn recurring income, and build lasting digital businesses.</p>
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
    <div class="apl-showcase__links reveal" style="margin-top:2rem">
      <a class="apl-link" href="{{ url('/how-it-works') }}">See the Complete Creator Guide <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
    </div>
  </section>

  {{-- CREATOR PLANS --}}
  <section class="apl-showcase apl-showcase--light" id="tiers">
    <p class="apl-showcase__eyebrow reveal">Creator Plans</p>
    <h2 class="apl-journey-heading reveal">Start Free.<br>Grow Your Way.</h2>
    <p class="apl-showcase__lead apl-journey-lead reveal">Start creating for free. Upgrade when you want more monetization, creator tools and visibility.</p>
    <div class="apl-tiers">
      <div class="apl-tier reveal">
        <div class="apl-tier__name">Basic</div>
        <div class="apl-tier__price">Free</div>
        <p class="apl-tier__blurb">Your starting point for creating and growing on Payhankey.</p>
        <ul>
          <li>Unlimited posts &amp; quizzes</li>
          <li>Payhankey Rolls (Videos)</li>
          <li>Full dashboard access</li>
          <li>Discover and join communities</li>
          <li>Up to $2 welcome bonus</li>
        </ul>
        <a class="apl-btn apl-btn--outline" href="{{ url('/register') }}">Start Free</a>
      </div>
      <div class="apl-tier apl-tier--featured reveal">
        <div class="apl-tier__badge">Most Popular</div>
        <div class="apl-tier__name">Creator</div>
        <div class="apl-tier__price">$1<small>/month</small></div>
        <p class="apl-tier__blurb">For creators ready to monetize their content and grow their audience.</p>
        <ul>
          <li>Everything in Basic</li>
          <li>Content monetization</li>
          <li>Create &amp; monetize communities</li>
          <li>Earn up to $2 per eligible post</li>
          <li>Verified creator badge</li>
          <li>Image &amp; video posting</li>
          <li>Priority discovery</li>
          <li>AI Creator support tools</li>
        </ul>
        <a class="apl-btn apl-btn--fill" href="{{ url('/register') }}">Become a Creator</a>
      </div>
      <div class="apl-tier reveal">
        <div class="apl-tier__name">Influencer</div>
        <div class="apl-tier__price">$5<small>/month</small></div>
        <p class="apl-tier__blurb">For established creators ready to increase their reach and earning potential.</p>
        <ul>
          <li>Everything in Creator</li>
          <li>Earn up to $5 per eligible post</li>
          <li>Influencer verification badge</li>
          <li>Influencer profile ring</li>
          <li>Higher content limits</li>
          <li>Top-feed placement</li>
          <li>Priority discovery</li>
          <li>Advanced creator opportunities</li>
        </ul>
        <a class="apl-btn apl-btn--outline" href="{{ url('/register') }}">Become an Influencer</a>
      </div>
    </div>
    <p class="apl-tiers__trust reveal">No long-term commitment. Cancel anytime.</p>
  </section>

  {{-- FAQ --}}
  <section class="apl-showcase apl-showcase--soft apl-showcase--to-close" id="faq">
    <p class="apl-showcase__eyebrow reveal">FAQ</p>
    <h2 class="apl-journey-heading reveal">Everything You Need<br>to Know.</h2>
    <p class="apl-showcase__lead apl-journey-lead reveal">From earning and withdrawals to Payhankey Rolls, communities and creator subscriptions, here's how Payhankey works.</p>
    <div class="apl-faq faq reveal">
      @foreach ([
        [
          'q' => 'What is Payhankey?',
          'a' => 'Payhankey is an AI-powered creator platform built to help creators grow their audience, monetize their content, build communities and create sustainable digital businesses. You can publish posts and Payhankey Rolls, create communities, earn from eligible monetization programs and receive payouts through supported payment methods.',
        ],
        [
          'q' => 'How do I earn money on Payhankey?',
          'a' => 'Creators can earn through eligible content monetization, creator rewards, community memberships, referrals and other monetization opportunities available on the platform. Your available earning options depend on your account plan, eligibility and the specific program.',
        ],
        [
          'q' => 'Do I need a certain number of followers to earn on Payhankey?',
          'a' => 'Payhankey is designed to give creators opportunities to monetize without requiring the large follower counts demanded by many traditional creator monetization programs. However, specific monetization features may have their own eligibility requirements.',
        ],
        [
          'q' => 'How much does Payhankey cost?',
          'a' => 'Creating a basic Payhankey account is free. Creators can optionally subscribe to the Creator plan for $1/month or the Influencer plan for $5/month to access additional creator, monetization and visibility features.',
        ],
        [
          'q' => 'How do Payhankey creator subscriptions work?',
          'a' => 'The Creator plan costs $1/month and the Influencer plan costs $5/month. Subscriptions unlock additional creator features, monetization opportunities and visibility. You can manage your subscription from your account and cancel according to the applicable subscription terms.',
        ],
        [
          'q' => 'How do I get paid on Payhankey?',
          'a' => 'Payhankey supports payout options including local bank accounts, PayPal and USDT, depending on your country and the payment methods available to you. Your available payout options are shown in your account.',
        ],
        [
          'q' => 'What are Payhankey Rolls?',
          'a' => 'Payhankey Rolls are Payhankey\'s short-form vertical videos. Creators can use Rolls to share entertaining, educational or informative content and reach new audiences through Payhankey\'s discovery experience.',
        ],
        [
          'q' => 'What are Payhankey Communities?',
          'a' => 'Communities let creators build dedicated spaces around their audience, interests or expertise. A creator can create a Public, Membership, Private or Request-to-Join community and, where supported, charge members for access to exclusive content and experiences.',
        ],
        [
          'q' => 'Can I make money from my Payhankey community?',
          'a' => 'Yes. Creators can create paid communities, set a membership price and earn recurring income from subscribers. Payhankey currently charges a 1–2% platform fee on eligible community earnings, subject to the applicable terms and payment processing arrangements.',
        ],
        [
          'q' => 'Who can join Payhankey?',
          'a' => 'Payhankey is built for creators and digital professionals across Africa and beyond. Whether you\'re a student, influencer, educator, entrepreneur, musician, comedian, writer or simply someone with something valuable to share, you can create an account and start building your audience. Availability of specific features and payout methods may vary by country.',
        ],
      ] as $item)
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

  @include('general.partials.apl-close-cta')

@endsection
