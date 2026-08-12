@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-author { display: flex; align-items: center; gap: .75rem; }
        .dash-author img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
        .dash-author__name { font-weight: 700; color: inherit; text-decoration: none; }
        .dash-author__name:hover { text-decoration: underline; }
        .dash-post-body {
            white-space: pre-wrap;
            word-break: break-word;
            font-size: .95rem;
            line-height: 1.55;
            margin: 0;
        }
        .dash-media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: .75rem;
            margin-top: 1rem;
        }
        .dash-media-grid img,
        .dash-media-grid video {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 10px;
            background: #0f1117;
        }
        .dash-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
        .dash-badge--danger { background: rgba(220, 53, 69, .12); color: #b42318; }
        .dash-badge--success { background: rgba(16, 185, 129, .12); color: #067647; }
        .dash-badge--warn { background: rgba(245, 158, 11, .14); color: #b54708; }
        .dash-btn--sm { padding: .35rem .65rem; font-size: .78rem; }
        .dash-btn--danger { background: #b42318; color: #fff; border: none; }
        .dash-btn--danger:hover { filter: brightness(1.05); }
        .dash-reason { width: 100%; min-height: 72px; }
        @media (max-width: 960px) {
            .dash-grid[style*="2fr 1fr"] { grid-template-columns: 1fr !important; }
        }
    </style>
@endsection

@section('content')
    @php
        $badge = match ($post->status) {
            'LIVE' => 'dash-badge--success',
            'HIDDEN' => 'dash-badge--warn',
            'SHADOW_BANNED' => 'dash-badge--danger',
            default => 'dash-badge--gray',
        };
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Review post</h1>
                    <p>Moderate timeline content and remove monetization if needed</p>
                </div>
                <a href="{{ route('admin.posts.index') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> All posts
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
                        <span class="dash-kpi__label">Likes</span>
                        <div class="dash-kpi__value">{{ number_format((int) sumCounter($post->likes, $post->likes_external)) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Views</span>
                        <div class="dash-kpi__value">{{ number_format((int) sumCounter($post->views, $post->views_external)) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Comments</span>
                        <div class="dash-kpi__value">{{ number_format((int) sumCounter($post->comments, $post->comment_external)) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Est. earnings (30d)</span>
                        <div class="dash-kpi__value">{{ getCurrencyCode() }}{{ number_format($estimatedEarnings, 2) }}</div>
                    </div>
                </div>
            </section>

            <div class="dash-grid" style="grid-template-columns: 2fr 1fr; gap: 1rem; align-items: start;">
                <section class="dash-card">
                    <div class="dash-card__head" style="display:flex;justify-content:space-between;gap:1rem;align-items:center">
                        <div>
                            <h2 class="dash-card__title">Content</h2>
                            <p class="dash-muted" style="margin:.25rem 0 0">
                                Posted {{ $post->created_at?->format('M j, Y g:i A') }}
                                · <span class="dash-badge {{ $badge }}">{{ str_replace('_', ' ', $post->status) }}</span>
                            </p>
                        </div>
                        <a href="{{ url('timeline/'.$post->id) }}" target="_blank" class="dash-btn dash-btn--ghost dash-btn--sm">
                            Open public
                        </a>
                    </div>
                    <div class="dash-card__body">
                        @if ($post->user)
                            <div class="dash-author" style="margin-bottom:1rem">
                                <img src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                <div>
                                    <a href="{{ route('admin.users.show', $post->user) }}" class="dash-author__name">
                                        {{ displayName($post->user->name) }}
                                    </a>
                                    <div class="dash-muted">{{ '@'.$post->user->username }} · {{ $post->user->email }}</div>
                                </div>
                            </div>
                        @endif

                        <p class="dash-post-body">{{ strip_tags($post->content) ?: '—' }}</p>

                        @if ($post->trends->isNotEmpty())
                            <div style="margin-top:.85rem;display:flex;flex-wrap:wrap;gap:.35rem">
                                @foreach ($post->trends as $trend)
                                    <span class="dash-badge dash-badge--gray">#{{ $trend->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if ($post->images->isNotEmpty() || $post->video)
                            <div class="dash-media-grid">
                                @foreach ($post->images as $image)
                                    <a href="{{ $image->path }}" target="_blank" rel="noopener">
                                        <img src="{{ $image->path }}" alt="Post image">
                                    </a>
                                @endforeach
                                @if ($post->video)
                                    <video src="{{ $post->video->path }}" controls playsinline
                                        poster="{{ $post->video->thumbnail_path }}"></video>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>

                <aside style="display:grid;gap:1rem">
                    <section class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">Moderation</h2>
                        </div>
                        <div class="dash-card__body">
                            <p class="dash-muted" style="margin-top:0">
                                Hide removes the post from public feeds. Delete permanently removes the post and its accrued monetization.
                            </p>

                            @if ($post->status === 'HIDDEN')
                                <form method="post" action="{{ route('admin.posts.unhide', $post) }}" style="margin-bottom:.75rem">
                                    @csrf
                                    <button type="submit" class="dash-btn dash-btn--primary" style="width:100%">Unhide post</button>
                                </form>
                            @else
                                <form method="post" action="{{ route('admin.posts.hide', $post) }}" style="margin-bottom:.75rem"
                                    onsubmit="return confirm('Hide this post from public feeds?')">
                                    @csrf
                                    <label class="dash-muted" style="display:block;margin-bottom:.35rem;font-size:.8rem">Reason (optional)</label>
                                    <textarea name="reason" class="dash-input dash-reason" placeholder="Why is this being hidden?"></textarea>
                                    <button type="submit" class="dash-btn dash-btn--ghost" style="width:100%;margin-top:.65rem">Hide post</button>
                                </form>
                            @endif

                            <form method="post" action="{{ route('admin.posts.destroy', $post) }}"
                                onsubmit="return confirm('Delete this post permanently? All accrued earnings for it will be removed. This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <label class="dash-muted" style="display:block;margin-bottom:.35rem;font-size:.8rem">Reason (optional)</label>
                                <textarea name="reason" class="dash-input dash-reason" placeholder="Why is this being deleted?"></textarea>
                                <button type="submit" class="dash-btn dash-btn--danger" style="width:100%;margin-top:.65rem">Delete permanently</button>
                            </form>
                        </div>
                    </section>

                    <section class="dash-card">
                        <div class="dash-card__head" style="display:flex;justify-content:space-between;gap:.75rem;align-items:center">
                            <h2 class="dash-card__title">Reports ({{ $post->reports_count }})</h2>
                            @if ($post->reports_count > 0)
                                <a href="{{ route('admin.reports.show', $post) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Open queue</a>
                            @endif
                        </div>
                        <div class="dash-card__body dash-card__body--flush">
                            @forelse ($post->reports as $report)
                                <div style="padding:0.85rem 1rem;border-bottom:1px solid var(--dash-border)">
                                    <div style="font-weight:600">
                                        {{ $report->user ? displayName($report->user->name) : 'Unknown user' }}
                                    </div>
                                    <div class="dash-muted" style="font-size:.8rem">
                                        {{ $report->user->username ?? '—' }} · {{ $report->created_at?->diffForHumans() }}
                                    </div>
                                    @if ($report->reason)
                                        <div style="margin-top:.35rem;font-size:.875rem">{{ $report->reason }}</div>
                                    @endif
                                </div>
                            @empty
                                <div class="dash-empty">No user reports on this post.</div>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
@endsection
