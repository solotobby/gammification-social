<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <style>
        .fa-heart {
            transition: transform .15s ease, color .15s ease;
        }

        .fa-heart:active {
            transform: scale(1.2);
        }

        .link-preview-card {
            display: block;
            margin-top: 8px;
            border: 1px solid #e1e8ed;
            border-radius: 12px;
            padding: 12px;
            text-decoration: none;
            color: inherit;
        }

        .link-preview-host {
            font-weight: 600;
            color: #1d9bf0;
        }

        .link-preview-url {
            font-size: 14px;
            color: #536471;
            word-break: break-all;
        }

        .og-card {
            display: block;
            border: 1px solid #e1e8ed;
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            margin-top: 8px;
        }

        .og-image {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
        }

        .og-body {
            padding: 12px;
        }

        .og-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .og-desc {
            font-size: 14px;
            color: #536471;
        }

        .og-host {
            font-size: 13px;
            color: #8899a6;
            margin-top: 6px;
        }


        .post-video {
            aspect-ratio: 18/29;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .post-video:hover {
            transform: scale(1.02);
        }

        .post-video img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-play-overlay {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .post-video:hover .video-play-overlay {
            opacity: 1;
        }

        .play-button {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #000;
            transition: transform 0.2s;
        }

        .play-button:hover {
            transform: scale(1.1);
        }

        .video-duration {
            font-size: 12px;
        }

        .video-stats {
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
        }

        .image-overlay {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 8px;
        }
    </style>
    {{-- <div wire:ignore.self class="block block-rounded block-bordered" id="posts"> --}}
    <div wire:poll.visible.430s class="block block-rounded block-bordered" id="posts">

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

                    <a href="{{ url('profile/', $post->user->username) }}" class="fs-sm text-muted mx-1"
                        title="{{ $post->user->username }}">
                        @<span>{{ Str::limit($post->user->username, 10, '') }}</span>
                    </a>
                    <span class="mx-1 text-muted">&middot;</span>

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

                    <a href="{{ url('profile/', $post->user->username) }}" class="fs-sm text-muted mx-1"
                        title="{{ $post->user->username }}">
                        @<span>{{ Str::limit($post->user->username, 10, '') }}</span>
                    </a>
                    <span class="mx-1 text-muted">&middot;</span>

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
                            fill="#1DA1F2">
                            <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z" />
                        </svg>
                        {{-- @endif --}}
                    </div>

                    <a href="{{ url('profile/', $post->user->username) }}" class="fs-sm text-muted mx-1"
                        title="{{ $post->user->username }}">
                        {{-- @<span>{{ $post->user->username }}</span> --}}
                        @<span>{{ Str::limit($post->user->username, 10, '') }}</span>
                    </a>
                    <span class="mx-1 text-muted">&middot;</span>

                    {{-- Timestamp --}}
                    <span class="fs-sm text-muted">
                        {{ $post->created_at?->shortAbsoluteDiffForHumans() }}
                    </span>
                @endif
            </div>



            {{-- <div class="block-options">

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
                                <a class="dropdown-item" href="javascript:void(0)"
                                    wire:click="openEditModal({{ $post->unicode }})" data-bs-toggle="modal"
                                    data-bs-target="#modal-block-from-edit">
                                    <i class="far fa-fw fa-edit text-primary me-1"></i> Edit Post
                                </a>

                                <a class="dropdown-item" href="javascript:void(0)"
                                    wire:click="deletePost({{ $post->unicode }})">
                                    <i class="far fa-fw fa-trash-alt text-danger me-1"></i> Delete Post
                                </a>
                            @endif
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="far fa-fw fa-thumbs-down text-warning me-1"></i> Stop following this user
                            </a>
                            <div role="separator" class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="fa fa-fw fa-exclamation-triangle me-1"></i> Report this post
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="fa fa-fw fa-bookmark me-1"></i> Bookmark this post
                            </a>
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

            </div> --}}


        </div>

        @php
            $url = extractFirstUrl($post->content);
        @endphp

        <div class="block-content">

            <div class="post-content" data-full="{!! e(strip_tags($post->content)) !!}"
                data-short="{{ e(Str::limit(strip_tags($post->content), 130)) }}" data-expanded="false">

                {!! Str::limit(strip_tags($post->content), 130) !!}

                @if (Str::length(strip_tags($post->content)) > 130)
                    <a href="#" class="see-more">See more</a>
                @endif
            </div>

            @php
                $imageCount = $post->images->count();
                // $videoCount = $post->videos->count();
            @endphp


            {{-- @if ($post->has_video && $post->video && $post->video->processing_status === 'completed')
               
                <div class="post-video position-relative" style="cursor: pointer;">
                    <a href="{{ url('rolls', $post->video->id) }}" class="stretched-link"></a>

                   
                    <img src="{{ $post->video->thumbnail_path }}" alt="Video thumbnail" class="w-100 rounded">

                    
                    <div class="video-play-overlay position-absolute top-50 start-50 translate-middle">
                        <div class="play-button">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>

                    
                    <span class="video-duration position-absolute bottom-0 end-0 m-2 badge bg-dark">
                        {{ $post->video->formatted_duration }}
                    </span>

                
                    <div class="video-stats position-absolute bottom-0 start-0 m-2 text-white">
                        <small>
                            <i class="fas fa-eye"></i>
                            {{ formatCount($post->video->view_count) }}
                        </small>
                    </div>
                </div>
            @endif
            @if ($post->has_video && $post->video && $post->video->processing_status === 'processing')
                <div class="alert alert-info">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Video is processing... Check back soon!
                </div>
            @endif --}}


            {{-- image Processsing --}}

            @if ($imageCount)
                <hr>
                <div class="row g-sm js-gallery img-fluid-100">

                    @php

                        $col = match ($imageCount) {
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

            {{-- @php
                $url = extractFirstUrl($post->content);
                $preview = $url ? getLinkPreview($url) : null;
            @endphp

            @if ($preview)
                <a href="{{ $preview['url'] }}" target="_blank" class="og-card">
                    @if ($preview['image'])
                        <img src="{{ $preview['image'] }}" class="og-image">
                    @endif

                    <div class="og-body">
                        <div class="og-title">{{ $preview['title'] }}</div>
                        <div class="og-desc">{{ $preview['description'] }}</div>
                        <div class="og-host">{{ @$preview['host'] }}</div>
                    </div>
                </a>
            @endif --}}

            {{-- @if ($url && ($embed = youtubeEmbed($url)))
                    <iframe width="100%" height="315"
                            src="{{ $embed }}"
                            frameborder="0"
                            allowfullscreen></iframe>
                @endif --}}

            {{-- @php
                $url = extractFirstUrl($post->content);
                $preview = $url ? getLinkPreview($url) : null;
            @endphp

            @if ($preview && !isEmbeddablePlatform($url))
                <a href="{{ $preview['url'] }}" target="_blank" rel="noopener" class="og-card">

                    @if (!empty($preview['image']))
                        <div class="og-image-wrapper">
                            <img src="{{ $preview['image'] }}" alt="{{ $preview['title'] ?? 'Link preview image' }}"
                                loading="lazy">
                        </div>
                    @endif

                    <div class="og-body">
                        @if (!empty($preview['title']))
                            <div class="og-title">
                                {{ Str::limit($preview['title'], 80) }}
                            </div>
                        @endif

                        @if (!empty($preview['description']))
                            <div class="og-description">
                                {{ Str::limit($preview['description'], 140) }}
                            </div>
                        @endif

                        <div class="og-host">
                            {{ @$preview['host'] }}
                        </div>
                    </div>
                </a>
            @endif --}}




            {{-- @if ($url && ($embed = youtubeEmbed($url)))
                <div class="youtube-embed">
                    <iframe width="100%" height="315" src="{{ $embed }}" frameborder="0"
                        allow="accelerometer; allow="autoplay; clipboard-write; encrypted-media; gyroscope;
                        picture-in-picture; web-share" allowfullscreen>
                    </iframe>
                </div>
            @endif

            @if ($url && isInstagramUrl($url))
                <blockquote class="instagram-media" data-instgrm-permalink="{{ $url }}"
                    data-instgrm-version="14" style="width:100%; margin: 0 auto;">
                </blockquote>
            @endif --}}






            {{-- @if ($url)
                @php $preview = buildLinkPreview($url); @endphp

                <a href="{{ $preview['url'] }}" target="_blank" class="link-preview-card">

                    <div class="link-preview-host">
                        {{ @$preview['host'] }}
                    </div>

                    <div class="link-preview-url">
                        {{ $preview['url'] }}
                    </div>
                </a>
            @endif --}}



            <ul class="nav nav-pills fs-sm push align-items-center">

                {{-- ❤️ Like --}}
                <li class="nav-item me-2">
                    <a class="nav-link d-flex align-items-center gap-1" wire:click="toggleLike"
                        href="javascript:void(0)">
                        <i class="fa fa-heart transition"
                            style="
                    color: {{ $likedByMe ? '#e0245e' : '#6c757d' }};
                    opacity: {{ $likedByMe ? '1' : '.6' }};"></i>

                        <span>{{ number_format($likesCount) }}</span>
                    </a>
                </li>

                {{-- 💬 Comments --}}
                <li class="nav-item me-2">
                    <a class="nav-link" href="{{ url('timeline/' . $post->id) }}">
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
                            {{ getCurrencyCode() }}{{ estimatedEarnings($post->id) }}
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

            </ul>

        </div>

        {{-- Comment section --}}
        <div class="block-content block-content-full bg-body-light">

            <livewire:user.post-comments :post="$post" :wire:key="'post-comments-'.$post->id" />
        </div>


        {{-- @if($showPlayer)

            <livewire:user.video-player 
                :videoId="$activeVideoId"
                wire:key="video-player-{{ $activeVideoId }}"
            />

        @endif --}}




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
                        </ul>

                    </div>
                    <div class="block-content block-content-full text-end bg-body">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END From Right Block Modal -->


    <script>
        document.addEventListener('click', function(e) {
            if (!e.target.classList.contains('see-more')) return;

            e.preventDefault();

            const container = e.target.closest('.post-content');
            if (!container) return;

            container.innerText = container.dataset.full;
        });
    </script>



</div>
