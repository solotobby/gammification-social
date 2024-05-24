<div>
    <!-- Simple -->
    <h2 class="content-heading">Post Analytics</h2>
    <div class="row">
      <div class="col-md-6 col-xl-4">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-arrow-up text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ $post->views }}
              </p>
              <p class="text-muted mb-0">
                Views
              </p>
            </div>
          </div>
        </a>
      </div>
       <div class="col-md-6 col-xl-4">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="far fa-2x fa-user-circle text-success"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                {{ $post->views_external }}
              </p>
              <p class="text-muted mb-0">
                Ext. Views
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-4">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div class="me-3">
              <p class="fs-3 fw-medium mb-0">
                ${{ floor($post->views/1000) * 0.9 }}
              </p>
              <p class="text-muted mb-0">
                Total Rev
              </p>
            </div>
            <div>
              <i class="fa fa-2x fa-dollar text-danger"></i>
            </div>
          </div>
        </a>
      </div>
      
      <div class="col-md-6 col-xl-4">
        <a class="block block-rounded block-link-shadow bg-primary" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-arrow-alt-circle-up text-primary-lighter"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="text-white fs-3 fw-medium mb-0">
                {{ $post->likes }}
              </p>
              <p class="text-white-75 mb-0">
                Likes
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-4">
        <a class="block block-rounded block-link-shadow bg-primary" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-arrow-alt-circle-up text-primary-lighter"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="text-white fs-3 fw-medium mb-0">
                {{ $post->likes_external }}
              </p>
              <p class="text-white-75 mb-0">
                Ext Likes
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-4">
        <a class="block block-rounded block-link-shadow bg-primary" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-arrow-alt-circle-up text-primary-lighter"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="text-white fs-3 fw-medium mb-0">
                $900
              </p>
              <p class="text-white-75 mb-0">
                Amount
              </p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-xl-6">
        <a class="block block-rounded block-link-shadow bg-success" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="far fa-2x fa-user text-success-light"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="text-white fs-3 fw-medium mb-0">
                {{ $post->comments }}
              </p>
              <p class="text-white-75 mb-0">
                Comments
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-6">
        <a class="block block-rounded block-link-shadow bg-success" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="far fa-2x fa-user text-success-light"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="text-white fs-3 fw-medium mb-0">
                $1299
              </p>
              <p class="text-white-75 mb-0">
                Amount
              </p>
            </div>
          </div>
        </a>
      </div>
     
     
      <div class="col-md-12">
        <a class="block block-rounded bg-gd-sublime" href="javascript:void(0)">
          <div class="block-content block-content-full">
            <div class="row text-center">
              <div class="col-3 border-end border-black-op">
                <div class="py-3">
                  <div class="item item-circle bg-black-25 mx-auto">
                    <i class="fa fa-briefcase text-white"></i>
                  </div>
                  <p class="text-white fs-3 fw-medium mt-3 mb-0">
                    {{ $post->views+ $post->views_external }}
                  </p>
                  <p class="text-white-75 mb-0">
                    Total Views
                  </p>
                </div>
              </div>
              <div class="col-3 border-end border-black-op">
                <div class="py-3">
                  <div class="item item-circle bg-black-25 mx-auto">
                    <i class="fa fa-chart-line text-white"></i>
                  </div>
                  <p class="text-white fs-3 fw-medium mt-3 mb-0">
                    {{ $post->likes+$post->likes_external }}
                  </p>
                  <p class="text-white-75 mb-0">
                    Likes
                  </p>
                </div>
              </div>
              <div class="col-3 border-end border-black-op">
                <div class="py-3">
                  <div class="item item-circle bg-black-25 mx-auto">
                    <i class="fa fa-users text-white"></i>
                  </div>
                  <p class="text-white fs-3 fw-medium mt-3 mb-0">
                    {{ $post->comments }}
                  </p>
                  <p class="text-white-75 mb-0">
                    Comments
                  </p>
                </div>
              </div>
              <div class="col-3">
                <div class="py-3">
                  <div class="item item-circle bg-black-25 mx-auto">
                    <i class="fa fa-users text-white"></i>
                  </div>
                  <p class="text-white fs-3 fw-medium mt-3 mb-0">
                    $1234
                  </p>
                  <p class="text-white-75 mb-0">
                    Amount
                  </p>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>
    <!-- END Simple -->


   
</div>
