<div>
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
      <!-- Post Update -->
      <div class="block block-bordered block-rounded">
        <div class="block-content block-content-full">
          <form wire:submit.prevent="post">
           
            <div class="input-group">
              <input type="text" wire:model="content" name="content"  :value="old('content')" class="form-control form-control-alt @error('content') is-invalid @enderror" placeholder="Say something amazing">
              
              <button type="submit" class="btn btn-primary border-0">
                <i class="fa fa-pencil-alt opacity-50 me-1"></i> Post
              </button>

            </div>
          </form>
        </div>
      </div>
      <!-- END Post Update -->

     
      <!-- Update #2 -->

      @if($timelines)
            @foreach ($timelines as $timeline)
                <div class="block block-rounded block-bordered" id="timelines">
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
                    <p>
                        {{ $timeline->content }}
                        ...Read More
                    </p>
                    <hr>
                    <ul class="nav nav-pills fs-sm push">
                        <li class="nav-item me-1">
                        <a class="nav-link" href="javascript:void(0)">
                            <i class="fa fa-thumbs-up opacity-50 me-1"></i> Like
                        </a>
                        </li>
                        <li class="nav-item me-1">
                        <a class="nav-link" href="javascript:void(0)">
                            <i class="fa fa-comment-alt opacity-50 me-1"></i> Comment
                        </a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)">
                            <i class="fa fa-share-alt opacity-50 me-1"></i> Share
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
                    <form action="" method="POST" onsubmit="return false;">
                        <input type="text" class="form-control form-control-alt" placeholder="Write a comment..">
                    </form>
                    </div>
                </div>
            @endforeach
      @else 
            NO TM
      @endif
      
      <!-- END Update #2 -->

</div>
