<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <style>
        .fa-heart {
            transition: transform .15s ease, color .15s ease;
        }

        .fa-heart:active {
            transform: scale(1.2);
        }
    </style>
    <div wire:ignore.self class="block block-rounded block-bordered" id="posts">

        <div class="block-header block-header-default">

            <div class="d-flex align-items-center">
                @if (userLevel($post->user->id) == 'Basic')
                    <a class="img-link me-1" href="{{ url('profile/' . $post->user->username) }}">
                        <img class="img-avatar img-avatar32 img-avatar-thumb"
                            src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                            alt="Avatar">
                    </a>

                    <a class="fw-semibold" href="{{ url('profile/' . $post->user->username) }}"
                        style="color: #5A4FDC">{{ displayName($post->user->name) }}</a>


                    <span class="fs-sm text-muted ms-2">{{ $post->created_at?->shortAbsoluteDiffForHumans() }}
                    </span>
                @elseif (userLevel($post->user->id) == 'Creator')
                    <a class="img-link me-1" href="{{ url('profile/' . $post->user->username) }}">
                        <img class="img-avatar img-avatar32 img-avatar-thumb"
                            src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                            alt="Avatar">
                    </a>
                    {{-- Username + Verified Tick --}}
                    <div class="d-flex align-items-center">
                        <a class="fw-semibold me-1" href="{{ url('profile/' . $post->user->username) }}"
                            style="color: #5A4FDC">
                            {{ displayName($post->user->name) }}
                        </a>

                        {{-- @if ($post->user->is_verified) --}}
                        <!-- Twitter-style blue tick SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="#1DA1F2" class="ms-1">
                            <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z" />
                        </svg>
                        {{-- @endif --}}
                    </div>

                    {{-- Timestamp --}}
                    <span class="fs-sm text-muted ms-2">
                        {{ $post->created_at?->shortAbsoluteDiffForHumans() }}
                    </span>
                @else
                    {{-- Avatar --}}
                    <a class="img-link me-2" href="{{ url('profile/' . $post->user->username) }}">
                        <img class="img-avatar img-avatar32 img-avatar-thumb rounded-circle border border-primary border-2"
                            src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                            alt="Avatar">
                    </a>

                    {{-- Username + Verified Tick --}}
                    <div class="d-flex align-items-center">
                        <a class="fw-semibold me-1" href="{{ url('profile/' . $post->user->username) }}"
                            style="color: #5A4FDC">
                            {{ displayName($post->user->name) }}
                        </a>

                        {{-- @if ($post->user->is_verified) --}}
                        <!-- Twitter-style blue tick SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="#1DA1F2" class="ms-1">
                            <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z" />
                        </svg>
                        {{-- @endif --}}
                    </div>

                    {{-- Timestamp --}}
                    <span class="fs-sm text-muted ms-2">
                        {{ $post->created_at?->shortAbsoluteDiffForHumans() }}
                    </span>
                @endif
            </div>



            <div class="block-options">

                @if (auth()->user()->id == $post->user_id)
                    <div class="dropdown">
                        <button type="button" class="btn-block-option dropdown-toggle text-muted fs-sm"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Est. Earning
                            {{ getCurrencyCode() }}{{ estimatedEarnings($post->id) }}
                        </button>

                        <div class="dropdown-menu dropdown-menu-end">

                            <a class="dropdown-item" href="{{ url('post/timeline/' . $post->id . '/analytics') }}">
                                <i class="far fa-fw fa-eye text-success me-1"></i>Posts Est. Earnings
                            </a>
                            @if (userLevel(auth()->user()->id) == 'Creator' || userLevel(auth()->user()->id) == 'Influencer')
                                {{-- <a class="dropdown-item"
                                    href="javascript:void(0)"
                                    wire:click="openEditModal({{ $post->unicode }})"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-block-from-edit">
                                        <i class="far fa-fw fa-edit text-primary me-1"></i> Edit Post
                                </a>

                                <a class="dropdown-item" href="javascript:void(0)" wire:click="deletePost({{ $post->unicode }})">
                                    <i class="far fa-fw fa-trash-alt text-danger me-1"></i> Delete Post
                                </a> --}}
                            @endif

                            {{-- <a class="dropdown-item" href="javascript:void(0)">
                                <i class="far fa-fw fa-thumbs-down text-warning me-1"></i> Stop following this user
                                </a>
                                <div role="separator" class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)">
                                <i class="fa fa-fw fa-exclamation-triangle me-1"></i> Report this post
                                </a>
                                <a class="dropdown-item" href="javascript:void(0)">
                                <i class="fa fa-fw fa-bookmark me-1"></i> Bookmark this post
                                </a> --}}
                        </div>
                    </div>
                @else
                    <div class="dropdown">
                        <button type="button" class="btn-block-option  text-muted fs-sm" aria-haspopup="true"
                            aria-expanded="false">
                            Est. Earning {{ getCurrencyCode() }}{{ estimatedEarnings($post->id) }}
                        </button>
                    </div>
                @endif

            </div>


        </div>



        <div class="block-content">
            <a href="{{ url('show/' . $post->id) }}" style="color: dimgrey">
                <p style="color: dimgrey">
                    {{-- {!! $post->content !!} --}}

                    {!! nl2br(e($post->content)) !!}

                </p>
            </a>

            @php
                $count = $post->images->count();
            @endphp

            @if ($count)
                <div class="row g-sm js-gallery img-fluid-100">

                    @php

                        $col = match ($count) {
                            1 => 'col-12',
                            2 => 'col-6',
                            3 => 'col-4',
                            4 => 'col-3',
                        };
                    @endphp


                    @foreach ($post->images as $image)
                        <div class="{{ $col }} mb-2">
                            <a class="img-link img-link-simple img-link-zoom-in img-lightbox"
                                href="{{ asset($image->path) }}">
                                <img class="img-fluid rounded" loading="lazy" src="{{ asset($image->path) }}"
                                    alt="Post image">
                            </a>
                        </div>
                    @endforeach

                </div>
            @endif

            <ul class="nav nav-pills fs-sm push align-items-center">

                {{-- ❤️ Like --}}
                <li class="nav-item me-2">
                    <a class="nav-link d-flex align-items-center gap-1" wire:click="toggleLike"
                        href="javascript:void(0)">
                        <i class="fa fa-heart transition"
                            style="
                    color: {{ $likedByMe ? '#e0245e' : '#6c757d' }};
                    opacity: {{ $likedByMe ? '1' : '.6' }};
                "></i>

                        <span>{{ number_format($likesCount) }}</span>
                    </a>
                </li>

                {{-- 💬 Comments --}}
                <li class="nav-item me-2">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-comment-alt opacity-50 me-1"></i>
                        {{ $commentCount }}
                        {{-- {{ sumCounter($post->comments, $post->comments_external) }} --}}
                    </a>
                </li>

                {{-- 👁 Views --}}
                <li class="nav-item me-2">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-eye opacity-50 me-1"></i>
                        {{ sumCounter($post->views, $post->views_external) }}
                    </a>
                </li>

                {{-- 🔁 Share --}}
                <li class="nav-item me-2">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal"
                        data-bs-target="#modal-block-fromright-{{ $post->id }}">
                        <i class="fa fa-share opacity-50"></i>
                    </a>
                </li>

                {{-- 📊 Analytics (owner only) --}}
                @if (auth()->id() === $post->user_id)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('post/timeline/' . $post->id . '/analytics') }}">
                            <i class="si si-bar-chart opacity-50"></i>
                        </a>
                    </li>
                @endif

            </ul>



            {{-- <ul class="nav nav-pills fs-sm push " style="color: #5A4FDC">
                <li class="nav-item me-1">


                    @if ($post->isLikedBy(auth()->user()))
                        <a class="nav-link" wire:click="toggleLike({{ $post->unicode }})" href="javascript:void(0)">
                            <i class="fa fa-thumbs-down opacity-50 me-1"></i>
                            {{ sumCounter($post->likes, $post->likes_external) }}
                        </a>
                    @else
                        <a class="nav-link" wire:click="toggleLike({{ $post->unicode }})" href="javascript:void(0)">
                            <i class="fa fa-thumbs-up opacity-50 me-1"></i>
                            {{ sumCounter($post->likes, $post->likes_external) }}
                        </a>
                    @endif


                </li>
                <li class="nav-item me-1">
                    <a class="nav-link" href="{{ url('show/' . $post->id) }}">
                        <i class="fa fa-comment-alt opacity-50 me-1"></i>
                        {{ sumCounter($post->comments, $post->comments_external) }}
                    </a>
                </li>
                <li class="nav-item me-1">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-eye opacity-50 me-1"></i>
                        {{ sumCounter($post->views, $post->views_external) }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal"
                        data-bs-target="#modal-block-fromright-{{ $post->id }}">
                        <i class="fa fa-share opacity-50 me-1"></i>
                    </a>
                </li>
                @if (auth()->user()->id == $post->user_id)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('post/timeline/' . $post->id . '/analytics') }}">
                            <i class="si si-bar-chart opacity-50 me-1"></i>
                        </a>
                    </li>
                @endif
            </ul> --}}


        </div>
         <div class="block-content block-content-full bg-body-light">

        <livewire:user.post-comments
                    :post="$post"
                    :wire:key="'post-comments-'.$post->id"
                />
         </div>

        {{-- @foreach ($posts as $timeline)
            <livewire:post-comments
                :post-id="$timeline->id"
                :wire:key="'post-comments-'.$timeline->id"
            />
        @endforeach --}}

        {{-- <livewire:user.post-comments
            :post-id="$timeline->id"
            :wire:key="'comments-'.$timeline->id"
        /> --}}


    </div>
    {{-- <div class="card mb-3">
        <div class="card-body">

            <strong>{{ $post->user->name }}</strong>
            <p class="mt-2">{{ $post->content }}</p>

            <div class="d-flex gap-3 text-muted">
                <button wire:click="toggleLike" class="btn btn-link p-0">
                    ❤️ {{ $likesCount }}
                </button>

                <span>
                    👁 {{ $post->views }}
                </span>
            </div>

            {{-- Comments --}}
    {{-- <livewire:post-comments
            :post-id="$post->id"
            :wire:key="'comments-'.$post->id"
        /> --
        </div>
    </div> --}}
</div>
