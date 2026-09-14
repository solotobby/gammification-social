{{-- Inline comments for PostContent (not a nested Livewire component). --}}
<form wire:submit.prevent="submitComment" class="pk-comment-form">
    <input
        type="text"
        wire:model="commentMessage"
        placeholder="Write a comment..."
        class="form-control form-control-alt"
        maxlength="500"
        autocomplete="off"
        aria-label="Write a comment"
    >
</form>

@if ($previewComments instanceof \Illuminate\Support\Collection && $previewComments->isNotEmpty())
    <div class="pt-3 fs-sm">
        @foreach ($previewComments as $comment)
            @php
                $replies = $comment['replies'] ?? [];
                $replyCount = count($replies);
            @endphp
            <div
                class="pk-comment-thread mb-3"
                wire:key="preview-comment-{{ $comment['id'] }}"
                x-data="{ replyOpen: false, showReplies: true }"
            >
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-2">
                        <x-user-avatar
                            :user-id="$comment['user_id'] ?? null"
                            :src="$comment['avatar'] ?? null"
                            :alt="$comment['name'] ?? 'User'"
                            :href="isset($comment['username']) ? url('profile/'.$comment['username']) : false"
                            size="sm"
                        />
                    </div>

                    <div class="flex-grow-1" style="min-width:0">
                        <div class="pk-comment-bubble">
                            <a class="fw-semibold text-dark text-decoration-none" href="{{ isset($comment['username']) ? url('profile/'.$comment['username']) : '#' }}">
                                {{ displayName($comment['name']) }}
                            </a>
                            <p class="mb-0 mt-1 text-break">{{ $comment['message'] }}</p>
                        </div>

                        {{-- Action buttons --}}
                        <div class="pk-comment-actions">
                            <span class="text-muted">
                                {{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}
                            </span>

                            @auth
                                <button
                                    type="button"
                                    class="pk-comment-action-btn"
                                    @click="replyOpen = !replyOpen; $nextTick(() => { if (replyOpen) $refs.replyInput?.focus() })"
                                >
                                    Reply
                                </button>
                            @endauth

                            @if ($this->canDeleteComment($comment['user_id'] ?? null))
                                <button
                                    type="button"
                                    class="pk-comment-action-btn pk-comment-action-btn--delete"
                                    wire:click="deleteComment('{{ $comment['id'] }}')"
                                    wire:confirm="Delete this comment?"
                                >
                                    Delete
                                </button>
                            @endif
                        </div>

                        {{-- Replies toggle if any --}}
                        @if ($replyCount > 0)
                            <div class="mt-1">
                                <button
                                    type="button"
                                    class="pk-replies-toggle"
                                    @click="showReplies = !showReplies"
                                >
                                    <i class="fa fa-reply fa-rotate-180"></i>
                                    <span x-text="showReplies ? 'Hide replies' : 'View ' + {{ $replyCount }} + ' ' + ({{ $replyCount }} === 1 ? 'reply' : 'replies')"></span>
                                </button>
                            </div>
                        @endif

                        {{-- Nested replies list --}}
                        @if ($replyCount > 0)
                            <div class="pk-replies-list" x-show="showReplies" x-transition>
                                @foreach ($replies as $reply)
                                    <div class="pk-reply-item" wire:key="preview-reply-{{ $reply['id'] }}">
                                        <div class="flex-shrink-0">
                                            <x-user-avatar
                                                :user-id="$reply['user_id'] ?? null"
                                                :src="$reply['avatar'] ?? null"
                                                :alt="$reply['name'] ?? 'User'"
                                                :href="isset($reply['username']) ? url('profile/'.$reply['username']) : false"
                                                size="xs"
                                            />
                                        </div>
                                        <div class="flex-grow-1" style="min-width:0">
                                            <div class="pk-reply-bubble">
                                                <a class="fw-semibold text-dark text-decoration-none" href="{{ isset($reply['username']) ? url('profile/'.$reply['username']) : '#' }}">
                                                    {{ displayName($reply['name']) }}
                                                </a>
                                                <p class="mb-0 mt-1 text-break">{{ $reply['message'] }}</p>
                                            </div>
                                            <div class="pk-comment-actions">
                                                <span class="text-muted">
                                                    {{ \Carbon\Carbon::parse($reply['created_at'])->diffForHumans() }}
                                                </span>
                                                @auth
                                                    <button
                                                        type="button"
                                                        class="pk-comment-action-btn"
                                                        @click="replyOpen = true; $nextTick(() => { $refs.replyInput?.focus() })"
                                                    >
                                                        Reply
                                                    </button>
                                                @endauth
                                                @if ($this->canDeleteComment($reply['user_id'] ?? null))
                                                    <button
                                                        type="button"
                                                        class="pk-comment-action-btn pk-comment-action-btn--delete"
                                                        wire:click="deleteComment('{{ $reply['id'] }}')"
                                                        wire:confirm="Delete this reply?"
                                                    >
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Inline reply form --}}
                        @auth
                            <div class="pk-inline-reply-box" x-show="replyOpen" x-transition x-cloak>
                                <form wire:submit.prevent="submitReply('{{ $comment['id'] }}')">
                                    <input
                                        type="text"
                                        class="form-control form-control-sm form-control-alt mb-2"
                                        wire:model="replyMessage.{{ $comment['id'] }}"
                                        placeholder="Write a reply…"
                                        x-ref="replyInput"
                                        autocomplete="off"
                                        maxlength="500"
                                    >
                                    @error('replyMessage.'.$comment['id'])
                                        <div class="text-danger small mb-1">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-end gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-link text-muted p-0 text-decoration-none"
                                            @click="replyOpen = false"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-primary py-0 px-2"
                                            wire:loading.attr="disabled"
                                            wire:target="submitReply('{{ $comment['id'] }}')"
                                        >
                                            <span wire:loading.remove wire:target="submitReply('{{ $comment['id'] }}')">Reply</span>
                                            <span wire:loading wire:target="submitReply('{{ $comment['id'] }}')">…</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@elseif ($standalone)
    <p class="pt-3 mb-0 text-muted fs-sm">No comments yet. Be the first to reply.</p>
@endif

@if ($standalone && $hasMoreComments)
    <div class="pt-2">
        <button type="button"
            class="btn btn-sm btn-link px-0 text-decoration-none fw-semibold"
            style="color:#5A4FDC"
            wire:click="loadMoreComments"
            wire:loading.attr="disabled"
            wire:target="loadMoreComments">
            <span wire:loading.remove wire:target="loadMoreComments">Load more comments</span>
            <span wire:loading wire:target="loadMoreComments">Loading…</span>
        </button>
    </div>
@endif
