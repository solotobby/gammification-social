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
    @else
        <div class="row">

            <div class="col-md-12">

                <div class="block block-rounded">
                    <div class="block-header block-header-default block-header">
                        <h3 class="block-title">Subscription and Pay Out Information</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option">
                                <i class="si si-settings"></i>
                            </button>
                        </div>
                    </div>

                    <div class="block-content block-content-full">

                        <ol class="list-group list-group-numbered">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Current Plan</div>
                                    {{ userLevel() }}
                                </div>
                                {{-- <span class="badge bg-primary rounded-pill">14</span> --}}
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Started </div>
                                    {{ Carbon\Carbon::parse($subscription->start_date)->format('F j, Y') == null ? Carbon\Carbon::parse($subscription->created_at)->format('F j, Y') : Carbon\Carbon::parse($subscription->start_date)->format('F j, Y') }}
                                </div>

                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Plan End</div>
                                    {{ $subscription->next_payment_date->format('F j, Y') }}
                                </div>

                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Next Payout Date</div>

                                    {{ Carbon\Carbon::now()->addMonth()->day(2)->format('F j, Y') }}
                                </div>

                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Currency</div>

                                    {{ @$payouts->currency ?? 'NGN' }}
                                </div>

                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Payout on Engagement</div>

                                    {{ number_format(@$payouts->amount ?? 'Pending') }}

                                    {{-- <i>Payment will be updated on the
                                       <b> {{ Carbon\Carbon::now()->addMonth()->day(1)->format('F j, Y') }}</b></i> --}}

                                    {{-- {{ getCurrencyCode() }}{{ $subscription->pay_out_amount }} --}}
                                </div>

                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Bonus</div>



                                    {{ number_format($wallets->balance, 2) }}

                                    {{-- <i>Payment will be updated on the
                                       <b> {{ Carbon\Carbon::now()->addMonth()->day(1)->format('F j, Y') }}</b></i> --}}

                                </div>

                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Total Pay</div>

                                    {{-- {{ number_format($payouts->amount ?? 'Pending') }} --}}

                                    {{ number_format($wallets->balance + $payouts->amount ?? 0, 2) }}

                                    {{-- <i>Payment will be updated on the
                                       <b> {{ Carbon\Carbon::now()->addMonth()->day(1)->format('F j, Y') }}</b></i> --}}

                                    {{-- {{ getCurrencyCode() }}{{ $subscription->pay_out_amount }} --}}
                                </div>

                            </li>

                        </ol>

                        <div class="alert alert-info mt-3" role="alert">
                            <strong>Note:</strong> Payouts are set on the 1st of every month and processed on the 2nd of
                            the same month. Ensure your withdrawal
                            method is set up correctly to avoid delays.
                        </div>

                    </div>
                </div>
            </div>
    @endif

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif

</div>
