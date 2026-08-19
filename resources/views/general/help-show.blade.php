@extends('general.master.apple')

@section('title', $article->seoTitle())
@section('meta_description', $article->seoDescription())
@section('body_class', 'page-landing-apple page-help-article')

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [[
        '@type' => 'Question',
        'name' => $article->title,
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => strip_tags((string) $article->body),
        ],
    ]],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Help Center',
    'title' => $article->title,
    'lead' => $article->category?->name ? $article->category->name.' · Help Center' : 'Help Center',
])

<section class="apl-faq-shell">
  <div class="apl-wrap apl-faq-page" style="max-width:760px;">
    <article class="apl-faq-entry reveal" style="border:0;padding:0;">
      <div class="apl-article-body" style="font-size:1.05rem;line-height:1.7;color:var(--apl-ink-soft, #444);">
        {!! $article->safeBodyHtml() !!}
      </div>
    </article>

    @if ($related->isNotEmpty())
      <div class="reveal" style="margin-top:3rem;">
        <h2 style="font-size:1.25rem;margin-bottom:1rem;">Related articles</h2>
        <div class="apl-faq-cat__body">
          @foreach ($related as $item)
            <article class="apl-faq-entry">
              <h3><a href="{{ route('help.show', $item->slug) }}" style="color:inherit;text-decoration:none;">{{ $item->title }}</a></h3>
              <p>{{ Str::limit(strip_tags($item->body), 140) }}</p>
            </article>
          @endforeach
        </div>
      </div>
    @endif

    <p class="reveal" style="margin-top:2rem;">
      <a class="apl-link" href="{{ route('help', $article->category?->slug ? ['category' => $article->category->slug] : []) }}">
        Back to Help Center
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </p>
  </div>
</section>

@include('general.partials.apl-close-cta')
@endsection
