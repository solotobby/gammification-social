@extends('general.master.apple')

@section('title', 'Blog · Payhankey')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Blog',
    'eyebrow' => 'Payhankey blog',
    'title' => 'Tips, guides &amp; creator stories',
    'lead' => 'Everything you need to earn more, grow faster, and get the most out of Payhankey.',
])

<section class="apl-section apl-section--soft">
    <div class="apl-wrap">
        <form method="get" action="{{ route('blog') }}" class="apl-blog-toolbar">
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
            <div class="apl-grid-3">
                @foreach ($blogs as $blog)
                    @include('general.partials.post-card', ['blog' => $blog])
                @endforeach
            </div>

            @if ($blogs->hasPages())
                <div style="margin-top:36px;display:flex;justify-content:center;gap:8px;flex-wrap:wrap">
                    @if ($blogs->onFirstPage())
                        <span class="apl-chip-filter" style="opacity:.45;cursor:not-allowed">← Prev</span>
                    @else
                        <a class="apl-chip-filter" href="{{ $blogs->previousPageUrl() }}">← Prev</a>
                    @endif
                    <span class="apl-chip-filter is-active" style="cursor:default">
                        Page {{ $blogs->currentPage() }} of {{ $blogs->lastPage() }}
                    </span>
                    @if ($blogs->hasMorePages())
                        <a class="apl-chip-filter" href="{{ $blogs->nextPageUrl() }}">Next →</a>
                    @else
                        <span class="apl-chip-filter" style="opacity:.45;cursor:not-allowed">Next →</span>
                    @endif
                </div>
            @endif
        @else
            <div class="reveal" style="padding:48px 20px;text-align:center;color:var(--ink-faint)">
                <h3 style="color:var(--ink);margin-bottom:8px;font-size:1.3rem">No articles found</h3>
                <p>Try a different search or category.</p>
            </div>
        @endif
    </div>
</section>

<section class="apl-close">
    <h2 class="reveal">Don't just read about earning — start.</h2>
    <p class="reveal">Put these tips into practice on a free Payhankey account.</p>
    <div class="apl-close__cta reveal">
        <a class="apl-btn apl-btn--fill" href="{{ url('/register') }}">Create free account</a>
    </div>
</section>
@endsection
