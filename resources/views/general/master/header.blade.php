@php
    $navItems = [
        ['href' => url('/'), 'label' => 'Home', 'active' => request()->is('/')],
        ['href' => route('features'), 'label' => 'Features', 'active' => request()->is('features')],
        ['href' => url('/how-it-works'), 'label' => 'How it works', 'active' => request()->is('how-it-works')],
        ['href' => url('/top-earners'), 'label' => 'Top earners', 'active' => request()->is('top-earners') || request()->is('top/earners')],
        ['href' => route('blog'), 'label' => 'Blog', 'active' => request()->routeIs('blog') || request()->is('blog*')],
        ['href' => url('/about'), 'label' => 'About', 'active' => request()->is('about')],
        ['href' => url('/contact'), 'label' => 'Contact', 'active' => request()->is('contact')],
    ];
@endphp
<div class="site-nav" data-nav>
  <header class="nav nav--apple">
    <div class="nav__bar wrap">
      <a class="nav__logo" href="{{ url('/') }}" aria-label="Payhankey home">
        <img src="{{ asset('logo.png') }}" alt="Payhankey" height="28" />
      </a>

      <button class="nav__toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="nav-sheet">
        <span class="nav__toggle-icon" aria-hidden="true"></span>
      </button>

      <nav class="nav__links" id="site-nav" aria-label="Main navigation">
        @foreach ($navItems as $item)
          <a href="{{ $item['href'] }}" @class(['is-active' => $item['active']])>{{ $item['label'] }}</a>
        @endforeach
      </nav>

      <div class="nav__actions">
        @auth
          <a class="nav__action-link" href="{{ route('home') }}">Dashboard</a>
        @else
          <a class="nav__action-link {{ request()->is('login') ? 'is-active' : '' }}" href="{{ url('/login') }}">Log in</a>
          <a class="nav__cta" href="{{ url('/register') }}">Sign up</a>
        @endauth
      </div>
    </div>
  </header>

  <div class="nav__sheet" id="nav-sheet" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Menu">
    <nav class="nav__sheet-links" aria-label="Mobile navigation">
      @foreach ($navItems as $item)
        <a href="{{ $item['href'] }}" @class(['is-active' => $item['active']])>{{ $item['label'] }}</a>
      @endforeach
    </nav>
    <div class="nav__sheet-actions">
      @auth
        <a class="nav__sheet-cta" href="{{ route('home') }}">Dashboard</a>
      @else
        <a class="nav__sheet-link" href="{{ url('/login') }}">Log in</a>
        <a class="nav__sheet-cta" href="{{ url('/register') }}">Sign up free</a>
      @endauth
    </div>
  </div>

  <button class="nav__backdrop" type="button" aria-label="Close menu" tabindex="-1"></button>
</div>
