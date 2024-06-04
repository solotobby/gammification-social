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
                    Main Balance
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
                    Referral Balance
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
                    Promotion Balance
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
                   8942
                  </p>
                  <p class="text-muted mb-0">
                    Total Withdrawal
                  </p>
                </div>
              </div>
            </a>
          </div>
        </div>
    

    
</div>
