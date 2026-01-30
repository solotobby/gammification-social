@forelse ($posts as $timeline)

    {{-- <div wire:poll.visible.430s class="block block-rounded block-bordered" id="timelines"> --}}
    <div wire:ignore.self class="block block-rounded block-bordered" id="timelines">

        <div class="block-header block-header-default">

            <div class="d-flex align-items-center">
                @if (userLevel($timeline->user->id) == 'Basic')
                    <a class="img-link me-1" href="{{ url('profile/' . $timeline->user->username) }}">
                        <img class="img-avatar img-avatar32 img-avatar-thumb"
                            src="{{ $timeline->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                            alt="Avatar">
                    </a>

                    <a class="fw-semibold" href="{{ url('profile/' . $timeline->user->username) }}"
                        style="color: #5A4FDC">{{ displayName($timeline->user->name) }}</a>


                    <span class="fs-sm text-muted ms-2">{{ $timeline->created_at?->shortAbsoluteDiffForHumans() }}
                    </span>
                @elseif (userLevel($timeline->user->id) == 'Creator')
                    <a class="img-link me-1" href="{{ url('profile/' . $timeline->user->username) }}">
                        <img class="img-avatar img-avatar32 img-avatar-thumb"
                            src="{{ $timeline->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                            alt="Avatar">
                    </a>
                    {{-- Username + Verified Tick --}}
                    <div class="d-flex align-items-center">
                        <a class="fw-semibold me-1" href="{{ url('profile/' . $timeline->user->username) }}"
                            style="color: #5A4FDC">
                            {{ displayName($timeline->user->name) }}
                        </a>

                        {{-- @if ($timeline->user->is_verified) --}}
                        <!-- Twitter-style blue tick SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="#1DA1F2" class="ms-1">
                            <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z" />
                        </svg>
                        {{-- @endif --}}
                    </div>

                    {{-- Timestamp --}}
                    <span class="fs-sm text-muted ms-2">
                        {{ $timeline->created_at?->shortAbsoluteDiffForHumans() }}
                    </span>
                @else
                    {{-- Avatar --}}
                    <a class="img-link me-2" href="{{ url('profile/' . $timeline->user->username) }}">
                        <img class="img-avatar img-avatar32 img-avatar-thumb rounded-circle border border-primary border-2"
                            src="{{ $timeline->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                            alt="Avatar">
                    </a>

                    {{-- Username + Verified Tick --}}
                    <div class="d-flex align-items-center">
                        <a class="fw-semibold me-1" href="{{ url('profile/' . $timeline->user->username) }}"
                            style="color: #5A4FDC">
                            {{ displayName($timeline->user->name) }}
                        </a>

                        {{-- @if ($timeline->user->is_verified) --}}
                        <!-- Twitter-style blue tick SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="#1DA1F2" class="ms-1">
                            <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z" />
                        </svg>
                        {{-- @endif --}}
                    </div>

                    {{-- Timestamp --}}
                    <span class="fs-sm text-muted ms-2">
                        {{ $timeline->created_at?->shortAbsoluteDiffForHumans() }}
                    </span>
                @endif
            </div>

            <div class="block-options">

                @if (auth()->user()->id == $timeline->user_id)
                    <div class="dropdown">
                        <button type="button" class="btn-block-option dropdown-toggle text-muted fs-sm"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Est. Earning
                            {{ getCurrencyCode() }}{{ estimatedEarnings($timeline->id) }}
                        </button>

                        <div class="dropdown-menu dropdown-menu-end">

                            <a class="dropdown-item" href="{{ url('post/timeline/' . $timeline->id . '/analytics') }}">
                                <i class="far fa-fw fa-eye text-success me-1"></i>Posts Est. Earnings
                            </a>
                            @if (userLevel(auth()->user()->id) == 'Creator' || userLevel(auth()->user()->id) == 'Influencer')
                                {{-- <a class="dropdown-item"
                                    href="javascript:void(0)"
                                    wire:click="openEditModal({{ $timeline->unicode }})"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-block-from-edit">
                                        <i class="far fa-fw fa-edit text-primary me-1"></i> Edit Post
                                </a>

                                <a class="dropdown-item" href="javascript:void(0)" wire:click="deletePost({{ $timeline->unicode }})">
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
                            Est. Earning {{ getCurrencyCode() }}{{ estimatedEarnings($timeline->id) }}
                        </button>
                    </div>
                @endif


            </div>

        </div>

        <div class="block-content">
            <a href="{{ url('timeline/' . $timeline->id) }}" style="color: dimgrey">
                <p style="color: dimgrey">
                    {{-- {!! $timeline->content !!} --}}

                    {{-- {!! nl2br(e($timeline->content)) !!} --}}

                     {!! nl2br(e(\Illuminate\Support\Str::limit($timeline->content, 160))) !!}

                    @if (strlen($timeline->content) > 160)
                        <a href="{{ url('timeline/' . $timeline->id) }}" class="text-primary fw-semibold">
                            Read more
                        </a>
                    @endif

                </p>
            </a>

            @php
                $count = $timeline->images->count();
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


                    @foreach ($timeline->images as $image)
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


            <ul class="nav nav-pills fs-sm push " style="color: #5A4FDC">
                <li class="nav-item me-1">

                    @if ($timeline->isLikedBy(auth()->user()))
                        <a class="nav-link" wire:click="toggleLike({{ $timeline->unicode }})"
                            href="javascript:void(0)">
                            <i class="fas fa-heart text-danger me-1"></i>
                            {{ sumCounter($timeline->likes, $timeline->likes_external) }}
                        </a>
                    @else
                        <a class="nav-link" wire:click="toggleLike({{ $timeline->unicode }})"
                            href="javascript:void(0)">
                            <i class="far fa-heart opacity-50 me-1"></i>
                            {{ sumCounter($timeline->likes, $timeline->likes_external) }}
                        </a>
                    @endif


                    {{-- @if ($timeline->isLikedBy(auth()->user()))
                        <a class="nav-link" wire:click="toggleLike({{ $timeline->unicode }})"
                            href="javascript:void(0)">
                            <i class="fa fa-thumbs-down opacity-50 me-1"></i>
                            {{ sumCounter($timeline->likes, $timeline->likes_external) }}
                        </a>
                    @else
                        <a class="nav-link" wire:click="toggleLike({{ $timeline->unicode }})"
                            href="javascript:void(0)">
                            <i class="fa fa-thumbs-up opacity-50 me-1"></i>
                            {{ sumCounter($timeline->likes, $timeline->likes_external) }}
                        </a>
                    @endif --}}


                </li>
                <li class="nav-item me-1">
                    <a class="nav-link" href="{{ url('timeline/' . $timeline->id) }}">
                        <i class="fa fa-comment-alt opacity-50 me-1"></i>
                        {{ sumCounter($timeline->comments, $timeline->comments_external) }}
                    </a>
                </li>
                <li class="nav-item me-1">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-eye opacity-50 me-1"></i>
                        {{ sumCounter($timeline->views, $timeline->views_external) }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal"
                        data-bs-target="#modal-block-fromright-{{ $timeline->id }}">
                        <i class="fa fa-share opacity-50 me-1"></i>
                    </a>
                </li>
                @if (auth()->user()->id == $timeline->user_id)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('post/timeline/' . $timeline->id . '/analytics') }}">
                            <i class="si si-bar-chart opacity-50 me-1"></i>
                        </a>
                    </li>
                @endif
            </ul>


        </div>


        <div class="block-content block-content-full bg-body-light">
            @if (userLevel() == 'Basic' && $timeline->user_id == auth()->user()->id)
                <li class="fa fa-usd"> </li> <a href="{{ url('upgrade') }}" class="text-mute">Monetize This Post</a>
            @endif
            <hr>

            {{-- <p class="fs-sm">
                <i class="fa fa-thumbs-up text-info"></i>
                <i class="fa fa-comment text-danger"></i>
                <i class="far fa-smile text-warning me-1"></i>
                <a class="f w-semibold" href="javascript:void(0)">Brian Cruz</a>,
                <a class="fw-semibold" href="javascript:void(0)">Lori Grant</a>,
                <a class="fw-semibold" href="javascript:void(0)">and 150 others</a>
            </p> --}}

            {{-- 
            <form method="POST" wire:submit.prevent="commentFeed({{ $timeline->id }})">
                 <input type="text" wire:model.defer="messages.{{ $timeline->id }}" placeholder="Write a comment..." class="form-control form-control-alt">
                  <button type="submit" class="btn btn-primary mt-2">Comment</button>
            </form>

            @if ($timeline->postComments->count() > 0)
                <div class="pt-3 fs-sm">
                    <div class="d-flex">
                        <a class="flex-shrink-0 img-link me-2" href="javascript:void(0)">
                            <img class="img-avatar img-avatar32 img-avatar-thumb"
                                src="{{ asset('src/assets/media/avatars/avatar3.jpg') }}" alt="">
                        </a>
                    
                        <div class="flex-grow-1">
                            @foreach ($timeline->postComments as $comment)
                            <div class="mb-1">
                                <a class="fw-semibold" href="javascript:void(0)">{{ $comment->user->name }}</a>
                              
                                    
                                 <small>{{ $comment->created_at->diffForHumans() }}</small>

                                 <p>{{ nl2br(e($comment->message)) }}</p>

                                   <p>
                                        <a href="javascript:void(0)" class="me-1">Like</a>
                                        <a href="javascript:void(0)">Comment</a>
                                    </p>
                            </div>
                            @endforeach

                           

                        </div>
                        
                    </div>
                </div>
            @endif --}}

        </div>


    </div>

    <!-- From Right Block Modal -->
    <div class="modal fade" id="modal-block-fromright-{{ $timeline->id }}" tabindex="-1" role="dialog"
        aria-labelledby="modal-block-fromright" aria-hidden="true">
        <div class="modal-dialog modal-dialog-fromright" role="document">
            <div class="modal-content">
                <div class="block block-rounded block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Share Post</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <p>
                            Share this Post on all social media and make money when people view, like or comment on the
                            post
                        </p>
                        <p>
                            {{ url('show/' . $timeline->id) }}
                        </p>

                        <?php
                        $url = url('show/' . $timeline->id);
                        ?>


                        <button type="button" onclick="copyToClipboard('{{ $url }}')"
                            class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Copy Link</button>
                        <hr>
                        <ul class="nav nav-pills fs-sm push">
                            <li class="nav-item me-1">


                                <a class="nav-link"
                                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}"
                                    target="_blank">
                                    <i class="fab fa-facebook fa-2x opacity-50 me-1"></i>
                                </a>


                            </li>
                            <li class="nav-item me-1">
                                <a class="nav-link"
                                    href="https://twitter.com/intent/tweet?url={{ urlencode($url) }}&text=Check%20this%20out!"
                                    target="_blank">
                                    <i class="fab fa-square-x-twitter fa-2x opacity-50 me-1"></i>
                                </a>
                            </li>
                            <li class="nav-item me-1">
                                <a class="nav-link" href="https://www.instagram.com/?url={{ urlencode($url) }}"
                                    target="_blank">
                                    <i class="fab fa-instagram fa-2x opacity-50 me-1"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($url) }}"
                                    target="_blank">
                                    <i class="fab fa-linkedin-in fa-2x opacity-50 me-1"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="https://pinterest.com/pin/create/button/?url={{ urlencode($url) }}"
                                    target="_blank">
                                    <i class="fab fa-pinterest-p fa-2x opacity-50 me-1"></i>
                                </a>
                            </li>

                            {{-- <button type="button" class="btn btn-primary push mb-md-0" data-bs-toggle="modal" data-bs-target="#modal-block-fromright">Block Design</button> --}}
                            {{-- <button type="button" class="btn btn-alt-primary push mb-md-0" data-bs-toggle="modal" data-bs-target="#modal-default-fromright">Default</button> --}}
                        </ul>

                    </div>
                    <div class="block-content block-content-full text-end bg-body">
                        {{-- <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Close</button> --}}
                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END From Right Block Modal -->


@empty
    <div class="alert alert-info">
        You have not made any post. Click <a href="{{ url('timeline') }}">here</a> to start posting
    </div>
@endforelse

<button wire:click='loadMore' class="btn btn-alt-primary btn-sm">Load More Feeds</button>
