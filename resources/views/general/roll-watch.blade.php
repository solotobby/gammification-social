@php
    $pageTitle = ($creator?->username ? '@'.$creator->username.' · ' : '').'Roll on Payhankey';
    $pageDesc = $caption !== ''
        ? \Illuminate\Support\Str::limit($caption, 155)
        : 'Watch this Payhankey Roll'.($creator?->username ? ' by @'.$creator->username : '').'.';
    $isVerified = in_array($level, ['Creator', 'Influencer'], true);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0a0a0a">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $shareUrl }}">

    <meta property="og:type" content="video.other">
    <meta property="og:site_name" content="Payhankey">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ $shareUrl }}">
    @if ($poster)
        <meta property="og:image" content="{{ $poster }}">
    @endif
    <meta property="og:video" content="{{ $srcMedium }}">
    <meta property="og:video:type" content="video/mp4">
    <meta name="twitter:card" content="player">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    @if ($poster)
        <meta name="twitter:image" content="{{ $poster }}">
    @endif

    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --pk-ink: #0a0a0a;
            --pk-panel: #141414;
            --pk-line: rgba(255,255,255,.12);
            --pk-text: #f5f5f5;
            --pk-muted: rgba(245,245,245,.68);
            --pk-accent: #e8ff47;
            --pk-accent-ink: #111;
            --pk-blue: #3b82f6;
            --font-display: 'Sora', system-ui, sans-serif;
            --font-body: 'Plus Jakarta Sans', system-ui, sans-serif;
        }

        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            min-height: 100%;
            background: var(--pk-ink);
            color: var(--pk-text);
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
        }

        .rw {
            min-height: 100dvh;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .rw-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            padding-top: max(14px, env(safe-area-inset-top));
            border-bottom: 1px solid var(--pk-line);
            background: rgba(10,10,10,.88);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .rw-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--pk-text);
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 15px;
            letter-spacing: -.02em;
        }
        .rw-brand img { height: 26px; width: auto; display: block; }

        .rw-top-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .rw-link {
            color: var(--pk-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 10px;
        }
        .rw-link:hover { color: var(--pk-text); }
        .rw-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 14px;
            border-radius: 999px;
            background: var(--pk-accent);
            color: var(--pk-accent-ink);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .rw-stage {
            display: grid;
            place-items: center;
            padding: 16px;
            padding-bottom: max(16px, env(safe-area-inset-bottom));
        }

        .rw-card {
            width: min(420px, 100%);
            background: var(--pk-panel);
            border: 1px solid var(--pk-line);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(0,0,0,.45);
        }

        .rw-player {
            position: relative;
            aspect-ratio: 9 / 16;
            background: #000;
            max-height: min(72dvh, 720px);
        }
        .rw-player video {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            background: #000;
        }

        .rw-play-hint {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            pointer-events: none;
            transition: opacity .2s ease;
        }
        .rw-play-hint span {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(0,0,0,.55);
            border: 1px solid rgba(255,255,255,.25);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 22px;
            backdrop-filter: blur(6px);
        }
        .rw-player.is-playing .rw-play-hint { opacity: 0; }

        .rw-meta {
            padding: 16px 16px 18px;
            display: grid;
            gap: 14px;
        }

        .rw-user {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .rw-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background: #222;
            flex-shrink: 0;
        }
        .rw-avatar-fallback {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #222;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }
        .rw-user-text { min-width: 0; }
        .rw-username {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            font-size: 14px;
            color: var(--pk-text);
            text-decoration: none;
        }
        .rw-username svg { width: 14px; height: 14px; flex-shrink: 0; }
        .rw-sub { color: var(--pk-muted); font-size: 12px; margin-top: 2px; }

        .rw-caption {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
            color: var(--pk-text);
            white-space: pre-wrap;
            word-break: break-word;
        }

        .rw-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .rw-btn {
            appearance: none;
            border: 1px solid var(--pk-line);
            background: transparent;
            color: var(--pk-text);
            border-radius: 12px;
            padding: 11px 12px;
            font: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .rw-btn:hover { background: rgba(255,255,255,.06); }
        .rw-btn--primary {
            background: var(--pk-accent);
            color: var(--pk-accent-ink);
            border-color: transparent;
        }
        .rw-btn--primary:hover { filter: brightness(1.05); }
        .rw-btn.is-copied { border-color: #22c55e; color: #86efac; }

        .rw-foot {
            padding: 12px 18px 18px;
            text-align: center;
            color: var(--pk-muted);
            font-size: 12px;
        }
        .rw-foot a { color: var(--pk-text); }

        @media (max-width: 480px) {
            .rw-stage { padding: 0; }
            .rw-card {
                width: 100%;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                min-height: calc(100dvh - 58px);
                display: grid;
                grid-template-rows: 1fr auto;
            }
            .rw-player {
                max-height: none;
                aspect-ratio: auto;
                min-height: 58dvh;
            }
            .rw-link { display: none; }
        }
    </style>
</head>
<body>
<div class="rw" x-data="rollWatch()" x-cloak>
    <header class="rw-top">
        <a class="rw-brand" href="{{ url('/') }}">
            <img src="{{ asset('logo.png') }}" alt="Payhankey">
            <span>Roll</span>
        </a>
        <div class="rw-top-actions">
            @auth
                <a class="rw-link" href="{{ route('rolls.show', ['video' => $video->id]) }}">Open in Rolls</a>
                <a class="rw-cta" href="{{ route('home') }}">Dashboard</a>
            @else
                <a class="rw-link" href="{{ url('/login') }}">Log in</a>
                <a class="rw-cta" href="{{ url('/register') }}">Join free</a>
            @endauth
        </div>
    </header>

    <main class="rw-stage">
        <article class="rw-card">
            <div class="rw-player" :class="{ 'is-playing': playing }" @click="toggle()">
                <video
                    x-ref="video"
                    poster="{{ $poster }}"
                    playsinline
                    preload="metadata"
                    controls
                    @play="playing = true; onFirstPlay()"
                    @pause="playing = false"
                    @ended="playing = false"
                >
                    <source src="{{ $srcHigh }}" type="video/mp4" data-quality="high">
                    <source src="{{ $srcMedium }}" type="video/mp4" data-quality="medium">
                    <source src="{{ $srcLow }}" type="video/mp4" data-quality="low">
                </video>
                <div class="rw-play-hint" x-show="!playing">
                    <span><i class="fa-solid fa-play" style="margin-left:3px"></i></span>
                </div>
            </div>

            <div class="rw-meta">
                <div class="rw-user">
                    @if ($creator?->avatar)
                        <img class="rw-avatar" src="{{ $creator->avatar }}" alt="">
                    @else
                        <div class="rw-avatar-fallback">{{ strtoupper(substr($creator->username ?? $creator->name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <div class="rw-user-text">
                        @if ($creator?->username)
                            @auth
                                <a class="rw-username" href="{{ url('profile/'.$creator->username) }}">
                                    {{ '@'.$creator->username }}
                                    @if ($isVerified)
                                        <svg viewBox="0 0 24 24" fill="#20d5ec" aria-label="Verified">
                                            <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.8 14.5-3.7-3.7 1.4-1.4 2.3 2.3 5-5 1.4 1.4-6.4 6.4z"/>
                                        </svg>
                                    @endif
                                </a>
                            @else
                                <span class="rw-username">
                                    {{ '@'.$creator->username }}
                                    @if ($isVerified)
                                        <svg viewBox="0 0 24 24" fill="#20d5ec" aria-label="Verified">
                                            <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.8 14.5-3.7-3.7 1.4-1.4 2.3 2.3 5-5 1.4 1.4-6.4 6.4z"/>
                                        </svg>
                                    @endif
                                </span>
                            @endauth
                        @else
                            <span class="rw-username">Creator</span>
                        @endif
                        <div class="rw-sub">Payhankey Roll · watch alone</div>
                    </div>
                </div>

                @if ($caption !== '')
                    <p class="rw-caption">{{ $caption }}</p>
                @endif

                <div class="rw-actions">
                    <button type="button" class="rw-btn" :class="{ 'is-copied': copied }" @click="copyLink()">
                        <i class="fa-solid fa-link"></i>
                        <span x-text="copied ? 'Copied' : 'Copy link'"></span>
                    </button>
                    @auth
                        <a class="rw-btn rw-btn--primary" href="{{ route('rolls.show', ['video' => $video->id]) }}">
                            <i class="fa-solid fa-clapperboard"></i> Open in Rolls
                        </a>
                    @else
                        <a class="rw-btn rw-btn--primary" href="{{ url('/register') }}">
                            <i class="fa-solid fa-user-plus"></i> Create account
                        </a>
                    @endauth
                </div>
            </div>
        </article>
    </main>

    <footer class="rw-foot">
        Shared from <a href="{{ url('/') }}">Payhankey</a> · Create, grow, earn
    </footer>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function rollWatch() {
        return {
            playing: false,
            copied: false,
            playedOnce: false,
            shareUrl: @json($shareUrl),

            toggle() {
                const v = this.$refs.video;
                if (!v) return;
                if (v.paused) v.play().catch(() => {});
                else v.pause();
            },

            onFirstPlay() {
                if (this.playedOnce) return;
                this.playedOnce = true;
            },

            async copyLink() {
                try {
                    await navigator.clipboard.writeText(this.shareUrl);
                } catch (e) {
                    const input = document.createElement('input');
                    input.value = this.shareUrl;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    input.remove();
                }
                this.copied = true;
                setTimeout(() => this.copied = false, 1800);
            },
        };
    }
</script>
</body>
</html>
