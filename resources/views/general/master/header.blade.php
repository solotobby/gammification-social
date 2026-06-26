<header class="nav">
  <div class="wrap nav__inner">
    <a class="nav__logo" href="{{url('/')}}">
        <img src="{{ asset('logo.png') }}" alt="" class="logo-dark" height="34" />
        {{-- <span class="nav__mark">P</span>Pay<span style="color:var(--violet)">hankey</span> --}}
    </a>
    <nav class="nav__links">
      <a href="{{url('/')}}" class="is-active">Home</a>
      <a href="{{url('/how-it-works')}}" >How It Works</a>
      <a href="{{url('/top-earners')}}" >Top Earners</a>
      <a href="{{url('/blog')}}" >Blog</a>
      <a href="{{url('/about')}}" >About</a>
      <a href="{{url('/contact')}}" >Contact</a>
    </nav>
    <div class="nav__actions">
      <a class="nav__login" href="{{url('/login')}}">Log in</a>
      <a class="btn btn-primary" href="{{url('/register')}}">Join Free</a>
    </div>
    <button class="nav__toggle" aria-label="Open menu"><span></span><span></span><span></span></button>
  </div>
</header>