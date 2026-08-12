@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-author { display: flex; align-items: center; gap: .75rem; }
        .dash-author img { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
        .dash-author__name { font-weight: 700; color: inherit; text-decoration: none; }
        .dash-author__name:hover { text-decoration: underline; }
        .dash-post-body { white-space: pre-wrap; word-break: break-word; font-size: .95rem; line-height: 1.55; margin: 0; }
        .dash-media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: .65rem;
            margin-top: 1rem;
        }
        .dash-media-grid img, .dash-media-grid video {
            width: 100%; height: 120px; object-fit: cover; border-radius: 10px; background: #0f1117;
        }
        .dash-badge--danger { background: rgba(220, 53, 69, .12); color: #b42318; }
        .dash-badge--success { background: rgba(16, 185, 129, .12); color: #067647; }
        .dash-badge--warn { background: rgba(245, 158, 11, .14); color: #b54708; }
        .dash-btn--danger { background: #b42318; color: #fff; border: none; }
        .dash-btn--sm { padding: .35rem .65rem; font-size: .78rem; }
        .dash-reason { width: 100%; min-height: 70px; }
        .dash-report-row { padding: .85rem 1rem; border-bottom: 1px solid var(--dash-border); }
        .dash-stack { display: grid; gap: 1rem; }
        @media (max-width: 960px) {
            .dash-mod-grid { grid-template-columns: 1fr !important; }
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
                    <h1>Report review</h1>
                    <p>{{ $pendingReports->count() }} pending · {{ $resolvedReports->count() }} recent resolved</p>
                </div>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap">
                    <a href="{{ route('admin.reports.index') }}" class="dash-btn dash-btn--ghost">
                        <i class="fa fa-arrow-left"></i> Queue
                    </a>
                    <a href="{{ route('admin.posts.show', $post) }}" class="dash-btn dash-btn--ghost">Open in posts</a>
                </div>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <div class="dash-mod-grid" style="display:grid;grid-template-columns:1.6fr 1fr;gap:1rem;align-items:start">
                <div class="dash-stack">
                    <section class="dash-card">
                        <div class="dash-card__head" style="display:flex;justify-content:space-between;gap:1rem;align-items:center">
                            <div>
                                <h2 class="dash-card__title">Reported post</h2>
                                <p class="dash-muted" style="margin:.25rem 0 0">
                                    {{ $post->created_at?->format('M j, Y g:i A') }}
                                    · <span class="dash-badge {{ $badge }}">{{ str_replace('_', ' ', $post->status) }}</span>
                                    · Est. {{ getCurrencyCode() }}{{ number_format($estimatedEarnings, 2) }}
                                </p>
                            </div>
                            <a href="{{ url('timeline/'.$post->id) }}" target="_blank" class="dash-btn dash-btn--ghost dash-btn--sm">Public view</a>
                        </div>
                        <div class="dash-card__body">
                            @if ($post->user)
                                <div class="dash-author" style="margin-bottom:1rem">
                                    <img src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                    <div>
                                        <a href="{{ route('admin.users.show', $post->user) }}" class="dash-author__name">
                                            {{ displayName($post->user->name) }}
                                        </a>
                                        <div class="dash-muted">
                                            {{ '@'.$post->user->username }} · {{ $post->user->email }}
                                            · author status: <strong>{{ $post->user->status }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <p class="dash-post-body">{{ strip_tags($post->content) ?: '—' }}</p>

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

                    <section class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">Pending reports ({{ $pendingReports->count() }})</h2>
                        </div>
                        <div class="dash-card__body dash-card__body--flush">
                            @forelse ($pendingReports as $report)
                                <div class="dash-report-row">
                                    <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start">
                                        <div>
                                            <div style="font-weight:600">
                                                {{ $report->user ? displayName($report->user->name) : 'Unknown reporter' }}
                                            </div>
                                            <div class="dash-muted" style="font-size:.8rem">
                                                {{ $report->user ? '@'.$report->user->username : '—' }}
                                                · {{ $report->created_at?->diffForHumans() }}
                                            </div>
                                            @if ($report->reason)
                                                <div style="margin-top:.35rem;font-size:.875rem">{{ $report->reason }}</div>
                                            @else
                                                <div class="dash-muted" style="margin-top:.35rem;font-size:.875rem">No reason provided</div>
                                            @endif
                                        </div>
                                        <form method="post" action="{{ route('admin.reports.dismiss', $report) }}">
                                            @csrf
                                            <button type="submit" class="dash-btn dash-btn--ghost dash-btn--sm">Dismiss</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="dash-empty">No pending reports on this post.</div>
                            @endforelse
                        </div>
                    </section>

                    @if ($resolvedReports->isNotEmpty())
                        <section class="dash-card">
                            <div class="dash-card__head">
                                <h2 class="dash-card__title">Recently resolved</h2>
                            </div>
                            <div class="dash-card__body dash-card__body--flush">
                                @foreach ($resolvedReports as $report)
                                    <div class="dash-report-row">
                                        <div style="font-weight:600">
                                            {{ $report->user ? displayName($report->user->name) : 'Unknown reporter' }}
                                        </div>
                                        <div class="dash-muted" style="font-size:.8rem">
                                            {{ str_replace('_', ' ', $report->resolution ?? 'resolved') }}
                                            · {{ $report->reviewed_at?->diffForHumans() }}
                                            @if ($report->reviewer)
                                                · by {{ displayName($report->reviewer->name) }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="dash-stack">
                    <section class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">Dismiss reports</h2>
                        </div>
                        <div class="dash-card__body">
                            <form method="post" action="{{ route('admin.reports.dismiss-all', $post) }}">
                                @csrf
                                <label class="dash-muted" style="display:block;margin-bottom:.35rem;font-size:.8rem">Note (optional)</label>
                                <textarea name="note" class="dash-input dash-reason" placeholder="Why are these reports being dismissed?"></textarea>
                                <button type="submit" class="dash-btn dash-btn--ghost" style="width:100%;margin-top:.65rem"
                                    @disabled($pendingReports->isEmpty())>
                                    Dismiss all pending
                                </button>
                            </form>
                        </div>
                    </section>

                    <section class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">Post actions</h2>
                        </div>
                        <div class="dash-card__body">
                            @if ($post->status !== 'HIDDEN')
                                <form method="post" action="{{ route('admin.reports.hide-post', $post) }}" style="margin-bottom:.75rem"
                                    onsubmit="return confirm('Hide this post and resolve pending reports?')">
                                    @csrf
                                    <label class="dash-muted" style="display:block;margin-bottom:.35rem;font-size:.8rem">Reason (optional)</label>
                                    <textarea name="reason" class="dash-input dash-reason" placeholder="Why is this being hidden?"></textarea>
                                    <button type="submit" class="dash-btn dash-btn--primary" style="width:100%;margin-top:.65rem">
                                        Hide post
                                    </button>
                                </form>
                            @else
                                <p class="dash-muted" style="margin-top:0">This post is already hidden.</p>
                            @endif

                            <form method="post" action="{{ route('admin.reports.destroy-post', $post) }}"
                                onsubmit="return confirm('Delete this post permanently? Monetization will be removed and reports cleared.')">
                                @csrf
                                @method('DELETE')
                                <label class="dash-muted" style="display:block;margin-bottom:.35rem;font-size:.8rem">Reason (optional)</label>
                                <textarea name="reason" class="dash-input dash-reason" placeholder="Why is this being deleted?"></textarea>
                                <button type="submit" class="dash-btn dash-btn--danger" style="width:100%;margin-top:.65rem">
                                    Delete post
                                </button>
                            </form>
                        </div>
                    </section>

                    <section class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">Action author</h2>
                        </div>
                        <div class="dash-card__body">
                            <form method="post" action="{{ route('admin.reports.action-author', $post) }}"
                                onsubmit="return confirm('Update this author\'s account status and resolve pending reports?')">
                                @csrf
                                <label class="dash-muted" style="display:block;margin-bottom:.35rem;font-size:.8rem">Status</label>
                                <select name="status" class="dash-input" required>
                                    <option value="SHADOW_BANNED">Shadow ban</option>
                                    <option value="BLOCKED">Block</option>
                                    <option value="ACTIVE">Restore to active</option>
                                </select>

                                <label class="dash-muted" style="display:block;margin:0.75rem 0 .35rem;font-size:.8rem">Reason (optional)</label>
                                <textarea name="reason" class="dash-input dash-reason" placeholder="Why is this author being actioned?"></textarea>

                                <label class="dash-muted" style="display:flex;align-items:center;gap:.45rem;margin-top:.75rem;font-size:.85rem">
                                    <input type="hidden" name="hide_post" value="0">
                                    <input type="checkbox" name="hide_post" value="1" checked>
                                    Also hide this post
                                </label>

                                <button type="submit" class="dash-btn dash-btn--primary" style="width:100%;margin-top:.85rem">
                                    Apply author action
                                </button>
                            </form>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
@endsection
