@extends('layouts.admin')



@section('content')
    <div class="content">
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
                                <div class="fw-bold">Full Name</div>
                                {{ $payout->user->name }}
                            </div>
                            {{-- <span class="badge bg-primary rounded-pill">14</span> --}}
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Current Plan</div>
                                {{ $payout->level }}
                            </div>
                            {{-- <span class="badge bg-primary rounded-pill">14</span> --}}
                        </li>
                        {{-- <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Started </div>
                                    {{ Carbon\Carbon::parse($subscription->start_date)->format('F j, Y') == null ? Carbon\Carbon::parse($subscription->created_at)->format('F j, Y') : Carbon\Carbon::parse($subscription->start_date)->format('F j, Y') }}
                                </div>

                            </li> --}}
                        {{-- <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Plan End</div>
                                    {{ $subscription->next_payment_date->format('F j, Y') }}
                                </div>

                            </li> --}}
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Total Engagement </div>

                                {{ $payout->total_engagement }}

                            </div>

                        </li>
                          <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Currency</div>

                                {{ $payout->currency }}

                            </div>

                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Next Payout Date</div>

                                {{ Carbon\Carbon::now()->addMonth()->day(1)->format('F j, Y') }}
                            </div>

                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Pay Out Amount</div>

                                {{ $payout->amount }}

                                {{-- <i>Payment will be updated on the

                                    <b> {{ Carbon\Carbon::now()->addMonth()->day(1)->format('F j, Y') }}</b></i> --}}

                                {{-- {{ getCurrencyCode() }}{{ $subscription->pay_out_amount }} --}}
                            </div>

                        </li>

                    </ol>

                    

                </div>
            </div>
        </div>

        {{-- <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Credit Wallet</h3>
            </div>
            <div class="block-content">
                <form method="POST" action="{{ route('update.current') }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <p class="text-muted">
                                You can change a user Currency here
                            </p>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            {{-- @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif --
                            <div class="mb-4">
                                <select class="form-control mt-2" name="currency" {{-- wire:model.defer="currency" -- required>

                                    <option value="">Select Currency</option>
                                    <option value="USD">USD – US Dollar</option>
                                    <option value="EUR">EUR – Euro</option>
                                    <option value="GBP">GBP – British Pound</option>
                                    <option value="NGN">NGN – Nigerian Naira</option>
                                </select>

                            </div>
                            {{-- <input type="hidden" value="{{ $user->id }}" name="user_id"> --


                            <div class="mb-4">
                                <button type="submit" class="btn btn-sm btn-primary">Change Currency</button>
                            </div>

                        </div>
                    </div>
                    <!-- END Text -->


                </form>
            </div>
        </div> --}}

    </div>
    @endsection
