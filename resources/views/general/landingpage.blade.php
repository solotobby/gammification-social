@extends('general.master.apple')

@section('title', 'Payhankey — Get paid for engagement')
@section('meta_description', 'Payhankey pays creators for likes, comments and views. No followers needed. Free to start. Withdraw from $1.')

@section('apple_content')
@php
    $podium = $previewEarners ?? collect();
    $stats = $platformStats ?? [];
    $userCount = (int) ($stats['users'] ?? 0);
    $paidOut = (float) ($stats['paid_out'] ?? 0);
    $gradients = [
        'linear-gradient(135deg,#F5B73C,#F25C8A)',
        'linear-gradient(135deg,#7C6FF2,#F25C8A)',
        'linear-gradient(135deg,#12B886,#5A4FDC)',
    ];
    $order = $podium->count() >= 3 ? [1, 0, 2] : range(0, min(2, max(0, $podium->count() - 1)));
@endphp

  <section class="apl-hero">
    <p class="apl-hero__kicker reveal">Payhankey</p>
    <h1 class="reveal">Post once.<br>Get paid.</h1>
    <p class="apl-hero__sub reveal">The creator platform where likes, comments, and views turn into real earnings — no follower count, no watch hours.</p>
    <div class="apl-hero__links reveal">
      <a class="apl-link" href="{{ url('/register') }}">Start free <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
      <a class="apl-link" href="{{ url('/how-it-works') }}">See how it works <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
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

  {{-- ENGAGEMENT --}}
  <section class="apl-showcase apl-showcase--dark" id="features">
    <p class="apl-showcase__eyebrow reveal">Engagement</p>
    <h2 class="reveal">Every view counts.<br>Every like pays.</h2>
    <p class="apl-showcase__lead reveal">Your content already gets attention. Payhankey converts that attention into income — tracked live, paid monthly.</p>
    <div class="apl-showcase__links reveal">
      <a class="apl-link apl-link--light" href="{{ route('features') }}">Explore features <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
    </div>
    <div class="apl-panel reveal">
      <div class="apl-panel__inner">
        <div>
          <div class="apl-panel__stat"><span data-count="{{ max($userCount, 1) }}" data-suffix="{{ $userCount >= 1000 ? '+' : '' }}">0</span></div>
          <div class="apl-panel__label">Creators on platform</div>
        </div>
        <div>
          <div class="apl-panel__stat"><span data-count="{{ max((int) $paidOut, 1) }}" data-prefix="$" data-suffix="+">0</span></div>
          <div class="apl-panel__label">Paid to members</div>
        </div>
        <div>
          <div class="apl-panel__stat"><span data-count="40" data-suffix="+">0</span></div>
          <div class="apl-panel__label">Countries supported</div>
        </div>
      </div>
    </div>
  </section>

  {{-- NO FOLLOWERS --}}
  <section class="apl-showcase apl-showcase--light">
    <p class="apl-showcase__eyebrow reveal">No gatekeeping</p>
    <h2 class="reveal">Zero followers.<br>Still earn.</h2>
    <p class="apl-showcase__lead reveal">Other platforms lock monetization behind impossible thresholds. Payhankey pays from your very first post.</p>
    <div class="apl-bento">
      <article class="apl-tile apl-tile--wide apl-tile--violet reveal">
        <span class="apl-tile__tag">Wallet</span>
        <h3>Withdraw from $1</h3>
        <p>PayPal, USDT, or local bank. Payouts processed on the 2nd of every month.</p>
      </article>
      <article class="apl-tile reveal">
        <span class="apl-tile__tag">Bonus</span>
        <h3>Up to $2 welcome</h3>
        <p>Sign up free and claim your starter bonus instantly.</p>
      </article>
      <article class="apl-tile reveal">
        <span class="apl-tile__tag">Referrals</span>
        <h3>Invite &amp; earn</h3>
        <p>Share your code. Earn when friends join and post.</p>
      </article>
      <article class="apl-tile apl-tile--dark reveal">
        <span class="apl-tile__tag">Communities</span>
        <h3>Build your tribe</h3>
        <p>Public, private, or paid communities with subscriptions.</p>
      </article>
      <article class="apl-tile reveal">
        <span class="apl-tile__tag">Rolls</span>
        <h3>Short video</h3>
        <p>Swipeable vertical video with built-in discovery.</p>
      </article>
      <article class="apl-tile reveal">
        <span class="apl-tile__tag">Analytics</span>
        <h3>Know what hits</h3>
        <p>Per-post views, likes, and estimated earnings.</p>
      </article>
    </div>
  </section>

  {{-- HOW IT WORKS --}}
  <section class="apl-showcase apl-showcase--soft" id="how">
    <p class="apl-showcase__eyebrow reveal">How it works</p>
    <h2 class="reveal">Four steps.<br>That’s it.</h2>
    <div class="apl-steps">
      <div class="apl-step reveal">
        <div class="apl-step__num">1</div>
        <h3>Create account</h3>
        <p>Free signup in under a minute. Verify email. Claim your bonus.</p>
      </div>
      <div class="apl-step reveal">
        <div class="apl-step__num">2</div>
        <h3>Post content</h3>
        <p>Text, photos, video, quizzes — whatever you already make.</p>
      </div>
      <div class="apl-step reveal">
        <div class="apl-step__num">3</div>
        <h3>Earn engagement</h3>
        <p>Likes, comments, and views add to your balance automatically.</p>
      </div>
      <div class="apl-step reveal">
        <div class="apl-step__num">4</div>
        <h3>Get paid</h3>
        <p>Withdraw from $1 via PayPal, USDT, or your local bank.</p>
      </div>
    </div>
    <div class="apl-showcase__links reveal" style="margin-top:2rem">
      <a class="apl-link" href="{{ url('/how-it-works') }}">Full walkthrough <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
    </div>
  </section>

  {{-- TIERS --}}
  <section class="apl-showcase apl-showcase--light" id="tiers">
    <p class="apl-showcase__eyebrow reveal">Plans</p>
    <h2 class="reveal">Start free.<br>Upgrade when ready.</h2>
    <p class="apl-showcase__lead reveal">Basic is free forever. Unlock monetization with Creator or Influencer.</p>
    <div class="apl-tiers">
      <div class="apl-tier reveal">
        <div class="apl-tier__name">Basic</div>
        <div class="apl-tier__price">Free</div>
        <ul>
          <li>Unlimited posts &amp; quizzes</li>
          <li>Up to $2 signup bonus</li>
          <li>Full dashboard access</li>
        </ul>
        <a class="apl-btn apl-btn--outline" href="{{ url('/register') }}">Get started</a>
      </div>
      <div class="apl-tier apl-tier--featured reveal">
        <div class="apl-tier__name">Creator</div>
        <div class="apl-tier__price">$1<small> activation</small></div>
        <ul>
          <li>Everything in Basic</li>
          <li>Earn up to $2 per post</li>
          <li>Verified blue tick</li>
          <li>Post images &amp; video</li>
        </ul>
        <a class="apl-btn apl-btn--fill" href="{{ url('/register') }}">Become a Creator</a>
      </div>
      <div class="apl-tier reveal">
        <div class="apl-tier__name">Influencer</div>
        <div class="apl-tier__price">$5<small> activation</small></div>
        <ul>
          <li>Everything in Creator</li>
          <li>Earn up to $5 per post</li>
          <li>$1 referral commissions</li>
          <li>Top feed placement</li>
        </ul>
        <a class="apl-btn apl-btn--outline" href="{{ url('/register') }}">Go Influencer</a>
      </div>
    </div>
  </section>

  {{-- TOP EARNERS --}}
  <section class="apl-showcase apl-showcase--soft" id="earners">
    <p class="apl-showcase__eyebrow reveal">Leaderboard</p>
    <h2 class="reveal">Real creators.<br>Real payouts.</h2>
    <div class="apl-podium reveal">
      @if ($podium->isNotEmpty())
        @foreach ($order as $slot)
          @if ($podium->has($slot))
            @php
              $earner = $podium[$slot];
              $initials = strtoupper(substr($earner->username ?? $earner->name ?? 'U', 0, 2));
              $rank = $slot + 1;
            @endphp
            <div class="apl-podium__item {{ $rank === 1 ? 'apl-podium__item--first' : '' }}">
              <div class="avatar avatar--lg" style="background:{{ $gradients[$slot] ?? $gradients[0] }};margin-inline:auto">{{ $initials }}</div>
              <div class="podium__name" style="margin-top:12px;font-weight:600">{{ $earner->name ?? 'Creator' }}</div>
              <div class="podium__handle">{{ '@' . ($earner->username ?? 'creator') }}</div>
              <div class="apl-podium__earn">${{ number_format((float) $earner->total_paid, 0) }}</div>
            </div>
          @endif
        @endforeach
      @else
        <div class="apl-podium__item"><div class="avatar avatar--lg" style="background:linear-gradient(135deg,#7C6FF2,#F25C8A);margin-inline:auto">KB</div><div style="margin-top:12px;font-weight:600">Kwame B.</div><div class="podium__handle">@kwamecreates</div><div class="apl-podium__earn">$3,910</div></div>
        <div class="apl-podium__item apl-podium__item--first"><div class="avatar avatar--lg" style="background:linear-gradient(135deg,#F5B73C,#F25C8A);margin-inline:auto">AO</div><div style="margin-top:12px;font-weight:600">Amara O.</div><div class="podium__handle">@amaravibes</div><div class="apl-podium__earn">$5,240</div></div>
        <div class="apl-podium__item"><div class="avatar avatar--lg" style="background:linear-gradient(135deg,#12B886,#5A4FDC);margin-inline:auto">ZM</div><div style="margin-top:12px;font-weight:600">Zinhle M.</div><div class="podium__handle">@zinhletalks</div><div class="apl-podium__earn">$3,180</div></div>
      @endif
    </div>
    <div class="apl-showcase__links reveal" style="margin-top:2rem">
      <a class="apl-link" href="{{ url('/top-earners') }}">View full leaderboard <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
    </div>
  </section>

  {{-- FAQ --}}
  <section class="apl-showcase apl-showcase--light" id="faq">
    <p class="apl-showcase__eyebrow reveal">FAQ</p>
    <h2 class="reveal">Questions?<br>Answered.</h2>
    <div class="apl-faq faq reveal">
      <div class="faq__item"><button class="faq__q" type="button">How do I join?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">Tap Start free, enter your details, and you're in. Signup takes under a minute with a welcome bonus of up to $2.</div></div></div>
      <div class="faq__item"><button class="faq__q" type="button">How do I earn?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">Every like, comment, and view on your posts adds to your balance. You also earn signup bonuses and referral commissions.</div></div></div>
      <div class="faq__item"><button class="faq__q" type="button">How do withdrawals work?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">Once you reach $1, withdraw to PayPal, USDT, or your local bank. Payouts are processed on the 2nd of every month.</div></div></div>
      <div class="faq__item"><button class="faq__q" type="button">Is it really free?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">Yes — Basic is 100% free. To monetize posts, upgrade to Creator ($1) or Influencer ($5) in your local currency.</div></div></div>
    </div>
  </section>

  {{-- CLOSE --}}
  <section class="apl-close">
    <h2 class="reveal">Your content deserves a paycheck.</h2>
    <p class="reveal">Join {{ config('payhankey.stats.creators', '32K+') }} creators across Africa. Free to start.</p>
    <div class="apl-close__cta reveal">
      <a class="apl-btn apl-btn--fill" href="{{ url('/register') }}">Create free account</a>
      <a class="apl-btn apl-btn--outline" href="{{ url('/login') }}">Log in</a>
    </div>
  </section>

@endsection
