@extends('general.master.apple')

@php
    $faqs = is_array($article->faq_schema) ? $article->faq_schema : [];
@endphp

@section('title', $article->seoTitle())
@section('meta_description', $article->seoDescription())
@section('body_class', 'page-landing-apple page-academy')

@push('head')
@if (! empty($faqs))
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faqs)->map(function ($item) {
        $q = is_array($item) ? ($item['q'] ?? $item['question'] ?? null) : null;
        $a = is_array($item) ? ($item['a'] ?? $item['answer'] ?? null) : null;
        if (! $q || ! $a) {
            return null;
        }
        return [
            '@type' => 'Question',
            'name' => $q,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $a,
            ],
        ];
    })->filter()->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article->title,
    'description' => $article->seoDescription(),
    'image' => $article->featured_image_url,
    'datePublished' => optional($article->published_at)->toAtomString(),
    'dateModified' => optional($article->updated_at)->toAtomString(),
    'author' => [
        '@type' => 'Person',
        'name' => $article->author ?: 'Payhankey Academy',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'Payhankey',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset('logo.png'),
        ],
    ],
    'mainEntityOfPage' => route('academy.show', $article->slug),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('apple_content')
<div class="apl-read-progress" id="readProgress" aria-hidden="true"></div>

<nav class="apl-article__mobile-bar" aria-label="Article actions">
  <a class="apl-article__back" href="{{ route('academy') }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
    <span>Academy</span>
  </a>
  <span class="apl-article__mobile-meta">{{ max(1, (int) $article->read_time) }} min read</span>
</nav>

<article class="apl-article">
  <header class="apl-article__hero">
    <div class="apl-article__hero-inner reveal">
      <div class="apl-crumbs apl-crumbs--article">
        <a href="{{ url('/') }}">Home</a>
        <span>/</span>
        <a href="{{ route('academy') }}">Academy</a>
        @if ($article->category)
          <span>/</span>
          <a href="{{ route('academy', ['category' => $article->category->slug]) }}">{{ $article->category->name }}</a>
        @endif
      </div>

      @if ($article->category)
        <span class="apl-article__cat">{{ $article->category->name }}</span>
      @endif

      <h1>{{ $article->title }}</h1>

      <div class="apl-article__byline">
        <div class="apl-article__avatar" aria-hidden="true">{{ strtoupper(substr($article->author ?: 'PA', 0, 2)) }}</div>
        <div class="apl-article__byline-text">
          <strong>{{ $article->author ?: 'Payhankey Academy' }}</strong>
          <span class="apl-article__meta-line">
            <span>{{ ($article->published_at ?? $article->created_at)?->format('M d, Y') }}</span>
            <span aria-hidden="true">·</span>
            <span>{{ max(1, (int) $article->read_time) }} min read</span>
          </span>
        </div>
      </div>
    </div>
  </header>

  @if ($article->hasFeaturedImage())
    <div class="apl-article__cover reveal" data-cover>
      <img
        src="{{ $article->featured_image_url }}"
        alt="{{ $article->title }}"
        loading="eager"
        decoding="async"
        onerror="this.closest('[data-cover]')?.remove()"
      >
    </div>
  @endif

  <div class="apl-article__body" id="articleBody">
    {!! $article->safeBodyHtml() !!}

    <aside class="apl-article__author">
      <div class="apl-article__avatar" aria-hidden="true">{{ strtoupper(substr($article->author ?: 'PA', 0, 2)) }}</div>
      <div>
        <span class="apl-article__author-label">Written by</span>
        <strong>{{ $article->author ?: 'Payhankey Academy' }}</strong>
        <p>Creator Academy lessons help African creators grow audiences, monetize content and build sustainable digital businesses.</p>
      </div>
    </aside>
  </div>
</article>

@if ($related->isNotEmpty())
  <section class="apl-blog-shell apl-blog-shell--related">
    <div class="apl-wrap">
      <div class="apl-blog-related-head reveal">
        <p class="apl-showcase__eyebrow">Keep learning</p>
        <h2>Related Academy guides</h2>
      </div>
      <div class="apl-blog-grid">
        @foreach ($related as $item)
          <article class="apl-blog-card reveal">
            <a href="{{ route('academy.show', $item->slug) }}" class="apl-blog-card__link">
              <div class="apl-blog-card__media {{ $item->hasFeaturedImage() ? '' : 'is-fallback' }}">
                @if ($item->hasFeaturedImage())
                  <img class="apl-blog-card__img" src="{{ $item->featured_image_url }}" alt="" loading="lazy">
                @endif
                <span class="apl-blog-card__cat">{{ $item->category->name ?? 'Academy' }}</span>
              </div>
              <div class="apl-blog-card__body">
                <h3>{{ $item->title }}</h3>
                <p>{{ Str::limit($item->seoDescription(), 100) }}</p>
              </div>
            </a>
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif

@include('general.partials.apl-close-cta', [
  'eyebrow' => 'Apply the lesson',
  'title' => 'Ready to put this into practice?',
  'lead' => 'Create a free Payhankey account and start growing your creator business.',
  'primaryLabel' => 'Start Free',
  'secondaryLabel' => 'Login',
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
  if (topBtn) topBtn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();
</script>
@endpush
