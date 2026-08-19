@extends('general.master.apple')

@section('title', 'Communities · Payhankey | Find & Join Creator Communities')
@section('meta_description', 'Discover Payhankey creator communities — public, paid and approval-based groups where African creators build audiences, memberships and recurring income.')
@section('body_class', 'page-landing-apple page-communities')

@section('apple_content')

<section class="apl-comm-hero">
  <div class="apl-wrap apl-comm-hero__inner">
    <p class="apl-comm-hero__eyebrow reveal">Communities</p>
    <h1 class="reveal">Find a community to join</h1>
    <p class="apl-comm-hero__lead reveal">Browse public and paid creator communities on Payhankey.</p>
  </div>
</section>

<section class="apl-comm-shell">
  <div class="apl-wrap">
    <form method="get" action="{{ route('communities') }}" class="apl-comm-toolbar reveal" role="search">
      @if (request('category'))
        <input type="hidden" name="category" value="{{ request('category') }}">
      @endif
      <div class="apl-chips-scroll" aria-label="Filter by category">
        <div class="apl-chips-row">
          <a href="{{ route('communities', request()->only('q')) }}"
            class="apl-chip-filter {{ ! request('category') ? 'is-active' : '' }}">All</a>
          @foreach ($categories as $category)
            <a href="{{ route('communities', array_filter(['category' => $category->id, 'q' => request('q')])) }}"
              class="apl-chip-filter {{ (string) request('category') === (string) $category->id ? 'is-active' : '' }}">
              {{ $category->name }}
            </a>
          @endforeach
        </div>
      </div>
      <div class="apl-search apl-comm-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search communities" aria-label="Search communities" enterkeyhint="search" autocomplete="off">
        @if (request('q'))
          <a class="apl-search__clear" href="{{ route('communities', request()->only('category')) }}" aria-label="Clear search">×</a>
        @endif
      </div>
    </form>

    @if ($communities->count())
      <div class="apl-comm-status reveal" id="commStatus">
        {{ number_format($communities->total()) }} {{ \Illuminate\Support\Str::plural('community', $communities->total()) }}
      </div>

      <div class="apl-comm-grid" id="commGrid">
        @foreach ($communities as $community)
          @include('general.partials.community-card', ['community' => $community])
        @endforeach
      </div>

      <div class="apl-comm-more" id="commMore">
        @if ($communities->hasMorePages())
          <button
            type="button"
            class="apl-comm-more__btn"
            id="commLoadMore"
            data-next-page="{{ $communities->currentPage() + 1 }}"
            data-url="{{ route('communities', request()->only(['category', 'q'])) }}"
          >
            <span class="apl-comm-more__label">Load more</span>
            <span class="apl-comm-more__spinner" aria-hidden="true"></span>
          </button>
        @endif
      </div>
    @else
      <div class="apl-comm-empty reveal">
        <h3>No communities found</h3>
        <p>Try another category or keyword.</p>
        <a href="{{ route('communities') }}">Clear filters</a>
      </div>
    @endif
  </div>
</section>

@include('general.partials.apl-close-cta', [
  'eyebrow' => 'Create yours',
  'title' => 'Start your own community',
  'lead' => 'Launch a public or paid community and turn your audience into members.',
  'primaryLabel' => 'Start Free',
  'secondaryLabel' => 'Login',
])

@endsection

@push('scripts')
<script>
(function () {
  var btn = document.getElementById('commLoadMore');
  var grid = document.getElementById('commGrid');
  var status = document.getElementById('commStatus');
  if (!btn || !grid) return;

  var loading = false;
  var total = {{ $communities->total() }};

  function setLoading(on) {
    loading = on;
    btn.classList.toggle('is-loading', on);
    btn.disabled = on;
  }

  btn.addEventListener('click', function () {
    if (loading) return;
    var page = parseInt(btn.getAttribute('data-next-page'), 10);
    if (!page) return;

    var base = btn.getAttribute('data-url') || window.location.pathname;
    var url = new URL(base, window.location.origin);
    url.searchParams.set('page', String(page));
    url.searchParams.set('partial', '1');

    setLoading(true);

    fetch(url.toString(), {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
      .then(function (res) {
        if (!res.ok) throw new Error('Failed to load');
        return res.json();
      })
      .then(function (data) {
        if (data.html) {
          var wrap = document.createElement('div');
          wrap.innerHTML = data.html;
          while (wrap.firstChild) grid.appendChild(wrap.firstChild);
        }

        total = data.total || total;
        if (status) {
          status.textContent = total.toLocaleString() + (total === 1 ? ' community' : ' communities');
        }

        if (data.has_more && data.next_page) {
          btn.setAttribute('data-next-page', String(data.next_page));
          setLoading(false);
        } else {
          btn.remove();
        }
      })
      .catch(function () {
        setLoading(false);
        btn.querySelector('.apl-comm-more__label').textContent = 'Try again';
      });
  });
})();
</script>
@endpush
