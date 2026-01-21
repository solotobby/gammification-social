<div>
   
   

    <div class="alert alert-info">
    🚀 Every <strong>1,000 engagements</strong> puts 
    <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency(1, auth()->user()->wallet->currency) }}</strong> 
    straight into your wallet. Keep engaging!
</div>

    <h2 class="content-heading">Analytics for {{ $month }}</h2>
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
    <h2 class="content-heading">Engagement from your Posts</h2>
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

    {{-- <h2 class="content-heading">Your Engagement (Total engagement you give)</h2>
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

    </div> --}}

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

    {{-- <h2 class="content-heading">Engagement Breakdown</h2> --}}

     {{-- <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Daily Breakdown of your engaments for {{ $this->month }}
                </h3>
            </div>
            <div class="block-content block-content-full">

              <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">
            <thead>
              <tr>
                <th>Date</th>
                <th>Views</th>
                <th>Likes</th>
                <th>Comments</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
                <?php
                    $totalViews = 0;
                    $totalLikes = 0;
                    $totalComments = 0;
                    $totalPoints = 0;
                ?>
                @foreach ($dailyEngagement as $engagement)
                <tr>
                    <td>
                     {{ \Carbon\Carbon::parse($engagement->date)->translatedFormat('jS F, Y') }}
                    </td>
                    <td>
                        {{ $engagement->views }}
                    </td>
                    <td>
                        {{ $engagement->likes }}
                    </td>
                    <td >
                      {{ $engagement->comments }}
                    </td>
                    <td>
                        {{ $engagement->points }}
                      </td>
                  </tr>
                  <?php
                      $totalViews += $engagement->views;
                      $totalLikes += $engagement->likes;
                      $totalComments += $engagement->comments;
                      $totalPoints += $engagement->points;
                  ?>
                @endforeach
            </tbody>
          </table>



            </div>
     </div> --}}


    
    
    @if(auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else

        @include('layouts.onboarding')

    @endif
</div>
