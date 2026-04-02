<div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    
    <style>
        .form-control {
            resize: none;

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
    <?php $userLevel = userLevel(); ?>


    <div class="row">
        <div class="col-md-8">
            {{-- @if ($userLevel === 'Basic') --}}
            <div class="alert alert-info">
                <h5 class="fs-5 fw-light mb-2">Let your friends join Payhankey!</h5>
                <p class="text-muteddd">
                    Your friends will vibe easily with your content & help you increase your engagement. <a
                        href="{{ url('referral/list') }}">Learn More</a>
                </p>

                <div class="input-group mb-4">
                    <input type="text" id="referralLink" class="form-control"
                        value="{{ url('/reg?referral_code=' . auth()->user()->referral_code) }}" readonly />
                    <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()"
                        title="Copy to clipboard">
                        <i class="fa fa-copy"></i>
                    </button>
                </div>
                <a href="{{ url('referral/list') }}" class="btn btn-primary mb-2">
                    View Referral List<i class="fa fa-arrow-right ms-1"></i>
                </a>
            </div>
            {{-- @endif --}}
            <div class="block block-bordered block-rounded">
                <div class="block-content block-content-full">
                    <div class="alert alert-info">
                        <b>Post, Grow engagements and Earn from every posts.</b><br>

                        Creator and Influencer accounts can post long text, images and also earn up to
                        {{ getCurrencyCode() }}{{ convertToBaseCurrency(1, auth()->user()->wallet->currency) }} per
                        1,000 engagement on every post. Basic users can earn but cannot withdraw earnings. Upgrade to a
                        Creator or Influencer to earn from your engagements.
                        <br>
                        Minimum payout is
                        {{ getCurrencyCode() }}{{ convertToBaseCurrency(1, auth()->user()->wallet->currency) }} which
                        will be paid on the 2nd of every month.
                        <br>
                        Learn more about <a
                            href="https://payhankey.com/blog/how-payout-for-content-monetization-works-on-payhankey-social-media-69a6ec0f95475"
                            target="_blank"> how Payout works here
                        </a>
                    </div>
                </div>
            </div>


             @foreach (['success' => 'success', 'info' => 'warning', 'error' => 'danger'] as $key => $type)
                @if (session()->has($key))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.opacity
                        class="alert alert-{{ $type }} mb-2" role="alert">
                        {{ session($key) }}
                    </div>
                @endif
            @endforeach

            @error('content')
                <div class="alert alert-danger mb-2" role="alert">{{ $message }}</div>
            @enderror

            @error('images')
                <div class="alert alert-danger mb-2" role="alert">{{ $message }}</div>
            @enderror

            @error('images.*')
                <div class="alert alert-danger mb-2" role="alert">{{ $message }}</div>
            @enderror



            <div class="card mb-4">

                <form wire:submit.prevent="createPost">
                    <div class="card-body ">

                        <div x-data="{ content: @entangle('content') }">
                            <textarea x-model="content" class="form-control" placeholder="Say something amazing" rows="4"
                                @if (!in_array(@$userLevel, ['Creator', 'Influencer'])) maxlength="160" @endif required></textarea>
                            @if (!in_array(@$userLevel, ['Creator', 'Influencer']))
                                <small class="text-muted" x-text="content.length + '/160 characters'"></small>
                            @endif
                        </div>

                        @if (in_array($userLevel, ['Creator', 'Influencer']))
                            <div class="mt-3" x-data="{ images: @entangle('images') }">

                                <label class="btn btn-light">
                                    <i class="fas fa-image"></i>
                                    <input type="file" wire:model="images" multiple accept="image/*" hidden
                                        @if (@$userLevel === 'Creator') x-bind:disabled="images.length >= 1" @endif
                                        @if (@$userLevel === 'Influencer') x-bind:disabled="images.length >= 4" @endif>

                                </label>

                                <small class="text-muted d-block mt-1">
                                    {{ $userLevel === 'Creator' ? 'Max 1 image' : 'Max 4 images' }}
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


</div>