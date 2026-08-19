@extends('general.master.apple')

@section('title', 'Creator Academy · Payhankey')
@section('meta_description', 'Creator Academy by Payhankey — practical guides on monetization, growth, AI, communities, creator economy, students and business for African creators.')
@section('body_class', 'page-landing-apple page-academy')

@section('apple_content')

<section class="apl-comm-hero">
  <div class="apl-wrap apl-comm-hero__inner">
    <div class="apl-crumbs reveal"><a href="{{ url('/') }}">Home</a> / <span>Academy</span></div>
    <p class="apl-comm-hero__eyebrow reveal">Creator Academy</p>
    <h1 class="reveal">Learn to create, grow and earn</h1>
    <p class="apl-comm-hero__lead reveal">Guides for monetization, growth, AI, communities and building a creator business — separate from the Payhankey blog.</p>
  </div>
</section>

<section class="apl-blog-shell">
  <div class="apl-wrap">
    <form method="get" action="{{ route('academy') }}" class="apl-blog-toolbar reveal" role="search">
      @if (request('category'))
        <input type="hidden" name="category" value="{{ request('category') }}">
      @endif
      <div class="apl-chips-scroll" aria-label="Filter by category">
        <div class="apl-chips-row">
          <a href="{{ route('academy', request()->only('q')) }}" class="apl-chip-filter {{ ! request('category') ? 'is-active' : '' }}">All</a>
          @foreach ($categories as $category)
            <a href="{{ route('academy', array_filter(['category' => $category->slug, 'q' => request('q')])) }}"
              class="apl-chip-filter {{ request('category') === $category->slug || request('category') === $category->id ? 'is-active' : '' }}">
              {{ $category->name }}
            </a>
          @endforeach
        </div>
      </div>
      <div class="apl-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search Academy" aria-label="Search Academy" enterkeyhint="search" autocomplete="off">
        @if (request('q'))
          <a class="apl-search__clear" href="{{ route('academy', request()->only('category')) }}" aria-label="Clear search">×</a>
        @endif
      </div>
    </form>

    @if ($articles->count())
      <div class="apl-blog-grid">
        @foreach ($articles as $article)
          <article class="apl-blog-card reveal">
            <a href="{{ route('academy.show', $article->slug) }}" class="apl-blog-card__link">
              <div class="apl-blog-card__media {{ $article->hasFeaturedImage() ? '' : 'is-fallback' }}">
                @if ($article->hasFeaturedImage())
                  <img class="apl-blog-card__img" src="{{ $article->featured_image_url }}" alt="" loading="lazy" decoding="async"
                    onerror="this.remove(); this.parentElement.classList.add('is-fallback');">
                @endif
                <span class="apl-blog-card__cat">{{ $article->category->name ?? 'Academy' }}</span>
              </div>
              <div class="apl-blog-card__body">
                <h3>{{ $article->title }}</h3>
                <p>{{ Str::limit($article->seoDescription(), 110) }}</p>
                <div class="apl-blog-meta">
                  <span>{{ $article->author ?: 'Payhankey Academy' }}</span>
                  <span aria-hidden="true">·</span>
                  <span>{{ max(1, (int) $article->read_time) }} min read</span>
                </div>
              </div>
            </a>
          </article>
        @endforeach
      </div>

      @if ($articles->hasPages())
        <nav class="apl-blog-pager reveal" aria-label="Academy pagination">
          @if ($articles->onFirstPage())
            <span class="apl-blog-pager__btn is-disabled" aria-disabled="true">Previous</span>
          @else
            <a class="apl-blog-pager__btn" href="{{ $articles->previousPageUrl() }}">Previous</a>
          @endif
          <span class="apl-blog-pager__status">
            <span class="apl-blog-pager__status-full">Page {{ $articles->currentPage() }} of {{ $articles->lastPage() }}</span>
            <span class="apl-blog-pager__status-short" aria-hidden="true">{{ $articles->currentPage() }} / {{ $articles->lastPage() }}</span>
          </span>
          @if ($articles->hasMorePages())
            <a class="apl-blog-pager__btn" href="{{ $articles->nextPageUrl() }}">Next</a>
          @else
            <span class="apl-blog-pager__btn is-disabled" aria-disabled="true">Next</span>
          @endif
        </nav>
      @endif
    @else
      <div class="apl-blog-empty reveal">
        <h3>No Academy guides yet</h3>
        <p>New Creator Academy lessons will appear here soon.</p>
      </div>
    @endif
  </div>
</section>

@include('general.partials.apl-close-cta', [
  'eyebrow' => 'Put learning into action',
  'title' => 'Start building on Payhankey today',
  'lead' => 'Create free, publish your first post, and apply what you learn in Creator Academy.',
  'primaryLabel' => 'Start Free',
  'secondaryLabel' => 'Login',
])

@endsection
