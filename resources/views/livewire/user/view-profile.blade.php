<div>
    <!-- Hero -->
    <div class="rounded border overflow-hidden push">
        <div class="bg-image pt-9"
            style="background-image: url('{{ asset('src/assets/media/photos/photo19@2x.jpg') }}');"></div>
        <div class="px-4 py-3 bg-body-extra-light d-flex flex-column flex-md-row align-items-center">

            @if (userLevel($user->id) == 'Basic')
                <a class="d-block img-link mt-n5" href="#">
                    <img class="img-avatar img-avatar128 img-avatar-thumb"
                        src="{{ asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                </a>
                <div class="ms-3 flex-grow-1 text-center text-md-start my-3 my-md-0">
                <h1 class="fs-4 fw-bold mb-1">{{ $user->name }}</h1>
                @elseif (userLevel($user->id) == 'Creator')

                    <a class="d-block img-link mt-n5" href="#">
                        <img class="img-avatar img-avatar128 img-avatar-thumb"
                            src="{{ asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                    </a>
                    <div class="ms-3 flex-grow-1 text-center text-md-start my-3 my-md-0">
                        <h1 class="fs-4 fw-bold mb-1">{{ $user->name }}

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="#1DA1F2" class="ms-1">
                                <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z" />
                            </svg>
                        </h1>
                    @else

                        <a class="d-block img-link mt-n5" href="#">
                            <img class="img-avatar img-avatar128 img-avatar-thumb rounded-circle border border-primary border-2"
                                src="{{ asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                        </a>
                        <div class="ms-3 flex-grow-1 text-center text-md-start my-3 my-md-0">
                            <h1 class="fs-4 fw-bold mb-1">{{ $user->name }}

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="#1DA1F2" class="ms-1">
                                    <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z" />
                                </svg>
                            </h1>
            @endif

            <h2 class="fs-sm fw-medium text-muted mb-0">
                <a href="javascript:void(0)" class="text-muted">{{ $user->followers()->count() }} Followers</a> &bull;
                <a href="javascript:void(0)" class="text-muted">{{ $user->following()->count() }} Following</a> &bull;
                {{-- <a href="javascript:void(0)" class="text-muted">{{ $user->total_views + $user->total_views_external }}
                    Views</a>  --}}
                   
                <a href="javascript:void(0)" class="text-muted">{{ $user->total_likes + $user->total_likes_external }}
                    Likes</a> 
                {{-- &bull; <a href="javascript:void(0)" class="text-muted">{{ $user->total_comments }} Comments</a> --}}
            </h2>
            <h2 class="fs-sm fw-medium text-muted mt-2">
                Referral Link: {{ url('/reg?referral_code=' . auth()->user()->referral_code) }}
            </h2>

        </div>

        <div class="space-x-1">
            @if (auth()->user()->id == $user->id)
                <a href="{{ url('settings') }}" class="btn btn-sm btn-alt-secondary space-x-1">
                    <i class="fa fa-pencil-alt opacity-50"></i>
                    <span>Edit Profile</span>
                </a>
            @else
            <a
                wire:click="toggleFollow()"
                href="javascript:void(0)"
                class="btn btn-sm space-x-1
                    {{ $isFollowing ? 'btn-alt-secondary' : 'btn-primary' }}"
            >
                <i class="fa {{ $isFollowing ? 'fa-user-minus' : 'fa-user-plus' }} opacity-50"></i>
                <span>
                    {{ $isFollowing ? 'Following' : 'Follow' }}
                </span>
            </a>

                {{-- <a wire:click="toggleFollow()" href="javascript:void(0)" class="btn btn-sm btn-alt-secondary space-x-1">
                    <i class="fa fa-pencil-alt opacity-50"></i>
                    <span>Follow</span>
                </a> --}}
            @endif
        </div>
    </div>
</div>
<!-- END Hero -->

<div class="row">
    <div class="col-md-8">
        <!-- Post Update -->

        @if (session()->has('success'))
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
        @include('layouts.posts', $timelines)



        <!-- END Update #2 -->

    </div>

    @include('layouts.engagement')


</div>

@if(auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else

        @include('layouts.onboarding')

    @endif
</div>
