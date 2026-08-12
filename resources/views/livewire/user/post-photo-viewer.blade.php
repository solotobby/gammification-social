<div>
@if($open && ($post || $communityPost))
<style>
.fpv-overlay {
    position: fixed;
    inset: 0;
    z-index: 10050;
    background: rgba(0, 0, 0, .92);
    display: flex;
    align-items: stretch;
    justify-content: center;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}
.fpv-shell {
    display: flex;
    width: 100%;
    max-width: 1200px;
    height: 100%;
    max-height: 100vh;
    max-height: 100dvh;
    background: #fff;
    overflow: hidden;
}
.fpv-stage {
    flex: 1;
    min-width: 0;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.fpv-stage img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    display: block;
    user-select: none;
}
.fpv-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, .92);
    color: #050505;
    font-size: 18px;
    cursor: pointer;
    display: grid;
    place-items: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .25);
    z-index: 2;
}
.fpv-nav:hover { background: #fff; }
.fpv-nav.prev { left: 16px; }
.fpv-nav.next { right: 16px; }
.fpv-nav:disabled { opacity: .35; cursor: not-allowed; }
.fpv-counter {
    position: absolute;
    top: 16px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, .55);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 999px;
    z-index: 2;
}
.fpv-close-stage {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: rgba(0, 0, 0, .55);
    color: #fff;
    font-size: 22px;
    cursor: pointer;
    z-index: 2;
    display: none;
}
.fpv-rail {
    width: 360px;
    max-width: 100%;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-left: 1px solid #e4e6eb;
}
.fpv-rail-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-bottom: 1px solid #e4e6eb;
}
.fpv-rail-head img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.fpv-rail-user { flex: 1; min-width: 0; }
.fpv-rail-name {
    font-size: 15px;
    font-weight: 600;
    color: #050505;
    text-decoration: none;
    display: block;
}
.fpv-rail-name:hover { text-decoration: underline; }
.fpv-rail-time { font-size: 13px; color: #65676b; }
.fpv-close {
    width: 36px;
    height: 36px;
    border: none;
    background: #e4e6eb;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    color: #050505;
    flex-shrink: 0;
}
.fpv-close:hover { background: #d8dadf; }
.fpv-caption {
    padding: 12px 16px;
    font-size: 15px;
    line-height: 1.45;
    color: #050505;
    border-bottom: 1px solid #e4e6eb;
    max-height: 120px;
    overflow-y: auto;
}
.fpv-stats {
    padding: 10px 16px;
    font-size: 15px;
    font-weight: 600;
    color: #050505;
    border-bottom: 1px solid #e4e6eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.fpv-stats span { color: #65676b; font-weight: 400; font-size: 14px; }
.fpv-actions {
    display: flex;
    border-bottom: 1px solid #e4e6eb;
}
.fpv-act {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 8px;
    border: none;
    background: transparent;
    font-family: inherit;
    font-size: 15px;
    font-weight: 600;
    color: #65676b;
    cursor: pointer;
}
.fpv-act:hover { background: #f0f2f5; }
.fpv-act.liked { color: #1877f2; }
.fpv-act.liked svg { fill: #1877f2; stroke: #1877f2; }
.fpv-comments {
    flex: 1;
    overflow-y: auto;
    padding: 12px 16px;
    min-height: 120px;
}
.fpv-comment {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
    align-items: flex-start;
}
.fpv-comment img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}
.fpv-comment-body { flex: 1; min-width: 0; }
.fpv-comment-bubble {
    display: inline-block;
    background: #f0f2f5;
    border-radius: 18px;
    padding: 8px 12px;
    max-width: 100%;
}
.fpv-comment-user {
    font-size: 13px;
    font-weight: 600;
    color: #050505;
    text-decoration: none;
    margin-right: 6px;
}
.fpv-comment-text {
    font-size: 15px;
    color: #050505;
    line-height: 1.35;
    word-break: break-word;
}
.fpv-comment-time {
    font-size: 12px;
    color: #65676b;
    margin-top: 4px;
    padding-left: 12px;
}
.fpv-empty {
    text-align: center;
    color: #65676b;
    font-size: 14px;
    padding: 24px 12px;
}
.fpv-load {
    display: block;
    width: 100%;
    border: none;
    background: transparent;
    color: #1877f2;
    font-weight: 600;
    font-size: 14px;
    padding: 8px;
    cursor: pointer;
    font-family: inherit;
}
.fpv-compose {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    border-top: 1px solid #e4e6eb;
    background: #fff;
}
.fpv-compose img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}
.fpv-compose input {
    flex: 1;
    border: none;
    background: #f0f2f5;
    border-radius: 20px;
    padding: 10px 14px;
    font-family: inherit;
    font-size: 15px;
    outline: none;
}
.fpv-compose button {
    border: none;
    background: transparent;
    color: #1877f2;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    padding: 8px;
    font-family: inherit;
    opacity: .4;
}
.fpv-compose button.active { opacity: 1; }
@media (max-width: 768px) {
    .fpv-shell { flex-direction: column; max-width: 100%; }
    .fpv-stage { flex: 1; min-height: 45vh; }
    .fpv-rail { width: 100%; max-height: 55vh; border-left: none; border-top: 1px solid #e4e6eb; }
    .fpv-close { display: none; }
    .fpv-close-stage { display: grid; place-items: center; }
    .fpv-nav.prev { left: 8px; }
    .fpv-nav.next { right: 8px; }
}
</style>

@php
    $images = $this->viewerImages();
    $current = $images->get($imageIndex) ?? $images->first();
    $user = $this->viewerAuthor();
    $caption = $this->viewerCaption();
    $detailUrl = $this->viewerDetailUrl();
    $createdAt = $this->viewerCreatedAt();
@endphp

<div class="fpv-overlay"
     wire:click.self="close"
     x-data
     @keydown.arrow-left.window="$wire.prevImage()"
     @keydown.arrow-right.window="$wire.nextImage()"
     @keydown.escape.window="$wire.close()"
     role="dialog"
     aria-modal="true"
     aria-label="Photo viewer">

    <div class="fpv-shell" @click.stop>

        {{-- Image stage --}}
        <div class="fpv-stage">
            <button type="button" class="fpv-close-stage" wire:click="close" aria-label="Close">&times;</button>

            @if($images->count() > 1)
                <span class="fpv-counter">{{ $imageIndex + 1 }} / {{ $images->count() }}</span>
                <button type="button" class="fpv-nav prev" wire:click="prevImage"
                    @if($imageIndex <= 0) disabled @endif aria-label="Previous photo">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button type="button" class="fpv-nav next" wire:click="nextImage"
                    @if($imageIndex >= $images->count() - 1) disabled @endif aria-label="Next photo">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            @endif

            <img src="{{ $this->imageUrl($current) }}" alt="Photo {{ $imageIndex + 1 }}" wire:key="fpv-img-{{ $imageIndex }}">
        </div>

        {{-- FB sidebar --}}
        <aside class="fpv-rail">
            <div class="fpv-rail-head">
                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1877f2&color=fff&size=80' }}" alt="">
                <div class="fpv-rail-user">
                    <a class="fpv-rail-name" href="{{ url('profile/'.$user->username) }}">{{ displayName($user->name) }}</a>
                    <div class="fpv-rail-time">{{ $createdAt?->diffForHumans() }}</div>
                </div>
                <button type="button" class="fpv-close" wire:click="close" aria-label="Close">&times;</button>
            </div>

            @if($caption)
                <div class="fpv-caption">{!! nl2br(e($caption)) !!}</div>
            @endif

            <div class="fpv-stats">
                <span>
                    @if($likesCount > 0)
                        <strong>{{ number_format($likesCount) }}</strong> {{ Str::plural('like', $likesCount) }}
                    @endif
                    @if($likesCount > 0 && $commentsCount > 0) · @endif
                    @if($commentsCount > 0)
                        <strong>{{ number_format($commentsCount) }}</strong> {{ Str::plural('comment', $commentsCount) }}
                    @endif
                    @if($likesCount === 0 && $commentsCount === 0)
                        Be the first to like or comment
                    @endif
                </span>
                <a href="{{ $detailUrl }}" style="font-size:13px;color:#1877f2;text-decoration:none;font-weight:600">Open post</a>
            </div>

            <div class="fpv-actions">
                <button type="button" class="fpv-act {{ $likedByMe ? 'liked' : '' }}"
                    wire:click="toggleLike" wire:loading.attr="disabled">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $likedByMe ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                    </svg>
                    Like
                </button>
                <button type="button" class="fpv-act" onclick="document.getElementById('fpv-comment-input')?.focus()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                    Comment
                </button>
                <a class="fpv-act" href="{{ $detailUrl }}" style="text-decoration:none">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                        <path d="M8.59 13.51l6.82 3.98M15.41 6.51l-6.82 3.98"/>
                    </svg>
                    Share
                </a>
            </div>

            <div class="fpv-comments">
                @forelse($comments as $comment)
                    <div class="fpv-comment" wire:key="fpv-c-{{ $comment['id'] }}">
                        <img src="{{ $comment['avatar'] ?? 'https://ui-avatars.com/api/?name='.urlencode($comment['name']).'&size=64&background=e4e6eb&color=050505' }}" alt="">
                        <div class="fpv-comment-body">
                            <div class="fpv-comment-bubble">
                                <a class="fpv-comment-user" href="{{ url('profile/'.$comment['username']) }}">{{ displayName($comment['name']) }}</a>
                                <span class="fpv-comment-text">{{ $comment['message'] }}</span>
                            </div>
                            <div class="fpv-comment-time">{{ $comment['created_at']->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <p class="fpv-empty">No comments yet.</p>
                @endforelse

                @if($hasMoreComments)
                    <button type="button" class="fpv-load" wire:click="loadMoreComments" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="loadMoreComments">View more comments</span>
                        <span wire:loading wire:target="loadMoreComments">Loading…</span>
                    </button>
                @endif
            </div>

            <form class="fpv-compose" wire:submit.prevent="submitComment">
                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=64&background=e4e6eb&color=050505' }}" alt="">
                <input id="fpv-comment-input" type="text" wire:model.live="commentText"
                    placeholder="Write a comment…" maxlength="500" autocomplete="off">
                <button type="submit" class="{{ trim($commentText) !== '' ? 'active' : '' }}"
                    @disabled(trim($commentText) === '') wire:loading.attr="disabled">
                    Post
                </button>
            </form>
        </aside>
    </div>
</div>
@endif
</div>
