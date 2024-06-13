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

        <h2 class="content-heading">Wallets - {{ $wallets->level }}</h2>
        <div class="row">
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
        </div>

        <div class="row">
{{-- 
          <div class="col-md-12">
            <form action="" method="POST">
              <div class="block block-rounded">
                <div class="block-header block-header-default block-header">
                  <h3 class="block-title">Withdrawal Method</h3>
                  <div class="block-options">
                    <button type="button" class="btn-block-option">
                      <i class="si si-settings"></i>
                    </button>
                  </div>
                </div>

                @if(session()->has('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(empty($wallets->usdt_wallet_address))
                    <div class="block-content">
                      <div class="row justify-content-center py-sm-3 py-md-5">
                        <div class="col-sm-10 col-md-6">
                          <div class="mb-4">
                            <label class="form-label" >Select Country</label>
                            <select class="form-control" id="country" name="country" onchange="handleCountryChange()">
                              <option value="">Select Country</option> 
                              @include('layouts.country_list')
                            </select>
                          </div>


                          <div id="nigeriaOptions" style="display: none;" class="mb-0">
                            <hr>
                            <div class="form-group">
                                <label for="bank">Select Bank</label>
                                <select class="form-control" id="bank" name="bank">
                                    <option value="naiara">Naiara</option>
                                    <option value="kobo">Kobo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="accountNumber">Account Number</label>
                                <input type="text" class="form-control" id="accountNumber" name="accountNumber" placeholder="Enter Account Number">
                            </div>
                            
                        </div>


                        <div id="otherOptions" style="display: none;" class="mb-3">
                          <hr>
                          <div class="form-group mb-2">
                              <label for="paymentMethod">Select Payment Method</label>
                              <select class="form-control" id="paymentMethod" name="paymentMethod">
                                  <option value="paypal">PayPal</option>
                                  <option value="usdt">USDT Wallet</option>
                              </select>
                          </div>
                          <div id="paypalFields" style="display: none;" class="mb-3">
                              <div class="form-group">
                                  <label for="paypalEmail">PayPal Email</label>
                                  <input type="email" class="form-control" id="paypalEmail" name="paypalEmail" placeholder="Enter PayPal Email">
                              </div>
                          </div>
                          <div id="usdtFields" style="display: none;" class="mb-3">
                              <div class="form-group">
                                  <label for="usdtWallet">USDT Wallet Address</label>
                                  <input type="text" class="form-control" id="usdtWallet" name="usdtWallet" placeholder="Enter USDT Wallet Address">
                              </div>
                          </div>
                      </div>


                        </div>
                      </div>
                    </div>
                @else
                  <div class="block-content">
                    <div class="row justify-content-center py-sm-3 py-md-5">
                    USDT Wallet Address: {{  maskCode($wallets->usdt_wallet_address) }}
                    </div>
                  </div>
                @endif 

                <div class="block-content block-content-full block-content-sm bg-body-light text-end">
               
                  @if(empty($wallets->usdt_wallet_address))
                  <button type="submit" class="btn btn-sm btn-alt-primary">
                    <i class="fa fa-check opacity-50 me-1"></i> Submit
                  </button>
                  @else
                  <button type="button" class="btn btn-sm btn-alt-primary">
                    <i class="fa fa-check opacity-50 me-1" @disabled(true)></i> Submitted
                  </button>
                  @endif
                </div>
              </div>
            </form>
          </div>
--}}





            <div class="col-md-6 col-xl-6">
                
                

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
                
            </div>
        </div>




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

         
      </script>



</div>
