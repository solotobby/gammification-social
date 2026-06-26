<footer class="footer">
  <div class="wrap">
    <div class="footer__grid">
      <div>
        <div class="footer__logo">
          <span><img src="{{ asset('rsc/logo_sm.png') }}" alt="Payhankey logo" width="32" height="32"></span>
          <span>Payhankey</span>
        </div>
        <p>The social platform that pays you for the posts, videos, quizzes and teasers you already make — no followers or watch hours required. A product of Freebyz Technologies Ltd.</p>
        <div class="footer__social">
          <a href="https://www.tiktok.com/@payhankeyofficial" aria-label="TikTok" target="_blank"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8.2a6.5 6.5 0 0 0 4 1.4V6.7a3.6 3.6 0 0 1-2.6-1.1A3.6 3.6 0 0 1 16.4 3H13.4v12.2a2.3 2.3 0 1 1-2.3-2.3c.24 0 .47.04.7.1v-3.1a5.4 5.4 0 1 0 4.2 5.3z"/></svg></a>
          <a href="https://www.instagram.com/payhankey_official" aria-label="Instagram" target="_blank"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a>
          <a href="https://www.facebook.com/profile.php?id=61561454191408" aria-label="Facebook" target="_blank"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V6h-3a4 4 0 0 0-4 4v2H7v3h3v6h3v-6h3l1-3h-4v-2a1 1 0 0 1 1-1z"/></svg></a>
          <a href="https://x.com/Payhankey" aria-label="X" target="_blank"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.2 3h2.8l-6.1 7 7.2 9.5h-5.6l-4.4-5.8-5 5.8H3.3l6.5-7.5L2.9 3h5.7l4 5.3z"/></svg></a>
        </div>
      </div>
      <div class="footer__col">
        <h5>Platform</h5>
        <a href="{{url('/how-it-works')}}">How it works</a>
        <a href="{{url('/top-earners')}}">Top earners</a>
        <a href="{{url('/register')}}">Create account</a>
        <a href="{{url('/login')}}">Log in</a>
      </div>
      <div class="footer__col">
        <h5>Company</h5>
        <a href="{{url('/about')}}">About us</a>
        <a href="{{url('/blog')}}">Blog</a>
        <a href="{{url('/contact')}}">Contact</a>
        {{-- <a href="#">Careers</a> --}}
      </div>
      <div class="footer__col">
        <h5>Legal</h5>
        <a href="{{url('/terms')}}">Terms &amp; Conditions</a>
        <a href="{{url('/privacy/policy')}}">Privacy Policy</a>
        <a href="{{url('/contact')}}">Support</a>
      </div>
    </div>
    <div class="footer__bottom">
      <span>© {{ date('Y') }} Payhankey Ltd · All rights reserved.</span>
      <span>Payouts via PayPal · USDT · Local bank — processed on the 2nd of every month.</span>
    </div>
  </div>
</footer>