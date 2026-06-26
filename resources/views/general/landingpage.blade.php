@extends('general.master.body')
{{-- @assets('', 'Landing Page') --}}

@section('content')

<!-- ===== HERO ===== -->
<section class="hero">
  <div class="wrap hero__inner">
    <div>
      <span class="hero__pill">Free to join <b>+$2 bonus</b></span>
      <h1>Get paid for the posts you <span class="grad">already make</span>.</h1>
      <p class="hero__sub">Payhankey is the social platform that turns your likes, comments and views into real money — no followers, subscribers or watch hours needed. Earn from your very first post.</p>
      <div class="hero__cta">
        <a class="btn btn--white btn--lg" href="{{ url('/register') }}">Create free account <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
        <a class="btn btn--light btn--lg" href="{{ url('/how-it-works') }}">See how it works</a>
      </div>
      <div class="hero__trust">
        <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> 100% free signup</div>
        <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> $1 minimum payout</div>
        <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> PayPal · USDT · Local bank</div>
      </div>
    </div>

    <div class="device float float--slow">
      <div class="device__tag device__tag--like float"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/></svg> +$0.12 / like</div>
      <div class="device__tag device__tag--view float float--slow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> +$0.05 / view</div>
      <div class="device__bal">
        <small>Available balance</small>
        <div class="device__amount" data-balance>$1,284<span class="cents">.50</span></div>
        <span class="device__delta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg> +$38.20 today</span>
        <div class="device__row">
          <div class="device__chip"><b>2.4k</b><span>Likes</span></div>
          <div class="device__chip"><b>880</b><span>Comments</span></div>
          <div class="device__chip"><b>61k</b><span>Views</span></div>
        </div>
        <div class="feed">
          <div class="feed__head"><span class="feed__dot"></span> Live payouts</div>
          <div class="feed__list" data-feed></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== PAYOUT METHODS STRIP ===== -->
{{-- <div class="payband">
  <div class="wrap payband__inner">
    <span>Withdraw your earnings to</span>
    <b>PayPal</b><b>USDT</b><b>Local Bank Transfer</b>
    <span>· Minimum payout <b style="color:var(--violet)">$1</b> · Paid on the 2nd of every month</span>
  </div>
</div> --}}

<!-- ===== HOW IT WORKS ===== -->
<section class="section" id="how">
  <div class="wrap">
    <div class="section-head center reveal">
      <span class="eyebrow eyebrow--center">How it works</span>
      <h2>Start earning in four simple steps</h2>
      <p class="lead">No complicated setup, no waiting to qualify. Sign up and your earnings begin on your first post.</p>
    </div>
    <div class="steps">
      <div class="step reveal"><div class="step__n"></div><div class="step__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h3>Create your account</h3><p>Sign up free in under a minute and claim your welcome bonus of up to $2 instantly.</p></div>
      <div class="step reveal"><div class="step__n"></div><div class="step__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div><h3>Post your content</h3><p>Share posts, facts, quizzes, teasers and videos. Everything you create can earn.</p></div>
      <div class="step reveal"><div class="step__n"></div><div class="step__icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/></svg></div><h3>Earn from engagement</h3><p>Every like, comment and view on your content adds real money to your balance.</p></div>
      <div class="step reveal"><div class="step__n"></div><div class="step__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg></div><h3>Withdraw your money</h3><p>Cash out from just $1 to PayPal, USDT or your local bank on the 2nd of each month.</p></div>
    </div>
  </div>
</section>

<!-- ===== STATS ===== -->
<section class="section--tight">
  <div class="wrap">
    <div class="statband reveal">
      <div class="statband__grid">
        <div class="stat"><div class="stat__num"><span data-count="30" data-suffix="K+">0</span></div><div class="stat__label">Creators earning</div></div>
        <div class="stat"><div class="stat__num accent"><span data-count="30697" data-prefix="$" data-suffix="+">0</span></div><div class="stat__label">Paid out to members</div></div>
        <div class="stat"><div class="stat__num"><span data-count="880" data-decimals="1" data-suffix="K+">869</span></div><div class="stat__label">Posts monetized</div></div>
        <div class="stat"><div class="stat__num"><span data-count="40" data-suffix="+">0</span></div><div class="stat__label">Countries supported</div></div>
      </div>
    </div>
  </div>
</section>

<!-- ===== BENEFITS (split) ===== -->
<section class="section" id="benefits">
  <div class="wrap split">
    <div class="reveal">
      <span class="eyebrow">Why creators choose us</span>
      <h2>Earning online, made genuinely simple.</h2>
      <p class="lead" style="margin:16px 0 28px;">Most platforms make you chase thousands of followers before you see a cent. Payhankey removes every barrier so anyone can start earning today.</p>
      <div class="benefit"><div class="benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div><div><h4>Earn from day one</h4><p>No follower counts, subscriber goals or watch-hour thresholds. Your first post can earn.</p></div></div>
      <div class="benefit"><div class="benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><div><h4>Secure &amp; transparent</h4><p>Your account and earnings are protected, and every cent you make is tracked in real time.</p></div></div>
      <div class="benefit"><div class="benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg></div><div><h4>Fast, flexible payouts</h4><p>Withdraw from just $1 to PayPal, USDT or your local bank — in your own currency.</p></div></div>
      <div class="benefit"><div class="benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg></div><div><h4>Bonuses &amp; referrals</h4><p>Claim up to $2 just for signing up, then earn affiliate commissions for every friend you invite.</p></div></div>
    </div>
    <div class="media-card reveal">
      <span class="eyebrow" style="color:#C9C2FF">Earning made easy</span>
      <h2 style="color:#fff;margin:14px 0 16px;">We pay you for your likes, comments &amp; views.</h2>
      <p style="color:rgba(255,255,255,.82)">Turn everyday engagement into income. The more your content connects, the more you earn — automatically.</p>
      <div class="device__row" style="margin-top:26px">
        <div class="device__chip" style="background:rgba(255,255,255,.12);color:#fff"><b style="color:#fff">$0.12</b><span style="color:rgba(255,255,255,.7)">per like</span></div>
        <div class="device__chip" style="background:rgba(255,255,255,.12);color:#fff"><b style="color:#fff">$0.08</b><span style="color:rgba(255,255,255,.7)">per comment</span></div>
        <div class="device__chip" style="background:rgba(255,255,255,.12);color:#fff"><b style="color:#fff">$0.05</b><span style="color:rgba(255,255,255,.7)">per view</span></div>
      </div>
      <a class="btn btn--white" style="margin-top:26px" href="{{ url('/register') }}">Get started for free <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
      <p style="color:rgba(255,255,255,.55);font-size:.82rem;margin-top:10px">Illustrative rates. Actual earnings vary by content, engagement and account level.</p>
    </div>
  </div>
</section>

<!-- ===== FEATURES ===== -->
<section class="section--tight" id="features">
  <div class="wrap">
    <div class="section-head center reveal">
      <span class="eyebrow eyebrow--center">Platform features</span>
      <h2>Everything you need to grow your income</h2>
      <p class="lead">A complete creator toolkit, built to make earning effortless and your progress easy to see.</p>
    </div>
    <div class="grid-3">
      <div class="card reveal"><div class="card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><h3>Earnings dashboard</h3><p>Watch your balance grow in real time, with a clear breakdown of where every cent comes from.</p></div>
      <div class="card reveal"><div class="card__icon card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg></div><h3>Secure payments</h3><p>Withdraw safely to PayPal, USDT or your local bank, with a low $1 minimum and monthly payouts.</p></div>
      <div class="card reveal"><div class="card__icon card__icon--gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg></div><h3>Referral program</h3><p>Share your code and earn affiliate commissions every time a friend joins and starts posting.</p></div>
      <div class="card reveal"><div class="card__icon card__icon--rose"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/></svg></div><h3>Reward system</h3><p>Signup bonuses, engagement rewards and promotional payouts keep your earnings stacking up.</p></div>
      <div class="card reveal"><div class="card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><h3>Activity history</h3><p>A full, transparent log of your posts, engagement and payouts — nothing hidden.</p></div>
      <div class="card reveal"><div class="card__icon card__icon--mint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div><h3>Promotional payouts</h3><p>Make viral videos about Payhankey and earn up to $10 per 1,000 views on top of your post earnings.</p></div>
    </div>
  </div>
</section>

<!-- ===== ACCOUNT TIERS ===== -->
<section class="section" id="tiers">
  <div class="wrap">
    <div class="section-head center reveal">
      <span class="eyebrow eyebrow--center">Account levels</span>
      <h2>Earn the way you want</h2>
      <p class="lead">Start free as a Basic user, then upgrade to earn more on every post you make.</p>
    </div>
    <div class="tiers">
      <div class="tier reveal">
        <div class="tier__name">Basic</div>
        <div class="tier__price">Free</div>
        <p class="tier__desc">The perfect place to start. Post freely and learn the platform.</p>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Unlimited posts, quizzes &amp; teasers</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Up to $2 signup bonus</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> $0 Referral commissions</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Full dashboard access</li>
        </ul>
        <a class="btn btn--ghost btn--block" href="{{ url('/register') }}">Start free</a>
      </div>
      <div class="tier tier--feature reveal">
        <span class="tier__flag">Most popular</span>
        <div class="tier__name">Creator <span class="badge-tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>
        <div class="tier__price">$1<span> / activation</span></div>
        <p class="tier__desc">Unlock post monetization and earn up to $2 on every post.</p>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Everything in Basic</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Earn up to $2 per post</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Blue tick verified badge</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Post images</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Priority in discovery</li>
        </ul>
        <a class="btn btn--primary btn--block" href="{{ url('/register') }}">Become a Creator</a>
      </div>
      <div class="tier reveal">
        <div class="tier__name">Influencer <span class="badge-ring"><span class="badge-tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></span></div>
        <div class="tier__price">$5<span> / activation</span></div>
        <p class="tier__desc">Maximum earnings and reach for serious creators.</p>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Everything in Creator</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Earn up to $5 per post</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> $1 Referral commissions</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Blue tick + influencer ring</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Longer videos &amp; more media</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Top placement in feeds</li>
        </ul>
        <a class="btn btn--ghost btn--block" href="{{ url('/register') }}">Become an Influencer</a>
      </div>
    </div>
    <p class="center" style="color:var(--ink-faint);font-size:.88rem;margin-top:22px">Activation fees can be paid in your local currency and keep your monetization status active.</p>
  </div>
</section>

<!-- ===== TOP EARNERS PREVIEW ===== -->
<section class="section--tight" id="earners">
  <div class="wrap">
    <div class="section-head center reveal">
      <span class="eyebrow eyebrow--center">Top earners</span>
      <h2>Real creators, real payouts</h2>
      <p class="lead">See who's leading the board this month — your name could be next.</p>
    </div>
    <div class="podium reveal">
      <div class="podium__card">
        <div class="podium__rank podium__rank--2">2</div>
        <div class="avatar avatar--lg" style="background:linear-gradient(135deg,#7C6FF2,#F25C8A)">KB</div>
        <div class="podium__name">Kwame B.</div><div class="podium__handle">@kwamecreates</div>
        <div class="podium__earn">$3,910</div>
      </div>
      <div class="podium__card podium__card--1">
        <div class="podium__rank podium__rank--1">1</div>
        <div class="avatar avatar--lg" style="background:linear-gradient(135deg,#F5B73C,#F25C8A)">AO</div>
        <div class="podium__name">Amara O.</div><div class="podium__handle">@amaravibes</div>
        <div class="podium__earn">$5,240</div>
      </div>
      <div class="podium__card">
        <div class="podium__rank podium__rank--3">3</div>
        <div class="avatar avatar--lg" style="background:linear-gradient(135deg,#12B886,#5A4FDC)">ZM</div>
        <div class="podium__name">Zinhle M.</div><div class="podium__handle">@zinhletalks</div>
        <div class="podium__earn">$3,180</div>
      </div>
    </div>
    <div class="center" style="margin-top:32px">
      <a class="btn btn--ghost" href="{{ url('/top-earners') }}">View all top earners <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
    </div>
  </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<section class="section">
  <div class="wrap">
    <div class="section-head center reveal">
      <span class="eyebrow eyebrow--center">Loved by creators</span>
      <h2>People are getting paid</h2>
    </div>
    <div class="grid-3">
      <div class="quote reveal"><div class="quote__stars">★★★★★</div><p>"I made my first $1 the same week I joined — without a single follower. Payhankey actually pays for the posts I was already making for fun."</p><div class="quote__who"><div class="avatar" style="width:44px;height:44px;background:var(--grad-brand)">FD</div><div><b>Fatima D.</b><span>Creator · Lagos</span></div></div></div>
      <div class="quote reveal"><div class="quote__stars">★★★★★</div><p>"Withdrawals to my local bank came through exactly on the 2nd. No stories, no excuses. That's why I trust this platform."</p><div class="quote__who"><div class="avatar" style="width:44px;height:44px;background:linear-gradient(135deg,#12B886,#5A4FDC)">CN</div><div><b>Chidi N.</b><span>Influencer · Accra</span></div></div></div>
      <div class="quote reveal"><div class="quote__stars">★★★★★</div><p>"The referral commissions alone cover my data. I invited my friends and now we all earn together every month."</p><div class="quote__who"><div class="avatar" style="width:44px;height:44px;background:linear-gradient(135deg,#F5B73C,#F25C8A)">LK</div><div><b>Lerato K.</b><span>Creator · Johannesburg</span></div></div></div>
    </div>
  </div>
</section>

<!-- ===== FAQ ===== -->
<section class="section--tight" id="faq">
  <div class="wrap">
    <div class="section-head center reveal">
      <span class="eyebrow eyebrow--center">Questions</span>
      <h2>Everything you need to know</h2>
    </div>
    <div class="faq">
      <div class="faq__item"><button class="faq__q">How do I join Payhankey?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">Tap any &ldquo;Create free account&rdquo; button, enter your details, and you're in. Signup takes under a minute, costs nothing, and you'll receive a welcome bonus of up to $2 right away.</div></div></div>
      <div class="faq__item"><button class="faq__q">How do I earn money?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">You earn from engagement on your content. Every like, comment and view on your posts, facts, quizzes, teasers and videos adds to your balance. You also earn signup and referral bonuses, plus promotional commissions for viral videos made about Payhankey.</div></div></div>
      <div class="faq__item"><button class="faq__q">How do withdrawals work?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">Once your balance reaches the $1 minimum, you can withdraw to PayPal, USDT or your local bank account. Payouts are processed on the 2nd of every month, in your local currency where available.</div></div></div>
      <div class="faq__item"><button class="faq__q">Is it really free?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">Yes — creating an account is 100% free, and Basic users can post without paying anything. To monetize your posts you can upgrade to Creator ($1) or Influencer ($5), which can be paid in your local currency.</div></div></div>
      <div class="faq__item"><button class="faq__q">How long does payment take?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">Withdrawal requests are settled on the 2nd of each month. Depending on your chosen method and bank, funds typically arrive within a few business days of processing.</div></div></div>
      <div class="faq__item"><button class="faq__q">Is my information secure?<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><div class="faq__a"><div class="faq__a-inner">Absolutely. Your account is protected and your earnings are tracked transparently in your dashboard. We never share your personal data, and you stay in control of your payout details.</div></div></div>
    </div>
  </div>
</section>

<!-- ===== FINAL CTA ===== -->
<section class="section">
  <div class="wrap">
    <div class="cta-band reveal">
      <span class="eyebrow eyebrow--center" style="color:#C9C2FF">Your earnings start here</span>
      <h2>Start building your earnings today</h2>
      <p>Join thousands of creators getting paid for the content they already love to make. Free to start, with a welcome bonus waiting.</p>
      <div class="hero__cta">
        <a class="btn btn--white btn--lg" href="{{ url('/register') }}">Register now <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
        <a class="btn btn--light btn--lg" href="{{ url('/login') }}">Log in</a>
      </div>
      <p class="cta-band__note">Free signup · No card required · Withdraw from just $1</p>
    </div>
  </div>
</section>


@endsection


