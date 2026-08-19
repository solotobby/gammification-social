@extends('general.master.apple')

@section('title', 'Contact · Payhankey')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Contact us',
    'eyebrow' => 'Contact us',
    'title' => 'We\'d love to hear from you',
    'lead' => 'Questions about earning, payouts or your account? Our team is here to help.',
])

<section class="apl-section apl-section--soft">
  <div class="apl-wrap apl-contact">
    <div class="reveal">
      <h2 style="font-size:1.6rem;font-weight:700;letter-spacing:-.02em;margin-bottom:20px">Get in touch</h2>
      <div class="apl-info">
        <div class="apl-info__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg></div>
        <div><b>Email support</b><a href="mailto:{{ config('payhankey.support_email') }}">{{ config('payhankey.support_email') }}</a></div>
      </div>
      <div class="apl-info">
        <div class="apl-info__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div><b>Response time</b><span>We reply within 24 hours, Monday to Saturday.</span></div>
      </div>
      <div class="apl-info">
        <div class="apl-info__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
        <div><b>Need a quick answer?</b><span>Check our <a href="{{ url('/how-it-works') }}" style="color:var(--violet);font-weight:600">How it works</a> guide or the <a href="{{ route('help') }}" style="color:var(--violet);font-weight:600">Help Center</a>.</span></div>
      </div>
      <div style="margin-top:24px">
        <b style="font-family:var(--font-display);display:block;margin-bottom:12px">Follow us</b>
        <div class="footer__social" style="margin-top:0">
          @foreach (config('payhankey.social', []) as $platform => $url)
            @if ($url)
              <a href="{{ $url }}" style="background:var(--lilac);color:var(--violet)" target="_blank" rel="noopener" aria-label="{{ ucfirst($platform) }}">↗</a>
            @endif
          @endforeach
        </div>
      </div>
    </div>

    <div class="apl-form-card reveal">
      @if (session('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
      @endif
      <form method="POST" action="{{ route('contact.submit') }}">
        @csrf
        <div class="field">
          <label for="c-name">Full name</label>
          <input id="c-name" type="text" name="name" value="{{ old('name') }}" placeholder="Your name" required>
          @error('name')<div class="field__hint" style="color:var(--rose)">{{ $message }}</div>@enderror
        </div>
        <div class="field">
          <label for="c-email">Email</label>
          <input id="c-email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
          @error('email')<div class="field__hint" style="color:var(--rose)">{{ $message }}</div>@enderror
        </div>
        <div class="field">
          <label for="c-subject">Subject</label>
          <input id="c-subject" type="text" name="subject" value="{{ old('subject') }}" placeholder="What's this about?" required>
          @error('subject')<div class="field__hint" style="color:var(--rose)">{{ $message }}</div>@enderror
        </div>
        <div class="field">
          <label for="c-msg">Message</label>
          <textarea id="c-msg" name="message" placeholder="Tell us how we can help..." required>{{ old('message') }}</textarea>
          @error('message')<div class="field__hint" style="color:var(--rose)">{{ $message }}</div>@enderror
        </div>
        <button class="apl-btn apl-btn--fill" style="width:100%" type="submit">Send message</button>
      </form>
    </div>
  </div>
</section>

@include('general.partials.apl-close-cta')
@endsection
