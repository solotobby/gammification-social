@extends('general.master.body')

@section('content')
    <section class="pagehero">
        <div class="wrap pagehero__inner">
            <div class="crumbs"><a href="{{ url('/') }}">Home</a> / <span>Blog</span></div>
            <span class="eyebrow">Payhankey blog</span>
            <h1>Tips, guides & creator stories</h1>
            <p>Everything you need to earn more, grow faster, and get the most out of Payhankey.</p>
        </div>
    </section>

    <section class="section--tight">
        <div class="wrap">
            <form method="get" action="{{ route('blog') }}"
                style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;margin-bottom:28px">
                <div class="blog-cats">
                    <a href="{{ route('blog') }}"
                        class="chip {{ ! request('category') ? 'is-active' : '' }}">All</a>
                    @foreach ($blogCategories as $category)
                        <a href="{{ route('blog', ['category' => $category->id]) }}"
                            class="chip {{ (string) request('category') === (string) $category->id ? 'is-active' : '' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
                <div class="searchbar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input class="input" type="search" name="q" value="{{ request('q') }}"
                        placeholder="Search articles" aria-label="Search articles">
                </div>
            </form>

            @if ($blogs->count())
                <div class="grid-3">
                    @foreach ($blogs as $blog)
                        @include('general.partials.post-card', ['blog' => $blog])
                    @endforeach
                </div>

                @if ($blogs->hasPages())
                    <div style="margin-top:36px;display:flex;justify-content:center;gap:8px;flex-wrap:wrap">
                        @if ($blogs->onFirstPage())
                            <span class="chip" style="opacity:.45;cursor:not-allowed">← Prev</span>
                        @else
                            <a class="chip" href="{{ $blogs->previousPageUrl() }}">← Prev</a>
                        @endif
                        <span class="chip is-active" style="cursor:default">
                            Page {{ $blogs->currentPage() }} of {{ $blogs->lastPage() }}
                        </span>
                        @if ($blogs->hasMorePages())
                            <a class="chip" href="{{ $blogs->nextPageUrl() }}">Next →</a>
                        @else
                            <span class="chip" style="opacity:.45;cursor:not-allowed">Next →</span>
                        @endif
                    </div>
                @endif
            @else
                <div class="center reveal" style="padding:48px 20px;color:var(--ink-faint)">
                    <h3 style="color:var(--ink);margin-bottom:8px">No articles found</h3>
                    <p>Try a different search or category.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="section">
        <div class="wrap">
            <div class="cta-band reveal">
                <h2>Don't just read about earning — start.</h2>
                <p>Put these tips into practice on a free Payhankey account and watch your first earnings roll in.</p>
                <div class="hero__cta">
                    <a class="btn btn--white btn--lg" href="{{ url('/register') }}">
                        Create free account
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
