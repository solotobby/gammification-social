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

        <h2 class="content-heading">Wallets - {{ $wallets->level }}</h2>
        <div class="row">
          @if(session()->has('status_refresh'))
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
                    ${{ $wallets->balance }}
                  </p>
                  <p class="text-muted mb-0">
                    Main Balance <span><small>(Earnings from signup bonus and content monetization)</small></span>
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
                    ${{ $wallets->referral_balance }}
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
                    ${{ $wallets->promoter_balance }}
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
                   $0
                  </p>
                  <p class="text-muted mb-0">
                    Total Withdrawal <span><small>(Earnings withdrawn from your wallet)</small></span>
                  </p>
                </div>
              </div>
            </a>
          </div>

          <div class="col-12 mb-3">
            <button class="btn btn-sm btn-info" wire:click="refresh">Refresh Wallet</button>
          </div>


        </div>

        <div class="row">

          <div class="col-md-12">
          
              <div class="block block-rounded">
                <div class="block-header block-header-default block-header">
                  <h3 class="block-title">Withdrawal Method</h3>
                  <div class="block-options">
                    <button type="button" class="btn-block-option">
                      <i class="si si-settings"></i>
                    </button>
                  </div>
                </div>

               
                @if(!$withdrawals)
                    <div class="block-content">
                      <div class="row justify-content-center py-sm-3 py-md-5">
                        <div class="col-sm-10 col-md-6">
                          <form action="" method="POST" wire:submit.prevent="createWithdrawalMethod">
                          
                          @if(session()->has('success'))
                            <div class="alert alert-success" role="alert">
                                  {{ session('success') }}
                              </div>
                          @endif
          
                          @if(session()->has('fail'))
                              <div class="alert alert-danger" role="alert">
                                  {{ session('fail') }}
                              </div>
                          @endif


                          <div class="mb-4">
                            <label class="form-label" >Select Country</label>
                            <select class="form-control" id="country" wire:model="country" onchange="handleCountryChange()" required>
                              <option value="">Select Country</option> 
                            
                              @include('layouts.country_list')
                            </select>
                            <div style="color: brown">@error('country') {{ $message }} @enderror</div>
                          </div>


                          <div id="nigeriaOptions" style="display: none;" class="mb-0">
                            <hr>
                            <div class="form-group">
                                <label for="bank">Select Bank </label>
                                <select class="form-control" id="bank" wire:model="bank_name">
                                  <option value="">Choose One...</option>
                                      @foreach (bankList() as $list)
                                        <option value="{{ $list['name'] }}">{{ $list['name'] }}</option>
                                      @endforeach
                                </select>
                                <div style="color: brown">@error('bank_name') {{ $message }} @enderror</div>
                            </div>
                            <div class="form-group">
                                <label for="accountNumber">Account Number</label>
                                <input type="text" class="form-control" id="accountNumber" wire:model="account_number" placeholder="Enter Account Number">
                            </div>
                            <div style="color: brown">@error('account_number') {{ $message }} @enderror</div>
                            
                        </div>


                        <div id="otherOptions" style="display: none;" class="mb-3">
                          <hr>
                          <div class="form-group mb-2">
                              <label for="paymentMethod">Select Payment Method</label>
                              <select class="form-control" id="paymentMethod" name="paymentMethod" wire:model="payment_method">
                                  <option value="">Choose One...</option>  
                                <option value="paypal">PayPal</option>
                                  <option value="usdt">USDT Wallet</option>
                              </select>
                              <div style="color: brown">@error('payment_method') {{ $message }} @enderror</div>
                          </div>
                          <div id="paypalFields" style="display: none;" class="mb-3">
                              <div class="form-group">
                                  <label for="paypalEmail">PayPal Email</label>
                                  <input type="email" class="form-control" id="paypalEmail" name="paypalEmail" wire:model="paypal_email" placeholder="Enter PayPal Email">
                                  <div style="color: brown">@error('paypal_email') {{ $message }} @enderror</div>
                              </div>
                          </div>
                          <div id="usdtFields" style="display: none;" class="mb-3">
                              <div class="form-group">
                                  <label for="usdtWallet">USDT Wallet Address</label>
                                  <input type="text" class="form-control" id="usdtWallet" name="usdtWallet" wire:model="usdt_wallet" placeholder="Enter USDT Wallet Address">
                                  <div style="color: brown">@error('usdt_wallet') {{ $message }} @enderror</div>
                              </div>
                          </div>
                      </div>

                      @if(!$withdrawals)
                      <button type="submit" class="btn btn-sm btn-alt-primary mt-3">
                        <i class="fa fa-check opacity-50 me-1"></i> Submit
                      </button>
                   
                    @endif
                  </form>

                        </div>
                      </div>
                    </div>
            


                @else
                  <div class="block-content">
                    <div class="row justify-content-center py-sm-3 py-md-2">
                      @if($withdrawals->payment_method == 'usdt')
                        <center class="mb-3"> <strong>Your preferred Payment method</strong></center>
                        USDT Wallet Address: {{  maskCode($withdrawals->usdt_wallet) }}
                      @elseif($withdrawals->payment_method == 'paypal')
                      <center class="mb-3"> <strong>Your preferred Payment method</strong></center>
                        PayPal Email: {{  maskCode($withdrawals->paypal_email) }}
                        @else
                        <center class="mb-3"> <strong>Your preferred Payment method</strong></center>
                        Bank Name: {{  $withdrawals->bank_name }}<br>
                        Account Number: {{ $withdrawals->account_number }}

                        @endif
                       
                    </div>
                   
                    <div class="row">
                      <div class="col-md-3"></div>
                        <div class="col-md-6">
                          <hr>
                          @if(session()->has('status'))
                            <div class="alert alert-success" role="alert">
                                  {{ session('status') }}
                              </div>
                          @endif
                          @if(session()->has('status_error'))
                            <div class="alert alert-danger" role="alert">
                                  {{ session('status_error') }}
                              </div>
                          @endif
                          <form wire:submit.prevent="submit">
                            <div class="form-group mb-2">
                            
                              <select class="form-control" name="wallet_type" wire:model="wallet_type">
                                  <option value="">Select Wallet </option>  
                                  <option value="main">Main</option>
                                  <option value="referral">Referral</option>
                                  <option value="promotion">Promotion</option>
                              </select>
                              <div style="color: brown">@error('wallet_type') {{ $message }} @enderror</div>
                            </div>

                            <div class="form-group mb-2">
                              <input type="text" class="form-control" min="10" wire:model="amount" placeholder="Enter Amount" required> 
                            </div>

                            <div style="color: brown">@error('amount') {{ $message }} @enderror</div> 

                            <button type="submit" class="btn btn-sm btn-alt-primary mb-4">
                              <i class="fa fa-check opacity-50 me-1"></i>Place Withdrawals
                            </button>
                            
                          </form>
                        </div>
                      <div class="col-md-3"></div>
                    </div>


                  </div>
                  {{-- <center>
                    <div class="col-md-6" id="form-container">
                      <hr>
                      <form action="" method="Post" wire:submit.prevent="withdrawal">
                        <div class="form-group mb-2">
                         
                          <select class="form-control" name="wallet_type" wire:model="wallet_type">
                              <option value="">Select Wallet </option>  
                              <option value="main">Main</option>
                              <option value="referral">Referral</option>
                              <option value="promotion">Promotion</option>
                          </select>
                          <div style="color: brown">@error('wallet_type') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group mb-2">
                         
                          <input type="text" class="form-control" wire:model="amount" placeholder="Enter Amount">
                        </div>
                        <div style="color: brown">@error('amount') {{ $message }} @enderror</div>

                        <button type="submit" class="btn btn-sm btn-alt-primary mb-4">
                          <i class="fa fa-check opacity-50 me-1"></i>Place Withdrawals
                        </button>
                      </form>
                    </div>
                  </center> --}}
                  




                 
                @endif 

               
              </div>
          
          </div>






            {{-- <div class="col-md-6 col-xl-6">
                
                

                @if(empty($wallets->usdt_wallet_address))
                    <form action="" method="POST" onsubmit="" wire:submit.prevent="updateUSDTWallet">
                        <div class="mb-4">
                        <label class="form-label" for="dm-profile-edit-password">USDT Wallet Address(TRC 20)</label>
                        <input type="text" class="form-control" id="dm-profile-edit-password" wire:model="usdt_wallet_address" placeholder="Enter Withdrawal Wallet Address" value="{{ $wallets->usdt_wallet_address }}" required>
                        </div>
                        
                        <button type="submit" class="btn btn-alt-primary">
                        <i class="fa fa-check-circle opacity-50 me-1"></i> Add Withdrawal Wallet
                        </button>
                    </form>
                @else

                    USDT Wallet Address: {{  maskCode($wallets->usdt_wallet_address) }}

                 

                @endif
                
            </div> --}}
        </div>

        @include('layouts.onboarding')




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
                } else if(country != '') {
                    nigeriaOptions.style.display = "none";
                    otherOptions.style.display = "block";
                }else if(country == ''){
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
