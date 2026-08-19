@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .dash-kpi {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1.25rem;
            background: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            box-shadow: var(--dash-shadow);
            height: 100%;
            color: inherit;
            text-decoration: none;
            transition: border-color .2s, transform .2s;
        }

        a.dash-kpi:hover {
            border-color: #c7d2fe;
            transform: translateY(-1px);
        }

        .dash-kpi__label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dash-muted);
        }

        .dash-kpi__value {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .dash-kpi__hint { font-size: 0.8125rem; color: var(--dash-muted); }
        .dash-badge--rose { background: #fff1f2; color: #be123c; }
        .dash-post-title { font-weight: 600; color: var(--dash-accent); text-decoration: none; }
        .dash-post-title:hover { text-decoration: underline; }

        .dash-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }

        @media (max-width: 1200px) {
            .dash-grid--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .dash-grid--4 { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Blog posts</h1>
                    <p>Manage published articles and drafts</p>
                </div>
                <a href="{{ route('admin.blog.create') }}" class="dash-btn dash-btn--primary">
                    <i class="fa fa-plus"></i> New post
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">All posts</span>
                        <div class="dash-kpi__value">{{ number_format($totalPosts) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Published</span>
                        <div class="dash-kpi__value">{{ number_format($totalPublished) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Drafts</span>
                        <div class="dash-kpi__value">{{ number_format($totalDrafts) }}</div>
                    </div>
                    <a href="{{ route('admin.blog.create') }}" class="dash-kpi">
                        <span class="dash-kpi__label">Create</span>
                        <div class="dash-kpi__value" style="font-size:1.125rem;">New post</div>
                        <div class="dash-kpi__hint">Write and publish</div>
                    </a>
                </div>
            </section>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">All posts</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            Showing {{ $blogs->firstItem() ?? 0 }}–{{ $blogs->lastItem() ?? 0 }} of {{ number_format($blogs->total()) }}
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
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Published</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($blogs as $blog)
                                    <tr>
                                        <td>
                                            <a href="{{ url('blog/' . $blog->slug) }}" target="_blank" class="dash-post-title">
                                                {{ $blog->title }}
                                            </a>
                                        </td>
                                        <td class="dash-muted">{{ $blog->blogCategory?->name ?? 'Uncategorized' }}</td>
                                        <td>
                                            @if ($blog->status === 'PUBLISHED')
                                                <span class="dash-badge dash-badge--emerald">Published</span>
                                            @else
                                                <span class="dash-badge dash-badge--gray">{{ $blog->status }}</span>
                                            @endif
                                        </td>
                                        <td class="dash-muted">{{ $blog->created_at?->format('M j, Y g:i A') }}</td>
                                        <td class="dash-muted">
                                            {{ $blog->published_at ? \Carbon\Carbon::parse($blog->published_at)->format('M j, Y g:i A') : '—' }}
                                        </td>
                                        <td>
                                            <div class="dash-actions">
                                                <a href="{{ route('admin.blog.edit', $blog->slug) }}" class="dash-btn dash-btn--ghost" style="padding:0.5rem 0.75rem;">
                                                    <i class="fa fa-pen"></i> Edit
                                                </a>
                                                <a href="{{ url('blog/' . $blog->slug) }}" target="_blank" class="dash-btn dash-btn--ghost" style="padding:0.5rem 0.75rem;">
                                                    <i class="fa fa-external-link"></i> View
                                                </a>
                                                <form method="POST" action="{{ route('admin.blog.delete', $blog->slug) }}"
                                                    onsubmit="return confirm('Delete “{{ $blog->title }}”?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dash-btn dash-btn--ghost" style="padding:0.5rem 0.75rem; color:#be123c;">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="dash-empty">No blog posts yet. <a href="{{ route('admin.blog.create') }}" class="dash-link">Create one</a>.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($blogs->hasPages())
                        <div class="dash-pagination">
                            {{ $blogs->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
