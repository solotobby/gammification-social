<div>
    <div class="row">
        <div class="col-md-8">
            <div wire:poll.visible.10s class="block block-rounded block-bordered" id="timelines">
                <div class="block-header block-header-default">
                <div>
                    <a class="img-link me-1" href="javascript:void(0)">
                    <img class="img-avatar img-avatar32 img-avatar-thumb" src="{{asset('src/assets/media/avatars/avatar11.jpg')}}" alt="">
                    </a>
                    <a class="fw-semibold" href="javascript:void(0)">{{$timeline->user->name}}</a>
                    <span class="fs-sm text-muted">{{$timeline->created_at?->shortAbsoluteDiffForHumans()}} ago</span>
                </div>
                <div class="block-options">
                    <div class="dropdown">
                    <button type="button" class="btn-block-option dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="javascript:void(0)">
                        <i class="far fa-fw fa-times-circle text-danger me-1"></i> Hide similar posts
                        </a>
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
                </div>
                </div>
                <div class="block-content">
                    <a href="#" wire:click="show({{$timeline->id}})">
                        <p style="color: dimgrey">
                            {{ $timeline->content }}
                        </p>
                    </a>
                <hr>
                <ul class="nav nav-pills fs-sm push">
                    <li class="nav-item me-1">
                        @if($timeline->userLikes()->count() > 0)
                        <a class="nav-link"  wire:click="dislike({{$timeline->id}})" href="javascript:void(0)">
                            <i class="fa fa-thumbs-down opacity-50 me-1"></i> {{ $timeline->likes }}
                        </a>
                        @else
                            <a class="nav-link"  wire:click="like({{$timeline->id}})" href="javascript:void(0)">
                                <i class="fa fa-thumbs-up opacity-50 me-1"></i> {{ $timeline->likes }}
                            </a>
                        @endif
                    </li>
                    <li class="nav-item me-1">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-comment-alt opacity-50 me-1"></i> 17k
                    </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-eye opacity-50 me-1"></i> 1.6M
                    </a>
                    </li>
                </ul>
                </div>
                <div class="block-content block-content-full bg-body-light">
                    <p class="fs-sm">
                    <i class="fa fa-thumbs-up text-info"></i>
                    <i class="fa fa-heart text-danger"></i>
                    <i class="far fa-smile text-warning me-1"></i>
                    <a class="fw-semibold" href="javascript:void(0)">Brian Cruz</a>,
                    <a class="fw-semibold" href="javascript:void(0)">Lori Grant</a>,
                    <a class="fw-semibold" href="javascript:void(0)">and 150 others</a>
                    </p>
                    <form action="db_social_compact.html" method="POST" onsubmit="return false;">
                    <input type="text" class="form-control form-control-alt" placeholder="Write a comment..">
                    </form>
                    {{-- <div class="pt-3 fs-sm">
                    <div class="d-flex">
                        <a class="flex-shrink-0 img-link me-2" href="javascript:void(0)">
                        <img class="img-avatar img-avatar32 img-avatar-thumb" src="{{asset('src/assets/media/avatars/avatar3.jpg')}}" alt="">
                        </a>
                        <div class="flex-grow-1">
                        <p class="mb-1">
                            <a class="fw-semibold" href="javascript:void(0)">Andrea Gardner</a>
                            Vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit.
                        </p>
                        <p>
                            <a href="javascript:void(0)" class="me-1">Like</a>
                            <a href="javascript:void(0)">Comment</a>
                        </p>
                        
                        </div>
                    </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
