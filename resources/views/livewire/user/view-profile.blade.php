<div>
   <!-- Hero -->
   <div class="rounded border overflow-hidden push">
        <div class="bg-image pt-9" style="background-image: url('{{asset('src/assets/media/photos/photo19@2x.jpg')}}');"></div>
            <div class="px-4 py-3 bg-body-extra-light d-flex flex-column flex-md-row align-items-center">
                <a class="d-block img-link mt-n5" href="#">
                    <img class="img-avatar img-avatar128 img-avatar-thumb" src="{{asset('src/assets/media/avatars/avatar13.jpg')}}" alt="">
                </a>
            <div class="ms-3 flex-grow-1 text-center text-md-start my-3 my-md-0">
                <h1 class="fs-4 fw-bold mb-1">{{ $user->name }}</h1>
                <h2 class="fs-sm fw-medium text-muted mb-0">
                {{-- <a href="javascript:void(0)" class="text-muted">4,5k Followers</a> &bull;  --}}
                {{-- <a href="javascript:void(0)" class="text-muted">100 Following</a> &bull; --}}
                <a href="javascript:void(0)" class="text-muted">{{$user->total_views }} Views</a> &bull;
                <a href="javascript:void(0)" class="text-muted">{{ $user->total_likes }} Likes</a> &bull;
                <a href="javascript:void(0)" class="text-muted">{{ $user->total_comments }} Comments</a>
                </h2>
            </div>
            <div class="space-x-1">
                @if(auth()->user()->id == $user->id)
                <a href="{{ url('settings') }}" class="btn btn-sm btn-alt-secondary space-x-1">
                <i class="fa fa-pencil-alt opacity-50"></i>
                <span>Edit Profile</span>
                </a>
                @endif
            </div>
        </div>
    </div>
  <!-- END Hero -->

  <div class="row">
    <div class="col-md-8">
        <!-- Post Update -->
    
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
    <!-- Post Update -->
    {{-- <div class="block block-bordered block-rounded">
        <div class="block-content block-content-full">
        <form wire:submit.prevent="post">
        
            <div class="input-group">
            <input type="text" wire:model="content" name="content"  :value="old('content')" class="form-control form-control-alt @error('content') is-invalid @enderror" placeholder="Say something amazing" required>
            
            <button type="submit" class="btn btn-primary border-0">
                <i class="fa fa-pencil-alt opacity-50 me-1"></i> Post
            </button>

            </div>
        </form>
        </div>
    </div> --}}
    <!-- END Post Update -->

    
    <!-- Update #2 -->
            @forelse ($timelines as $timeline)
                <div class="block block-rounded block-bordered" id="timelines">
                    <div class="block-header block-header-default">
                    <div>
                        <a class="img-link me-1" href="{{ url('profile/'.$timeline->user->id) }}">
                        <img class="img-avatar img-avatar32 img-avatar-thumb" src="{{asset('src/assets/media/avatars/avatar11.jpg')}}" alt="">
                        </a>
                        <a class="fw-semibold" href="{{ url('profile/'.$timeline->user->id) }}">{{$timeline->user->name}}</a>
                        <span class="fs-sm text-muted">{{$timeline->created_at?->shortAbsoluteDiffForHumans()}} ago</span>
                    </div>
                    <div class="block-options">
                        <div class="dropdown">
                        <button type="button" class="btn-block-option dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                        <div class="dropdown-menu dropdown-menu-end">
                            @if(auth()->user()->id == $timeline->user_id)
                                <a class="dropdown-item" href="{{ url('post/timeline/'.$timeline->id.'/analytics') }}">
                                    <i class="far fa-fw fa-eye text-success me-1"></i> View Posts Analytics
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
                    </div>
                    </div>
                    <div class="block-content">
                        <a href="{{ url('show/'.$timeline->id) }}">
                            <p style="color: dimgrey">
                                {{ $timeline->content }}
                            </p>
                        </a>
                    <hr>
                    <ul class="nav nav-pills fs-sm push">
                        <li class="nav-item me-1">
                            @if($timeline->isLikedBy(auth()->user()))
                                        <a class="nav-link"  wire:click="toggleLike({{$timeline->unicode}})" href="javascript:void(0)">
                                            <i class="fa fa-thumbs-down opacity-50 me-1"></i> {{ $timeline->likes }}
                                        </a>
                            @else
                                        <a class="nav-link"  wire:click="toggleLike({{$timeline->unicode}})" href="javascript:void(0)">
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
                    </ul>
                    </div>
                    <div class="block-content block-content-full bg-body-light">
                    <p class="fs-sm">
                        <i class="fa fa-heart text-danger"></i>
                        <a class="fw-semibold" href="javascript:void(0)">Albert Ray</a>,
                        <a class="fw-semibold" href="javascript:void(0)">Melissa Rice</a>,
                        <a class="fw-semibold" href="javascript:void(0)">and 36 others</a>
                    </p>
                    {{-- <form action="" method="POST" onsubmit="return false;">
                        <input type="text" class="form-control form-control-alt" placeholder="Write a comment..">
                    </form> --}}
                    </div>
                </div>
            @empty
                no posts
            @endforelse
    
    <!-- END Update #2 -->

    </div>
    
    @include('layouts.engagement')
   

</div>
</div>
