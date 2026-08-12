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
            <div class="d-flex mb-2" wire:key="preview-comment-{{ $comment['id'] }}">
                <a class="flex-shrink-0 img-link me-2" href="{{ url('profile/'.$comment['username']) }}">
                    <img class="img-avatar img-avatar32 img-avatar-thumb"
                        src="{{ $comment['avatar'] ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                        alt="{{ $comment['name'] }}">
                </a>

                <div class="flex-grow-1">
                    <div class="bg-light rounded px-3 py-2">
                        <a class="fw-semibold" href="{{ url('profile/'.$comment['username']) }}">
                            {{ displayName($comment['name']) }}
                        </a>
                        <small class="text-muted ms-1">
                            {{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}
                        </small>
                        <p class="mb-1">{{ $comment['message'] }}</p>
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
