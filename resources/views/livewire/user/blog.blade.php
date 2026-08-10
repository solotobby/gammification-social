<div class="pk-app">
    @include('livewire.user.partials.pk-app-ui')

    <div class="pk-app-hero">
        <div class="pk-app-hero-inner">
            <span class="pk-app-kicker">Creator resources</span>
            <h1>Payhankey blog</h1>
            <p>Tips, guides, and stories to help you earn more, grow faster, and get the most from the platform.</p>
        </div>
    </div>

    <div class="pk-stat-grid" style="grid-template-columns:repeat(2,1fr)">
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:var(--pk-violet-soft);color:var(--pk-violet);">
                <i class="fa fa-newspaper-o"></i>
            </div>
            <p class="pk-stat-card-value">{{ number_format($totalPublished) }}</p>
            <p class="pk-stat-card-label">Published articles</p>
        </article>
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:var(--pk-mint-soft);color:var(--pk-mint);">
                <i class="fa fa-tags"></i>
            </div>
            <p class="pk-stat-card-value">{{ $blogCategories->count() }}</p>
            <p class="pk-stat-card-label">Topics covered</p>
        </article>
    </div>

    <div class="pk-panel">
        <div class="pk-panel-head">
            <h2>Latest articles</h2>
            @if ($search !== '' || $category)
                <button type="button" class="pk-btn pk-btn--ghost" wire:click="clearFilters">
                    <i class="fa fa-times"></i> Clear filters
                </button>
            @endif
        </div>

        <div class="pk-panel-body">
            <div class="pk-blog-toolbar">
                <div class="pk-blog-chips">
                    <button type="button"
                        @class(['pk-blog-chip', 'is-active' => ! $category])
                        wire:click="setCategory(null)">
                        All
                    </button>
                    @foreach ($blogCategories as $cat)
                        <button type="button"
                            @class(['pk-blog-chip', 'is-active' => $category === $cat->id])
                            wire:click="setCategory({{ $cat->id }})">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>

                <div class="pk-blog-search">
                    <i class="fa fa-search"></i>
                    <input type="search"
                        wire:model.live.debounce.350ms="search"
                        placeholder="Search articles…"
                        aria-label="Search articles">
                </div>
            </div>

            @if ($blogs->count())
                <div class="pk-blog-grid">
                    @foreach ($blogs as $blog)
                        @include('livewire.user.partials.blog-card', ['blog' => $blog])
                    @endforeach
                </div>
            @else
                <div class="pk-empty">
                    <h3>No articles found</h3>
                    <p>
                        @if ($search !== '' || $category)
                            Try a different search or category, or clear your filters.
                        @else
                            New articles will appear here as they're published.
                        @endif
                    </p>
                    @if ($search !== '' || $category)
                        <button type="button" class="pk-btn pk-btn--primary" wire:click="clearFilters" style="margin-top:12px">
                            Show all articles
                        </button>
                    @endif
                </div>
            @endif
        </div>

        @if ($blogs->hasPages())
            <div class="pk-pagination">
                <div class="pk-pg-info">
                    Showing {{ $blogs->firstItem() }}–{{ $blogs->lastItem() }} of {{ $blogs->total() }}
                </div>
                <div class="pk-pg-btns">
                    <button type="button" class="pk-pg-btn" wire:click="previousPage" @disabled($blogs->onFirstPage())>
                        Prev
                    </button>
                    <span class="pk-pg-btn" style="cursor:default;border-color:#C7D2FE;background:var(--pk-violet-soft);color:var(--pk-violet-dark)">
                        {{ $blogs->currentPage() }} / {{ $blogs->lastPage() }}
                    </span>
                    <button type="button" class="pk-pg-btn" wire:click="nextPage" @disabled(! $blogs->hasMorePages())>
                        Next
                    </button>
                </div>
            </div>
        @endif
    </div>

    <div class="pk-panel">
        <div class="pk-panel-body" style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap">
            <div>
                <h3 style="margin:0 0 4px;font-size:.95rem">Want the full blog experience?</h3>
                <p style="margin:0;font-size:.84rem;color:var(--pk-muted)">Browse articles on the public blog with rich reading layout and related posts.</p>
            </div>
            <a href="{{ route('blog') }}" target="_blank" rel="noopener" class="pk-btn pk-btn--primary">
                Open public blog <i class="fa fa-external-link"></i>
            </a>
        </div>
    </div>

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
