@extends('general.master.body')

@section('content')
<section class="pagehero">
  <div class="wrap pagehero__inner">
    <div class="crumbs"><a href="{{ url('/') }}">Home</a> / <span>Contact us</span></div>
    <span class="eyebrow">Contact us</span>
    <h1>We'd love to hear from you</h1>
    <p>Questions about earning, payouts or your account? Our team is here to help you make the most of Payhankey.</p>
  </div>
</section>
<section class="section">
  <div class="wrap contact-grid">
    <div class="reveal">
      <h2 style="font-size:1.7rem;margin-bottom:18px">Get in touch</h2>
      <div class="info-card"><div class="info-card__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg></div><div><b>Email support</b><a href="mailto:support@payhankey.com">support@payhankey.com</a></div></div>
      <div class="info-card"><div class="info-card__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div><b>Response time</b><span>We reply within 24 hours, Monday to Saturday.</span></div></div>
      <div class="info-card"><div class="info-card__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div><div><b>Need a quick answer?</b><span>Most questions are covered in our <a href="how-it-works.html" style="color:var(--violet);font-weight:600">How it works</a> guide and homepage FAQ.</span></div></div>
      {{-- <div style="margin-top:22px">
        <b style="font-family:var(--font-display);display:block;margin-bottom:12px">Follow us</b>
        <div class="footer__social" style="margin-top:0">
          <a href="https://www.tiktok.com/@payhankeyofficial" style="background:var(--lilac);color:var(--violet)" aria-label="TikTok"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8.2a6.5 6.5 0 0 0 4 1.4V6.7a3.6 3.6 0 0 1-2.6-1.1A3.6 3.6 0 0 1 16.4 3H13.4v12.2a2.3 2.3 0 1 1-2.3-2.3c.24 0 .47.04.7.1v-3.1a5.4 5.4 0 1 0 4.2 5.3z"/></svg></a>
          <a href="https://www.instagram.com/payhankey_official" style="background:var(--lilac);color:var(--violet)" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a>
          <a href="https://www.facebook.com/profile.php?id=61561454191408" style="background:var(--lilac);color:var(--violet)" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V6h-3a4 4 0 0 0-4 4v2H7v3h3v6h3v-6h3l1-3h-4v-2a1 1 0 0 1 1-1z"/></svg></a>
        </div>
      </div> --}}
    </div>

    <div class="form-card reveal">
      <form data-demo-form>
        <div class="field"><label for="c-name">Full name</label><input id="c-name" type="text" placeholder="Your name" required></div>
        <div class="field"><label for="c-email">Email</label><input id="c-email" type="email" placeholder="you@example.com" required></div>
        <div class="field"><label for="c-subject">Subject</label><input id="c-subject" type="text" placeholder="What's this about?" required></div>
        <div class="field"><label for="c-msg">Message</label><textarea id="c-msg" placeholder="Tell us how we can help..." required></textarea></div>
        <button class="btn btn--primary btn--block btn--lg" type="submit">Send message <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
        {{-- <p data-form-note hidden style="margin-top:16px;color:var(--mint);font-weight:600;display:flex;align-items:center;gap:8px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Thanks! Your message has been sent — we'll reply within 24 hours.</p> --}}
      </form>
    </div>
  </div>
</section>
@endsection