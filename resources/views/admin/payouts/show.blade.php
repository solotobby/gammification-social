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
                                <div class="fw-bold">Original Currency</div>

                                {{ $wallet->currency }}

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
                                <div class="fw-bold">Pay Out Status</div>

                                {{ $payout->status }}
                            </div>

                        </li>
                         <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Wallet Balance</div>

                                {{ $wallet->balance }}

                                {{-- <i>Payment will be updated on the

                                    <b> {{ Carbon\Carbon::now()->addMonth()->day(1)->format('F j, Y') }}</b></i> --}}

                                {{-- {{ getCurrencyCode() }}{{ $subscription->pay_out_amount }} --}}
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
                         <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Total PayOut</div>

                             {{ $payout->amount +  $wallet->balance}}
                            </div>

                        </li>

                       

                    </ol>
                   

                </div>

                
            </div>
             
        </div>
        <a href="{{ url('user/info/'.$payout->user->id) }}" class="btn btn-sm btn-primary mb-3"> User Info Page </a>



        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Withdrawal Method</h3>
            </div>
            <div class="block-content">

                <div class="row justify-content-center py-sm-3 py-md-2">
                    @if (@$withdrawals->payment_method == 'usdt')
                        <center class="mb-3"> <strong>Your preferred Payment method: USDT</strong></center>
                        USDT Wallet Address: {{ maskCode(@$withdrawals->usdt_wallet) }}
                    @elseif(@$withdrawals->payment_method == 'paypal')
                        <center class="mb-3"> <strong>Your preferred Payment method : Paypal</strong></center>
                        PayPal Email: {{ maskCode(@$withdrawals->paypal_email) }}
                    @else
                        <center class="mb-3"> <strong>Your account Information</strong></center>
                        Account Name: {{ @$withdrawals->account_name }} <br>
                        Bank Name: {{ @$withdrawals->bank_name }}<br>
                        Account Number: {{ @$withdrawals->account_number }}
                    @endif
                    <br>
                    <hr>
                    {{-- <form method="POST" action="{{ route('fund.transfer') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4">
                                <p class="text-muted">
                                    You can change a user Currency here
                                </p>
                            </div>
                            <div class="col-lg-8 col-xl-5">
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            Validate
                                        </span>
                                        <input type="text" class="form-control" name="validationCode" id="validationCode"
                                            placeholder="Enter validation code" required>
                                    </div>
                                </div>
                                <input type="hidden" value="{{ $payout->user->id }}" name="user_id">


                                <div class="mb-4">
                                    <button type="submit" class="btn btn-sm btn-primary">Process Transfer</button>
                                </div>

                            </div>
                        </div>
                        <!-- END Text -->


                    </form> --}}

                    <form id="fund-transfer-form" method="POST" action="{{ route('fund.transfer') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4">
                                <p class="text-muted">
                                    You can change a user Currency here
                                </p>
                            </div>
                            <div class="col-lg-8 col-xl-5">

                                <div id="transfer-response"></div> <!-- Response will appear here -->

                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            Validate
                                        </span>
                                        <input type="text" class="form-control" name="validationCode" id="validationCode"
                                            placeholder="Enter validation code" required>
                                    </div>
                                </div>
                                <input type="hidden" value="{{ $payout->user->id }}" name="user_id">
                                 <input type="hidden" value="{{ $payout->id }}" name="payout_id">

                                <div class="mb-4">
                                    <button type="submit" class="btn btn-sm btn-primary">Process Transfer</button>
                                </div>

                            </div>
                        </div>
                    </form>








                </div>



            </div>
        </div>




        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(function() {
                $('#fund-transfer-form').on('submit', function(e) {
                    e.preventDefault();

                    let form = $(this);
                    let url = form.attr('action');
                    let data = form.serialize();

                    // Clear previous response
                    $('#transfer-response').html('');

                    $.post(url, data)
                        .done(function(response) {
                            let alertType = response.status === 'success' ? 'alert-success' :
                            'alert-danger';
                            $('#transfer-response').html(
                                `<div class="alert ${alertType}" role="alert">${response.message}</div>`
                                );
                        })
                        .fail(function(xhr) {
                            let message = xhr.responseJSON?.message ?? 'Something went wrong';
                            $('#transfer-response').html(
                                `<div class="alert alert-danger" role="alert">${message}</div>`);
                        });
                });
            });
        </script>




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
