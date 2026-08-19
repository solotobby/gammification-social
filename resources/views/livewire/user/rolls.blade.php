{{-- Payhankey Rolls Player — TikTok-style --}}
<div>
<style>
/* ═══════════════════════════════════════════════════════════════
   ROLLS / TIKTOK PLAYER
   ═══════════════════════════════════════════════════════════════ */
.reels-app, .reels-app * { box-sizing: border-box; }

.reels-app {
    --tt-like: #fe2c55;
    --tt-accent: #20d5ec;
    --tt-ink: #fff;
    --tt-muted: rgba(255,255,255,.72);
    --tt-dim: rgba(255,255,255,.55);
    --tt-sheet: #121212;
    --tt-sheet-ink: #f1f1f1;
    --tt-sheet-muted: rgba(255,255,255,.45);
    --tt-sheet-line: rgba(255,255,255,.08);
    /* TikTok: height-first 9:16, capped like tiktok.com desktop */
    --reels-h: 100dvh;
    --reels-col: min(calc(var(--reels-h) * 9 / 16), 100vw, 692px);
    --reels-sheet-max: var(--reels-col);
    --reels-rail-w: 48px;
    --reels-gutter: 0px;
    --reels-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: #000;
    font-family: var(--reels-font);
    color: var(--tt-ink);
    overflow: hidden;
    width: 100vw;
    height: 100vh;
    height: 100dvh;
}

[x-cloak] { display: none !important; }

/* ── Viewport — centered TikTok column ──────────────────── */
.reels-viewport {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #000;
}

.reels-stage {
    position: relative;
    width: var(--reels-col);
    max-width: 100%;
    height: 100dvh;
    max-height: 100dvh;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    background: #000;
    overflow: hidden;
}

@media (min-width: 768px) {
    .reels-app { --reels-gutter: 72px; }

    .reels-stage {
        width: calc(var(--reels-col) + var(--reels-gutter));
    }
}

/* ── Header — TikTok minimal chrome ─────────────────────── */
.reels-header {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 200;
    width: var(--reels-col);
    max-width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: max(12px, env(safe-area-inset-top)) 10px 8px;
    pointer-events: none;
}
.reels-header > * { pointer-events: auto; }

.reels-sound-hint {
    position: absolute;
    top: max(56px, calc(env(safe-area-inset-top) + 44px));
    left: 50%;
    transform: translateX(-50%);
    z-index: 210;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(0, 0, 0, .72);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    pointer-events: none;
    white-space: nowrap;
}

.reels-icon-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    color: #fff;
    border-radius: 50%;
    display: grid;
    place-items: center;
    cursor: pointer;
    text-decoration: none;
    font-size: 22px;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,.35));
}
.reels-icon-btn:hover { opacity: .85; }

/* ── Feed ───────────────────────────────────────────────── */
.reels-feed {
    flex: 1;
    width: 100%;
    height: var(--reels-h);
    overflow-y: scroll;
    scroll-snap-type: y mandatory;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-y: contain;
    scrollbar-width: none;
    background: #000;
    touch-action: pan-y;
}
.reels-feed::-webkit-scrollbar { display: none; }

/* ── Slide ──────────────────────────────────────────────── */
.reels-slide {
    position: relative;
    width: 100%;
    height: var(--reels-h);
    scroll-snap-align: start;
    scroll-snap-stop: always;
    background: #000;
    display: flex;
    flex-direction: row;
    align-items: stretch;
    justify-content: center;
    flex-shrink: 0;
}

.reels-slide-media {
    position: relative;
    flex: 0 0 var(--reels-col);
    width: var(--reels-col);
    max-width: 100%;
    height: 100%;
    overflow: hidden;
    background: #000;
}

@media (max-width: 767px) {
    .reels-slide {
        display: block;
    }

    .reels-slide-media {
        position: absolute;
        inset: 0;
        width: 100%;
        flex: none;
    }
}

.reels-video {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center center;
    display: block;
    cursor: pointer;
    background: #000;
}

.reels-vignette {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 5;
    background: linear-gradient(
        to top,
        rgba(0,0,0,.85) 0%,
        rgba(0,0,0,.45) 18%,
        rgba(0,0,0,.08) 38%,
        transparent 52%
    );
}

@media (max-width: 767px) {
    .reels-vignette {
        background:
            linear-gradient(to top, rgba(0,0,0,.82) 0%, rgba(0,0,0,.35) 20%, transparent 42%),
            linear-gradient(to left, rgba(0,0,0,.4) 0%, transparent 32%);
    }
}

/* Progress bar — TikTok thin scrubber */
.tt-progress {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 25;
    height: 3px;
    background: rgba(255,255,255,.15);
    pointer-events: none;
}
.tt-progress-fill {
    height: 100%;
    width: 0%;
    background: #fff;
    transition: width .08s linear;
}

/* Play / pause flash */
.reels-flash {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(.85);
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: rgba(0,0,0,.4);
    backdrop-filter: blur(4px);
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 26px;
    opacity: 0;
    pointer-events: none;
    z-index: 30;
}
@keyframes reelsFlash {
    0%   { opacity: 1; transform: translate(-50%,-50%) scale(.85); }
    70%  { opacity: 1; transform: translate(-50%,-50%) scale(1); }
    100% { opacity: 0; transform: translate(-50%,-50%) scale(1.05); }
}
.reels-flash.show { animation: reelsFlash .4s ease-out forwards; }

/* Double-tap heart — TikTok red */
.reels-heart-burst {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    font-size: 100px;
    color: var(--tt-like);
    filter: drop-shadow(0 4px 20px rgba(0,0,0,.4));
    opacity: 0;
    pointer-events: none;
    z-index: 35;
    line-height: 1;
}
@keyframes reelsHeartBurst {
    0%   { opacity: 0; transform: translate(-50%,-50%) scale(.2); }
    15%  { opacity: 1; transform: translate(-50%,-55%) scale(1.1); }
    100% { opacity: 0; transform: translate(-50%,-65%) scale(1.2); }
}
.reels-heart-burst.show { animation: reelsHeartBurst .85s ease-out forwards; }

/* ── Right rail — TikTok action stack ───────────────────── */
.reels-rail {
    position: absolute;
    right: 8px;
    bottom: max(72px, calc(16px + env(safe-area-inset-bottom)));
    z-index: 20;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    width: var(--reels-rail-w);
}

@media (min-width: 768px) {
    .reels-rail {
        position: relative;
        flex: 0 0 var(--reels-gutter);
        width: var(--reels-gutter);
        align-self: flex-end;
        justify-content: flex-end;
        right: auto;
        bottom: auto;
        padding-bottom: max(72px, calc(16px + env(safe-area-inset-bottom)));
        background: #000;
        gap: 18px;
    }
}

.reels-rail-avatar-wrap {
    position: relative;
    margin-bottom: 2px;
}

.reels-rail-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    border: 1px solid #fff;
    display: block;
    text-decoration: none;
    flex-shrink: 0;
}

.reels-rail-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.reels-rail-follow {
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--tt-like);
    border: 2px solid #121212;
    color: #fff;
    font-size: 11px;
    display: grid;
    place-items: center;
    cursor: pointer;
    padding: 0;
    z-index: 1;
}
.reels-rail-follow.is-following {
    background: #3a3a3a;
}

.reels-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    color: inherit;
    font-family: inherit;
    -webkit-tap-highlight-color: transparent;
}
.reels-action:disabled { cursor: default; opacity: .5; }

.reels-action-icon {
    width: 35px;
    height: 35px;
    display: grid;
    place-items: center;
    font-size: 26px;
    color: #fff;
    transition: transform .12s ease;
}
.reels-action:active .reels-action-icon { transform: scale(.88); }
.reels-action.is-liked .reels-action-icon,
.reels-action.is-liked .reels-action-icon i {
    color: #fe2c55 !important;
}
.reels-action.is-liked .reels-action-icon i {
    font-weight: 900;
}

.reels-action-label {
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    text-align: center;
    line-height: 1.1;
    letter-spacing: -.01em;
}

.reels-action--share .reels-action-label,
.reels-action--share .reels-action-icon { font-size: 24px; }

/* TikTok spinning sound disc */
.tt-disc {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, #222 0%, #111 100%);
    box-shadow: inset 0 0 0 3px rgba(255,255,255,.12);
    display: block;
    animation: ttDiscSpin 5s linear infinite;
    flex-shrink: 0;
    margin-top: 2px;
    text-decoration: none;
}
.tt-disc img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
@keyframes ttDiscSpin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

@media (min-width: 768px) {
    .reels-rail-avatar {
        width: 48px;
        height: 48px;
    }

    .reels-action-icon {
        color: rgba(255,255,255,.96);
    }

    .reels-action-label {
        color: rgba(255,255,255,.9);
    }
}

@keyframes reelsLikePop {
    0%   { transform: scale(1); }
    40%  { transform: scale(1.3); }
    100% { transform: scale(1); }
}
.reels-action.pop .reels-action-icon { animation: reelsLikePop .3s ease; }

/* ── Bottom meta — TikTok layout ────────────────────────── */
.reels-meta {
    position: absolute;
    left: 12px;
    right: calc(var(--reels-rail-w) + 20px);
    bottom: max(16px, env(safe-area-inset-bottom));
    z-index: 15;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}

@media (min-width: 768px) {
    .reels-meta {
        right: 16px;
        max-width: calc(var(--reels-col) - 24px);
    }
}
.reels-meta a, .reels-meta button { pointer-events: auto; }

.reels-user-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.reels-username {
    font-size: 17px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
}
.reels-username:hover { opacity: .85; }

.reels-follow {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    background: transparent;
    border: 1px solid rgba(255,255,255,.65);
    border-radius: 4px;
    padding: 3px 12px;
    cursor: pointer;
    font-family: inherit;
    line-height: 1.4;
}
.reels-follow:hover {
    background: rgba(255,255,255,.12);
}
.reels-follow.is-following {
    color: rgba(255,255,255,.72);
    border-color: rgba(255,255,255,.28);
    background: rgba(255,255,255,.08);
}

.reels-verified {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
}

.reels-caption {
    font-size: 15px;
    line-height: 1.35;
    color: #fff;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin: 0;
    max-width: 100%;
}
.reels-caption.expanded {
    -webkit-line-clamp: unset;
    display: block;
}

.reels-more {
    background: none;
    border: none;
    padding: 0;
    font-family: inherit;
    font-size: 15px;
    font-weight: 600;
    color: var(--tt-muted);
    cursor: pointer;
}

/* Sound row — TikTok marquee */
.tt-sound {
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: min(240px, 72vw);
    overflow: hidden;
}
.tt-sound i {
    font-size: 14px;
    color: #fff;
    flex-shrink: 0;
}
.tt-sound-track {
    overflow: hidden;
    flex: 1;
    mask-image: linear-gradient(90deg, #000 85%, transparent);
}
.tt-sound-marquee {
    display: inline-flex;
    white-space: nowrap;
    animation: ttMarquee 10s linear infinite;
    font-size: 14px;
    font-weight: 500;
    color: #fff;
}
@keyframes ttMarquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* ── Comments — TikTok dark sheet ───────────────────────── */
.reels-scrim {
    position: fixed;
    inset: 0;
    z-index: 300;
    background: rgba(0,0,0,.6);
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s ease;
}
.reels-scrim.open { opacity: 1; pointer-events: auto; }

.reels-comments {
    position: fixed;
    left: 50%;
    bottom: 0;
    z-index: 310;
    width: 100%;
    max-width: var(--reels-sheet-max);
    max-height: min(52vh, 480px);
    background: var(--tt-sheet);
    border-radius: 12px 12px 0 0;
    display: flex;
    flex-direction: column;
    transform: translate(-50%, 100%);
    transition: transform .28s cubic-bezier(.32,.72,.24,1);
}
.reels-comments.open { transform: translate(-50%, 0); }

.reels-comments-handle {
    width: 40px;
    height: 4px;
    border-radius: 2px;
    background: rgba(255,255,255,.2);
    margin: 8px auto 0;
    flex-shrink: 0;
}

.reels-comments-head {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 12px 16px;
    border-bottom: 1px solid var(--tt-sheet-line);
    flex-shrink: 0;
}
.reels-comments-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--tt-sheet-ink);
}
.reels-comments-close {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    color: var(--tt-sheet-ink);
    font-size: 20px;
    cursor: pointer;
    display: grid;
    place-items: center;
}

.reels-comments-list {
    flex: 1;
    overflow-y: auto;
    padding: 4px 16px;
    min-height: 100px;
}
.reels-comments-list::-webkit-scrollbar { width: 4px; }
.reels-comments-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 99px; }

.reels-comment {
    display: flex;
    gap: 12px;
    padding: 10px 0;
    align-items: flex-start;
}
.reels-comment-av {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}
.reels-comment-body { flex: 1; min-width: 0; }
.reels-comment-user {
    font-size: 13px;
    font-weight: 600;
    color: var(--tt-sheet-muted);
    margin-right: 6px;
}
.reels-comment-text {
    font-size: 14px;
    line-height: 1.45;
    color: var(--tt-sheet-ink);
    margin: 2px 0 0;
    word-break: break-word;
}
.reels-comment-time {
    font-size: 12px;
    color: var(--tt-sheet-muted);
    margin-top: 4px;
}
.reels-comments-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--tt-sheet-muted);
    font-size: 14px;
}

.reels-comments-input {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px calc(10px + env(safe-area-inset-bottom));
    border-top: 1px solid var(--tt-sheet-line);
    flex-shrink: 0;
}
.reels-comments-input img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}
.reels-comments-field {
    flex: 1;
    border: none;
    outline: none;
    font-family: inherit;
    font-size: 14px;
    color: var(--tt-sheet-ink);
    background: rgba(255,255,255,.08);
    border-radius: 20px;
    padding: 8px 14px;
}
.reels-comments-field::placeholder { color: var(--tt-sheet-muted); }
.reels-comments-post {
    border: none;
    background: transparent;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    color: var(--tt-accent);
    opacity: .35;
    cursor: pointer;
    padding: 8px 4px;
}
.reels-comments-post.active { opacity: 1; }

/* ── Share sheet ────────────────────────────────────────── */
.reels-share-scrim {
    position: fixed;
    inset: 0;
    z-index: 400;
    background: rgba(0,0,0,.65);
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
.reels-share {
    width: 100%;
    max-width: var(--reels-sheet-max);
    background: var(--tt-sheet);
    border-radius: 12px 12px 0 0;
    padding: 20px 20px calc(24px + env(safe-area-inset-bottom));
    color: #fff;
}
.reels-share h3 {
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 16px;
    text-align: center;
}
.reels-share-copy {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
}
.reels-share-copy input {
    flex: 1;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 8px;
    padding: 10px 12px;
    color: #fff;
    font-size: 13px;
    outline: none;
}
.reels-share-copy button {
    border: none;
    background: var(--tt-like);
    color: #fff;
    font-weight: 600;
    font-size: 13px;
    padding: 0 16px;
    border-radius: 8px;
    cursor: pointer;
}
.reels-share-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
}
.reels-share-links a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    text-decoration: none;
}

/* ── Load more ──────────────────────────────────────────── */
.reels-loadmore {
    height: var(--reels-h);
    scroll-snap-align: start;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    background: #000;
}
.reels-loadmore-btn {
    border: 1px solid rgba(255,255,255,.2);
    background: rgba(255,255,255,.06);
    color: #fff;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    padding: 12px 28px;
    border-radius: 8px;
    cursor: pointer;
}
.reels-empty {
    color: rgba(255,255,255,.45);
    font-size: 15px;
    padding: 0 24px;
    text-align: center;
}

@media (orientation: landscape) and (max-height: 500px) {
    .reels-rail { gap: 10px; bottom: max(48px, env(safe-area-inset-bottom)); }
    .reels-meta { gap: 4px; }
    .reels-caption { -webkit-line-clamp: 1; }
    .tt-sound { display: none; }
}
</style>

<div class="reels-app" x-data="rollsPlayer($wire)" x-init="boot()" x-cloak>

    {{-- Scrim --}}
    <div class="reels-scrim @if($showComments) open @endif" wire:click="closeComments"></div>

    <div class="reels-viewport">
        <div class="reels-stage">

            {{-- Header inside video column --}}
            <header class="reels-header">
                <a href="javascript:history.back()" class="reels-icon-btn" aria-label="Back">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                <button type="button" class="reels-icon-btn" @click="toggleMute()" aria-label="Toggle sound">
                    <i class="fa-solid" :class="muted ? 'fa-volume-xmark' : 'fa-volume-high'"></i>
                </button>
            </header>

            <div class="reels-sound-hint" x-show="_autoplayBlocked" x-transition.opacity x-cloak>
                <i class="fa-solid fa-volume-high"></i> Tap anywhere for sound
            </div>

            <div class="reels-feed" x-ref="feed" @click="onTap($event)">

                @forelse($videos as $post)
                    @php
                        $vid = $post->video;
                        $user = $post->user;
                        $postKey = (string) $post->id;
                        $liked = (bool) ($likeOverrides[$postKey]['liked'] ?? $post->liked_by_me ?? false);
                        $likes = $likeOverrides[$postKey]['count'] ?? $post->totalLikes();
                        $comments = $post->totalComments();
                        $qualities = $vid->quality_versions ?? [];
                        $srcHigh = $qualities['high'] ?? $vid->path;
                        $srcMedium = $qualities['medium'] ?? $srcHigh;
                        $srcLow = $qualities['low'] ?? $srcMedium;
                        $src = $srcMedium;
                        $poster = $vid->thumbnail_path ?? '';
                        $caption = strip_tags($post->content ?? '');
                        $shareUrl = route('rolls.public', ['video' => $vid->id]);
                        $level = userLevel($user->id);
                        $isVerified = in_array($level, ['Creator', 'Influencer']);
                        $jsId = json_encode($postKey);
                        $isOwner = auth()->id() === $post->user_id;
            @endphp

                    <article class="reels-slide reels-card"
                 data-post-id="{{ $post->id }}"
                             data-src="{{ $src }}"
                             data-src-high="{{ $srcHigh }}"
                             data-src-medium="{{ $srcMedium }}"
                             data-src-low="{{ $srcLow }}">

                        <div class="reels-slide-media">
                        <video class="reels-video" poster="{{ $poster }}" playsinline preload="auto" loop></video>
                        <div class="reels-vignette"></div>

                        <div class="reels-flash" id="flash-{{ $post->id }}">
                            <i id="flash-ic-{{ $post->id }}" class="fa-solid fa-play"></i>
                    </div>
                        <div class="reels-heart-burst" id="hburst-{{ $post->id }}">
                            <i class="fa-solid fa-heart"></i>
                </div>

                        <div class="tt-progress" aria-hidden="true">
                            <div class="tt-progress-fill" id="prog-{{ $post->id }}"></div>
                </div>

                        {{-- Bottom meta --}}
                        <footer class="reels-meta">
                            <div class="reels-user-row">
                                <a class="reels-username" href="{{ url('profile/'.$user->username) }}">{{ '@'.$user->username }}</a>
                                @if($isVerified)
                                    <svg class="reels-verified" viewBox="0 0 24 24" fill="#20d5ec" aria-label="Verified">
                                        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.8 14.5-3.7-3.7 1.4-1.4 2.3 2.3 5-5 1.4 1.4-6.4 6.4z"/>
                                    </svg>
                                @endif
                                @if(!$isOwner)
                                    @php $isFollowing = (bool) ($followingMap[(string) $user->id] ?? false); @endphp
                                    <button type="button"
                                        class="reels-follow {{ $isFollowing ? 'is-following' : '' }}"
                                        data-follow-user="{{ $user->id }}"
                                        @click.stop="toggleFollow({{ json_encode((string) $user->id) }})">
                                        {{ $isFollowing ? 'Following' : 'Follow' }}
                                    </button>
                                @endif
                            </div>

                            @if($caption)
                                @if(strlen($caption) > 90)
                                    <p class="reels-caption" id="cap-{{ $post->id }}">{{ Str::limit($caption, 120) }}</p>
                                    <button type="button" class="reels-more"
                                        @click.stop="expandCaption({{ $jsId }}, {{ json_encode($caption) }})">more</button>
                                @else
                                    <p class="reels-caption">{{ $caption }}</p>
                                @endif
                            @endif

                            <div class="tt-sound">
                                <i class="fa-solid fa-music"></i>
                                <div class="tt-sound-track">
                                    <div class="tt-sound-marquee">
                                        <span>{{ $user->username }} · Original sound — {{ $user->username }} · Original sound — </span>
                        </div>
                    </div>
                            </div>
                        </footer>
                        </div>{{-- /reels-slide-media --}}

                        {{-- Right rail — sits on black gutter (desktop) or dark gradient zone (mobile) --}}
                        <aside class="reels-rail">
                            <div class="reels-rail-avatar-wrap">
                                <x-user-avatar :user="$user" size="lg" class="reels-ua" />
                                @if(!$isOwner)
                                    @php $isFollowing = (bool) ($followingMap[(string) $user->id] ?? false); @endphp
                                    <button type="button"
                                        class="reels-rail-follow {{ $isFollowing ? 'is-following' : '' }}"
                                        data-follow-user="{{ $user->id }}"
                                        aria-label="{{ $isFollowing ? 'Unfollow' : 'Follow' }}"
                                        @click.stop="toggleFollow({{ json_encode((string) $user->id) }})">
                                        <i class="fa-solid {{ $isFollowing ? 'fa-check' : 'fa-plus' }}"></i>
                                    </button>
                                @endif
                            </div>

                            <button type="button"
                                class="reels-action {{ $liked ? 'is-liked' : '' }}"
                         id="like-{{ $post->id }}"
                                @click.stop="likePost({{ $jsId }})"
                                aria-label="Like"
                                aria-pressed="{{ $liked ? 'true' : 'false' }}">
                                <span class="reels-action-icon">
                                    <i class="fa-solid fa-heart" @if($liked) style="color:#fe2c55" @endif></i>
                                </span>
                                <span class="reels-action-label" id="lc-{{ $post->id }}">{{ number_format($likes) }}</span>
                            </button>

                            <button type="button" class="reels-action"
                                @click.stop="openComments({{ $jsId }})" aria-label="Comments">
                                <span class="reels-action-icon"><i class="fa-solid fa-comment"></i></span>
                                <span class="reels-action-label" id="cc-{{ $post->id }}">{{ number_format($comments) }}</span>
                            </button>

                            <button type="button" class="reels-action reels-action--share"
                                @click.stop="openShare('{{ $shareUrl }}')" aria-label="Share">
                                <span class="reels-action-icon"><i class="fa-solid fa-share"></i></span>
                                <span class="reels-action-label">Share</span>
                            </button>

                            <a href="{{ url('profile/'.$user->username) }}" class="tt-disc" aria-label="Original sound">
                                <x-user-avatar :user="$user" size="sm" :href="false" />
                            </a>
                        </aside>

                    </article>

        @empty
                    <div class="reels-loadmore">
                        <p class="reels-empty">No rolls yet. Be the first to post a video.</p>
            </div>
        @endforelse

        @if($hasMore)
                    <div class="reels-loadmore">
                        <button type="button" class="reels-loadmore-btn" wire:click="loadMore" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="loadMore">Load more</span>
                            <span wire:loading wire:target="loadMore"><i class="fa-solid fa-spinner fa-spin"></i></span>
                </button>
            </div>
        @endif

    </div>{{-- /feed --}}

        </div>{{-- /stage --}}
    </div>{{-- /viewport --}}

    {{-- Comments sheet --}}
    <div class="reels-comments @if($showComments) open @endif">
        <div class="reels-comments-handle"></div>
        <div class="reels-comments-head">
            <span class="reels-comments-title">
                {{ $sheetCommentCount > 0 ? number_format($sheetCommentCount).' comments' : 'Comments' }}
            </span>
            <button type="button" class="reels-comments-close" wire:click="closeComments" aria-label="Close">&times;</button>
        </div>

        <div class="reels-comments-list" id="commentList">
            @if($showComments && $activeComments && $activeComments->count())
                @foreach($activeComments as $c)
                    <div class="reels-comment" wire:key="rc-{{ $c->id }}">
                        <x-user-avatar :user="$c->user" size="sm" />
                        <div class="reels-comment-body">
                        <div>
                                <span class="reels-comment-user">{{ $c->user->username ?? 'user' }}</span>
                            </div>
                            <p class="reels-comment-text">{{ $c->message }}</p>
                            <div class="reels-comment-time">{{ $c->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            @elseif($showComments)
                <div class="reels-comments-empty">No comments yet. Start the conversation.</div>
            @endif
        </div>

        <div class="reels-comments-input">
            <x-user-avatar :user="auth()->user()" size="sm" :href="false" />
            <input type="text" class="reels-comments-field" x-model="commentText"
                placeholder="Add comment..." maxlength="500"
                @keydown.enter.prevent="submitComment()">
            <button type="button" class="reels-comments-post"
                :class="{ active: commentText.trim().length > 0 }"
                :disabled="!commentText.trim().length"
                @click="submitComment()">Post</button>
        </div>
    </div>

    {{-- Share sheet --}}
    <div class="reels-share-scrim" x-show="shareOpen" x-transition.opacity @click.self="shareOpen = false" style="display:none">
        <div class="reels-share" @click.stop>
            <h3>Share roll</h3>
            <div class="reels-share-copy">
                <input type="text" :value="shareUrl" readonly x-ref="shareInput">
                <button type="button" @click="copyLink()" x-text="copyLabel"></button>
            </div>
            <div class="reels-share-links">
                <a :href="'https://wa.me/?text='+encodeURIComponent(shareUrl)" target="_blank" rel="noopener" style="background:#25d366"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                <a :href="'https://twitter.com/intent/tweet?url='+encodeURIComponent(shareUrl)" target="_blank" rel="noopener" style="background:#000;border:1px solid #333"><i class="fa-brands fa-x-twitter"></i> X</a>
                <a :href="'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(shareUrl)" target="_blank" rel="noopener" style="background:#1877f2"><i class="fa-brands fa-facebook"></i> Facebook</a>
            </div>
        </div>
    </div>

</div>

@script
<script>
Alpine.data('rollsPlayer', function(wire) {
    return {
        muted: false,
        _autoplayBlocked: false,
        commentPostId: null,
        commentText: '',
        shareOpen: false,
        shareUrl: '',
        copyLabel: 'Copy',
        _following: @js($followingMap),

        _feed: null,
        _activeCard: null,
        _activeVideo: null,
        _activePostId: null,
        _watchStart: null,
        _watchSent: false,
        _playRecorded: {},
        _isFirstPlay: {},
        _seen: null,
        _observer: null,
        _lastTap: 0,
        _tapTimer: null,

        boot() {
            this._feed = this.$refs.feed;
            this._seen = new WeakSet();
            this._setupObserver();
            this._setupKeys();
            this._setupLivewireEvents();

            setTimeout(() => {
                if (!this._activeCard) {
                    const first = this._feed.querySelector('.reels-card[data-post-id]');
                    if (first) this._activate(first);
                }
            }, 80);

            const unlockAudio = () => {
                this.muted = false;
                this._autoplayBlocked = false;
                if (this._activeVideo) {
                    this._activeVideo.muted = false;
                    this._activeVideo.volume = 1;
                    if (this._activeVideo.paused) {
                        this._activeVideo.play().catch(() => {});
                    }
                }
            };
            this.$refs.feed?.addEventListener('pointerdown', unlockAudio, { once: true, passive: true });
            this.$refs.feed?.addEventListener('touchstart', unlockAudio, { once: true, passive: true });
            this.$refs.feed?.addEventListener('click', unlockAudio, { once: true, passive: true });

            Livewire.hook('morph.updated', () => {
                this._feed.querySelectorAll('.reels-card[data-post-id]').forEach(c => {
                    if (!this._seen.has(c)) {
                        this._seen.add(c);
                        this._observer.observe(c);
                    }
                });
                if (this._activeCard && this._activeVideo?.paused) {
                    this._doPlay(this._activeVideo);
                }
            });
        },

        _setupObserver() {
            this._observer = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) this._activate(e.target);
                    else this._deactivate(e.target);
                });
            }, { root: this._feed, threshold: 0.55 });

            this._feed.querySelectorAll('.reels-card[data-post-id]').forEach(c => {
                this._seen.add(c);
                this._observer.observe(c);
            });
        },

        _activate(card) {
            if (this._activeCard === card && this._activeVideo && !this._activeVideo.paused) return;
            if (this._activeCard && this._activeCard !== card) this._deactivate(this._activeCard);

            this._activeCard = card;
            this._activePostId = card.dataset.postId;
            this._activeVideo = card.querySelector('.reels-video');

            if (this._isFirstPlay[this._activePostId] === undefined) {
                this._isFirstPlay[this._activePostId] = true;
            }

            this._loadAndPlay(card, this._activeVideo);
            this._watchStart = Date.now();
            this._watchSent = false;
            wire.call('recordView', this._activePostId).catch(() => {});

            if (!this._playRecorded[this._activePostId]) {
                this._playRecorded[this._activePostId] = true;
                this._isFirstPlay[this._activePostId] = false;
                wire.call('recordPlay', this._activePostId).catch(() => {
                    this._playRecorded[this._activePostId] = false;
                    this._isFirstPlay[this._activePostId] = true;
                });
            }
        },

        _deactivate(card) {
            const video = card.querySelector('.reels-video');
            const postId = card.dataset.postId;
            if (video && !video.paused) video.pause();
            this._resetProgress(postId);
            this._flushWatch(postId);
        },

        _loadAndPlay(card, video) {
            video.muted = this.muted;
            video.volume = 1;
            video.setAttribute('playsinline', '');
            video.setAttribute('webkit-playsinline', '');
            const src = this._pickQuality(card);
            if (!card.dataset.loaded) {
                card.dataset.loaded = '1';
                card.dataset.currentQuality = src.label;
                video.src = src.url;
                video.addEventListener('canplay', () => {
                    if (this._activeCard === card) this._doPlay(video);
                }, { once: true });
                video.addEventListener('timeupdate', () => this._updateProgress(card.dataset.postId, video));
                video.addEventListener('ended', () => this._resetProgress(card.dataset.postId));
                video.addEventListener('waiting', () => this._maybeDowngrade(card, video));
                video.load();
            } else {
                this._doPlay(video);
            }
        },

        _pickQuality(card) {
            const high = card.dataset.srcHigh || card.dataset.src;
            const medium = card.dataset.srcMedium || high;
            const low = card.dataset.srcLow || medium;
            const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            const type = conn?.effectiveType || '4g';
            const saveData = conn?.saveData === true;

            if (saveData || type === 'slow-2g' || type === '2g') {
                return { url: low, label: 'low' };
            }
            if (type === '3g') {
                return { url: medium, label: 'medium' };
            }
            return { url: high, label: 'high' };
        },

        _maybeDowngrade(card, video) {
            const current = card.dataset.currentQuality || 'high';
            const order = ['high', 'medium', 'low'];
            const idx = order.indexOf(current);
            if (idx === -1 || idx >= order.length - 1) return;

            const next = order[idx + 1];
            const nextUrl = card.dataset['src' + next.charAt(0).toUpperCase() + next.slice(1)];
            if (!nextUrl || nextUrl === video.src) return;

            const t = video.currentTime;
            card.dataset.currentQuality = next;
            video.src = nextUrl;
            video.load();
            video.currentTime = t;
            this._doPlay(video);
        },

        _updateProgress(postId, video) {
            const fill = document.getElementById('prog-' + postId);
            if (!fill || !video.duration || !isFinite(video.duration)) return;
            fill.style.width = ((video.currentTime / video.duration) * 100) + '%';
        },

        _resetProgress(postId) {
            const fill = document.getElementById('prog-' + postId);
            if (fill) fill.style.width = '0%';
        },

        _doPlay(video) {
            // Prefer unmuted autoplay; browsers may block until a gesture.
            this.muted = false;
            video.muted = false;
            video.volume = 1;
            const p = video.play();
            if (p?.then) {
                p.then(() => {
                    this._autoplayBlocked = false;
                    video.muted = false;
                }).catch(() => {
                    // Temporary mute only to satisfy autoplay policy, then hint for sound.
                    video.muted = true;
                    video.play().then(() => {
                        this._autoplayBlocked = true;
                        this.muted = true;
                    }).catch(() => {});
                });
            }
        },

        onTap(e) {
            const card = e.target.closest('.reels-card');
            if (!card) return;
            if (e.target.closest('.reels-rail') || e.target.closest('.reels-meta')) return;

            const video = card.querySelector('.reels-video');
            const postId = card.dataset.postId;
            const now = Date.now();

            // Any tap unlocks audio preference.
            if (this.muted || this._autoplayBlocked) {
                this.muted = false;
                this._autoplayBlocked = false;
                if (video) {
                    video.muted = false;
                    video.volume = 1;
                }
            }

            if (now - this._lastTap < 280) {
                clearTimeout(this._tapTimer);
                this.likePost(postId, true);
                this._heartBurst(postId);
                this._lastTap = 0;
                return;
            }

            this._lastTap = now;
            clearTimeout(this._tapTimer);
            this._tapTimer = setTimeout(() => {
            if (video.paused) {
                this._doPlay(video);
                this._flash(postId, 'fa-play');
                this._watchStart = Date.now();
                    this._watchSent = false;
            } else {
                video.pause();
                this._flash(postId, 'fa-pause');
                this._flushWatch(postId);
            }
            }, 260);
        },

        toggleMute() {
            this.muted = !this.muted;
            this._autoplayBlocked = false;
            if (this._activeVideo) {
                this._activeVideo.muted = this.muted;
                this._activeVideo.volume = 1;
                if (!this.muted && this._activeVideo.paused) {
                    this._activeVideo.play().catch(() => {});
                }
            }
        },

        _applyLikeState(postId, liked, count) {
            const btn = document.getElementById('like-' + postId);
            const countEl = document.getElementById('lc-' + postId);
            const icon = btn?.querySelector('.reels-action-icon i');
            if (btn) {
                btn.classList.toggle('is-liked', !!liked);
                btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
            }
            if (icon) {
                icon.className = 'fa-solid fa-heart';
                icon.style.color = liked ? '#fe2c55' : '';
            }
            if (countEl) countEl.textContent = this._fmt(count ?? 0);
        },

        likePost(postId, fromDoubleTap = false) {
            const btn = document.getElementById('like-' + postId);
            const countEl = document.getElementById('lc-' + postId);
            if (!btn || !countEl) return;

            const wasLiked = btn.classList.contains('is-liked');
            if (fromDoubleTap && wasLiked) return;

            const current = this._parseCount(countEl.textContent);
            const nextLiked = !wasLiked;
            const nextCount = Math.max(0, wasLiked ? current - 1 : current + 1);

            this._applyLikeState(postId, nextLiked, nextCount);
            btn.classList.add('pop');
            setTimeout(() => btn.classList.remove('pop'), 350);

            wire.call('toggleLike', postId)
                .then(r => { if (r?.postId) this._applyLikeState(r.postId, r.liked, r.count); })
                .catch(() => this._applyLikeState(postId, wasLiked, current));
        },

        toggleFollow(userId) {
            userId = String(userId || '');
            if (!userId) return;

            const prev = !!this._following[userId];
            const next = !prev;
            this._following[userId] = next;
            this._applyFollowState(userId, next);

            wire.call('toggleFollow', userId)
                .then(r => {
                    if (!r?.userId) return;
                    this._following[r.userId] = !!r.following;
                    this._applyFollowState(r.userId, !!r.following);
                })
                .catch(() => {
                    this._following[userId] = prev;
                    this._applyFollowState(userId, prev);
                });
        },

        _applyFollowState(userId, following) {
            document.querySelectorAll('[data-follow-user="' + userId + '"]').forEach(btn => {
                btn.classList.toggle('is-following', !!following);
                btn.setAttribute('aria-label', following ? 'Unfollow' : 'Follow');
                if (btn.classList.contains('reels-follow')) {
                    btn.textContent = following ? 'Following' : 'Follow';
                }
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = 'fa-solid ' + (following ? 'fa-check' : 'fa-plus');
                }
            });
        },

        _heartBurst(postId) {
            const el = document.getElementById('hburst-' + postId);
            if (!el) return;
            el.classList.remove('show');
            void el.offsetWidth;
            el.classList.add('show');
        },

        _flash(postId, icon) {
            const el = document.getElementById('flash-' + postId);
            const ic = document.getElementById('flash-ic-' + postId);
            if (!el || !ic) return;
            ic.className = 'fa-solid ' + icon;
            el.classList.remove('show');
            void el.offsetWidth;
            el.classList.add('show');
        },

        openComments(postId) {
            this.commentPostId = postId;
            this.commentText = '';
            wire.call('openComments', postId);
            this.$nextTick(() => this._feed.querySelectorAll('.reels-video').forEach(v => v.pause()));
        },

        closeComments() {
            wire.call('closeComments');
            if (this._activeVideo?.paused) this._doPlay(this._activeVideo);
        },

        submitComment() {
            const text = this.commentText.trim();
            if (!text || !this.commentPostId) return;
            const postId = this.commentPostId;
            wire.call('submitComment', text, postId);
            this.commentText = '';
        },

        openShare(url) {
            this.shareUrl = url;
            this.copyLabel = 'Copy';
            this.shareOpen = true;
        },

        copyLink() {
            navigator.clipboard.writeText(this.shareUrl)
                .then(() => { this.copyLabel = 'Copied'; })
                .catch(() => {
                    this.$refs.shareInput?.select();
                        document.execCommand('copy');
                    this.copyLabel = 'Copied';
                });
        },

        expandCaption(postId, full) {
            const el = document.getElementById('cap-' + postId);
            if (!el) return;
            el.textContent = full;
            el.classList.add('expanded');
            el.nextElementSibling?.remove();
        },

        _flushWatch(postId) {
            if (!this._watchSent && this._watchStart && postId === this._activePostId) {
                const s = (Date.now() - this._watchStart) / 1000;
                if (s >= 0.25) {
                    // Play already counted in _activate via recordPlay; never send is_first_play here.
                    wire.call('recordWatch', postId, s, false).catch(() => {});
                    this._watchSent = true;
                }
            }
            this._watchStart = null;
        },

        _setupLivewireEvents() {
            const apply = (payload) => payload?.[0] ?? payload ?? {};

            Livewire.on('viewCounted', () => {});

            Livewire.on('likeUpdated', payload => {
                const { postId, liked, count } = apply(payload);
                if (postId) this._applyLikeState(postId, liked, count);
            });

            Livewire.on('followUpdated', payload => {
                const { userId, following } = apply(payload);
                if (!userId) return;
                this._following[userId] = !!following;
                this._applyFollowState(userId, !!following);
            });

            Livewire.on('commentCountUpdated', payload => {
                const { postId, count } = apply(payload);
                if (!postId || count === undefined) return;
                const ccEl = document.getElementById('cc-' + postId);
                if (ccEl) ccEl.textContent = this._fmt(count);
            });
        },

         _setupKeys() {
            document.addEventListener('keydown', e => {
                const tag = document.activeElement?.tagName;
                if (tag === 'INPUT' || tag === 'TEXTAREA') return;
                const h = this._feed?.clientHeight || window.innerHeight;
                if (e.key === 'ArrowDown') this._feed.scrollBy({ top: h, behavior: 'smooth' });
                if (e.key === 'ArrowUp') this._feed.scrollBy({ top: -h, behavior: 'smooth' });
                if (e.key === ' ') {
                    e.preventDefault();
                    if (!this._activeVideo) return;
                        if (this._activeVideo.paused) {
                            this._doPlay(this._activeVideo);
                            this._watchStart = Date.now();
                            this._watchSent = false;
                        } else {
                            this._activeVideo.pause();
                            this._flushWatch(this._activePostId);
                        }
                    }
                if (e.key === 'm') this.toggleMute();
                if (e.key === 'Escape') { wire.call('closeComments'); this.shareOpen = false; }
            });

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden || !this._watchStart || !this._activePostId || this._watchSent) return;
                const s = (Date.now() - this._watchStart) / 1000;
                if (s < 0.25) return;
                navigator.sendBeacon('{{ url("api/rolls/watch") }}', new Blob([JSON.stringify({
                    post_id: this._activePostId,
                    watch_seconds: s,
                    is_first_play: false,
                    _token: document.querySelector('meta[name="csrf-token"]')?.content
                })], { type: 'application/json' }));
                this._watchSent = true;
                this._watchStart = null;
            });
        },

        _fmt(n) {
            n = parseInt(n, 10) || 0;
            if (n >= 1_000_000) return (n / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M';
            if (n >= 10_000) return (n / 1_000).toFixed(1).replace(/\.0$/, '') + 'K';
            if (n >= 1_000) return (n / 1_000).toFixed(1).replace(/\.0$/, '') + 'K';
            return n.toLocaleString();
        },

        _parseCount(str) {
            str = (str || '').toString().trim().replace(/,/g, '');
            if (str.endsWith('M')) return Math.round(parseFloat(str) * 1_000_000);
            if (str.endsWith('K')) return Math.round(parseFloat(str) * 1_000);
            return parseInt(str, 10) || 0;
        },
    };
});
</script>
@endscript

</div>
