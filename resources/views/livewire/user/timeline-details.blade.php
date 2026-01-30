<div>
    {{-- The whole world belongs to you. --}}

    <div class="row">
        <div class="col-md-8">
            <div class="block block-rounded block-bordered" id="timelines">
                <div class="block-header block-header-default">
                    <div>
                        <a class="img-link me-1" href="javascript:void(0)">
                            <img class="img-avatar img-avatar32 img-avatar-thumb"
                                src="{{ $post->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}"
                                alt="">
                        </a>
                        <a class="fw-semibold" href="{{ url('profile/' . $post->user->username) }}"
                            style="color: #5A4FDC">{{ displayName($post->user->name) }}</a>
                        <span class="fs-sm text-muted">{{ $post->created_at?->shortAbsoluteDiffForHumans() }}
                            ago</span>
                    </div>


                    <div class="block-options">

                        @if ($user->id == $post->user_id)
                            <div class="dropdown">
                                <button type="button" class="btn-block-option dropdown-toggle text-muted fs-sm"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Est. Earning
                                    {{ getCurrencyCode() }}{{ estimatedEarnings($post->id) }}
                                </button>

                                <div class="dropdown-menu dropdown-menu-end">

                                    <a class="dropdown-item"
                                        href="{{ url('post/timeline/' . $post->id . '/analytics') }}">
                                        <i class="far fa-fw fa-eye text-success me-1"></i> View Posts Earnings
                                    </a>

                                    {{-- @if (userLevel(auth()->user()->id) == 'Creator' || userLevel(auth()->user()->id) == 'Influencer')
                                        <a class="dropdown-item" href="javascript:void(0)">
                                            <i class="far fa-fw fa-edit text-primary me-1"></i> Edit Post
                                        </a>

                                        <a class="dropdown-item" href="javascript:void(0)"
                                            wire:click="deletePost({{ $post->unicode }})">
                                            <i class="far fa-fw fa-trash-alt text-danger me-1"></i> Delete Post
                                        </a>
                                    @endif --}}


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

                    <p style="color: dimgrey">
                        {{-- {!! $timeline->content !!} --}}
                        {!! nl2br(e($post->content)) !!}
                    </p>


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
                    <hr>



                    {{-- Reactions --}}
                    <livewire:user.timeline-details-reaction :post="$post" :wire:key="'reactions-'.$post->id" />

                    {{-- Comments --}}
                    <livewire:user.timeline-details-comments :post="$post" :wire:key="'comments-'.$post->id" />

                </div>

            </div>
            <a href="javascript:void(0)" class="btn btn-secondary btn-sm mb-4" onclick="history.back();">
                <i class="fa fa-arrow-left opacity-50 me-1"></i> Back
            </a>

        </div>

        @include('layouts.engagement')


    </div>

    <!-- From Right Block Modal -->
    <div class="modal fade" id="modal-block-fromright-{{ $post->id }}" tabindex="-1" role="dialog"
        aria-labelledby="modal-block-fromright" aria-hidden="true">
        <div class="modal-dialog modal-dialog-fromright" role="document">
            <div class="modal-content">
                <div class="block block-rounded block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Share Post</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <p>
                            Share this Post on all social media and make money when people view, like or comment on
                            the post
                        </p>
                        <p>
                            {{ url('timeline/' . $post->id) }}
                        </p>

                        <?php
                        
                        $url = url('timeline/' . $post->id);
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


    @include('layouts.onboarding')
</div>
