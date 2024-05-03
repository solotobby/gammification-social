<div>
   <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Add Product') }}</div>
    
                    <div class="card-body">

                        @if(session()->has('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="#" wire:submit.prevent="save">
                            @csrf
    
                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Product Name') }}</label>
    
                                <div class="col-md-6">
                                    <input id="email" type="name"  wire:model="name" class="form-control @error('name') is-invalid @enderror" name="name" :value="old('name')"" required autocomplete="email" autofocus>
    
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
    
    
                </div>
            </div>
        </div>
    </div> 


    <!-- Timeline -->

    <!-- Update #2 -->
    <div class="block block-rounded block-bordered">
        <div class="block-header block-header-default">
            <div>
            <a class="img-link me-1" href="javascript:void(0)">
                <img class="img-avatar img-avatar32 img-avatar-thumb" src="assets/media/avatars/avatar11.jpg" alt="">
            </a>
            <a class="fw-semibold" href="javascript:void(0)">Ralph Murray</a>
            <span class="fs-sm text-muted">5 hrs ago</span>
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
            <p>Dolor posuere proin blandit accumsan senectus netus nullam curae, ornare laoreet adipiscing luctus mauris adipiscing pretium eget fermentum, tristique lobortis est ut metus lobortis tortor tincidunt himenaeos habitant quis dictumst proin odio sagittis purus mi, nec taciti vestibulum quis in sit varius lorem sit metus mi.</p>
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
            <form action="db_social_compact.html" method="POST" onsubmit="return false;">
            <input type="text" class="form-control form-control-alt" placeholder="Write a comment..">
            </form>
        </div>
    </div>
    <!-- END Update #2 -->
                      
</div>
