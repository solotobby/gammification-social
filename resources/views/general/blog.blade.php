@extends('general.master.apple')

@section('title', 'Blog · Payhankey | Creator Tips, Guides & Stories')
@section('meta_description', 'Tips, guides and creator stories from Payhankey — learn how to grow your audience, monetize content, build communities and earn with creator tools built for Africa.')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Blog',
    'title' => 'Creator Tips, Guides &amp; Stories',
    'lead' => 'Practical ideas to grow your audience, monetize content, build communities and turn your creativity into a sustainable digital business.',
])

<section class="apl-blog-shell">
  <div class="apl-wrap">
    <form method="get" action="{{ route('blog') }}" class="apl-blog-toolbar reveal">
      <div class="apl-chips-row">
        <a href="{{ route('blog') }}" class="apl-chip-filter {{ ! request('category') ? 'is-active' : '' }}">All</a>
        @foreach ($blogCategories as $category)
          <a href="{{ route('blog', ['category' => $category->id]) }}"
            class="apl-chip-filter {{ (string) request('category') === (string) $category->id ? 'is-active' : '' }}">
            {{ $category->name }}
          </a>
        @endforeach
      </div>
      <div class="apl-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search articles" aria-label="Search articles">
      </div>
    </form>

    @if ($blogs->count())
      @php
        $featured = $blogs->first();
        $rest = $blogs->slice(1);
      @endphp

      @if ($blogs->currentPage() === 1 && ! request('q') && ! request('category'))
        <a href="{{ url('blog/' . $featured->slug) }}" class="apl-blog-featured reveal">
          <div class="apl-blog-featured__media">
            @if ($featured->cover_url)
              <img
                class="apl-blog-featured__img"
                src="{{ $featured->cover_url }}"
                alt=""
                loading="eager"
                decoding="async"
                onerror="this.remove(); this.parentElement.classList.add('is-fallback');"
              >
            @endif
          </div>
          <div class="apl-blog-featured__body">
            <span class="apl-blog-featured__cat">{{ $featured->blogCategory->name ?? 'Article' }}</span>
            <h2>{{ $featured->title }}</h2>
            <p>{{ Str::limit(strip_tags($featured->excerpt ?? ''), 160) }}</p>
            <div class="apl-blog-meta">
              <span>{{ ($featured->published_at ?? $featured->created_at)?->format('M d, Y') }}</span>
              @if (! empty($featured->content))
                <span>·</span>
                <span>{{ max(1, (int) ceil(str_word_count(strip_tags($featured->content)) / 200)) }} min read</span>
              @endif
            </div>
          </div>
        </a>
      @endif

      <div class="apl-blog-grid">
        @foreach (($blogs->currentPage() === 1 && ! request('q') && ! request('category') ? $rest : $blogs) as $blog)
          @include('general.partials.post-card', ['blog' => $blog])
        @endforeach
      </div>

      @if ($blogs->hasPages())
        <nav class="apl-blog-pager reveal" aria-label="Blog pagination">
          @if ($blogs->onFirstPage())
            <span class="is-disabled">Previous</span>
          @else
            <a href="{{ $blogs->previousPageUrl() }}">Previous</a>
          @endif
          <span class="apl-blog-pager__status">Page {{ $blogs->currentPage() }} of {{ $blogs->lastPage() }}</span>
          @if ($blogs->hasMorePages())
            <a href="{{ $blogs->nextPageUrl() }}">Next</a>
          @else
            <span class="is-disabled">Next</span>
          @endif
        </nav>
      @endif
    @else
      <div class="apl-blog-empty reveal">
        <h3>No articles found</h3>
        <p>Try a different search or category.</p>
      </div>
    @endif
  </div>
</section>

@include('general.partials.apl-close-cta')
@endsection
