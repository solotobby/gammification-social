@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .dash-kpi {
            display: flex; flex-direction: column; gap: .75rem; padding: 1.25rem;
            background: var(--dash-surface); border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius); box-shadow: var(--dash-shadow); height: 100%;
        }
        .dash-kpi__label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--dash-muted); }
        .dash-kpi__value { font-size: 1.5rem; font-weight: 700; letter-spacing: -.03em; }
        .dash-post-title { font-weight: 600; color: var(--dash-accent); text-decoration: none; }
        .dash-post-title:hover { text-decoration: underline; }
        .dash-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
        @media (max-width: 1200px) { .dash-grid--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 640px) { .dash-grid--4 { grid-template-columns: 1fr; } }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Help Center</h1>
                    <p>Support articles that replace the public FAQ</p>
                </div>
                <a href="{{ route('admin.help.create') }}" class="dash-btn dash-btn--primary">
                    <i class="fa fa-plus"></i> New article
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">All articles</span>
                        <div class="dash-kpi__value">{{ number_format($total) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Published</span>
                        <div class="dash-kpi__value">{{ number_format($published) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Drafts</span>
                        <div class="dash-kpi__value">{{ number_format($drafts) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Public</span>
                        <div class="dash-kpi__value" style="font-size:1.125rem;"><a href="{{ route('help') }}" target="_blank" class="dash-link">/help</a></div>
                    </div>
                </div>
            </section>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">All Help articles</h2>
                        <p class="dash-muted" style="margin:.25rem 0 0;">
                            Showing {{ $articles->firstItem() ?? 0 }}–{{ $articles->lastItem() ?? 0 }} of {{ number_format($articles->total()) }}
                        </p>
                    </div>
                </div>
                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($articles as $article)
                                    <tr>
                                        <td>
                                            <a href="{{ route('help.show', $article->slug) }}" target="_blank" class="dash-post-title">
                                                {{ $article->title }}
                                            </a>
                                        </td>
                                        <td class="dash-muted">{{ $article->category?->name ?? '—' }}</td>
                                        <td class="dash-muted">{{ $article->sort_order }}</td>
                                        <td>
                                            @if ($article->published)
                                                <span class="dash-badge dash-badge--emerald">Published</span>
                                            @else
                                                <span class="dash-badge dash-badge--gray">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dash-actions">
                                                <a href="{{ route('help.show', $article->slug) }}" target="_blank" class="dash-btn dash-btn--ghost" style="padding:.5rem .75rem;">
                                                    <i class="fa fa-external-link"></i> View
                                                </a>
                                                <form method="POST" action="{{ route('admin.help.delete', $article->slug) }}"
                                                    onsubmit="return confirm('Delete “{{ $article->title }}”?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dash-btn dash-btn--ghost" style="padding:.5rem .75rem;color:#be123c;">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="dash-empty">No Help articles yet. <a href="{{ route('admin.help.create') }}" class="dash-link">Create one</a>.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($articles->hasPages())
                        <div class="dash-pagination">{{ $articles->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
