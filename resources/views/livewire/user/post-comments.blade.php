<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}



    {{-- Comment Input --}}
    <form wire:submit.prevent="commentFeed">
        <input type="text" wire:model.defer="message" placeholder="Write a comment..."
            class="form-control form-control-alt">
    </form>

    {{-- Comments --}}
    @if ($comments->count())
        <div class="pt-3 fs-sm">
            @if ($comments->isNotEmpty())
                @foreach ($comments as $comment)
                    {{-- @if ($comment['id'] === 'demo-1') @continue @endif --}}

                    <div class="d-flex mb-2">
                        <a class="flex-shrink-0 img-link me-2" href="{{ url('profile/'.$comment['username']) }}">
                            <img class="img-avatar img-avatar32 img-avatar-thumb" {{-- src="{{ asset('src/assets/media/avatars/avatar3.jpg') }}" --}}
                                src="{{ $comment['avatar'] ?? asset('src/assets/media/avatars/avatar13.jpg') }}">
                        </a>

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

                            {{-- <small class="text-muted">
                                <a href="javascript:void(0)" class="me-2">Like</a>
                                <a href="javascript:void(0)">Reply</a>
                            </small> --}}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endif






</div>
