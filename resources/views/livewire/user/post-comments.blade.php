<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}



    {{-- Comment Input --}}
    <form wire:submit.prevent="commentFeed">
        <input type="text" wire:model="message" placeholder="Write a comment..."
            class="form-control form-control-alt"
            maxlength="500"
            autocomplete="off">
    </form>

    {{-- Comments --}}
    @if ($comments && $comments->count())
        <div class="pt-3 fs-sm">
            @foreach ($comments as $comment)
                <div class="d-flex mb-2" wire:key="comment-{{ $comment['id'] }}">
                    <div class="flex-shrink-0 me-2">
                        <x-user-avatar
                            :user-id="$comment['user_id'] ?? null"
                            :src="$comment['avatar'] ?? null"
                            :alt="$comment['name'] ?? 'User'"
                            :href="isset($comment['username']) ? url('profile/'.$comment['username']) : false"
                            size="sm"
                        />
                    </div>

                    <div class="flex-grow-1">
                        <div class="bg-light rounded px-3 py-2">
                            <a class="fw-semibold" href="{{ url('profile/'.$comment['username']) }}">
                                {{ displayName($comment['name']) }}
                            </a>
                            <small class="text-muted ms-1">
                                {{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}
                            </small>

                            <p class="mb-1">
                                {{ $comment['message'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif






</div>
