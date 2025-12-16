<div>
    <!-- Simple -->
    <h2 class="content-heading">Post Analytics - <i>{{ auth()->user()->level->name }}</i></h2>


    <blockquote class="blockquote bg-light p-3 rounded border-start border-4 border-primary">
        {{-- <p class="mb-1 fw-semibold">
              💡 Tip: Always verify your access code before proceeding.
          </p> --}}

        <p class="mb-3">
            {{ $post->content }}
        </p>
        <footer class="blockquote-footer">
            {{ $post->created_at }}
        </footer>


    </blockquote>




    <div class="row">
        <div class="col-md-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fa fa-2x fa-eye text-primary"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="fs-3 fw-medium mb-0">
                            {{ $unpaidViews }}
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
                        <i class="far fa-2x fa-eye text-primary"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="fs-3 fw-medium mb-0">
                            {{ $post->views_external }}
                        </p>
                        <p class="text-muted mb-0">
                            Unmonetized Views
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
                    <div class="me-3 text-end">
                        <p class="fs-3 fw-medium mb-0">

                            {{ $unpaidExternalViews }}
                        </p>
                        <p class="text-muted mb-0">
                            Unique External views
                        </p>
                    </div>

                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fa fa-2x fa-dollar text-primary"></i>
                    </div>
                    <div class="me-3 text-end">
                        <p class="fs-3 fw-medium mb-0">

                            {{ getCurrencyCode() }}{{ viewsAmountCalculator($unpaidViews, $unpaidExternalViews) }}
                        </p>
                        <p class="text-muted mb-0">
                            All Views Revenue
                        </p>
                    </div>

                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl-6">
            <a class="block block-rounded block-link-shadow bg-primary" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fa fa-2x fa-thumbs-up text-primary-lighter"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="text-white fs-3 fw-medium mb-0">
                            {{ $unpaidLikes }}
                        </p>
                        <p class="text-white-75 mb-0">
                            Monetized Likes
                        </p>
                    </div>
                </div>
            </a>
        </div>
        {{-- <div class="col-md-6 col-xl-4">
        <a class="block block-rounded block-link-shadow bg-primary" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-thumbs-down text-primary-lighter"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="text-white fs-3 fw-medium mb-0">
                {{ $post->likes_external }}
              </p>
              <p class="text-white-75 mb-0">
                Unmonetized  Likes
              </p>
            </div>
          </div>
        </a>
      </div> --}}
        <div class="col-md-6 col-xl-6">
            <a class="block block-rounded block-link-shadow bg-primary" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fa fa-2x fa-usd text-primary-lighter"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="text-white fs-3 fw-medium mb-0">
                            {{ getCurrencyCode() }}{{ likesAmountCalculator($unpaidLikes) }}
                        </p>
                        <p class="text-white-75 mb-0">
                            Amount
                        </p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl-4">
            <a class="block block-rounded block-link-shadow bg-success" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="far fa-2x fa-comments text-success-light"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="text-white fs-3 fw-medium mb-0">
                            {{ $unpaidComments }}
                        </p>
                        <p class="text-white-75 mb-0">
                            Monetized Comments
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a class="block block-rounded block-link-shadow bg-success" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="far fa-2x fa-comments text-success-light"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="text-white fs-3 fw-medium mb-0">
                            {{ $unpaidExternalComments }}
                        </p>
                        <p class="text-white-75 mb-0">
                            Unmonetized Comment
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a class="block block-rounded block-link-shadow bg-success" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="far fa-2x fa-usd text-success-light"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="text-white fs-3 fw-medium mb-0">
                            {{ getCurrencyCode() }}{{ commentsAmountCalculator($unpaidComments) }}
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
                                    {{ $unpaidViews }}
                                </p>
                                <p class="text-white-75 mb-0">
                                    Total Monetized Views
                                </p>
                            </div>
                        </div>
                        <div class="col-3 border-end border-black-op">
                            <div class="py-3">
                                <div class="item item-circle bg-black-25 mx-auto">
                                    <i class="fa fa-chart-line text-white"></i>
                                </div>
                                <p class="text-white fs-3 fw-medium mt-3 mb-0">
                                    {{ $unpaidLikes }}
                                </p>
                                <p class="text-white-75 mb-0">
                                    Total Monetized Likes
                                </p>
                            </div>
                        </div>
                        <div class="col-3 border-end border-black-op">
                            <div class="py-3">
                                <div class="item item-circle bg-black-25 mx-auto">
                                    <i class="fa fa-users text-white"></i>
                                </div>
                                <p class="text-white fs-3 fw-medium mt-3 mb-0">
                                    {{ $unpaidComments }}
                                </p>
                                <p class="text-white-75 mb-0">
                                    Total Monetized Comments
                                </p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="py-3">
                                <div class="item item-circle bg-black-25 mx-auto">
                                    <i class="fa fa-users text-white"></i>
                                </div>
                                <p class="text-white fs-3 fw-medium mt-3 mb-0">
                                    <?php
                                    $all = commentsAmountCalculator($unpaidComments) + likesAmountCalculator($unpaidLikes) + viewsAmountCalculator($unpaidViews, $unpaidExternalViews);
                                    ?>
                                    {{ getCurrencyCode() }}{{ $all }}
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
