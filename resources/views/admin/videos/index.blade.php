@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-author { display:flex; align-items:center; gap:.65rem; }
        .dash-author img { width:36px; height:36px; border-radius:50%; object-fit:cover; }
        .dash-author__name { font-weight:600; color:inherit; text-decoration:none; }
        .dash-badge--danger { background: rgba(220,53,69,.12); color:#b42318; }
        .dash-badge--success { background: rgba(16,185,129,.12); color:#067647; }
        .dash-badge--warn { background: rgba(245,158,11,.14); color:#b54708; }
        .dash-btn--sm { padding:.35rem .65rem; font-size:.78rem; }
        .dash-thumb { width:72px; height:72px; object-fit:cover; border-radius:8px; background:#111; }
    </style>
@endsection

@section('content')
<div class="content p-0"><div class="dash">
    <header class="dash-header">
        <div>
            <h1>Rolls / Videos</h1>
            <p>Monitor processing failures, stuck uploads, and take down video posts</p>
        </div>
    </header>

    @if (session('success'))<div class="dash-alert dash-alert--success">{{ session('success') }}</div>@endif

    <section class="dash-section">
        <div class="dash-grid dash-grid--4">
            <div class="dash-kpi"><span class="dash-kpi__label">Total</span><div class="dash-kpi__value">{{ number_format($stats['total']) }}</div></div>
            <div class="dash-kpi"><span class="dash-kpi__label">Completed</span><div class="dash-kpi__value">{{ number_format($stats['completed']) }}</div></div>
            <div class="dash-kpi"><span class="dash-kpi__label">Failed</span><div class="dash-kpi__value">{{ number_format($stats['failed']) }}</div><div class="dash-muted">{{ number_format($stats['processing']) }} processing</div></div>
            <div class="dash-kpi"><span class="dash-kpi__label">Anomalies</span><div class="dash-kpi__value">{{ number_format($stats['anomalies']) }}</div><div class="dash-muted">{{ number_format($stats['stuck']) }} stuck &gt;6h</div></div>
        </div>
    </section>

    <section class="dash-section">
        <form method="get" class="dash-toolbar">
            <input type="search" name="q" value="{{ $search }}" class="dash-input" placeholder="Search user, public_id, video/post id">
            <select name="status" class="dash-input" style="flex:0 0 150px">
                <option value="">All statuses</option>
                @foreach (['processing','completed','failed'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            <select name="filter" class="dash-input" style="flex:0 0 150px">
                <option value="">No quick filter</option>
                <option value="failed" @selected($filter === 'failed')>Failed only</option>
                <option value="stuck" @selected($filter === 'stuck')>Stuck processing</option>
                <option value="anomalies" @selected($filter === 'anomalies')>All anomalies</option>
            </select>
            <button class="dash-btn dash-btn--primary" type="submit">Filter</button>
            @if ($search || $status || $filter)
                <a href="{{ route('admin.videos.index') }}" class="dash-btn dash-btn--ghost">Clear</a>
            @endif
        </form>

        <div class="dash-table-wrap dash-card">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Video</th>
                        <th>Owner</th>
                        <th>Processing</th>
                        <th>Post</th>
                        <th>Plays / Views</th>
                        <th>Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($videos as $video)
                        @php
                            $badge = match ($video->processing_status) {
                                'completed' => 'dash-badge--success',
                                'failed' => 'dash-badge--danger',
                                default => 'dash-badge--warn',
                            };
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex;gap:.65rem;align-items:center">
                                    <img class="dash-thumb" src="{{ $video->thumbnail_path ?: asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                    <div>
                                        <div style="font-weight:600;font-size:.85rem">{{ \Illuminate\Support\Str::limit($video->public_id ?: $video->id, 28) }}</div>
                                        <div class="dash-muted" style="font-size:.75rem">{{ $video->duration ? gmdate('i:s', $video->duration) : '—' }} · {{ $video->format ?: 'n/a' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($video->user)
                                    <div class="dash-author">
                                        <img src="{{ $video->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                        <div>
                                            <a class="dash-author__name" href="{{ route('admin.users.show', $video->user) }}">{{ displayName($video->user->name) }}</a>
                                            <div class="dash-muted" style="font-size:.75rem">{{ '@'.$video->user->username }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="dash-muted">—</span>
                                @endif
                            </td>
                            <td><span class="dash-badge {{ $badge }}">{{ $video->processing_status }}</span></td>
                            <td>
                                @if ($video->post)
                                    <span class="dash-badge dash-badge--gray">{{ $video->post->status }}</span>
                                    <div class="dash-muted" style="font-size:.75rem;margin-top:.25rem">{{ \Illuminate\Support\Str::limit(strip_tags($video->post->content), 60) }}</div>
                                @else
                                    <span class="dash-muted">No post</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ number_format($video->play_count) }}</strong> plays
                                <div class="dash-muted" style="font-size:.75rem">{{ number_format($video->view_count) }} views</div>
                            </td>
                            <td class="dash-muted">{{ $video->updated_at?->diffForHumans() }}</td>
                            <td><a href="{{ route('admin.videos.show', $video) }}" class="dash-btn dash-btn--primary dash-btn--sm">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="dash-empty">No videos match these filters.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($videos->hasPages())
            <div class="dash-pagination">{{ $videos->links('pagination::bootstrap-5') }}</div>
        @endif
    </section>
</div></div>
@endsection
