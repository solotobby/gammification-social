<div>

<style>
    .phk-post {
        background: #fff;
        border: 1px solid #eff0f2;
        border-radius: 16px;
        margin-bottom: 12px;
        transition: box-shadow 0.15s ease;
        overflow: hidden;
    }
    .phk-post:hover {
        box-shadow: 0 2px 12px rgba(90, 79, 220, 0.08);
    }

    /* ── Header ── */
    .phk-post-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 14px 16px 0;
    }
    .phk-avatar-wrap { position: relative; flex-shrink: 0; }
    .phk-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid transparent;
    }
    .phk-avatar.creator  { border-color: #1DA1F2; }
    .phk-avatar.influencer { border-color: #5A4FDC; }

    .phk-level-dot {
        position: absolute;
        bottom: 1px;
        right: 1px;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    .phk-level-dot.creator    { background: #1DA1F2; }
    .phk-level-dot.influencer { background: #5A4FDC; }

    .phk-meta { flex: 1; margin-left: 10px; min-width: 0; }
    .phk-display-name {
        font-weight: 700;
        font-size: 0.92rem;
        color: #0f1419;
        text-decoration: none;
        line-height: 1.2;
    }
    .phk-display-name:hover { text-decoration: underline; color: #5A4FDC; }

    .phk-verified {
        display: inline-block;
        width: 15px;
        height: 15px;
        vertical-align: middle;
        margin-left: 3px;
        margin-top: -2px;
    }

    .phk-username-row {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }
    .phk-username {
        font-size: 0.78rem;
        color: #8b98a5;
        text-decoration: none;
    }
    .phk-username:hover { text-decoration: underline; color: #5A4FDC; }
    .phk-dot { color: #ccd0d5; font-size: 0.7rem; }
    .phk-time { font-size: 0.78rem; color: #8b98a5; }

    /* ── Options menu ── */
    .phk-options .btn-block-option {
        color: #8b98a5;
        padding: 4px 8px;
        border-radius: 50%;
        border: none;
        background: transparent;
        transition: background 0.15s, color 0.15s;
    }
    .phk-options .btn-block-option:hover {
        background: rgba(90, 79, 220, 0.08);
        color: #5A4FDC;
    }

    /* ── Body ── */
    .phk-post-body { padding: 10px 16px 0; }

    .phk-content {
        font-size: 0.95rem;
        line-height: 1.6;
        color: #0f1419;
        word-break: break-word;
        white-space: pre-wrap;
    }
    .phk-content a { color: #5A4FDC; text-decoration: none; }
    .phk-content a:hover { text-decoration: underline; }

    .phk-see-more {
        color: #5A4FDC;
        font-size: 0.85rem;
        cursor: pointer;
        margin-left: 4px;
        text-decoration: none;
        font-weight: 600;
    }

    /* ── Trends ── */
    .phk-trends {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }
    .phk-trend-tag {
        display: inline-block;
        background: rgba(90, 79, 220, 0.07);
        color: #5A4FDC;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        text-decoration: none;
        transition: background 0.15s;
    }
    .phk-trend-tag:hover {
        background: rgba(90, 79, 220, 0.15);
        color: #5A4FDC;
    }

    /* ── Media grid ── */
    .phk-media { margin-top: 12px; border-radius: 12px; overflow: hidden; }

    .phk-media-grid {
        display: grid;
        gap: 2px;
        border-radius: 12px;
        overflow: hidden;
    }
    .phk-media-grid.count-1 { grid-template-columns: 1fr; }
    .phk-media-grid.count-2 { grid-template-columns: 1fr 1fr; }
    .phk-media-grid.count-3 { grid-template-columns: 1fr 1fr; }
    .phk-media-grid.count-3 .phk-media-item:first-child { grid-row: span 2; }
    .phk-media-grid.count-4 { grid-template-columns: 1fr 1fr; }

    .phk-media-item {
        position: relative;
        overflow: hidden;
        background: #f0f2f5;
        cursor: pointer;
    }
    .phk-media-grid.count-1 .phk-media-item { max-height: 460px; }
    .phk-media-grid.count-2 .phk-media-item,
    .phk-media-grid.count-3 .phk-media-item,
    .phk-media-grid.count-4 .phk-media-item { height: 200px; }

    .phk-media-item img,
    .phk-media-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.2s ease;
    }
    .phk-media-item:hover img,
    .phk-media-item:hover video { transform: scale(1.02); }

    .phk-media-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0);
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .phk-media-item:hover .phk-media-overlay { background: rgba(0,0,0,0.15); }

    .phk-play-btn {
        width: 52px;
        height: 52px;
        background: rgba(0,0,0,0.55);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.2rem;
        opacity: 0;
        transition: opacity 0.2s;
        backdrop-filter: blur(4px);
    }
    .phk-media-item:hover .phk-play-btn { opacity: 1; }

    .phk-video-duration {
        position: absolute;
        bottom: 8px;
        right: 10px;
        background: rgba(0,0,0,0.65);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
        letter-spacing: 0.03em;
    }

    /* ── Divider ── */
    .phk-divider {
        border: none;
        border-top: 1px solid #eff0f2;
        margin: 12px 0 0;
    }

    /* ── Actions bar ── */
    .phk-actions {
        display: flex;
        align-items: center;
        padding: 4px 8px 8px;
        gap: 2px;
    }
    .phk-action-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 7px 10px;
        border: none;
        background: transparent;
        border-radius: 20px;
        color: #8b98a5;
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        text-decoration: none;
        flex: 1;
        justify-content: center;
    }
    .phk-action-btn i { font-size: 1rem; }

    .phk-action-btn.like:hover   { background: rgba(249, 24, 128, 0.08); color: #f91880; }
    .phk-action-btn.comment:hover{ background: rgba(29, 155, 240, 0.08);  color: #1d9bf0; }
    .phk-action-btn.view:hover   { background: rgba(0, 186, 124, 0.08);   color: #00ba7c; }
    .phk-action-btn.share:hover  { background: rgba(90, 79, 220, 0.08);   color: #5A4FDC; }

    .phk-action-btn.liked { color: #f91880; }
    .phk-action-btn.liked i { animation: phk-pop 0.3s ease; }

    @keyframes phk-pop {
        0%   { transform: scale(1); }
        50%  { transform: scale(1.35); }
        100% { transform: scale(1); }
    }

    /* ── Earnings chip ── */
    .phk-earning-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #00ba7c;
        background: rgba(0,186,124,0.08);
        border-radius: 20px;
        padding: 3px 9px;
        margin-left: auto;
        white-space: nowrap;
    }
</style>

@php
    $level      = userLevel($post->user->id);
    $isOwner    = auth()->id() === $post->user_id;
    $imageCount = $post->images->count();
    $videoCount = isset($post->videos) ? $post->videos->count() : 0;
    $fullText   = strip_tags($post->content);
    $shortText  = Str::limit($fullText, 220);
    $needsMore  = Str::length($fullText) > 220;
    $postTrends = $post->trends ?? collect();
@endphp

<div class="phk-post">

    {{-- ── Header ── --}}
    <div class="phk-post-header">
        <div class="d-flex align-items-start flex-grow-1 min-w-0">

            {{-- Avatar --}}
            <div class="phk-avatar-wrap me-2">
                <a href="{{ url('profile/' . $post->user->username) }}">
                    <img class="phk-avatar {{ strtolower($level) }}"
                         src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                         alt="{{ $post->user->name }}">
                </a>
                @if ($level === 'Creator')
                    <span class="phk-level-dot creator"></span>
                @elseif ($level === 'Influencer')
                    <span class="phk-level-dot influencer"></span>
                @endif
            </div>

            {{-- Name / username / time --}}
            <div class="phk-meta">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <a class="phk-display-name" href="{{ url('profile/' . $post->user->username) }}">
                        {{ displayName($post->user->name) }}
                    </a>

                    @if (in_array($level, ['Creator', 'Influencer']))
                        <svg class="phk-verified" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             fill="{{ $level === 'Influencer' ? '#5A4FDC' : '#1DA1F2' }}">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>

                <div class="phk-username-row">
                    <a class="phk-username" href="{{ url('profile/' . $post->user->username) }}">
                        @{{ Str::limit($post->user->username, 14, '') }}
                    </a>
                    <span class="phk-dot">&middot;</span>
                    <span class="phk-time">{{ $post->created_at?->shortAbsoluteDiffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- Options / earning --}}
        <div class="d-flex align-items-center gap-2 phk-options ms-2">
            @if ($isOwner)
                <span class="phk-earning-chip">
                    <i class="fa fa-coins"></i>
                    {{ getCurrencyCode() }}{{ estimatedEarnings($post->id) }}
                </span>
            @endif

            @if ($isOwner)
                <div class="dropdown">
                    <button type="button"
                            class="btn-block-option dropdown-toggle"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="fa fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:12px">
                        <a class="dropdown-item" href="{{ url('post/timeline/' . $post->id . '/analytics') }}">
                            <i class="far fa-fw fa-chart-bar text-success me-2"></i> View earnings
                        </a>
                        @if (in_array($level, ['Creator', 'Influencer']))
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="far fa-fw fa-edit text-primary me-2"></i> Edit post
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="javascript:void(0)"
                               wire:click="deletePost({{ $post->unicode }})">
                                <i class="far fa-fw fa-trash-alt me-2"></i> Delete post
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="dropdown">
                    <button type="button"
                            class="btn-block-option dropdown-toggle"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="fa fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:12px">
                        <a class="dropdown-item" href="javascript:void(0)">
                            <i class="fa fa-fw fa-bookmark me-2 text-primary"></i> Bookmark
                        </a>
                        <a class="dropdown-item" href="javascript:void(0)">
                            <i class="fa fa-fw fa-user-minus me-2 text-warning"></i> Unfollow @{{ $post->user->username }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="javascript:void(0)">
                            <i class="fa fa-fw fa-flag me-2"></i> Report post
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Post text ── --}}
    <div class="phk-post-body">
        <div class="phk-content post-content-{{ $post->id }}"
             data-full="{{ e($fullText) }}"
             data-short="{{ e($shortText) }}"
             data-expanded="false">
            {!! $needsMore ? e($shortText) : e($fullText) !!}
            @if ($needsMore)
                <a href="#" class="phk-see-more" data-post="{{ $post->id }}">... more</a>
            @endif
        </div>

        {{-- ── Trends ── --}}
        @if ($postTrends->isNotEmpty())
            <div class="phk-trends">
                @foreach ($postTrends as $trend)
                    <span class="phk-trend-tag">#{{ $trend->name }}</span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Media: Images ── --}}
    @if ($imageCount > 0)
        <div class="phk-media px-2 mt-2">
            @php $gridClass = 'count-' . min($imageCount, 4); @endphp
            <div class="phk-media-grid {{ $gridClass }}">
                @foreach ($post->images->take(4) as $i => $image)
                    @php $isLast = ($i === 3 && $imageCount > 4); @endphp
                    <div class="phk-media-item">
                        <a class="img-lightbox" href="{{ asset($image->path) }}">
                            <img loading="lazy"
                                 src="{{ asset($image->path) }}"
                                 alt="Post image">
                            <div class="phk-media-overlay">
                                @if ($isLast)
                                    <span style="font-size:1.4rem;font-weight:700;color:#fff;text-shadow:0 1px 4px rgba(0,0,0,0.5)">
                                        +{{ $imageCount - 3 }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Media: Videos ── --}}
    @if ($videoCount > 0)
        <div class="phk-media px-2 mt-2">
            @foreach ($post->videos->take(1) as $video)
                <div class="phk-media-item" style="border-radius:12px; max-height:360px;"
                     wire:click="$dispatch('openVideoPlayer', { videoId: '{{ $video->id }}' })">
                    @if ($video->thumbnail_path)
                        <img loading="lazy"
                             src="{{ $video->thumbnail_path }}"
                             alt="Video thumbnail"
                             style="width:100%;height:360px;object-fit:cover;">
                    @else
                        <div style="width:100%;height:220px;background:#1a1a2e;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-film" style="font-size:2rem;color:#5A4FDC;opacity:0.4;"></i>
                        </div>
                    @endif
                    <div class="phk-media-overlay" style="background:rgba(0,0,0,0.25)">
                        <div class="phk-play-btn" style="opacity:1">
                            <i class="fa fa-play ms-1"></i>
                        </div>
                    </div>
                    @if ($video->formatted_duration)
                        <span class="phk-video-duration">{{ $video->formatted_duration }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Divider ── --}}
    <hr class="phk-divider">

    {{-- ── Actions ── --}}
    <div class="phk-actions">
        {{-- Like --}}
        <livewire:user.timeline-details-reaction :post="$post" :wire:key="'reactions-'.$post->id" />

        {{-- Comment --}}
        <button class="phk-action-btn comment"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#comments-collapse-{{ $post->id }}">
            <i class="far fa-comment"></i>
            <span>{{ formatNumber($post->comments) }}</span>
        </button>

        {{-- Views --}}
        <span class="phk-action-btn view" style="cursor:default">
            <i class="far fa-eye"></i>
            <span>{{ formatNumber($post->views + ($post->views_external ?? 0)) }}</span>
        </span>

        {{-- Share --}}
        <button class="phk-action-btn share"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#modal-block-fromright-{{ $post->id }}">
            <i class="fa fa-share-alt"></i>
        </button>
    </div>

    {{-- ── Comments (collapsed) ── --}}
    <div class="collapse" id="comments-collapse-{{ $post->id }}">
        <div class="px-3 pb-3">
            <livewire:user.timeline-details-comments :post="$post" :wire:key="'comments-'.$post->id" />
        </div>
    </div>

</div>

{{-- ── Share modal ── --}}
@php $shareUrl = url('timeline/' . $post->id); @endphp
<div class="modal fade" id="modal-block-fromright-{{ $post->id }}"
     tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-fromright" role="document">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden">
            <div class="block block-rounded block-themed block-transparent mb-0">
                <div class="block-header" style="background: linear-gradient(135deg, #5A4FDC, #7c6ef0)">
                    <h3 class="block-title">
                        <i class="fa fa-share-alt me-2"></i> Share Post
                    </h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option text-white"
                                data-bs-dismiss="modal">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <p class="text-muted mb-2" style="font-size:0.88rem">
                        Share this post and earn when people engage with it.
                    </p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-sm"
                               value="{{ $shareUrl }}" readonly id="share-url-{{ $post->id }}">
                        <button class="btn btn-sm btn-outline-primary"
                                onclick="navigator.clipboard.writeText('{{ $shareUrl }}')">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                           target="_blank" class="btn btn-sm" style="background:#1877F2;color:#fff;border-radius:8px">
                            <i class="fab fa-facebook-f me-1"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text=Check+this+out!"
                           target="_blank" class="btn btn-sm" style="background:#000;color:#fff;border-radius:8px">
                            <i class="fab fa-x-twitter me-1"></i> X
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($shareUrl) }}"
                           target="_blank" class="btn btn-sm" style="background:#25D366;color:#fff;border-radius:8px">
                            <i class="fab fa-whatsapp me-1"></i> WhatsApp
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($shareUrl) }}"
                           target="_blank" class="btn btn-sm" style="background:#0A66C2;color:#fff;border-radius:8px">
                            <i class="fab fa-linkedin-in me-1"></i> LinkedIn
                        </a>
                    </div>
                </div>
                <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-primary px-4"
                            style="border-radius:20px" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('phk-see-more')) return;
    e.preventDefault();
    const postId = e.target.dataset.post;
    const el = document.querySelector('.post-content-' + postId);
    if (!el) return;
    const expanded = el.dataset.expanded === 'true';
    if (!expanded) {
        el.innerHTML = el.dataset.full;
        el.dataset.expanded = 'true';
    }
});
</script>