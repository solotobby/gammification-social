<div>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=dm-mono@400,500&display=swap" rel="stylesheet">

    @php
        $level = userLevel($post->user->id);
        $isVerified = in_array($level, ['Creator', 'Influencer']);
        $shareUrl = url('timeline/' . $post->id);
        $encodedShareUrl = urlencode($shareUrl);
        $shareText = urlencode('Check this out on ' . config('app.name'));
        $imgs = $post->images ?? collect();
        $imgCount = $imgs->count();
        $vid = $post->video;
        $isOwner = auth()->id() === $post->user_id;
    @endphp

    <style>
        .td-page {
            --td-violet: #5A4FDC;
            --td-violet-dark: #4338CA;
            --td-violet-soft: rgba(90, 79, 220, .10);
            --td-ink: #0F1117;
            --td-muted: #8B90A5;
            --td-line: rgba(15, 17, 23, .08);
            --td-bg: #F8F9FC;
            --td-card: #fff;
            --td-fb: #F0F2F5;
            font-family: 'Instrument Sans', 'Plus Jakarta Sans', system-ui, sans-serif;
            color: var(--td-ink);
            padding: 0 0 48px;
            min-height: 60vh;
        }

        /* Rich content / link previews inside post body */
        .td-body .link-preview-card,
        .td-body .og-card {
            display: block;
            margin-top: 12px;
            border: 1px solid var(--td-line);
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
        }
        .td-body .link-preview-card { padding: 12px; }
        .td-body .link-preview-host { font-weight: 600; color: var(--td-violet); }
        .td-body .link-preview-url { font-size: .82rem; color: var(--td-muted); word-break: break-all; }
        .td-body .og-image { width: 100%; max-height: 220px; object-fit: cover; display: block; }
        .td-body .og-body { padding: 12px; }
        .td-body .og-title { font-weight: 600; margin-bottom: 4px; }
        .td-body .og-desc { font-size: .84rem; color: var(--td-muted); }
        .td-body .og-host { font-size: .78rem; color: var(--td-muted); margin-top: 6px; }

        .td-page * { box-sizing: border-box; }

        .td-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .88rem;
            font-weight: 600;
            color: var(--td-muted);
            text-decoration: none;
            padding: 16px 0 12px;
            transition: color .15s;
        }
        .td-back:hover { color: var(--td-violet); }
        .td-back svg { width: 18px; height: 18px; }

        .td-card {
            background: var(--td-card);
            border: 1px solid var(--td-line);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(15, 17, 23, .06);
            overflow: hidden;
        }

        /* Header */
        .td-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 20px 20px 0;
        }
        .td-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(15,17,23,.10);
        }
        .td-avatar-ring {
            box-shadow: 0 0 0 2px var(--td-violet);
        }
        .td-header-main { flex: 1; min-width: 0; }
        .td-name-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }
        .td-name {
            font-weight: 700;
            font-size: 1rem;
            color: var(--td-ink);
            text-decoration: none;
        }
        .td-name:hover { text-decoration: underline; }
        .td-tick { width: 16px; height: 16px; flex-shrink: 0; }
        .td-meta {
            font-size: .82rem;
            color: var(--td-muted);
            margin-top: 2px;
        }
        .td-meta a {
            color: var(--td-muted);
            text-decoration: none;
        }
        .td-meta a:hover { text-decoration: underline; }
        .td-earn {
            flex-shrink: 0;
            font-family: 'DM Mono', ui-monospace, monospace;
            font-size: .72rem;
            font-weight: 600;
            color: #0A7040;
            background: rgba(31, 174, 100, .12);
            padding: 6px 12px;
            border-radius: 999px;
            text-decoration: none;
            white-space: nowrap;
        }
        .td-earn:hover { background: rgba(31, 174, 100, .18); color: #0A7040; }

        /* Body */
        .td-body {
            padding: 16px 20px 0;
            font-size: 1rem;
            line-height: 1.6;
            word-break: break-word;
        }
        .td-body a { color: var(--td-violet); }

        /* Trends */
        .td-trends {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
            padding-left: 10px;
            border-left: 2px solid var(--td-violet);
        }
        .td-trend {
            font-size: .82rem;
            font-weight: 700;
            color: var(--td-violet);
            text-decoration: none;
        }

        /* Media — Facebook full-bleed */
        .td-media {
            margin: 0;
            border-radius: 0;
            overflow: hidden;
            border: none;
            border-top: 1px solid #ced0d4;
            border-bottom: 1px solid #ced0d4;
        }
        .fb-img-grid {
            display: grid;
            gap: 2px;
            background: #000;
        }
        .fb-img-grid.n1 { grid-template-columns: 1fr; }
        .fb-img-grid.n1 .fb-img-cell { height: min(520px, 75vw); max-height: 520px; }
        .fb-img-grid.n2 { grid-template-columns: 1fr 1fr; }
        .fb-img-grid.n2 .fb-img-cell { height: 320px; }
        .fb-img-grid.n3 { grid-template-columns: 1fr 1fr; }
        .fb-img-grid.n3 .fb-img-cell:first-child { grid-row: span 2; min-height: 320px; }
        .fb-img-grid.n3 .fb-img-cell { height: 160px; }
        .fb-img-grid.n4 { grid-template-columns: 1fr 1fr; }
        .fb-img-grid.n4 .fb-img-cell { height: 240px; }
        .fb-img-cell {
            position: relative;
            overflow: hidden;
            background: #1c1e21;
        }
        .fb-img-cell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .fb-img-more {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,.55);
            color: #fff;
            font-weight: 700;
            font-size: 2rem;
            display: grid;
            place-items: center;
            pointer-events: none;
        }

        /* FB video */
        .fb-video {
            display: block;
            position: relative;
            background: #1c1e21;
            text-decoration: none;
            overflow: hidden;
            max-height: 520px;
        }
        .fb-video img {
            width: 100%;
            max-height: 520px;
            object-fit: cover;
            display: block;
        }
        .fb-video-play {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            background: rgba(0,0,0,.22);
        }
        .fb-video-play span {
            width: 64px;
            height: 64px;
            background: rgba(255,255,255,.95);
            border-radius: 50%;
            display: grid;
            place-items: center;
            box-shadow: 0 2px 12px rgba(0,0,0,.35);
        }
        .fb-video-placeholder {
            width: 100%;
            height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1c1e21;
        }
        .fb-video-pill {
            position: absolute;
            top: 12px;
            left: 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #fff;
            background: rgba(0,0,0,.65);
            padding: 4px 10px;
            border-radius: 4px;
        }
        .fb-video-dur {
            position: absolute;
            bottom: 12px;
            right: 12px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            background: rgba(0,0,0,.75);
            padding: 3px 8px;
            border-radius: 4px;
        }

        /* Reactions slot */
        .td-reactions {
            padding: 4px 12px 0;
            border-top: 1px solid var(--td-line);
            margin-top: 16px;
        }

        /* Comments slot */
        .td-comments-wrap {
            background: var(--td-fb);
            border-top: 1px solid var(--td-line);
            padding: 16px 20px 20px;
        }

        /* Share modal */
        .td-share-modal .modal-content {
            border: none;
            border-radius: 18px;
            overflow: hidden;
        }
        .td-share-head {
            background: linear-gradient(135deg, var(--td-violet), var(--td-violet-dark));
            color: #fff;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .td-share-head h3 { margin: 0; font-size: 1rem; font-weight: 700; }
        .td-share-body { padding: 20px; }
        .td-share-url {
            display: flex;
            gap: 8px;
            padding: 10px 12px;
            background: var(--td-bg);
            border: 1px solid var(--td-line);
            border-radius: 10px;
            font-size: .8rem;
            margin: 12px 0 16px;
        }
        .td-share-url span {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-family: 'DM Mono', ui-monospace, monospace;
        }
        .td-share-url button {
            border: none;
            background: var(--td-violet-soft);
            color: var(--td-violet-dark);
            font-weight: 700;
            font-size: .76rem;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
        }
        .td-share-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .td-share-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #fff;
            text-decoration: none;
        }
        .td-share-fb { background: #1877F2; }
        .td-share-x { background: #111; }
        .td-share-li { background: #0A66C2; }
        .td-share-wa { background: #25D366; }
        .td-share-tg { background: #229ED9; }
        .td-share-btn svg { width: 20px; height: 20px; }

        @media (max-width: 575px) {
            .td-page { padding-bottom: 32px; }
            .td-header, .td-body, .td-comments-wrap { padding-left: 16px; padding-right: 16px; }
            .td-media { margin-left: 16px; margin-right: 16px; }
        }

        @media (min-width: 992px) {
            .td-layout-row > .col-md-4 {
                position: sticky;
                top: 80px;
                align-self: flex-start;
            }
        }
    </style>

    <div class="row td-layout-row">
        <div class="col-md-8">
            <div class="td-page">

    <a href="{{ url('timeline') }}" class="td-back" wire:navigate>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to feed
    </a>

    <article class="td-card">

        {{-- Author --}}
        <header class="td-header">
            <a href="{{ url('profile/' . $post->user->username) }}">
                <img class="td-avatar @if($level === 'Influencer') td-avatar-ring @endif"
                    src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                    alt="{{ $post->user->name }}">
            </a>
            <div class="td-header-main">
                <div class="td-name-row">
                    <a class="td-name" href="{{ url('profile/' . $post->user->username) }}">
                        {{ displayName($post->user->name) }}
                    </a>
                    @if ($isVerified)
                        <svg class="td-tick" viewBox="0 0 22 22" fill="none" aria-label="Verified">
                            <circle cx="11" cy="11" r="11" fill="#1d9bf0"/>
                            <path d="M7 11l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
                    @if ($level === 'Influencer')
                        <span style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--td-violet);background:var(--td-violet-soft);padding:2px 6px;border-radius:4px;">Influencer</span>
                    @endif
                </div>
                <div class="td-meta">
                    <a href="{{ url('profile/' . $post->user->username) }}">@{{ $post->user->username }}</a>
                    · {{ $post->created_at?->diffForHumans() }}
                </div>
            </div>
            @if ($isOwner)
                <a href="{{ url('post/timeline/' . $post->id . '/analytics') }}" class="td-earn" title="View earnings">
                    {{ getCurrencyCode() }}{{ number_format($estimatedEarnings ?? 0, 2) }}
                </a>
            @else
                <span class="td-earn" style="cursor:default">
                    {{ getCurrencyCode() }}{{ number_format($estimatedEarnings ?? 0, 2) }}
                </span>
            @endif
        </header>

        {{-- Content --}}
        @if ($post->content)
            <div class="td-body">
                {!! $post->content !!}
                @if ($post->trends->isNotEmpty())
                    <div class="td-trends">
                        @foreach ($post->trends as $trend)
                            <a href="javascript:void(0)" class="td-trend">#{{ $trend->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Images --}}
        @if ($imgCount > 0)
            @php
                $shown = $imgs->take(4);
                $remaining = $imgCount - 4;
                $gridClass = 'n' . min($imgCount, 4);
            @endphp
            <div class="td-media">
                <div class="fb-img-grid {{ $gridClass }}">
                    @foreach ($shown as $i => $image)
                        <div class="fb-img-cell" wire:click="openPhotoViewer({{ $i }})" role="button" tabindex="0"
                            @keydown.enter.prevent="$wire.openPhotoViewer({{ $i }})" aria-label="View photo {{ $i + 1 }}"
                            style="cursor:pointer">
                            <img src="{{ $image->path }}" alt="Post image" loading="lazy">
                            @if ($i === 3 && $remaining > 0)
                                <span class="fb-img-more">+{{ $remaining }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Video --}}
        @if ($vid)
            @php
                $poster = $vid->thumbnail_path ?? ($vid->public_id ? $vid->poster_url ?? null : null);
                $playerUrl = route('rolls.show', ['video' => $vid->id]);
            @endphp
            <div class="td-media">
                <a href="{{ $playerUrl }}" class="fb-video">
                    @if ($poster)
                        <img src="{{ $poster }}" alt="Video" loading="lazy"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="fb-video-placeholder" style="display:none">
                            <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="1.5" opacity=".4" viewBox="0 0 24 24">
                                <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14"/><rect x="2" y="6" width="13" height="12" rx="2"/>
                            </svg>
                        </div>
                    @else
                        <div class="fb-video-placeholder">
                            <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="1.5" opacity=".4" viewBox="0 0 24 24">
                                <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14"/><rect x="2" y="6" width="13" height="12" rx="2"/>
                            </svg>
                        </div>
                    @endif
                    <div class="fb-video-play">
                        <span>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="#1c1e21"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        </span>
                    </div>
                    <span class="fb-video-pill">Roll</span>
                    @if (!empty($vid->duration))
                        <span class="fb-video-dur">{{ gmdate('i:s', $vid->duration) }}</span>
                    @endif
                </a>
            </div>
        @endif

        {{-- Reactions --}}
        <div class="td-reactions">
            <livewire:user.timeline-details-reaction
                :post="$post"
                :estimated-earnings="$estimatedEarnings ?? 0"
                :wire:key="'reactions-'.$post->id" />
        </div>

        {{-- Comments --}}
        <div class="td-comments-wrap">
            <livewire:user.timeline-details-comments :post="$post" :wire:key="'comments-'.$post->id" />
        </div>

    </article>

    {{-- Share modal --}}
    <div class="modal fade td-share-modal" id="modal-block-fromright-{{ $post->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="td-share-head">
                    <h3>Share this post</h3>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="td-share-body">
                    <p style="font-size:.88rem;color:var(--td-muted);margin:0 0 4px">Share and earn when people engage with this post.</p>
                    <div class="td-share-url">
                        <span id="td-share-url-{{ $post->id }}">{{ $shareUrl }}</span>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $shareUrl }}');this.textContent='Copied!'">Copy</button>
                    </div>
                    <div class="td-share-grid">
                        <a class="td-share-btn td-share-wa" target="_blank" rel="noopener"
                            href="https://wa.me/?text={{ $shareText }}%20{{ $encodedShareUrl }}" aria-label="WhatsApp">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.28-1.39a9.9 9.9 0 0 0 4.76 1.21h.01c5.46 0 9.9-4.45 9.9-9.91C22 6.45 17.5 2 12.04 2Z"/></svg>
                        </a>
                        <a class="td-share-btn td-share-x" target="_blank" rel="noopener"
                            href="https://twitter.com/intent/tweet?url={{ $encodedShareUrl }}&text={{ $shareText }}" aria-label="X">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-7.2 8.2L23.3 22H16.6l-5.2-6.8L5.4 22H2.3l7.7-8.8L1 2h6.9l4.7 6.2L18.9 2Z"/></svg>
                        </a>
                        <a class="td-share-btn td-share-fb" target="_blank" rel="noopener"
                            href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedShareUrl }}" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7.5H16l.4-3H13.5V8.4c0-.87.24-1.46 1.5-1.46H16.5V4.3c-.26-.03-1.14-.1-2.16-.1-2.14 0-3.6 1.3-3.6 3.7v2.6H8.5v3h2.24V21h2.76Z"/></svg>
                        </a>
                        <a class="td-share-btn td-share-li" target="_blank" rel="noopener"
                            href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedShareUrl }}" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3.5A1.96 1.96 0 1 0 5.27 7.42 1.96 1.96 0 0 0 5.25 3.5ZM20.45 20h-3.37v-5.98c0-1.43-.03-3.26-1.99-3.26-2 0-2.3 1.56-2.3 3.16V20H9.42V8.5h3.24v1.57h.05c.45-.86 1.56-1.77 3.2-1.77 3.43 0 4.06 2.26 4.06 5.19V20Z"/></svg>
                        </a>
                        <a class="td-share-btn td-share-tg" target="_blank" rel="noopener"
                            href="https://t.me/share/url?url={{ $encodedShareUrl }}&text={{ $shareText }}" aria-label="Telegram">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="m21.9 4.3-3 15c-.2.9-.8 1.1-1.6.7l-4.5-3.3-2.2 2.1c-.2.2-.4.4-.8.4l.3-4.3 7.9-7.1c.3-.3-.1-.5-.5-.2l-9.7 6.1-4.2-1.3c-.9-.3-.9-.9.2-1.3L20.6 3.4c.8-.3 1.5.2 1.3.9Z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


            </div>{{-- /.td-page --}}
        </div>{{-- /.col-md-8 --}}

        @include('layouts.engagement')
    </div>{{-- /.row --}}

    <livewire:user.post-photo-viewer />

    @include('layouts.onboarding')
</div>
