<div>
    {{-- Do your work, then step back. --}}

    <ul class="nav nav-pills fs-sm align-items-center">

        {{-- ❤️ Like --}}
        <li class="nav-item me-3" x-data="{ burst: false }">
            <a class="nav-link d-flex align-items-center gap-1" href="javascript:void(0)"
                @click="burst=true; setTimeout(()=>burst=false,300)" wire:click="toggleLike">

                <i class="fa fa-heart" :class="burst ? 'scale-150' : ''"
                    style="
                   color: {{ $likedByMe ? '#e0245e' : '#6c757d' }};
                   opacity: {{ $likedByMe ? 1 : 0.6 }};
               ">
                </i>

                <span>{{ number_format($likesCount) }}</span>
            </a>
        </li>

        {{-- 💬 Comments --}}
        <li class="nav-item me-3">
            <span class="nav-link">
                <i class="fa fa-comment-alt opacity-50 me-1"></i>
                {{ number_format($commentsCount) }}
            </span>
        </li>

        {{-- 👁 Views --}}
        <li class="nav-item">
            <span class="nav-link">
                <i class="fa fa-eye opacity-50 me-1"></i>
                {{ number_format($viewsCount) }}
            </span>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal"
                data-bs-target="#modal-block-fromright-{{ $post->id }}">
                <i class="fa fa-share opacity-50 me-1"></i>
            </a>
        </li>
        {{-- 📊 Analytics (owner only) --}}
        @if (auth()->id() === $post->user_id)
            <li class="nav-item">
                <a class="nav-link" href="{{ url('post/timeline/' . $post->id . '/analytics') }}">
                    <i class="si si-bar-chart opacity-50"></i> {{ getCurrencyCode() }}{{ estimatedEarnings($post->id) }}
                </a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)">
                    <i class="si si-bar-chart opacity-50"></i>
                    {{ getCurrencyCode() }}{{ estimatedEarnings($post->id) }}
                </a>
            </li>
        @endif
        {{-- @if ($user->id == $post->user_id)
            <li class="nav-item">
                <a class="nav-link" href="{{ url('post/timeline/' . $post->id . '/analytics') }}">
                    <i class="si si-bar-chart opacity-50 me-1"></i>
                </a>
            </li>
        @endif --}}

    </ul>



</div>
