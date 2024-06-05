<div>
    {{-- The whole world belongs to you. --}}


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

            <div class="col-md-6 col-xl-6">
                
                @if(session()->has('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

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
        
</div>
