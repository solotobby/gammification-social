{{--
    livewire/user/post-content.blade.php

    Renders a single post card in the feed.
    Handles: text · images (1-4) · video (thumbnail + play → video page)
--}}

<div>
<style>
/* ── Post card ─────────────────────────────────────────────── */
.post-card {
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e4e6eb;
    margin-bottom: 12px;
    overflow: hidden;
}

/* ── Header ────────────────────────────────────────────────── */
.post-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px 8px;
}
.post-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}
.post-meta { flex: 1; min-width: 0; }
.post-author {
    font-weight: 700;
    font-size: 14px;
    color: #050505;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
}
.post-author:hover { text-decoration: underline; color: #050505; }
.post-time {
    font-size: 12px;
    color: #65676b;
    margin-top: 1px;
}
.badge-creator {
    display: inline-flex; align-items: center;
    background: #e7f3ff; color: #1877f2;
    font-size: 10px; font-weight: 700;
    padding: 1px 6px; border-radius: 99px;
    letter-spacing: .04em; text-transform: uppercase;
}
.badge-influencer {
    display: inline-flex; align-items: center;
    background: #f0e6ff; color: #7c3aed;
    font-size: 10px; font-weight: 700;
    padding: 1px 6px; border-radius: 99px;
    letter-spacing: .04em; text-transform: uppercase;
}

/* ── Post text ─────────────────────────────────────────────── */
.post-text {
    padding: 0 16px 10px;
    font-size: 15px;
    line-height: 1.5;
    color: #050505;
    white-space: pre-wrap;
    word-break: break-word;
}
.post-text a { color: #1877f2; text-decoration: none; }
.post-text a:hover { text-decoration: underline; }
.see-more-btn {
    background: none; border: none; padding: 0;
    color: #65676b; font-size: 14px; font-weight: 600;
    cursor: pointer; font-family: inherit;
}

/* ── Image grid ────────────────────────────────────────────── */
.post-images { padding: 0; }

.img-grid {
    display: grid;
    gap: 2px;
    max-height: 500px;
    overflow: hidden;
}
/* 1 image: full width, fixed height */
.img-grid.count-1 {
    grid-template-columns: 1fr;
}
.img-grid.count-1 .img-cell { height: 380px; }

/* 2 images: side by side */
.img-grid.count-2 {
    grid-template-columns: 1fr 1fr;
}
.img-grid.count-2 .img-cell { height: 300px; }

/* 3 images: first full width, two below */
.img-grid.count-3 {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 250px 250px;
}
.img-grid.count-3 .img-cell:first-child {
    grid-column: 1 / -1;
    height: 250px;
}
.img-grid.count-3 .img-cell { height: 250px; }

/* 4 images: 2×2 grid */
.img-grid.count-4 {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 220px 220px;
}
.img-grid.count-4 .img-cell { height: 220px; }

.img-cell {
    position: relative;
    overflow: hidden;
    background: #f0f2f5;
    cursor: pointer;
}
.img-cell img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .2s;
}
.img-cell:hover img { transform: scale(1.02); }

/* "+N more" overlay on last image when > 4 */
.img-more-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 28px;
    font-weight: 700;
}

/* ── Video thumbnail in feed ───────────────────────────────── */
.post-video-thumb {
    position: relative;
    background: #000;
    cursor: pointer;
    overflow: hidden;
    max-height: 400px;
}
.post-video-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .2s;
    max-height: 400px;
}
.post-video-thumb:hover img { transform: scale(1.02); }

/* Play button overlay */
.video-play-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,.25);
    transition: background .2s;
}
.post-video-thumb:hover .video-play-overlay {
    background: rgba(0,0,0,.4);
}
.play-btn {
    width: 64px; height: 64px;
    background: rgba(255,255,255,.92);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    transition: transform .15s;
}
.post-video-thumb:hover .play-btn { transform: scale(1.1); }
.play-btn svg { margin-left: 4px; }

/* Video badge: duration / role */
.video-badge {
    position: absolute;
    bottom: 10px; left: 10px;
    background: rgba(0,0,0,.65);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
    letter-spacing: .03em;
}
.video-type-badge {
    position: absolute;
    top: 10px; left: 10px;
    background: #f02849;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: .05em;
}

/* ── Action bar ────────────────────────────────────────────── */
.post-actions {
    display: flex;
    border-top: 1px solid #e4e6eb;
    margin: 8px 16px 0;
    padding: 4px 0;
}
.post-action-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 4px;
    border: none;
    background: transparent;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #65676b;
    cursor: pointer;
    transition: background .15s;
    font-family: inherit;
    text-decoration: none;
}
.post-action-btn:hover { background: #f0f2f5; color: #050505; }
.post-action-btn.liked { color: #e0245e; }
.post-action-btn svg, .post-action-btn i { flex-shrink: 0; }

/* ── Comments ──────────────────────────────────────────────── */
.post-comments {
    padding: 10px 16px 12px;
    border-top: 1px solid #e4e6eb;
    background: #f7f8fa;
}
</style>

<div class="post-card">

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="post-header">
        <a href="{{ url('profile/' . $post->user->username) }}">
            <img class="post-avatar"
                 src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                 alt="{{ $post->user->name }}">
        </a>

        <div class="post-meta">
            <a class="post-author" href="{{ url('profile/' . $post->user->username) }}">
                {{ displayName($post->user->name) }}

                {{-- Verified tick for Creator / Influencer --}}
                @if(in_array(userLevel($post->user->id), ['Creator', 'Influencer']))
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#1877f2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif

                {{-- Role badge --}}
                {{-- @php $role = userLevel($post->user->id); @endphp
                @if($role === 'Creator')
                    <span class="badge-creator">Creator</span>
                @elseif($role === 'Influencer')
                    <span class="badge-influencer">Influencer</span>
                @endif --}}
            </a>
            <div class="post-time">
                <span>@</span>{{ $post->user->username }} · {{ $post->created_at->diffForHumans() }}
            </div>
        </div>

        {{-- Earnings (owner only) --}}
        {{-- @if(auth()->id() === $post->user_id) --}}
            <a href="{{ url('post/timeline/'.$post->id.'/analytics') }}"
               style="font-size:12px;color:#65676b;text-decoration:none;white-space:nowrap">
                {{ getCurrencyCode() }}{{ estimatedEarnings($post->id) }}
            </a>
        {{-- @endif --}}
    </div>

    {{-- ── Post text ───────────────────────────────────────── --}}
    @if($post->content)
        <div class="post-text" id="post-text-{{ $post->id }}">
            @php
                $fullText   = strip_tags($post->content);
                $shortText  = Str::limit($fullText, 280);
                $needsMore  = strlen($fullText) > 280;
            @endphp

            <span class="text-body" id="text-body-{{ $post->id }}">{{ $shortText }}</span>

            @if($needsMore)
                <button class="see-more-btn"
                        onclick="
                            document.getElementById('text-body-{{ $post->id }}').textContent = {{ json_encode($fullText) }};
                            this.remove();
                        ">
                    See more
                </button>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         IMAGES
         ═══════════════════════════════════════════════════════ --}}
    @if($post->images && $post->images->count())
        @php
            $imgs      = $post->images;
            $total     = $imgs->count();
            $shown     = $imgs->take(4);
            $remaining = $total - 4;
        @endphp

        <div class="post-images">
            <div class="img-grid count-{{ min($total, 4) }}">
                @foreach($shown as $i => $image)
                    <div class="img-cell">
                        <a href="{{ $image->path }}"
                           data-fslightbox="gallery-{{ $post->id }}">
                            <img src="{{ $image->path }}"
                                 alt="Post image"
                                 loading="lazy">
                        </a>

                        {{-- "+N more" overlay on the 4th image --}}
                        @if($i === 3 && $remaining > 0)
                            <a href="{{ $image->path }}"
                               data-fslightbox="gallery-{{ $post->id }}"
                               class="img-more-overlay text-decoration-none">
                                +{{ $remaining }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         VIDEO THUMBNAIL IN FEED
         Clicking takes the user to the full video player page.

         Thumbnail priority:
         1. $vid->thumbnail  — stored URL from Cloudinary at upload time
         2. $vid->poster_url — accessor builds it from public_id on the fly
         3. Placeholder       — dark box with video icon
         ═══════════════════════════════════════════════════════ --}}
    @if($post->video)
        @php
            $vid       = $post->video;
            $playerUrl = url('rolls/' . $vid->id);

            // Best thumbnail: stored column, then on-the-fly Cloudinary URL, then nothing
            $poster = $vid->thumbnail_path
                   ?? ($vid->public_id ? $vid->poster_url : null);

                //    echo $playerUrl;
        @endphp

        <a href="{{ $playerUrl }}" class="post-video-thumb d-block text-decoration-none">

            {{-- Poster frame --}}
            @if($poster)
                <img src="{{ $poster }}"
                     alt="Video thumbnail"
                     loading="lazy"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                {{-- Hidden fallback shown if image fails to load --}}
                <div style="display:none;height:300px;background:#111;align-items:center;justify-content:center">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" opacity=".4">
                        <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14"/>
                        <rect x="2" y="6" width="13" height="12" rx="2"/>
                    </svg>
                </div>
            @else
                {{-- No thumbnail: dark placeholder with video icon --}}
                <div style="height:300px;background:#111;display:flex;align-items:center;justify-content:center">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" opacity=".4">
                        <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14"/>
                        <rect x="2" y="6" width="13" height="12" rx="2"/>
                    </svg>
                </div>
            @endif

            {{-- Play button overlay --}}
            <div class="video-play-overlay">
                <div class="play-btn">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="#050505">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                </div>
            </div>

            {{-- "● VIDEO" badge --}}
            <span class="video-type-badge">● Video</span>

            {{-- Duration badge --}}
            @if(!empty($vid->duration))
                <span class="video-badge">{{ gmdate('i:s', $vid->duration) }}</span>
            @endif

        </a>
    @endif

    {{-- ── Action bar ──────────────────────────────────────── --}}
    <div class="post-actions">

        {{-- Like --}}
        <button class="post-action-btn {{ $likedByMe ? 'liked' : '' }}"
                wire:click="toggleLike">
            <svg width="18" height="18" viewBox="0 0 24 24"
                 fill="{{ $likedByMe ? 'currentColor' : 'none' }}"
                 stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06
                         a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                         1.06-1.06a5.5 5.5 0 000-7.78z"/>
            </svg>
            {{ number_format($likesCount) }}
        </button>

        {{-- Comment --}}
        <a class="post-action-btn" href="{{ url('timeline/'.$post->id) }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
            </svg>
            {{ $commentCount }}
        </a>

        {{-- Views --}}
        <span class="post-action-btn" style="cursor:default">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            {{ sumCounter($post->views, $post->views_external) }}
        </span>

        {{-- Share --}}
        <button class="post-action-btn"
                data-bs-toggle="modal"
                data-bs-target="#share-modal-{{ $post->id }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2">
                <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/>
                <circle cx="18" cy="19" r="3"/>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
            </svg>
            Share
        </button>

    </div>

    {{-- ── Comments section ───────────────────────────────── --}}
    <div class="post-comments">
        @if(userLevel() === 'Basic' && $post->user_id === auth()->id())
            <a href="{{ url('upgrade') }}" style="font-size:12px;color:#65676b">
                💰 Monetize this post
            </a>
            <hr class="my-2">
        @endif

        <livewire:user.post-comments
            :post="$post"
            :wire:key="'post-comments-'.$post->id" />
    </div>

</div>

{{-- ── Share modal ─────────────────────────────────────────── --}}
<div class="modal fade" id="share-modal-{{ $post->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-fromright">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Share Post</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Share and earn when people engage with this post.</p>

                @php $shareUrl = url('timeline/'.$post->id); @endphp

                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-sm"
                           value="{{ $shareUrl }}" readonly id="share-url-{{ $post->id }}">
                    <button class="btn btn-outline-secondary btn-sm"
                            onclick="navigator.clipboard.writeText('{{ $shareUrl }}')
                                     .then(()=>this.textContent='Copied!')">
                        Copy
                    </button>
                </div>

                <div class="d-flex gap-3 flex-wrap">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                       target="_blank" class="btn btn-sm btn-primary">
                        <i class="fab fa-facebook me-1"></i>Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}"
                       target="_blank" class="btn btn-sm btn-dark">
                        <i class="fab fa-x-twitter me-1"></i>Twitter
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($shareUrl) }}"
                       target="_blank" class="btn btn-sm btn-success">
                        <i class="fab fa-whatsapp me-1"></i>WhatsApp
                    </a>
                    <a href="https://t.me/share/url?url={{ urlencode($shareUrl) }}"
                       target="_blank" class="btn btn-sm btn-info text-white">
                        <i class="fab fa-telegram me-1"></i>Telegram
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</div>