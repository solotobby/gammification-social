@extends('general.master.body')

@section('content')
 <section class="pagehero">
  <div class="wrap pagehero__inner">
    <div class="crumbs"><a href="{{ url('/') }}">Home</a> / <span>How it works</span></div>
    <span class="eyebrow">How it works</span>
    <h1>From signup to payout in plain English</h1>
    <p>Payhankey was built so anyone can earn — no jargon, no gatekeeping. Here's exactly how money lands in your account.</p>
  </div>
</section>
<section class="section">
  <div class="wrap">
    <div class="steps">
      <div class="step reveal"><div class="step__n"></div><div class="step__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h3>Create your account</h3><p>Sign up free in under a minute. Confirm your email, set your payout method, and claim a welcome bonus of up to $2.</p></div>
      <div class="step reveal"><div class="step__n"></div><div class="step__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div><h3>Post your content</h3><p>Share posts, facts, quizzes, teasers and videos. There's no follower requirement and no waiting period.</p></div>
      <div class="step reveal"><div class="step__n"></div><div class="step__icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/></svg></div><h3>Earn from engagement</h3><p>Likes, comments and views on your content all convert into earnings, tracked live in your dashboard.</p></div>
      <div class="step reveal"><div class="step__n"></div><div class="step__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg></div><h3>Withdraw your money</h3><p>Once you reach $1, request a payout to PayPal, USDT or your local bank — paid on the 2nd of every month.</p></div>
    </div>
  </div>
</section>

<section class="section--tight">
  <div class="wrap split">
    <div class="reveal">
      <span class="eyebrow">Where earnings come from</span>
      <h2>Five ways to grow your balance</h2>
      <p class="lead" style="margin:16px 0 26px">Your income isn't tied to one thing. Payhankey stacks multiple earning streams so your balance keeps climbing.</p>
      <div class="benefit"><div class="benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg></div><div><h4>Signup bonus</h4><p>Get up to $1 the moment you join — before you've even posted.</p></div></div>
      <div class="benefit"><div class="benefit__ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/></svg></div><div><h4>Engagement earnings</h4><p>Every like, comment and view on your posts adds to your balance automatically.</p></div></div>
      <div class="benefit"><div class="benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div><h4>Referral commissions</h4><p>Invite friends with your code and earn affiliate commissions when they join and post.</p></div></div>
      <div class="benefit"><div class="benefit__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div><div><h4>Promotional payouts</h4><p>Make a viral video about Payhankey, tag us, and earn up to $10 per 1,000 views.</p></div></div>
    </div>
    <div class="media-card reveal">
      <span class="eyebrow" style="color:#C9C2FF">Account levels</span>
      <h2 style="color:#fff;margin:14px 0 14px">Upgrade to earn more per post</h2>
      <p style="color:rgba(255,255,255,.82);margin-bottom:22px">Basic accounts can post and earn bonuses. To monetize your posts, activate a Creator or Influencer status.</p>
      <div class="tier" style="background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.18);color:#fff;margin-bottom:14px;padding:20px 24px">
        <div class="tier__name" style="color:#fff">Creator <span class="badge-tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span> <span style="margin-left:auto;color:#7EE8C4">$1</span></div>
        <p style="color:rgba(255,255,255,.7);margin-top:6px">Blue tick badge · earn up to $2 per post · post images &amp; videos.</p>
      </div>
      <div class="tier" style="background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.18);color:#fff;padding:20px 24px">
        <div class="tier__name" style="color:#fff">Influencer <span class="badge-ring"><span class="badge-tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></span> <span style="margin-left:auto;color:#7EE8C4">$5</span></div>
        <p style="color:rgba(255,255,255,.7);margin-top:6px">Blue tick + ring · earn up to $5 per post · longer videos &amp; top placement.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="cta-band reveal">
      <h2>Ready to earn from your first post?</h2>
      <p>It's free to start and takes less than a minute. Your welcome bonus is waiting.</p>
      <div class="hero__cta"><a class="btn btn--white btn--lg" href="{{ url('/register') }}">Create free account <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a></div>
    </div>
  </div>
</section>
@endsection