{{--
    livewire/user/post-content.blade.php
    Twitter/X + Facebook hybrid card
    Data expected on $post:
      $post->user, $post->images (collection), $post->video (single),
      $post->trends (collection), $post->content,
      $post->views, $post->views_external, $post->comments
    Component properties: $likedByMe (bool), $likesCount (int), $commentCount (int)
--}}

<div>
    <style>
        /* ── Reset / scope ──────────────────────────────────────── */
        .pk-card *,
        .pk-card *::before,
        .pk-card *::after {
            box-sizing: border-box;
        }

        /* ── Card shell ─────────────────────────────────────────── */
        .pk-card {
            background: #fff;
            border: 1px solid #eff3f4;
            border-radius: 0;
            /* X-style: no radius on feed cards */
            margin-bottom: 1px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .pk-card+.pk-card {
            border-top: none;
        }

        /* Round only when it's a standalone detail card */
        .pk-card.pk-standalone {
            border-radius: 12px;
            margin-bottom: 12px;
            border: 1px solid #eff3f4;
        }

        /* ── Header ─────────────────────────────────────────────── */
        .pk-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px 0;
        }

        .pk-avatar-col {
            flex-shrink: 0;
        }

        .pk-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
        }

        .pk-name-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
            line-height: 1.2;
        }

        .pk-name {
            font-size: 15px;
            font-weight: 700;
            color: #0f1419;
            text-decoration: none;
        }

        .pk-name:hover {
            text-decoration: underline;
            color: #0f1419;
        }

        /* Verified badge — creator = blue, influencer = brand purple */
        .pk-tick {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        /* Influencer crown label */
        .pk-influencer-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #5A4FDC;
            background: rgba(90, 79, 220, .08);
            border-radius: 4px;
            padding: 1px 5px;
        }

        .pk-handle-row {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 1px;
        }

        .pk-handle {
            font-size: 14px;
            color: #536471;
            text-decoration: none;
        }

        .pk-handle:hover {
            text-decoration: underline;
            color: #536471;
        }

        .pk-sep {
            color: #ccd3d8;
            font-size: 13px;
        }

        .pk-time {
            font-size: 14px;
            color: #536471;
        }

        /* Earnings pill — owner only */
        .pk-earn {
            font-size: 12px;
            font-weight: 600;
            color: #00ba7c;
            background: rgba(0, 186, 124, .08);
            border-radius: 20px;
            padding: 2px 8px;
            white-space: nowrap;
            text-decoration: none;
        }

        .pk-earn:hover {
            background: rgba(0, 186, 124, .15);
            color: #00ba7c;
        }

        /* Options kebab */
        .pk-options-btn {
            background: none;
            border: none;
            padding: 6px;
            border-radius: 50%;
            color: #536471;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s;
            margin-left: auto;
            flex-shrink: 0;
        }

        .pk-options-btn:hover {
            background: rgba(90, 79, 220, .08);
            color: #5A4FDC;
        }

        /* ── Body ───────────────────────────────────────────────── */
        .pk-body {
            padding: 10px 16px 0 72px;
        }

        /* 72px = 44px avatar + 12px gap + 16px left pad */

        .pk-text {
            font-size: 15px;
            line-height: 1.55;
            color: #0f1419;
            white-space: pre-wrap;
            word-break: break-word;
            margin: 0;
        }

        .pk-text a {
            color: #1d9bf0;
            text-decoration: none;
        }

        .pk-text a:hover {
            text-decoration: underline;
        }

        .pk-see-more {
            background: none;
            border: none;
            padding: 0;
            color: #1d9bf0;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            margin-left: 4px;
        }

        .pk-see-more:hover {
            text-decoration: underline;
        }

        /* ── Trend tags ─────────────────────────────────────────── */
        /* Signature: left-border rule — editorial eyebrow, not a chip cloud */
        .pk-trends {
            margin-top: 10px;
            padding-left: 10px;
            border-left: 2px solid #5A4FDC;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }

        .pk-trend {
            font-size: 13px;
            font-weight: 700;
            color: #5A4FDC;
            text-decoration: none;
            letter-spacing: -.01em;
        }

        .pk-trend:hover {
            text-decoration: underline;
            color: #5A4FDC;
        }

        /* ── Media ──────────────────────────────────────────────── */
        /* Sits edge-to-edge below the indented body */
        .pk-media {
            margin: 12px 16px 0 72px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #eff3f4;
        }

        /* Image grid — Facebook-style */
        .pk-img-grid {
            display: grid;
            gap: 2px;
            background: #000;
        }

        .pk-img-grid.n1 {
            grid-template-columns: 1fr;
        }

        .pk-img-grid.n1 .pk-img-cell {
            height: 360px;
        }

        .pk-img-grid.n2 {
            grid-template-columns: 1fr 1fr;
        }

        .pk-img-grid.n2 .pk-img-cell {
            height: 280px;
        }

        .pk-img-grid.n3 {
            grid-template-columns: 1fr 1fr;
        }

        .pk-img-grid.n3 .pk-img-cell:first-child {
            grid-row: span 2;
            height: 100%;
            min-height: 280px;
        }

        .pk-img-grid.n3 .pk-img-cell {
            height: 200px;
        }

        .pk-img-grid.n4 {
            grid-template-columns: 1fr 1fr;
        }

        .pk-img-grid.n4 .pk-img-cell {
            height: 200px;
        }

        .pk-img-cell {
            position: relative;
            overflow: hidden;
            background: #0f1419;
            cursor: pointer;
        }

        .pk-img-cell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .25s ease;
        }

        .pk-img-cell:hover img {
            transform: scale(1.03);
        }

        .pk-img-more {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 26px;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: -.02em;
        }

        /* Video thumbnail */
        .pk-video {
            position: relative;
            background: #000;
            cursor: pointer;
            overflow: hidden;
            display: block;
            text-decoration: none;
        }

        .pk-video img {
            width: 100%;
            max-height: 380px;
            object-fit: cover;
            display: block;
            transition: transform .25s;
        }

        .pk-video:hover img {
            transform: scale(1.02);
        }

        .pk-video-placeholder {
            height: 280px;
            background: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pk-video-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s;
        }

        .pk-video:hover .pk-video-overlay {
            background: rgba(0, 0, 0, .42);
        }

        .pk-play {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, .93);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform .15s;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .3);
        }

        .pk-video:hover .pk-play {
            transform: scale(1.08);
        }

        .pk-video-pill {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #f02849;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .pk-video-dur {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, .7);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
        }

        /* ── Action bar ─────────────────────────────────────────── */
        .pk-actions {
            display: flex;
            padding: 4px 8px 4px 64px;
            gap: 0;
            margin-top: 8px;
            border-top: 1px solid #eff3f4;
        }

        .pk-action {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 6px;
            border: none;
            background: transparent;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 400;
            color: #536471;
            cursor: pointer;
            transition: background .15s, color .15s;
            font-family: inherit;
            text-decoration: none;
            min-width: 0;
            white-space: nowrap;
        }

        .pk-action:hover {
            color: #1d9bf0;
            background: rgba(29, 155, 240, .08);
        }

        .pk-action.pk-like:hover {
            color: #f91880;
            background: rgba(249, 24, 128, .08);
        }

        .pk-action.pk-share:hover {
            color: #5A4FDC;
            background: rgba(90, 79, 220, .08);
        }

        .pk-action.pk-view {
            cursor: default;
        }

        .pk-action.pk-view:hover {
            background: rgba(0, 186, 124, .08);
            color: #00ba7c;
        }

        .pk-action.pk-liked {
            color: #f91880;
        }

        .pk-action.pk-liked svg {
            animation: pk-pop .25s ease;
        }

        @keyframes pk-pop {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.4);
            }

            100% {
                transform: scale(1);
            }
        }

        /* ── Comments ───────────────────────────────────────────── */
        .pk-comments {
            padding: 10px 16px 14px;
            border-top: 1px solid #eff3f4;
            background: #f7f8fa;
        }

        /* ── Share modal overrides ──────────────────────────────── */
        .pk-modal-header {
            background: linear-gradient(120deg, #5A4FDC 0%, #7c6ef0 100%);
            border: none;
            border-radius: 0;
            padding: 16px 20px;
        }

        .pk-share-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: opacity .15s;
        }

        .pk-share-btn:hover {
            opacity: .85;
        }
    </style>

    @php
        $level = userLevel($post->user->id);
        $isOwner = auth()->id() === $post->user_id;
        $fullText = strip_tags($post->content);
        $shortText = Str::limit($fullText, 280);
        $needsMore = mb_strlen($fullText) > 280;
        $imgs = $post->images ?? collect();
        $imgCount = $imgs->count();
        $vid = $post->video ?? null;
        $postTrends = $post->trends ?? collect();
        $shareUrl = url('timeline/' . $post->id);
    @endphp

    <div class="pk-card">

        {{-- ══════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════ --}}
        <div class="pk-header">

            {{-- Avatar --}}
            <div class="pk-avatar-col">
                <a href="{{ url('profile/' . $post->user->username) }}">
                    <img class="pk-avatar"
                        src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                        alt="{{ $post->user->name }}">
                </a>
            </div>

            {{-- Name / handle / time --}}
            <div style="flex:1;min-width:0">
                <div class="pk-name-row">
                    <a class="pk-name" href="{{ url('profile/' . $post->user->username) }}">
                        {{ displayName($post->user->name) }}
                    </a>

                    @if ($level === 'Creator')
                        <svg class="pk-tick" viewBox="0 0 22 22" fill="none">
                            <circle cx="11" cy="11" r="11" fill="#1d9bf0" />
                            <path d="M7 11l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    @elseif ($level === 'Influencer')
                        <svg class="pk-tick" viewBox="0 0 22 22" fill="none">
                            <circle cx="11" cy="11" r="11" fill="#5A4FDC" />
                            <path d="M7 11l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        {{-- <span class="pk-influencer-label">Influencer</span> --}}
                    @endif

                    {{-- @if ($isOwner) --}}

                    {{-- @endif --}}
                </div>

                <div class="pk-handle-row">
                    <a class="pk-handle" href="{{ url('profile/' . $post->user->username) }}">
                        <span>@</span>{{ Str::limit($post->user->username, 18, '') }}
                    </a>
                    <span class="pk-sep">·</span>
                    <span class="pk-time">{{ $post->created_at->diffForHumans() }}</span>
                </div>
            </div>

            {{-- Options --}}
            <div class="dropdown">
                <a href="{{ url('post/timeline/' . $post->id . '/analytics') }}" class="pk-earns ms-1">
                    {{ getCurrencyCode() }}{{ estimatedEarnings($post->id) }}
                </a>

                {{-- <button class="pk-options-btn" data-bs-toggle="dropdown" aria-expanded="false">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/>
                </svg>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;min-width:200px">
                @if ($isOwner)
                    <a class="dropdown-item py-2" href="{{ url('post/timeline/'.$post->id.'/analytics') }}">
                        <i class="far fa-fw fa-chart-bar text-success me-2"></i> View earnings
                    </a>
                    @if (in_array($level, ['Creator', 'Influencer']))
                        <a class="dropdown-item py-2" href="javascript:void(0)">
                            <i class="far fa-fw fa-edit text-primary me-2"></i> Edit post
                        </a>
                        <div class="dropdown-divider m-0"></div>
                        <a class="dropdown-item py-2 text-danger" href="javascript:void(0)"
                           wire:click="deletePost({{ $post->unicode }})">
                            <i class="far fa-fw fa-trash-alt me-2"></i> Delete post
                        </a>
                    @endif
                @else
                    <a class="dropdown-item py-2" href="javascript:void(0)">
                        <i class="fa fa-fw fa-bookmark text-primary me-2"></i> Bookmark
                    </a>
                     <a class="dropdown-item py-2" href="javascript:void(0)">
                        <i class="fa fa-fw fa-user-minus text-warning me-2"></i>
                        Unfollow @{{ $post - > user - > username }}
                    </a> 
                     <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item py-2 text-danger" href="javascript:void(0)">
                        <i class="fa fa-fw fa-flag me-2"></i> Report post 
                    </a>
                @endif
            </div> --}}
            </div>

        </div>{{-- /pk-header --}}

        {{-- ══════════════════════════════════════════
         BODY — text + trends
    ══════════════════════════════════════════ --}}
        <div class="pk-body">

            {{-- Text --}}
            @if ($post->content)

            {!! $post->content !!}
                {{-- <p class="pk-text" id="pk-text-{{ $post->id }}">
                    <span id="pk-span-{{ $post->id }}">{{ $shortText }}</span>
                    @if ($needsMore)
                        <button class="pk-see-more"
                            onclick="(function(btn){
                                document.getElementById('pk-span-{{ $post->id }}').textContent = {{ json_encode($fullText) }};
                                btn.remove();
                            })(this)">
                            Show more
                        </button>
                    @endif
                </p> --}}
                <br>
            @endif

            {{-- Trends — editorial left-rule treatment --}}
            @if ($postTrends->isNotEmpty())
                <div class="pk-trends">
                    @foreach ($postTrends as $trend)
                        <a href="javascript:void(0)" class="pk-trend">#{{ $trend->name }}</a>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- ══════════════════════════════════════════
         IMAGES
    ══════════════════════════════════════════ --}}
        @if ($imgCount > 0)
            @php
                $shown = $imgs->take(4);
                $remaining = $imgCount - 4;
                $gridClass = 'n' . min($imgCount, 4);
            @endphp
            <div class="pk-media">
                <div class="pk-img-grid {{ $gridClass }}">
                    @foreach ($shown as $i => $image)
                        <div class="pk-img-cell">
                            <a href="{{ $image->path }}" data-fslightbox="gal-{{ $post->id }}">
                                <img src="{{ $image->path }}" alt="Post image" loading="lazy">
                            </a>
                            @if ($i === 3 && $remaining > 0)
                                <a href="{{ $image->path }}" data-fslightbox="gal-{{ $post->id }}"
                                    class="pk-img-more">
                                    +{{ $remaining }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ══════════════════════════════════════════
         VIDEO
    ══════════════════════════════════════════ --}}
        @if ($vid)
            @php
                $poster = $vid->thumbnail_path ?? ($vid->public_id ? $vid->poster_url ?? null : null);
                $playerUrl = url('rolls/' . $vid->id);
            @endphp
            <div class="pk-media">
                <a href="{{ $playerUrl }}" class="pk-video">

                    @if ($poster)
                        <img src="{{ $poster }}" alt="Video" loading="lazy"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="pk-video-placeholder" style="display:none">
                            <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="1.5"
                                opacity=".4" viewBox="0 0 24 24">
                                <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14" />
                                <rect x="2" y="6" width="13" height="12" rx="2" />
                            </svg>
                        </div>
                    @else
                        <div class="pk-video-placeholder">
                            <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="1.5"
                                opacity=".4" viewBox="0 0 24 24">
                                <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14" />
                                <rect x="2" y="6" width="13" height="12" rx="2" />
                            </svg>
                        </div>
                    @endif

                    <div class="pk-video-overlay">
                        <div class="pk-play">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="#0f1419">
                                <polygon points="5 3 19 12 5 21 5 3" />
                            </svg>
                        </div>
                    </div>

                    <span class="pk-video-pill">● Video</span>

                    @if (!empty($vid->duration))
                        <span class="pk-video-dur">{{ gmdate('i:s', $vid->duration) }}</span>
                    @endif

                </a>
            </div>
        @endif

        {{-- ══════════════════════════════════════════
         ACTION BAR
    ══════════════════════════════════════════ --}}
        <div class="pk-actions">

            {{-- Like --}}
            <button class="pk-action pk-like {{ $likedByMe ? 'pk-liked' : '' }}" wire:click="toggleLike">
                <svg width="18" height="18" viewBox="0 0 24 24"
                    fill="{{ $likedByMe ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06
                         a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                         1.06-1.06a5.5 5.5 0 000-7.78z" />
                </svg>
                {{ $likesCount > 0 ? number_format($likesCount) : '' }}
            </button>

            {{-- Comment --}}
            <a class="pk-action" href="{{ url('timeline/' . $post->id) }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                </svg>
                {{ $commentCount > 0 ? number_format($commentCount) : '' }}
            </a>

            {{-- Views --}}
            <span class="pk-action pk-view">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                {{ sumCounter($post->views, $post->views_external) }}
            </span>

            {{-- Share --}}
            <button class="pk-action pk-share" data-bs-toggle="modal"
                data-bs-target="#pk-share-{{ $post->id }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3" />
                    <circle cx="6" cy="12" r="3" />
                    <circle cx="18" cy="19" r="3" />
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
                </svg>
                Share
            </button>
        </div>

        {{-- ══════════════════════════════════════════
         COMMENTS
    ══════════════════════════════════════════ --}}
        <div class="pk-comments">
            @if (userLevel() === 'Basic' && $isOwner)
                <a href="{{ url('upgrade') }}"
                    style="font-size:12px;color:#5A4FDC;font-weight:600;text-decoration:none">
                    💰 Upgrade to monetize this post
                </a>
                <hr class="my-2">
            @endif

            <livewire:user.post-comments :post="$post" :wire:key="'post-comments-'.$post->id" />
        </div>

    </div>{{-- /pk-card --}}

    {{-- ══════════════════════════════════════════
     SHARE MODAL
══════════════════════════════════════════ --}}
    <div class="modal fade" id="pk-share-{{ $post->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 overflow-hidden" style="border-radius:16px">
                <div class="pk-modal-header d-flex align-items-center justify-content-between">
                    <span style="font-size:15px;font-weight:700;color:#fff">
                        Share post
                    </span>
                    <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-3" style="font-size:13px">
                        Share and earn when people engage with this post.
                    </p>

                    <div class="input-group mb-4" style="border-radius:8px;overflow:hidden">
                        <input type="text" class="form-control form-control-sm border-end-0"
                            value="{{ $shareUrl }}" readonly>
                        <button class="btn btn-outline-secondary btn-sm border-start-0"
                            style="font-size:12px;font-weight:600"
                            onclick="navigator.clipboard.writeText('{{ $shareUrl }}').then(()=>{this.textContent='Copied ✓';setTimeout(()=>this.textContent='Copy',1500)})">
                            Copy
                        </button>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                            target="_blank" class="pk-share-btn text-white" style="background:#1877f2">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}" target="_blank"
                            class="pk-share-btn text-white" style="background:#000">
                            <i class="fab fa-x-twitter"></i> X
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($shareUrl) }}" target="_blank"
                            class="pk-share-btn text-white" style="background:#25d366">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode($shareUrl) }}" target="_blank"
                            class="pk-share-btn text-white" style="background:#229ed9">
                            <i class="fab fa-telegram"></i> Telegram
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
