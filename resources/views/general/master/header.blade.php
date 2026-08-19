@php
    $navItems = [
        ['href' => url('/'), 'label' => 'Home', 'active' => request()->is('/')],
        ['href' => route('creators'), 'label' => 'Creators', 'active' => request()->routeIs('creators')],
        ['href' => route('communities'), 'label' => 'Communities', 'active' => request()->routeIs('communities')],
        ['href' => route('earn'), 'label' => 'Earn', 'active' => request()->routeIs('earn')],
        ['href' => route('ai'), 'label' => 'AI Tools', 'active' => request()->routeIs('ai')],
        ['href' => route('academy'), 'label' => 'Academy', 'active' => request()->routeIs('academy', 'academy.show')],
        ['href' => route('blog'), 'label' => 'Blog', 'active' => request()->is('blog') || request()->is('blog/*')],
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
          <a class="nav__action-link {{ request()->is('login') ? 'is-active' : '' }}" href="{{ url('/login') }}">Login</a>
          <a class="nav__cta" href="{{ url('/register') }}">Start Free</a>
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
        <a class="nav__sheet-link" href="{{ url('/login') }}">Login</a>
        <a class="nav__sheet-cta" href="{{ url('/register') }}">Start Free</a>
      @endauth
    </div>
  </div>

  <button class="nav__backdrop" type="button" aria-label="Close menu" tabindex="-1"></button>
</div>
