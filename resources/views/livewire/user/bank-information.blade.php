<div>
    {{-- Success is as dangerous as failure. --}}

    {{-- <div class="row">

        <div class="col-md-12">

            <div class="block block-rounded">
                <div class="block-header block-header-default block-header">
                    <h3 class="block-title">Bank Information</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option">
                            <i class="si si-settings"></i>
                        </button>
                    </div>
                </div>

                   <div class="block-content">
                        <div class="row justify-content-center py-sm-3 py-md-5">
                            <div class="col-sm-10 col-md-6">
                                <h4> Setup Bank Information For payout </h4>
                                @if ($baseCurrency == 'NGN')
                                    <form action="" method="POST" wire:submit.prevent="UpdateBankInformation">

                                    @if (session()->has('success'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    @if (session()->has('fail'))
                                        <div class="alert alert-danger" role="alert">
                                            {{ session('fail') }}
                                        </div>
                                    @endif


                                  
                                    <div id="nigeriaOptions" class="mb-0">
                                        <hr>
                                        <div class="form-group">
                                            <label for="bank">Select Bank </label>
                                            <select class="form-control" id="bank" wire:model="bank_name">
                                                <option value="">Choose One...</option>
                                                @foreach (bankList() as $list)
                                                    <option value="{{ $list['name'] }}">{{ $list['name'] }}</option>
                                                @endforeach
                                            </select>
                                            <div style="color: brown">
                                                @error('bank_name')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="accountNumber">Account Number</label>
                                            <input type="text" class="form-control" id="accountNumber"
                                                wire:model="account_number" placeholder="Enter Account Number">
                                        </div>
                                        <div style="color: brown">
                                            @error('account_number')
                                                {{ $message }}
                                            @enderror
                                        </div>

                                    </div>



                                </form>
                                @endif
                                
                            </div>
                        </div>
                   </div>

            </div>
        </div>
    </div> --}}



    <div class="row">

        <div class="col-md-12">

            <div class="block block-rounded">
                <div class="block-header block-header-default block-header">
                    <h3 class="block-title">Pay Out Information - {{ $baseCurrency }}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option">
                            <i class="si si-settings"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content">
                    <div class="row justify-content-center py-sm-3 py-md-5">
                        <div class="col-sm-10 col-md-6">
                            @if ($withdrawals)

                                @if ($baseCurrency == 'NGN')

                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fa fa-bank me-2"></i> Bank Payout Details
                                        </h5>
                                    </div>

                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Bank Name</div>
                                            <div class="col-7 fw-semibold">
                                                {{ $withdrawals->bank_name }}
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Account Number</div>
                                            <div class="col-7 fw-semibold">
                                                {{-- {{ $withdrawals->account_number }} --}}
                                                {{ Str::mask($withdrawals->account_number, '*', 0, 6) }}
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-5 text-muted">Account Name</div>
                                            <div class="col-7 fw-semibold">
                                                {{ $withdrawals->account_name ?? '—' }}
                                            </div>
                                        </div>

                                        {{-- <button wire:click="editWithdrawal" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-edit me-1"></i> Update Payout Details
                                        </button> --}}

                                        <button wire:click="openEditModal" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-edit"></i> Update Payout Details
                                            </button>
                                    </div>
                                @else
                                    <p><strong>Payment Method:</strong> {{ ucfirst($withdrawals->payment_method) }}
                                    </p>

                                    @if ($withdrawals->payment_method === 'paypal')
                                        <p><strong>PayPal Email:</strong> {{ $withdrawals->paypal_email }}</p>
                                    @endif

                                    @if ($withdrawals->payment_method === 'usdt')
                                        <p><strong>USDT Wallet:</strong> {{ $withdrawals->usdt_wallet }}</p>
                                    @endif

                                     <button wire:click="openEditModal" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-edit"></i> Update Payout Details
                                            </button>
                                @endif
                            @else
                                <form action="" method="POST" wire:submit.prevent="createWithdrawalMethod">

                                    @if (session()->has('error'))
                                        <div class="alert alert-danger" role="alert">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    @if ($baseCurrency == 'NGN')

                                        <h5>Add Bank Information</h5>

                                        <div class="form-group">
                                            <label for="bank">Select Bank </label>
                                            <select class="form-control" id="bank" wire:model="bank_code">
                                                <option value="">Choose One...</option>
                                                @foreach (bankList() as $list)
                                                    <option value="{{ $list['code'] }}, {{ $list['name'] }}">
                                                        {{ $list['name'] }}</option>
                                                @endforeach
                                            </select>
                                            <div style="color: brown">
                                                @error('bank_name')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="accountNumber">Account Number</label>
                                            <input type="text" class="form-control" id="accountNumber"
                                                wire:model="account_number" placeholder="Enter Account Number">
                                        </div>
                                        
                                        <button class="btn btn-primary mt-2" type="submit"> Enter Pay Out Information
                                        </button>
                                        <div style="color: brown" class="mt-2">
                                            @error('account_number')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    @else
                                        <h5> Enter your Payout Information</h5>


                                        <div class="form-group mb-2">
                                            <label for="paymentMethod">Select Payment Method</label>
                                            <select class="form-control" id="paymentMethod" name="paymentMethod"
                                                wire:model="payment_method">
                                                <option value="">Choose One...</option>
                                                <option value="paypal">PayPal</option>
                                                <option value="usdt">USDT Wallet</option>
                                            </select>
                                            <div style="color: brown">
                                                @error('payment_method')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="paypalFields" style="display: none;" class="mb-3">
                                            <div class="form-group">
                                                <label for="paypalEmail">PayPal Email</label>
                                                <input type="email" class="form-control" id="paypalEmail"
                                                    name="paypalEmail" wire:model="paypal_email"
                                                    placeholder="Enter PayPal Email">
                                                <div style="color: brown">
                                                    @error('paypal_email')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div id="usdtFields" style="display: none;" class="mb-3">
                                            <div class="form-group">
                                                <label for="usdtWallet">USDT Wallet Address</label>
                                                <input type="text" class="form-control" id="usdtWallet"
                                                    name="usdtWallet" wire:model="usdt_wallet"
                                                    placeholder="Enter USDT Wallet Address">
                                                <div style="color: brown">
                                                    @error('usdt_wallet')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary mt-2" type="submit"> Enter Pay Out Information
                                        </button>
                                        <div style="color: brown" class="mt-2">
                                            @error('account_number')
                                                {{ $message }}
                                            @enderror
                                        </div>
                        </div>


                        @endif

                        </form>

                        @endif

                    </div>
                </div>
            </div>

        </div>

    </div>


    @if ($showEditModal)
<div class="modal fade show d-block" style="background: rgba(0,0,0,.6)">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Update Payout Information</h5>
                <button class="btn-close" wire:click="$set('showEditModal', false)"></button>
            </div>



            <form wire:submit.prevent="updateWithdrawalMethod">
                <div class="modal-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>There were some errors:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- NGN --}}
                    @if ($baseCurrency === 'NGN')
                        <div class="form-group">
                            <label>Bank</label>
                            <select class="form-control" wire:model="bank_code">
                                <option value="">Select Bank</option>
                                @foreach (bankList() as $list)
                                    <option value="{{ $list['code'] }}, {{ $list['name'] }}">
                                        {{ $list['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mt-2">
                            <label>Account Number</label>
                            <input class="form-control" wire:model="account_number">
                        </div>
                    @else
                        {{-- Non-NGN --}}
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select class="form-control" wire:model="payment_method">
                                <option value="paypal">PayPal</option>
                                <option value="usdt">USDT</option>
                            </select>
                        </div>

                        @if ($payment_method === 'paypal')
                            <div class="form-group mt-2">
                                <label>PayPal Email</label>
                                <input type="email" class="form-control" wire:model="paypal_email">
                            </div>
                        @endif

                        @if ($payment_method === 'usdt')
                            <div class="form-group mt-2">
                                <label>USDT Wallet</label>
                                <input class="form-control" wire:model="usdt_wallet">
                            </div>
                        @endif
                    @endif

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        wire:click="$set('showEditModal', false)">
                        Cancel
                    </button>
                    <button class="btn btn-primary">
                        Update
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endif


</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethod = document.getElementById('paymentMethod');
        const paypalFields = document.getElementById('paypalFields');
        const usdtFields = document.getElementById('usdtFields');

        function toggleFields() {
            const value = paymentMethod.value;

            paypalFields.style.display = value === 'paypal' ? 'block' : 'none';
            usdtFields.style.display = value === 'usdt' ? 'block' : 'none';
        }

        paymentMethod.addEventListener('change', toggleFields);

        // Run on page load (important for Livewire re-renders)
        toggleFields();
    });
</script>




<script>
    function handleCountryChange() {
        var country = document.getElementById("country").value;
        var nigeriaOptions = document.getElementById("nigeriaOptions");
        var otherOptions = document.getElementById("otherOptions");
        var paypalFields = document.getElementById("paypalFields");
        var usdtFields = document.getElementById("usdtFields");

        if (country === "Nigeria") {
            nigeriaOptions.style.display = "block";
            otherOptions.style.display = "none";
        } else if (country != '') {
            nigeriaOptions.style.display = "none";
            otherOptions.style.display = "block";
        } else if (country == '') {
            nigeriaOptions.style.display = "none";
            otherOptions.style.display = "none";
        }

        var paymentMethod = document.getElementById("paymentMethod").value;

        if (paymentMethod === "paypal") {
            paypalFields.style.display = "block";
            usdtFields.style.display = "none";
        } else if (paymentMethod === "usdt") {
            paypalFields.style.display = "none";
            usdtFields.style.display = "block";
        }
    }

    function handlePaymentMethodChange() {
        var paymentMethod = document.getElementById("paymentMethod").value;
        var paypalFields = document.getElementById("paypalFields");
        var usdtFields = document.getElementById("usdtFields");

        if (paymentMethod === "paypal") {
            paypalFields.style.display = "block";
            usdtFields.style.display = "none";
        } else if (paymentMethod === "usdt") {
            paypalFields.style.display = "none";
            usdtFields.style.display = "block";
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        handleCountryChange(); // Call initially to set up the form correctly

        // Attach onchange event listener to paymentMethod select
        document.getElementById("paymentMethod").addEventListener("change", handlePaymentMethodChange);
    });


    document.getElementById('show-form-button').addEventListener('click', function() {
        document.getElementById('form-container').style.display = 'block';
    });
</script>




</div>
