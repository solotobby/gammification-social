<div>
   

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
                {{ $post->sum('views') }}
              </p>
              <p class="text-muted mb-0">
                Views
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
                {{ $post->sum('comments') }}
              </p>
              <p class="text-muted mb-0">
                Comments
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
