@extends('general.master.apple')

@section('title', 'Payhankey Help Center: Accounts, Payments, Communities & More')
@section('meta_description', 'Find answers about Payhankey accounts, payments, communities, subscriptions, verification, AI tools, policies and security.')
@section('body_class', 'page-landing-apple page-help')

@push('head')
@if (! empty($schemaEntities))
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $schemaEntities,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endpush

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Help Center',
    'title' => 'How can we help?',
    'lead' => 'Guides for accounts, payments, communities, subscriptions, verification, AI, policies and security — replacing the old FAQ with a searchable Help Center.',
])

<section class="apl-faq-shell">
  <div class="apl-wrap apl-faq-page">
    <form method="get" action="{{ route('help') }}" class="apl-blog-toolbar reveal" role="search" style="margin-bottom:1.5rem;">
      @if (request('category'))
        <input type="hidden" name="category" value="{{ request('category') }}">
      @endif
      <div class="apl-chips-scroll" aria-label="Filter by category">
        <div class="apl-chips-row">
          <a href="{{ route('help', request()->only('q')) }}" class="apl-chip-filter {{ ! request('category') ? 'is-active' : '' }}">All</a>
          @foreach ($allCategories as $category)
            <a href="{{ route('help', array_filter(['category' => $category->slug, 'q' => request('q')])) }}"
              class="apl-chip-filter {{ request('category') === $category->slug || request('category') === $category->id ? 'is-active' : '' }}">
              {{ $category->name }}
            </a>
          @endforeach
        </div>
      </div>
      <div class="apl-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search Help Center" aria-label="Search Help Center" enterkeyhint="search" autocomplete="off">
        @if (request('q'))
          <a class="apl-search__clear" href="{{ route('help', request()->only('category')) }}" aria-label="Clear search">×</a>
        @endif
      </div>
    </form>

    @if ($categories->isEmpty() || $categories->every(fn ($c) => $c->articles->isEmpty()))
      <div class="apl-blog-empty reveal">
        <h3>No help articles found</h3>
        <p>Try another category or search term, or <a href="{{ url('/contact') }}">contact support</a>.</p>
      </div>
    @else
      <nav class="apl-faq-toc reveal" aria-label="Help Center categories">
        @foreach ($categories as $i => $category)
          @continue($category->articles->isEmpty())
          <a href="#{{ $category->slug }}">
            <span class="apl-faq-toc__num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
            {{ $category->name }}
          </a>
        @endforeach
      </nav>

      <div class="apl-faq-stack">
        @foreach ($categories as $i => $category)
          @continue($category->articles->isEmpty())
          <section class="apl-faq-cat reveal" id="{{ $category->slug }}">
            <header class="apl-faq-cat__head">
              <span class="apl-faq-cat__index">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
              <h2>{{ $category->name }}</h2>
            </header>
            <div class="apl-faq-cat__body">
              @foreach ($category->articles as $article)
                <article class="apl-faq-entry">
                  <h3><a href="{{ route('help.show', $article->slug) }}" style="color:inherit;text-decoration:none;">{{ $article->title }}</a></h3>
                  <p>{{ Str::limit(strip_tags($article->body), 220) }}</p>
                </article>
              @endforeach
            </div>
          </section>
        @endforeach
      </div>
    @endif
  </div>
</section>

@include('general.partials.apl-close-cta', [
  'eyebrow' => 'Still stuck?',
  'title' => 'Talk to the Payhankey team',
  'lead' => 'If you cannot find an answer in the Help Center, send us a message and we will help.',
])
@endsection
