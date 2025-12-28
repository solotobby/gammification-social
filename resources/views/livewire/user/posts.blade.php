<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}

    <style>
        .form-control {
            resize: none;
            height: 100px;
            /* Adjust the height as needed */
        }

        .stylish-button {
            display: block;
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            /* Space between textarea and button */
            text-align: center;
        }

        .stylish-button:hover {
            background-color: #0056b3;
        }

        .stylish-button i {
            margin-right: 5px;
        }

        .btn-block {
            display: block;
            width: 100%;
        }
    </style>

    <div class="row">
        <div class="col-md-8">

            <div class="block block-bordered block-rounded">
                <div class="block-content block-content-full">
                    <div class="alert alert-info">

                        <b>You're missing something out</b><br>

                        Sharing your post gives you a wider reach and makes you earn from your existing traffic on other
                        social media. You can earn up to $100 on each post daily when you share to other social media.
                        You can also earn $20 on each review video you make about Payhankey.
                        <br>Simply make a 2-5mins video daily and tag us @payhankeyofficial on
                        Instagram and TikTok.
                    </div>

                    {{-- <form wire:submit.prevent="post">
                        <div class="input-group mb-3">
                            <textarea wire:model="content" name="content" class="form-control form-control-alt @error('content') is-invalid @enderror" placeholder="Say something amazing" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-pencil-alt opacity-50 me-1"></i> Post
                        </button>
                    </form> --}}
                </div>
            </div>
            @if (session()->has('info'))
                <div class="alert alert-warning mb-2" role="alert">
                    {{ session('info') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger mb-2" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @error('content')
                <div class="alert alert-danger mb-2" role="alert">{{ $message }}</div>
            @enderror


            <div class="card mb-4">

                <form wire:submit.prevent="post">
                    <div class="card-body">
                        <div x-data="{ content: @entangle('content') }">
                            <textarea x-model="content" class="form-control" placeholder="Say something amazing"
                                @if (!in_array(userLevel(), ['Creator', 'Influencer'])) maxlength="160" @endif ></textarea>
                            @if (!in_array(userLevel(), ['Creator', 'Influencer']))
                                <small class="text-muted" x-text="content.length + '/160 characters'"></small>
                            @endif
                        </div>

                        @if (in_array(UserLevel(), ['Creator', 'Influencer']))
                            <div class="mt-3" x-data="{ images: @entangle('images') }">

                                <label class="btn btn-light">
                                    <i class="fas fa-image"></i>
                                    <input type="file" wire:model="images" multiple accept="image/*" hidden
                                        @if (UserLevel() === 'Creator') x-bind:disabled="images.length >= 1" @endif
                                        @if (UserLevel() === 'Influencer') x-bind:disabled="images.length >= 4" @endif>
                                        
                                </label>

                                <small class="text-muted d-block mt-1">
                                    {{ UserLevel() === 'Creator' ? 'Max 1 image' : 'Max 4 images' }}
                                </small>

                                {{-- Preview + Remove --}}
                                <div class="row mt-3">
                                    @foreach ($images as $index => $image)
                                        <div class="col-3 position-relative mb-2">

                                            <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded">

                                            <button type="button" wire:click="removeImage({{ $index }})"
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1">
                                                &times;
                                            </button>

                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-block"> <i class="fa fa-pencil-alt opacity-50 me-1"></i>
                            Post</button>
                    </div>
                </form>
            </div>


            @include('layouts.posts', $posts)


        </div>
        @include('layouts.engagement')
    </div>



    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif

</div>
