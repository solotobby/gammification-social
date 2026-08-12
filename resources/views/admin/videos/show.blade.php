@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-btn--danger { background:#b42318; color:#fff; border:none; }
        .dash-reason { width:100%; min-height:70px; }
        .dash-badge--danger { background: rgba(220,53,69,.12); color:#b42318; }
        .dash-badge--success { background: rgba(16,185,129,.12); color:#067647; }
        .dash-badge--warn { background: rgba(245,158,11,.14); color:#b54708; }
        .dash-stack { display:grid; gap:1rem; }
        video.dash-player { width:100%; max-height:420px; background:#000; border-radius:12px; }
        @media (max-width:960px){ .dash-mod-grid{grid-template-columns:1fr!important;} }
    </style>
@endsection

@section('content')
@php
    $badge = match ($video->processing_status) {
        'completed' => 'dash-badge--success',
        'failed' => 'dash-badge--danger',
        default => 'dash-badge--warn',
    };
@endphp
<div class="content p-0"><div class="dash">
    <header class="dash-header">
        <div>
            <h1>Video review</h1>
            <p><span class="dash-badge {{ $badge }}">{{ $video->processing_status }}</span>
                · post {{ $video->post?->status ?? 'missing' }}</p>
        </div>
        <a href="{{ route('admin.videos.index') }}" class="dash-btn dash-btn--ghost"><i class="fa fa-arrow-left"></i> All videos</a>
    </header>

    @if (session('success'))<div class="dash-alert dash-alert--success">{{ session('success') }}</div>@endif

    <div class="dash-mod-grid" style="display:grid;grid-template-columns:1.6fr 1fr;gap:1rem;align-items:start">
        <section class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Playback</h2></div>
            <div class="dash-card__body">
                @if ($video->path)
                    <video class="dash-player" src="{{ $video->path }}" controls playsinline poster="{{ $video->thumbnail_path }}"></video>
                @else
                    <div class="dash-empty">No playable path yet.</div>
                @endif
                <div class="dash-muted" style="margin-top:1rem;font-size:.85rem;line-height:1.6">
                    <div><strong>Public ID:</strong> {{ $video->public_id ?: '—' }}</div>
                    <div><strong>Path:</strong> {{ $video->path ?: '—' }}</div>
                    <div><strong>Duration:</strong> {{ $video->duration ? gmdate('i:s', $video->duration) : '—' }}</div>
                    <div><strong>Plays / views:</strong> {{ number_format($video->play_count) }} / {{ number_format($video->view_count) }}</div>
                    <div><strong>Avg watch:</strong> {{ number_format((float) $video->avg_watch_time, 1) }}s</div>
                    @if ($video->post)
                        <div><strong>Caption:</strong> {{ \Illuminate\Support\Str::limit(strip_tags($video->post->content), 200) ?: '—' }}</div>
                        <div style="margin-top:.5rem">
                            <a href="{{ route('admin.posts.show', $video->post) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Open post</a>
                            @if ($video->user)
                                <a href="{{ route('admin.users.show', $video->user) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Owner</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <aside class="dash-stack">
            <section class="dash-card">
                <div class="dash-card__head"><h2 class="dash-card__title">Processing</h2></div>
                <div class="dash-card__body">
                    <form method="post" action="{{ route('admin.videos.mark-failed', $video) }}" style="margin-bottom:.75rem">
                        @csrf
                        <textarea name="reason" class="dash-input dash-reason" placeholder="Mark failed reason (optional)"></textarea>
                        <button class="dash-btn dash-btn--ghost" style="width:100%;margin-top:.65rem" type="submit">Mark failed</button>
                    </form>
                    <form method="post" action="{{ route('admin.videos.mark-completed', $video) }}">
                        @csrf
                        <textarea name="reason" class="dash-input dash-reason" placeholder="Force completed reason (optional)"></textarea>
                        <button class="dash-btn dash-btn--primary" style="width:100%;margin-top:.65rem" type="submit">Mark completed</button>
                    </form>
                </div>
            </section>

            <section class="dash-card">
                <div class="dash-card__head"><h2 class="dash-card__title">Takedown</h2></div>
                <div class="dash-card__body">
                    @if ($video->post)
                        <form method="post" action="{{ route('admin.videos.hide', $video) }}" style="margin-bottom:.75rem"
                            onsubmit="return confirm('Hide this video post from feeds and rolls?')">
                            @csrf
                            <textarea name="reason" class="dash-input dash-reason" placeholder="Hide reason (optional)"></textarea>
                            <button class="dash-btn dash-btn--ghost" style="width:100%;margin-top:.65rem" type="submit">Hide post</button>
                        </form>
                        <form method="post" action="{{ route('admin.videos.destroy', $video) }}"
                            onsubmit="return confirm('Delete this video post permanently? Monetization will be removed.')">
                            @csrf
                            @method('DELETE')
                            <textarea name="reason" class="dash-input dash-reason" placeholder="Delete reason (optional)"></textarea>
                            <button class="dash-btn dash-btn--danger" style="width:100%;margin-top:.65rem" type="submit">Delete post</button>
                        </form>
                    @else
                        <p class="dash-muted" style="margin:0">No linked post to take down.</p>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div></div>
@endsection
