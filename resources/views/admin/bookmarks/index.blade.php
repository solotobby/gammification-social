@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-author { display:flex; align-items:center; gap:.65rem; }
        .dash-author img { width:36px; height:36px; border-radius:50%; object-fit:cover; }
        .dash-author__name { font-weight:600; color:inherit; text-decoration:none; }
        .dash-badge--success { background: rgba(16,185,129,.12); color:#067647; }
        .dash-badge--warn { background: rgba(245,158,11,.14); color:#b54708; }
        .dash-trend { display:flex; gap:.35rem; align-items:flex-end; height:72px; }
        .dash-trend span { flex:1; background: rgba(90,79,220,.18); border-radius:4px 4px 0 0; min-width:8px; }
    </style>
@endsection

@section('content')
@php $maxTrend = max(1, (int) $dailyTrend->max('total')); @endphp
<div class="content p-0"><div class="dash">
    <header class="dash-header">
        <div>
            <h1>Bookmark analytics</h1>
            <p>Growth signal only — {{ $dateRange->label() }}</p>
        </div>
    </header>

    @include('admin.partials.date-range-filter', ['routeName' => 'admin.bookmarks.index'])

    <section class="dash-section">
        <div class="dash-grid dash-grid--4">
            <div class="dash-kpi"><span class="dash-kpi__label">Bookmarks</span><div class="dash-kpi__value">{{ number_format($stats['total_bookmarks']) }}</div></div>
            <div class="dash-kpi"><span class="dash-kpi__label">Unique posts</span><div class="dash-kpi__value">{{ number_format($stats['unique_posts']) }}</div></div>
            <div class="dash-kpi"><span class="dash-kpi__label">Unique users</span><div class="dash-kpi__value">{{ number_format($stats['unique_users']) }}</div></div>
            <div class="dash-kpi"><span class="dash-kpi__label">Avg / day</span><div class="dash-kpi__value">{{ number_format($stats['avg_per_day'], 1) }}</div></div>
        </div>
    </section>

    <section class="dash-section">
        <div class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Daily trend · {{ $dateRange->label() }}</h2></div>
            <div class="dash-card__body">
                @if ($dailyTrend->isEmpty())
                    <div class="dash-empty">No bookmark activity in this window.</div>
                @else
                    <div class="dash-trend">
                        @foreach ($dailyTrend as $row)
                            <span title="{{ $row->day }}: {{ $row->total }}" style="height:{{ max(8, ($row->total / $maxTrend) * 100) }}%"></span>
                        @endforeach
                    </div>
                    <div class="dash-muted" style="margin-top:.5rem;font-size:.75rem;display:flex;justify-content:space-between">
                        <span>{{ $dailyTrend->first()->day }}</span>
                        <span>{{ $dailyTrend->last()->day }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:1rem;align-items:start" class="dash-section">
        <section class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Most bookmarked posts</h2></div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>Post</th><th>Author</th><th>Bookmarks</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($topPosts as $post)
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 80) ?: '—' }}</td>
                                    <td class="dash-muted">{{ $post->user ? '@'.$post->user->username : '—' }}</td>
                                    <td><strong>{{ number_format($post->bookmarks_count) }}</strong></td>
                                    <td><span class="dash-badge {{ $post->status === 'LIVE' ? 'dash-badge--success' : 'dash-badge--warn' }}">{{ $post->status }}</span></td>
                                    <td><a href="{{ route('admin.posts.show', $post) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5"><div class="dash-empty">No bookmarks yet.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Top bookmarking users</h2></div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>User</th><th>Saved</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($topUsers as $user)
                                <tr>
                                    <td>
                                        <div class="dash-author">
                                            <img src="{{ $user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                            <div>
                                                <div style="font-weight:600">{{ displayName($user->name) }}</div>
                                                <div class="dash-muted" style="font-size:.75rem">{{ '@'.$user->username }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong>{{ number_format($user->bookmarks_count) }}</strong></td>
                                    <td><a href="{{ route('admin.users.show', $user) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Profile</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="3"><div class="dash-empty">No users yet.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <section class="dash-section">
        <div class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Recent bookmarks</h2></div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>User</th><th>Post</th><th>When</th></tr></thead>
                        <tbody>
                            @forelse ($recent as $bookmark)
                                <tr>
                                    <td class="dash-muted">{{ $bookmark->user ? '@'.$bookmark->user->username : '—' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($bookmark->post?->content), 90) ?: '—' }}</td>
                                    <td class="dash-muted">{{ $bookmark->created_at?->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3"><div class="dash-empty">No recent bookmarks.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($recent->hasPages())
                    <div class="dash-pagination">{{ $recent->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>
    </section>
</div></div>
@endsection
