<div>
    <style>
        /* Ensure wrapper is relative */
        .d-block.position-relative {
            position: relative;
        }

        /* Overlay positioning */
        .camera-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            transform: translate(25%, 25%);
            width: 40px;
            /* size of the circle */
            height: 40px;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 10;
            background-color: rgba(0, 0, 0, 0.6);
            /* semi-transparent dark */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Show overlay on hover */
        .d-block.position-relative:hover .camera-overlay {
            opacity: 1;
        }

        /* Optional icon size */
        .camera-overlay i.fa-camera {
            font-size: 16px;
        }
        .profile-meta span {
    white-space: nowrap;
}
    </style>


    <div class="rounded border overflow-hidden push">
        <div class="bg-image pt-9"
            style="background-image: url('{{ asset('src/assets/media/photos/photo19@2x.jpg') }}');"></div>

        <div class="px-4 py-3 bg-body-extra-light d-flex flex-column flex-md-row align-items-center">

            {{-- Avatar + Camera Overlay --}}
            <div class="d-block position-relative mt-n5">
                <a href="javascript:void(0)">
                    <img src="{{ $user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="User Avatar"
                        class="img-avatar img-avatar128 img-avatar-thumb rounded-circle border shadow-sm

                    @if (in_array(userLevel($user->id), ['Influencer'])) border-primary border-3 @endif">
                </a>

                @if (auth()->user()->id == $user->id)
                    <label for="avatarInput"
                        class="camera-overlay d-flex align-items-center justify-content-center rounded-circle bg-dark text-white"
                        style="cursor: pointer;">
                        <i class="fa fa-camera"></i>
                    </label>

                    <input type="file" id="avatarInput" wire:model="avatar" class="d-none" accept="image/*">
                @endif
            </div>

            {{-- User Info --}}
            <div class="ms-3 flex-grow-1 text-center text-md-start my-3 my-md-0">
                <h1 class="fs-4 fw-bold mb-1">
                    {{ $user->name }}
                    @if (in_array(userLevel($user->id), ['Creator', 'Influencer']))
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="#1DA1F2" class="ms-1">
                            <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z" />
                        </svg>
                    @endif
                </h1>

                <h2 class="fs-sm fw-medium text-muted mb-0">
                    <a href="{{ url('profile/' . $user->username . '/connection') }}"
                        class="text-muted">{{ $user->followers }} Followers</a> &bull;
                    <a href="{{ url('profile/' . $user->username . '/connection') }}"
                        class="text-muted">{{ $user->following }} Following</a> &bull;
                    <a href="javascript:void(0)"
                        class="text-muted">{{ sumCounter($user->total_likes, $user->total_likes_external) }} Likes</a>
                </h2>
                
                {{-- Bio / Meta Info --}}
                <div class="mt-2 text-muted fs-sm d-flex flex-wrap gap-3 justify-content-center justify-content-md-start">

                    {{-- About --}}
                    @if ($user->profile->about)
                        <span class="d-flex align-items-center">
                            <i class="fa fa-user me-1 opacity-50"></i>
                            {{ $user->profile->about ?? 'Not set'}}
                        </span>
                    @endif

                    {{-- Date of Birth (Month + Year only) --}}
                    @if ($user->profile->date_of_birth)
                        <span class="d-flex align-items-center">
                            <i class="fa fa-calendar-alt me-1 opacity-50"></i>
                            {{ \Carbon\Carbon::parse($user->profile->date_of_birth)->format('F Y') ?? 'Not set'}}
                        </span>
                    @endif

                    {{-- Location --}}
                    @if ($user->profile->location)
                        <span class="d-flex align-items-center">
                            <i class="fa fa-map-marker-alt me-1 opacity-50"></i>
                            {{ $user->profile->location ?? ' Not Set ' }}
                        </span>
                    @endif

                </div>
                
                @if (auth()->user()->id == $user->id)
                    <h2 class="fs-sm fw-medium text-muted mt-2">
                        <i class="fa fa-share me-1 opacity-50"></i> {{ url('/reg?referral_code=' . auth()->user()->referral_code) }}
                    </h2>
                @endif

            </div>

            {{-- Action Buttons --}}
            <div class="space-x-1">
                @if (auth()->user()->id == $user->id)
                    <a href="{{ url('settings') }}" class="btn btn-sm btn-alt-secondary space-x-1">
                        <i class="fa fa-pencil-alt opacity-50"></i>
                        <span>Edit Profile</span>
                    </a>
                @else
                    <a wire:click="toggleFollow()" href="javascript:void(0)"
                        class="btn btn-sm space-x-1 {{ $isFollowing ? 'btn-alt-secondary' : 'btn-primary' }}">
                        <i class="fa {{ $isFollowing ? 'fa-user-minus' : 'fa-user-plus' }} opacity-50"></i>
                        <span>{{ $isFollowing ? 'Following' : 'Follow' }}</span>
                    </a>
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
            @include('layouts.feeds', $timelines)



            <!-- END Update #2 -->

        </div>

        @include('layouts.engagement')


    </div>

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
