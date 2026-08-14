@extends('general.master.apple')

@php
    $published = $blog->published_at ?? $blog->created_at;
    $readMins = max(1, (int) ceil(str_word_count(strip_tags($blog->content ?? '')) / 200));
    $excerptText = trim(strip_tags($blog->excerpt ?? ''));
@endphp

@section('title', $blog->title . ' · Payhankey Blog')
@section('meta_description', $excerptText !== '' ? Str::limit($excerptText, 155) : 'Read ' . $blog->title . ' on the Payhankey blog — creator tips, monetization guides and stories for African creators.')

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $blog->title,
    'description' => $excerptText !== '' ? $excerptText : null,
    'image' => $blog->cover_url ?: null,
    'datePublished' => optional($published)->toAtomString(),
    'dateModified' => optional($blog->updated_at ?? $published)->toAtomString(),
    'author' => [
        '@type' => 'Organization',
        'name' => 'Payhankey',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'Payhankey',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset('logo.png'),
        ],
    ],
    'mainEntityOfPage' => url('blog/' . $blog->slug),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('apple_content')
<div class="apl-read-progress" id="readProgress" aria-hidden="true"></div>

<article class="apl-article">
  <header class="apl-article__hero">
    <div class="apl-article__hero-inner reveal">
      <div class="apl-crumbs">
        <a href="{{ url('/') }}">Home</a>
        <span>/</span>
        <a href="{{ route('blog') }}">Blog</a>
        <span>/</span>
        <span>{{ Str::limit($blog->title, 42) }}</span>
      </div>

      @if ($blog->blogCategory)
        <span class="apl-article__cat">{{ $blog->blogCategory->name }}</span>
      @endif

      <h1>{{ $blog->title }}</h1>

      @if ($excerptText !== '')
        <p class="apl-article__dek">{{ $excerptText }}</p>
      @endif

      <div class="apl-article__byline">
        <div class="apl-article__avatar" aria-hidden="true">PK</div>
        <div>
          <strong>Payhankey Team</strong>
          <span>{{ $published?->format('M d, Y') }} · {{ $readMins }} min read@if($blog->views) · {{ number_format($blog->views) }} reads@endif</span>
        </div>
      </div>
    </div>
  </header>

  @if ($blog->hasCover())
    <div class="apl-article__cover reveal" data-cover>
      <img
        src="{{ $blog->cover_url }}"
        alt="{{ $blog->title }}"
        loading="eager"
        decoding="async"
        onerror="this.closest('[data-cover]')?.remove()"
      >
    </div>
  @endif

  <div class="apl-article__body reveal" id="articleBody">
    {!! $blog->safeContentHtml() !!}

    <aside class="apl-article__author">
      <div class="apl-article__avatar" aria-hidden="true">PK</div>
      <div>
        <span class="apl-article__author-label">Written by</span>
        <strong>Payhankey Team</strong>
        <p>Guides and stories to help creators grow audiences, build communities and develop sustainable digital income streams.</p>
      </div>
    </aside>
  </div>
</article>

@if ($suggestions->isNotEmpty())
  <section class="apl-blog-shell apl-blog-shell--related">
    <div class="apl-wrap">
      <div class="apl-blog-related-head reveal">
        <p class="apl-showcase__eyebrow">Further reading</p>
        <h2>Similar stories</h2>
        <p class="apl-blog-related-lead">Keep exploring creator tips, monetization guides and platform updates.</p>
      </div>
      <div class="apl-blog-grid">
        @foreach ($suggestions as $suggestion)
          @include('general.partials.post-card', ['blog' => $suggestion])
        @endforeach
      </div>
      <div class="apl-showcase__links reveal" style="margin-top:2rem">
        <a class="apl-link" href="{{ route('blog') }}">Browse all articles <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
      </div>
    </div>
  </section>
@endif

@include('general.partials.apl-close-cta', [
  'title' => 'Don\'t just read about earning — start.',
  'lead' => 'Put these habits into practice on a free Payhankey account.',
  'primaryLabel' => 'Create free account',
  'showSecondary' => false,
])

<button class="apl-to-top" id="toTop" type="button" aria-label="Back to top">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
</button>
@endsection

@push('scripts')
<script>
(function () {
  var bar = document.getElementById('readProgress');
  var body = document.getElementById('articleBody');
  var topBtn = document.getElementById('toTop');
  if (!bar || !body) return;

  function onScroll() {
    var rect = body.getBoundingClientRect();
    var total = body.offsetHeight - window.innerHeight;
    var scrolled = Math.min(Math.max(-rect.top, 0), Math.max(total, 1));
    var pct = total > 0 ? (scrolled / total) * 100 : 0;
    bar.style.width = pct + '%';
    if (topBtn) topBtn.classList.toggle('is-visible', window.scrollY > 480);
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  if (topBtn) {
    topBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
})();
</script>
@endpush
