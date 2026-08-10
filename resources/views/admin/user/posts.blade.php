@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }

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

        .dash-post {
            max-width: 320px;
            font-size: 0.875rem;
            line-height: 1.45;
        }

        .dash-post__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem;
            margin-top: 0.375rem;
        }

        .dash-num { font-weight: 600; font-variant-numeric: tabular-nums; }

        @media (max-width: 960px) {
            .dash-grid--3 { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    @php
        $pagePosts = collect($posts->items());
        $pageViews = $pagePosts->sum(fn ($p) => (int) sumCounter($p->views, $p->views_external));
        $pageLikes = $pagePosts->sum(fn ($p) => (int) sumCounter($p->likes, $p->likes_external));
        $pageComments = $pagePosts->sum(fn ($p) => (int) sumCounter($p->comments, $p->comment_external));
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Posts</h1>
                    <p>{{ $user->name }} · {{ $user->email }}</p>
                </div>
                <a href="{{ route('admin.users.show', $user) }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> User profile
                </a>
            </header>

            <section class="dash-section">
                <div class="dash-grid dash-grid--3">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Total posts</span>
                        <div class="dash-kpi__value">{{ number_format($posts->total()) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Views (this page)</span>
                        <div class="dash-kpi__value">{{ number_format($pageViews) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Engagement (this page)</span>
                        <div class="dash-kpi__value">{{ number_format($pageLikes + $pageComments) }}</div>
                    </div>
                </div>
            </section>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Published content</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            Showing {{ $posts->firstItem() ?? 0 }}–{{ $posts->lastItem() ?? 0 }} of {{ number_format($posts->total()) }}
                        </p>
                    </div>
                </div>

                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Content</th>
                                    <th>Likes</th>
                                    <th>Views</th>
                                    <th>Comments</th>
                                    <th>Posted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posts as $post)
                                    <tr>
                                        <td>
                                            <div class="dash-post">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 120) ?: '—' }}
                                                <div class="dash-post__meta">
                                                    @if ($post->has_video)
                                                        <span class="dash-badge dash-badge--indigo">Video</span>
                                                    @endif
                                                    @if ($post->has_images)
                                                        <span class="dash-badge dash-badge--gray">Images</span>
                                                    @endif
                                                    @if ($post->status)
                                                        <span class="dash-badge dash-badge--gray">{{ $post->status }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="dash-num">{{ number_format((int) sumCounter($post->likes, $post->likes_external)) }}</span>
                                            <span class="dash-muted" style="display:block; font-size:0.75rem;">
                                                {{ number_format((int) $post->likes) }} internal
                                            </span>
                                        </td>
                                        <td>
                                            <span class="dash-num">{{ number_format((int) sumCounter($post->views, $post->views_external)) }}</span>
                                            <span class="dash-muted" style="display:block; font-size:0.75rem;">
                                                {{ number_format((int) $post->views) }} unique
                                            </span>
                                        </td>
                                        <td>
                                            <span class="dash-num">{{ number_format((int) sumCounter($post->comments, $post->comment_external)) }}</span>
                                            <span class="dash-muted" style="display:block; font-size:0.75rem;">
                                                {{ number_format((int) $post->comments) }} unique
                                            </span>
                                        </td>
                                        <td class="dash-muted">
                                            {{ $post->created_at?->format('M j, Y') }}
                                            <span style="display:block; font-size:0.75rem;">
                                                {{ $post->created_at?->diffForHumans() }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="dash-empty">This user has not published any posts yet.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($posts->hasPages())
                        <div class="dash-pagination">
                            {{ $posts->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
