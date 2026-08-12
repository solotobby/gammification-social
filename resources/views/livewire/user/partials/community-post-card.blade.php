@include('livewire.user.partials.post-card-ui')

@php
    $display = socialPostDisplay($post->content ?? '', 50);
    $mediaCount = $post->media->count();
    $showLinkEmbed = ! empty($display['embed']) && $mediaCount === 0;
    $showLinkCard = ! empty($display['link_card']) && $mediaCount === 0 && ! $showLinkEmbed;
    $profileUrl = $post->user ? url('profile/' . $post->user->username) : '#';
    $level = $post->user ? userLevel($post->user->id) : 'Basic';
    $isPostOwner = auth()->id() === ($post->user_id ?? null);
    $isFollowingAuthor = ($followingAuthorIds ?? collect())->contains($post->user_id);
@endphp

<article class="pk-card pk-feed-post" wire:init="recordView('{{ $post->id }}')" wire:key="cpost-{{ $post->id }}">
    <div class="pk-header">
        <div class="pk-avatar-col">
            <a href="{{ $profileUrl }}">
                <img class="pk-avatar"
                    src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                    alt="{{ $post->user->name ?? 'Deleted user' }}">
            </a>
        </div>

        <div style="flex:1;min-width:0">
            <div class="pk-name-row">
                <a class="pk-name" href="{{ $profileUrl }}">{{ displayName($post->user->name ?? 'Deleted user') }}</a>
                @if ($level === 'Creator')
                    <svg class="pk-tick" viewBox="0 0 22 22" fill="none">
                        <circle cx="11" cy="11" r="11" fill="#1d9bf0" />
                        <path d="M7 11l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                @elseif ($level === 'Influencer')
                    <svg class="pk-tick" viewBox="0 0 22 22" fill="none">
                        <circle cx="11" cy="11" r="11" fill="#5A4FDC" />
                        <path d="M7 11l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                @endif
            </div>
            <div class="pk-handle-row">
                <a class="pk-handle" href="{{ $profileUrl }}">
                    @<span>{{ $post->user->username ?? 'deleted' }}</span>
                </a>
                <span class="pk-sep">·</span>
                <span class="pk-time">{{ $post->created_at->diffForHumans() }}</span>
            </div>
        </div>

        @include('livewire.user.partials.post-card-actions', [
            'post' => $post,
            'context' => 'community',
            'isOwner' => $isPostOwner,
            'canDelete' => $this->canDeletePost($post->id),
            'isFollowing' => $isFollowingAuthor,
            'showEarnings' => false,
        ])
    </div>

    @if ($display['full_html'] !== '')
        <div class="pk-body">
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
        </div>
    @endif

    @if ($mediaCount > 0)
        @php
            $images = $post->media->where('type', '!=', 'video')->values();
            $imgCount = $images->count();
            $videos = $post->media->where('type', 'video');
        @endphp

        @if ($imgCount > 0)
            @php
                $shown = $images->take(4);
                $remaining = $imgCount - 4;
                $gridClass = 'n' . min($imgCount, 4);
            @endphp
            <div class="pk-media">
                <div class="fb-img-grid {{ $gridClass }}">
                    @foreach ($shown as $i => $item)
                        <div class="fb-img-cell" wire:click="openCommunityPhotoViewer('{{ $post->id }}', {{ $i }})"
                            role="button" tabindex="0"
                            @keydown.enter.prevent="$wire.openCommunityPhotoViewer('{{ $post->id }}', {{ $i }})"
                            aria-label="View photo {{ $i + 1 }}">
                            <img src="{{ $item->url }}" alt="Post image" loading="lazy">
                            @if ($i === 3 && $remaining > 0)
                                <span class="fb-img-more">+{{ $remaining }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @foreach ($videos as $item)
            <div class="pk-media">
                <video src="{{ $item->url }}" controls playsinline></video>
            </div>
        @endforeach
    @endif

    @if ($showLinkEmbed || $showLinkCard)
        <div class="pk-media pk-link-preview">
            @if ($showLinkEmbed)
                @include('livewire.user.partials.post-link-embed', ['embed' => $display['embed']])
            @else
                @include('livewire.user.partials.post-link-card', ['card' => $display['link_card']])
            @endif
        </div>
    @endif

    <div class="pk-actions">
        <button type="button" class="pk-action pk-like @if ($post->liked_by_me) pk-liked @endif"
            wire:click="toggleLike('{{ $post->id }}')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
            </svg>
            {{ number_format($post->likes_count) }}
        </button>

        <span class="pk-action">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
            </svg>
            {{ number_format($post->comments_count) }}
        </span>

        <span class="pk-action pk-view">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            {{ number_format($post->views_count) }}
        </span>
    </div>

    <div class="pk-comments">
        @if ($this->isMember())
            <div class="pk-comment-input-row">
                <input type="text" maxlength="500" wire:model="newComment.{{ $post->id }}"
                    wire:keydown.enter="addComment('{{ $post->id }}')"
                    placeholder="Write a comment…">
                <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                    wire:click="addComment('{{ $post->id }}')">Send</button>
            </div>
        @endif

        @foreach ($post->comments->take(3) as $comment)
            <div class="pk-fb-comment" wire:key="comment-{{ $comment->id }}">
                <img class="pk-fb-comment-av"
                    src="{{ $comment->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                    alt="{{ $comment->user->name ?? 'User' }}">
                <div class="pk-fb-comment-bubble">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <span class="pk-fb-comment-name">{{ $comment->user->name ?? 'Deleted user' }}</span>
                            <span class="pk-fb-comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                            <p class="pk-fb-comment-text mb-0">{{ $comment->content }}</p>
                        </div>
                        @if ($this->canDeleteComment($comment->id))
                            <button type="button" class="pk-icon-btn pk-icon-btn-sm pk-icon-danger"
                                wire:click="deleteComment('{{ $comment->id }}')"
                                aria-label="Delete comment">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14Z"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</article>
