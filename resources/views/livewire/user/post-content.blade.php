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
    @include('livewire.user.partials.post-card-ui')


    @php
        $level = userLevel($post->user->id);
        $isOwner = auth()->id() === $post->user_id;
        $display = socialPostDisplay($post->content, $formatText ? ($standalone ? null : 50) : null);
        $imgs = $post->images ?? collect();
        $imgCount = $imgs->count();
        $vid = $post->video ?? null;
        $postTrends = $post->trends ?? collect();
        $shareUrl = url('timeline/' . $post->id);
        $showLinkEmbed = ! empty($display['embed']) && ! $vid;
        $showLinkCard = ! empty($display['link_card']) && ! $vid && ! $showLinkEmbed;
        $plainText = plainPostText($post->content ?? '');
        $postText = ($display['preview_url'] && ($showLinkEmbed || $showLinkCard))
            ? stripUrlFromPlainText($plainText, $display['preview_url'])
            : $plainText;
    @endphp

    <div @class(['pk-card', 'pk-standalone' => $standalone]) wire:init="recordView">

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

            @include('livewire.user.partials.post-card-actions', [
                'post' => $post,
                'isOwner' => $isOwner,
                'estimatedEarnings' => $estimatedEarnings,
                'showPostMenu' => $showPostMenu,
                'isFollowing' => $isFollowing,
                'isBookmarked' => $isBookmarked,
                'canManagePost' => $canManagePost,
                'context' => 'post',
            ])

        </div>{{-- /pk-header --}}

        {{-- ══════════════════════════════════════════
         BODY — text + trends
    ══════════════════════════════════════════ --}}
        <div class="pk-body">

            {{-- Text --}}
            @if ($formatText && $display['full_html'] !== '')

                @if ($display['needs_more'])
                    <div x-data="{ expanded: false }">
                        <p class="pk-text">
                            <span x-show="!expanded">{!! $display['short_html'] !!}</span>
                            <span x-show="expanded" x-cloak>{!! $display['full_html'] !!}</span>
                            <button type="button" class="pk-see-more" x-show="!expanded" @click="expanded = true">
                                Show more
                            </button>
                        </p>
                    </div>
                @else
                    <p class="pk-text">{!! $display['full_html'] !!}</p>
                @endif

            @elseif (! $formatText && $postText !== '')
                <p class="pk-text">{!! nl2br(e($postText)) !!}</p>
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
                <div class="fb-img-grid {{ $gridClass }}">
                    @foreach ($shown as $i => $image)
                        <div class="fb-img-cell" wire:click="openPhotoViewer({{ $i }})" role="button" tabindex="0"
                            @keydown.enter.prevent="$wire.openPhotoViewer({{ $i }})" aria-label="View photo {{ $i + 1 }}">
                            <img src="{{ $image->path }}" alt="Post image" loading="lazy">
                            @if ($i === 3 && $remaining > 0)
                                <span class="fb-img-more">+{{ $remaining }}</span>
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
                $playerUrl = route('rolls.show', ['video' => $vid->id]);
            @endphp
            <div class="pk-media">
                <a href="{{ $playerUrl }}" class="fb-video">

                    @if ($poster)
                        <img src="{{ $poster }}" alt="Video" loading="lazy"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="fb-video-placeholder" style="display:none">
                            <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="1.5"
                                opacity=".4" viewBox="0 0 24 24">
                                <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14" />
                                <rect x="2" y="6" width="13" height="12" rx="2" />
                            </svg>
                        </div>
                    @else
                        <div class="fb-video-placeholder">
                            <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="1.5"
                                opacity=".4" viewBox="0 0 24 24">
                                <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14" />
                                <rect x="2" y="6" width="13" height="12" rx="2" />
                            </svg>
                        </div>
                    @endif

                    <div class="fb-video-overlay">
                        <div class="fb-play">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="#1c1e21">
                                <polygon points="5 3 19 12 5 21 5 3" />
                            </svg>
                        </div>
                    </div>

                    <span class="fb-video-pill">Roll</span>

                    @if (!empty($vid->duration))
                        <span class="fb-video-dur">{{ gmdate('i:s', $vid->duration) }}</span>
                    @endif

                </a>
            </div>
        @endif

        {{-- ══════════════════════════════════════════
         LINK PREVIEW / EMBED (bottom)
    ══════════════════════════════════════════ --}}
        @if ($showLinkEmbed || $showLinkCard)
            <div class="pk-media pk-link-preview">
                @if ($showLinkEmbed)
                    @include('livewire.user.partials.post-link-embed', ['embed' => $display['embed']])
                @else
                    @include('livewire.user.partials.post-link-card', ['card' => $display['link_card']])
                @endif
            </div>
        @endif

        {{-- ══════════════════════════════════════════
         ACTION BAR
    ══════════════════════════════════════════ --}}
        <div class="pk-actions">

            {{-- Like --}}
            <button class="pk-action pk-like {{ $likedByMe ? 'pk-liked' : '' }}"
                wire:click="toggleLike"
                wire:loading.attr="disabled"
                wire:target="toggleLike">
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

            @include('livewire.user.partials.post-card-comments')
        </div>

    </div>{{-- /pk-card --}}

    @if ($editingPost)
        <div class="pk-edit-overlay" wire:click.self="cancelEditPost" role="dialog" aria-modal="true" aria-label="Edit post">
            <div class="pk-edit-panel" @click.stop>
                <div class="pk-edit-head">
                    <strong>Edit post</strong>
                    <button type="button" class="pk-edit-close" wire:click="cancelEditPost" aria-label="Close">&times;</button>
                </div>
                <form wire:submit.prevent="savePost">
                    <textarea
                        class="pk-edit-textarea"
                        wire:model="editContent"
                        rows="5"
                        maxlength="5000"
                        placeholder="Update your post…"
                        required
                    ></textarea>
                    @error('editContent')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                    <div class="pk-edit-actions">
                        <button type="button" class="pk-edit-btn pk-edit-btn--ghost" wire:click="cancelEditPost">Cancel</button>
                        <button type="submit" class="pk-edit-btn pk-edit-btn--primary"
                            wire:loading.attr="disabled" wire:target="savePost">
                            <span wire:loading.remove wire:target="savePost">Save</span>
                            <span wire:loading wire:target="savePost">Saving…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
