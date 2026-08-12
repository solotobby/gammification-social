@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-post-preview { max-width: 360px; font-size: .875rem; line-height: 1.45; }
        .dash-post-preview__meta { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .4rem; }
        .dash-author { display: flex; align-items: center; gap: .65rem; min-width: 160px; }
        .dash-author img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .dash-author__name { font-weight: 600; color: inherit; text-decoration: none; }
        .dash-author__name:hover { text-decoration: underline; }
        .dash-actions { display: flex; flex-wrap: wrap; gap: .4rem; justify-content: flex-end; }
        .dash-badge--danger { background: rgba(220, 53, 69, .12); color: #b42318; }
        .dash-badge--success { background: rgba(16, 185, 129, .12); color: #067647; }
        .dash-badge--warn { background: rgba(245, 158, 11, .14); color: #b54708; }
        .dash-btn--sm { padding: .35rem .65rem; font-size: .78rem; }
        .dash-btn--danger { background: #b42318; color: #fff; border: none; }
        .dash-btn--danger:hover { filter: brightness(1.05); }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Timeline Posts</h1>
                    <p>Browse, hide, or permanently delete social timeline posts</p>
                </div>
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
                        <span class="dash-kpi__label">Total posts</span>
                        <div class="dash-kpi__value">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Live</span>
                        <div class="dash-kpi__value">{{ number_format($stats['live']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Hidden</span>
                        <div class="dash-kpi__value">{{ number_format($stats['hidden']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Reported</span>
                        <div class="dash-kpi__value">{{ number_format($stats['reported']) }}</div>
                        <div class="dash-muted">{{ number_format($stats['shadow']) }} shadow-banned</div>
                    </div>
                </div>
            </section>

            <section class="dash-section">
                <form method="get" class="dash-toolbar">
                    <input type="search" name="q" value="{{ $search }}" placeholder="Search content, username, email, or post ID" class="dash-input">
                    <select name="status" class="dash-input" style="flex:0 0 150px">
                        <option value="">All statuses</option>
                        @foreach (['LIVE', 'HIDDEN', 'SHADOW_BANNED'] as $option)
                            <option value="{{ $option }}" @selected($status === $option)>{{ str_replace('_', ' ', $option) }}</option>
                        @endforeach
                    </select>
                    <select name="media" class="dash-input" style="flex:0 0 130px">
                        <option value="">All media</option>
                        <option value="images" @selected($media === 'images')>Images</option>
                        <option value="video" @selected($media === 'video')>Video</option>
                        <option value="text" @selected($media === 'text')>Text only</option>
                    </select>
                    <label class="dash-muted" style="display:inline-flex;align-items:center;gap:.4rem;white-space:nowrap">
                        <input type="checkbox" name="reported" value="1" @checked($reportedOnly)>
                        Reported only
                    </label>
                    <button type="submit" class="dash-btn dash-btn--primary">Filter</button>
                    @if ($search || $status || $media || $reportedOnly)
                        <a href="{{ route('admin.posts.index') }}" class="dash-btn dash-btn--ghost">Clear</a>
                    @endif
                </form>

                <div class="dash-table-wrap dash-card">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Post</th>
                                <th>Author</th>
                                <th>Engagement</th>
                                <th>Status</th>
                                <th>Posted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($posts as $post)
                                <tr>
                                    <td>
                                        <div class="dash-post-preview">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 140) ?: '—' }}
                                            <div class="dash-post-preview__meta">
                                                @if ($post->has_images || $post->images_count)
                                                    <span class="dash-badge dash-badge--gray">Images</span>
                                                @endif
                                                @if ($post->has_video || $post->video_exists)
                                                    <span class="dash-badge dash-badge--indigo">Video</span>
                                                @endif
                                                @if (($post->reports_count ?? 0) > 0)
                                                    <span class="dash-badge dash-badge--danger">{{ $post->reports_count }} reports</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($post->user)
                                            <div class="dash-author">
                                                <img src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                                <div>
                                                    <a href="{{ route('admin.users.show', $post->user) }}" class="dash-author__name">
                                                        {{ displayName($post->user->name) }}
                                                    </a>
                                                    <div class="dash-muted" style="font-size:.75rem">{{ '@'.$post->user->username }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="dash-muted">Deleted user</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dash-muted" style="font-size:.8rem;line-height:1.5">
                                            <div><strong>{{ number_format((int) sumCounter($post->likes, $post->likes_external)) }}</strong> likes</div>
                                            <div><strong>{{ number_format((int) sumCounter($post->views, $post->views_external)) }}</strong> views</div>
                                            <div><strong>{{ number_format((int) sumCounter($post->comments, $post->comment_external)) }}</strong> comments</div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badge = match ($post->status) {
                                                'LIVE' => 'dash-badge--success',
                                                'HIDDEN' => 'dash-badge--warn',
                                                'SHADOW_BANNED' => 'dash-badge--danger',
                                                default => 'dash-badge--gray',
                                            };
                                        @endphp
                                        <span class="dash-badge {{ $badge }}">{{ str_replace('_', ' ', $post->status) }}</span>
                                    </td>
                                    <td class="dash-muted">
                                        {{ $post->created_at?->format('M j, Y') }}
                                        <span style="display:block;font-size:.75rem">{{ $post->created_at?->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <div class="dash-actions">
                                            <a href="{{ route('admin.posts.show', $post) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Review</a>
                                            @if ($post->status === 'HIDDEN')
                                                <form method="post" action="{{ route('admin.posts.unhide', $post) }}">
                                                    @csrf
                                                    <button type="submit" class="dash-btn dash-btn--primary dash-btn--sm">Unhide</button>
                                                </form>
                                            @else
                                                <form method="post" action="{{ route('admin.posts.hide', $post) }}"
                                                    onsubmit="return confirm('Hide this post from public feeds?')">
                                                    @csrf
                                                    <button type="submit" class="dash-btn dash-btn--ghost dash-btn--sm">Hide</button>
                                                </form>
                                            @endif
                                            <form method="post" action="{{ route('admin.posts.destroy', $post) }}"
                                                onsubmit="return confirm('Delete this post permanently? All accrued earnings for it will be removed.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dash-btn dash-btn--danger dash-btn--sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="dash-empty">No timeline posts match these filters.</div>
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
            </section>
        </div>
    </div>
@endsection
