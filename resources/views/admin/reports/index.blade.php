@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-post-preview { max-width: 380px; font-size: .875rem; line-height: 1.45; }
        .dash-author { display: flex; align-items: center; gap: .65rem; }
        .dash-author img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .dash-author__name { font-weight: 600; color: inherit; text-decoration: none; }
        .dash-author__name:hover { text-decoration: underline; }
        .dash-badge--danger { background: rgba(220, 53, 69, .12); color: #b42318; }
        .dash-badge--success { background: rgba(16, 185, 129, .12); color: #067647; }
        .dash-badge--warn { background: rgba(245, 158, 11, .14); color: #b54708; }
        .dash-btn--sm { padding: .35rem .65rem; font-size: .78rem; }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Post Reports</h1>
                    <p>Review user-reported timeline posts and take moderation action</p>
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
                        <span class="dash-kpi__label">Pending reports</span>
                        <div class="dash-kpi__value">{{ number_format($stats['pending_reports']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Posts in queue</span>
                        <div class="dash-kpi__value">{{ number_format($stats['pending_posts']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Resolved today</span>
                        <div class="dash-kpi__value">{{ number_format($stats['resolved_today']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">All-time resolved</span>
                        <div class="dash-kpi__value">{{ number_format($stats['total_resolved']) }}</div>
                    </div>
                </div>
            </section>

            <section class="dash-section">
                <form method="get" class="dash-toolbar">
                    <input type="search" name="q" value="{{ $search }}" class="dash-input"
                        placeholder="Search post, author, or reporter">
                    <button type="submit" class="dash-btn dash-btn--primary">Search</button>
                    @if ($search)
                        <a href="{{ route('admin.reports.index') }}" class="dash-btn dash-btn--ghost">Clear</a>
                    @endif
                </form>

                <div class="dash-table-wrap dash-card">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Post</th>
                                <th>Author</th>
                                <th>Reports</th>
                                <th>Status</th>
                                <th>Latest report</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($posts as $post)
                                @php
                                    $badge = match ($post->status) {
                                        'LIVE' => 'dash-badge--success',
                                        'HIDDEN' => 'dash-badge--warn',
                                        'SHADOW_BANNED' => 'dash-badge--danger',
                                        default => 'dash-badge--gray',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="dash-post-preview">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 140) ?: '—' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($post->user)
                                            <div class="dash-author">
                                                <img src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                                <div>
                                                    <a class="dash-author__name" href="{{ route('admin.users.show', $post->user) }}">
                                                        {{ displayName($post->user->name) }}
                                                    </a>
                                                    <div class="dash-muted" style="font-size:.75rem">
                                                        {{ '@'.$post->user->username }}
                                                        · {{ $post->user->status }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="dash-muted">Deleted user</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="dash-badge dash-badge--danger">
                                            {{ number_format($post->pending_reports_count) }} pending
                                        </span>
                                        <div class="dash-muted" style="font-size:.75rem;margin-top:.25rem">
                                            {{ number_format($post->total_reports_count) }} total
                                        </div>
                                    </td>
                                    <td>
                                        <span class="dash-badge {{ $badge }}">{{ str_replace('_', ' ', $post->status) }}</span>
                                    </td>
                                    <td class="dash-muted">
                                        {{ $post->latest_report_at ? \Carbon\Carbon::parse($post->latest_report_at)->diffForHumans() : '—' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.reports.show', $post) }}" class="dash-btn dash-btn--primary dash-btn--sm">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="dash-empty">No pending post reports. Queue is clear.</div>
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
