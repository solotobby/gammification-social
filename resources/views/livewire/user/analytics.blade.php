<div>
   
   

    <div class="alert alert-info">
    🚀 Every <strong>1,000 engagements</strong> puts 
    <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency(1, auth()->user()->wallet->currency) }}</strong> 
    straight into your wallet. Keep engaging!
</div>

    <h2 class="content-heading">Analytics</h2>
    <div class="row">
      <div class="col-md-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-list text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ $post->count() }}
              </p>
              <p class="text-muted mb-0">
                Posts
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-eye text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ sumCounter($post->sum('views'), $post->sum('views_external'))  }}
              </p>
              <p class="text-muted mb-0">
                Total Views
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-thumbs-up text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ $post->sum('likes') }}
              </p>
              <p class="text-muted mb-0">
                Likes
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-comments text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ sumCounter($post->sum('comments'), $post->sum('comment_external')  ) }}
              </p>
              <p class="text-muted mb-0">
                Total Comments
              </p>
            </div>
          </div>
        </a>
      </div>
    </div>
    <h2 class="content-heading">Engagement</h2>
    <div class="row">
      <div class="col-md-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-eye text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ $post->sum('views') }}
              </p>
              <p class="text-muted mb-0">
                Monetized Views
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-thumbs-up text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ $post->sum('likes') }}
              </p>
              <p class="text-muted mb-0">
                Monetized Likes
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-comments text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ $post->sum('comments') }}
              </p>
              <p class="text-muted mb-0">
                Monetized Comments
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-list text-primary"></i>
            </div>
            <?php 
            $total = $post->sum('likes') + $post->sum('comments') + $post->sum('views');
            ?>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ $total }}
              </p>
              <p class="text-muted mb-0">
                Total Engagement
              </p>
            </div>
          </div>
        </a>
      </div>

    </div>

  <h2 class="content-heading">Potential Earnings</h2>
    <div class="row">
      <div class="col-md-12 col-xl-12">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-usd text-primary"></i>
            </div>
            <?php $amountUSD = engagementEarnings($total); ?>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ getCurrencyCode() }}{{  convertToBaseCurrency($amountUSD, auth()->user()->wallet->currency) }}
              </p>
              <p class="text-muted mb-0">
                Estimated. Earning
              </p>
            </div>
          </div>
        </a>
      </div>
    </div>

    
    
    @if(auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else

        @include('layouts.onboarding')

    @endif
</div>
