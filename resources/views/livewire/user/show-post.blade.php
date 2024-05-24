<div>
    <div class="row">
        <div class="col-md-8">
            <div wire:poll.visible.2s class="block block-rounded block-bordered" id="timelines">
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
                    {{-- <a href="#" wire:click="show({{$timeline->id}})"> --}}
                        <p style="color: dimgrey">
                            {{ $timeline->content }}
                        </p>
                    {{-- </a> --}}
                <hr>
                <ul class="nav nav-pills fs-sm push">
                    <li class="nav-item me-1">
                        @if($timeline->userLikes()->count() > 0)
                        <a class="nav-link"  wire:click="dislike({{$timeline->unicode}})" href="javascript:void(0)">
                            <i class="fa fa-thumbs-down opacity-50 me-1"></i> {{ $timeline->likes }}
                        </a>
                        @else
                            <a class="nav-link"  wire:click="like({{$timeline->unicode}})" href="javascript:void(0)">
                                <i class="fa fa-thumbs-up opacity-50 me-1"></i> {{ $timeline->likes }}
                            </a>
                        @endif
                    </li>
                    <li class="nav-item me-1">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-comment-alt opacity-50 me-1"></i> {{$timeline->comments}}
                    </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-eye opacity-50 me-1"></i> {{$timeline->views}}
                    </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)">
                            <i class="fa fa-share opacity-50 me-1"></i>
                        </a>
                    </li>
                </ul>
                </div>
                <div class="block-content block-content-full bg-body-light">
                    {{-- <p class="fs-sm">
                    <i class="fa fa-thumbs-up text-info"></i>
                    <i class="fa fa-heart text-danger"></i>
                    <i class="far fa-smile text-warning me-1"></i>
                    <a class="fw-semibold" href="javascript:void(0)">Brian Cruz</a>,
                    <a class="fw-semibold" href="javascript:void(0)">Lori Grant</a>,
                    <a class="fw-semibold" href="javascript:void(0)">and 150 others</a>
                    </p> --}}
                   <form method="POST" wire:submit.prevent="comment">
                        <input type="text" wire:model="message" name="message"  :value="old('message')" class="form-control form-control-alt" placeholder="Write a comment.." required>
                    </form>
                    <div class="pt-3 fs-sm">

                        @foreach ($comments as $comment)
                            <div class="d-flex">
                                <a class="flex-shrink-0 img-link me-2" href="javascript:void(0)">
                                <img class="img-avatar img-avatar32 img-avatar-thumb" src="{{asset('src/assets/media/avatars/avatar3.jpg')}}" alt="">
                                </a>
                                <div class="flex-grow-1">
                                    <p class="mb-1">
                                        <a class="fw-semibold" href="javascript:void(0)">{{ $comment->user->name }}</a>   <span class="fs-sm text-muted">{{$comment->created_at?->shortAbsoluteDiffForHumans()}} ago</span> <br>
                                        {{ $comment->message }}
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        @if ($this->page <= $this->timeline->postComments()->paginate($this->perPage)->lastPage())
                           
                                <p> 
                                    <a href="javascript:void(0)" wire:click="loadMore">See More</a>
                                </p>
                           
                        @endif
                       
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Group Suggestions -->
            <div class="block block-rounded bg-body-dark">
            <div class="block-content block-content-full">
                <div class="row g-sm mb-2">
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo18.jpg" alt="">
                </div>
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo16.jpg" alt="">
                </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a class="fw-semibold" href="javascript:void(0)">Hiking</a>
                    <div class="fs-sm text-muted">68k Members</div>
                </div>
                <a class="btn btn-sm btn-alt-secondary d-inline-block" href="javascript:void(0)">
                    <i class="fa fa-fw fa-plus-circle"></i>
                </a>
                </div>
            </div>
            </div>
            <div class="block block-rounded bg-body-dark">
            <div class="block-content block-content-full">
                <div class="row g-sm mb-2">
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo12.jpg" alt="">
                </div>
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo13.jpg" alt="">
                </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a class="fw-semibold" href="javascript:void(0)">Travel Photos</a>
                    <div class="fs-sm text-muted">65k Members</div>
                </div>
                <a class="btn btn-sm btn-alt-secondary d-inline-block" href="javascript:void(0)">
                    <i class="fa fa-fw fa-plus-circle"></i>
                </a>
                </div>
            </div>
            </div>
            <div class="block block-rounded bg-body-dark">
            <div class="block-content block-content-full">
                <div class="row g-sm mb-2">
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo22.jpg" alt="">
                </div>
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo23.jpg" alt="">
                </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a class="fw-semibold" href="javascript:void(0)">Coding Frenzy</a>
                    <div class="fs-sm text-muted">109k Members</div>
                </div>
                <a class="btn btn-sm btn-alt-secondary d-inline-block" href="javascript:void(0)">
                    <i class="fa fa-fw fa-plus-circle"></i>
                </a>
                </div>
            </div>
            </div>
            <div class="block block-rounded bg-body-dark">
            <div class="block-content block-content-full">
                <div class="row g-sm mb-2">
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo9.jpg" alt="">
                </div>
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo6.jpg" alt="">
                </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a class="fw-semibold" href="javascript:void(0)">Nature Lovers</a>
                    <div class="fs-sm text-muted">32k Members</div>
                </div>
                <a class="btn btn-sm btn-alt-secondary d-inline-block" href="javascript:void(0)">
                    <i class="fa fa-fw fa-plus-circle"></i>
                </a>
                </div>
            </div>
            </div>
            <!-- END Group Suggestions -->
        </div>
    </div>
</div>
