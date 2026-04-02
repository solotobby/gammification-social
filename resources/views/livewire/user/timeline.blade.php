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


            

</div>