<div>
    {{-- The whole world belongs to you. --}}
    <style>
        .mt-3 {
            margin-top: 1rem;
        }

        .hidden {
            display: none;
        }
    </style>

    <style>
        #form-container {
            display: none;
            margin-top: 20px;
        }
    </style>

    <h2 class="content-heading">Wallets - {{ userLevel() }}</h2>
    <div class="row">
        @if (session()->has('status_refresh'))
            <div class="alert alert-success" role="alert">
                {{ session('status_refresh') }}
            </div>
        @endif

        <div class="col-md-6 col-xl-6">

            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fa fa-2x fa-list text-primary"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="fs-3 fw-medium mb-0">
                            {{ getCurrencyCode() }}{{ $wallets->balance }}
                        </p>
                        <p class="text-muted mb-0">
                            Main Balance <span><small>(Earnings from signup bonus and content
                                    monetization)</small></span>
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-6">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fa fa-2x fa-users text-primary"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="fs-3 fw-medium mb-0">
                            {{ getCurrencyCode() }}{{ $wallets->referral_balance }}
                        </p>
                        <p class="text-muted mb-0">
                            Referral Balance <span><small>(Earnings from inviting friends on Payhankey)</small></span>
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-6">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fa fa-2x fa-thumbs-up text-primary"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="fs-3 fw-medium mb-0">
                            {{ getCurrencyCode() }}{{ $wallets->promoter_balance }}
                        </p>
                        <p class="text-muted mb-0">
                            Promotion Balance <span><small>(Earnings from promoting Payhankey)</small></span>
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-6">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fa fa-2x fa-comments text-primary"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="fs-3 fw-medium mb-0">
                            {{ getCurrencyCode() }}{{ $paidWithdrawals }}
                        </p>
                        <p class="text-muted mb-0">
                            Total Withdrawal <span><small>(Earnings withdrawn from your wallet)</small></span>
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- @if (userLevel() == 'Influencer' || userLevel() == 'Creator' || userLevel() == 'Basic')
            <div class="col-12 mb-3">
                <button class="btn btn-sm btn-info" wire:click="refresh">Refresh Wallet</button>
            </div>
        @endif --}}




    </div>

    @if (userLevel() == 'Basic')
        @include('layouts.upgrade')
    @endif

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif

</div>
