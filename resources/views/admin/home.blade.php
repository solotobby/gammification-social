@extends('layouts.admin')

@section('content')
    <!-- Main Container -->
    <!-- Hero -->
    {{-- url('{{asset('src/assets/media/photos/photo10@2x.jpg')}});" --}}
    <div class="bg-image" style="background-image: url('{{ asset('src/assets/media/photos/photo10@2x.jpg') }}');">
        <div class="bg-black-75">
            <div class="content content-full content-top text-center">
                <div class="pt-4 pb-3">
                    <h1 class="fw-light text-white mb-2">{{ auth()->user()->name }}</h1>
                    <h2 class="h4 fw-light text-white-75">Welcome to Payhankey Secret Admin Panel</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="row">
            <!-- Quick Stats -->
            
            <div class="col-6 col-md-3">
                <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 mb-1">{{ $onlineUsers }}</div>
                                <div class="text-muted">Users Online</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 mb-1">{{ fetchActive() }}</div>
                                <div class="text-muted">Active Today</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 mb-1">{{ fetchActive(7) }} </div>
                                <div class="text-muted">Active Last 7 days</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 mb-1">{{ fetchActive(30) }}</div>
                                <div class="text-muted">Active Last 30 days</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>


            <div class="col-6 col-md-3">
                <a class="block block-rounded block-link-pop text-center" href="{{ url('user/list/all') }}">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 mb-1">{{ $userCount }}</div>
                                <div class="text-muted">Users</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @foreach ($levelCounts as $level)
                <div class="col-6 col-md-3">
                    <a class="block block-rounded block-link-pop text-center"
                        href="{{ url('user/list/' . $level->plan_name) }}">
                        <div class="block-content block-content-full ratio ratio-16x9">
                            <div class="d-flex justify-content-center align-items-center">
                                <div>
                                    <div class="fs-2 mb-1">{{ $level->total }}</div>
                                    <div class="text-muted">{{ $level->plan_name }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach

           

            <!-- END Quick Stats -->

            <!-- Charts -->
            <!-- jQuery Sparkline (.js-sparkline class is initialized in Helpers.jqSparkline() -->
            <!-- For more info and examples you can check out http://omnipotent.net/jquery.sparkline/#s-about -->
            <div class="col-md-6">
                <a class="block block-rounded block-link-pop bg-in" href="javascript:void(0)">
                    <div class="block-content block-content-full d-flex justify-content-between">
                        <div class="me-3">
                            <i class="fa fa-2x fa-briefcase text-primary-lighter"></i>
                        </div>
                        <div>
                            <p class="fs-3 fw-light mb-0">
                                {{ number_format($posts->count()) }}
                            </p>
                            <p class="text-muted mb-0">
                                Total Posts
                            </p>
                        </div>
                    </div>
                    <div class="block-content block-content-full overflow-hidden">
                        <!-- Sparkline Container -->
                        <span class="js-sparkline" data-type="line" data-points="[120,140,60,85,160,180,120]"
                            data-width="100%" data-height="140px" data-fill-color="transparent"
                            data-spot-color="transparent" data-min-spot-color="transparent"
                            data-max-spot-color="transparent" data-highlight-spot-color="#0665d0"
                            data-highlight-line-color="#0665d0" data-tooltip-prefix="$"></span>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                    <div class="block-content block-content-full d-flex justify-content-between">
                        <div class="me-3">
                            <p class="fs-3 fw-light mb-0">
                                {{ number_format($posts->sum('views')) }}
                            </p>
                            <p class="text-muted mb-0">
                                Views
                            </p>
                        </div>
                        <div>
                            <i class="fa fa-2x fa-eye text-primary-lighter"></i>
                        </div>
                    </div>
                    <div class="block-content block-content-full overflow-hidden">
                        <!-- Sparkline Container -->
                        <span class="js-sparkline" data-type="line" data-points="[45,28,36,63,70,85,120]" data-width="100%"
                            data-height="140px" data-line-color="#689550" data-fill-color="transparent"
                            data-spot-color="transparent" data-min-spot-color="transparent"
                            data-max-spot-color="transparent" data-highlight-spot-color="#689550"
                            data-highlight-line-color="#689550" data-tooltip-suffix="Sales"></span>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                    <div class="block-content block-content-full d-flex justify-content-between">
                        <div class="me-3">
                            <i class="fa fa-2x fa-thumbs-up text-primary-lighter"></i>
                        </div>
                        <div>
                            <p class="fs-3 fw-light mb-0">
                                {{ number_format($posts->sum('likes')) }}
                            </p>
                            <p class="text-muted mb-0">
                                Likes
                            </p>
                        </div>
                    </div>
                    <div class="block-content block-content-full overflow-hidden">
                        <!-- Sparkline Container -->
                        <span class="js-sparkline" data-type="line" data-points="[320,420,180,98,520,630,250]"
                            data-width="100%" data-height="140px" data-line-color="#333" data-fill-color="transparent"
                            data-spot-color="transparent" data-min-spot-color="transparent"
                            data-max-spot-color="transparent" data-highlight-spot-color="#333"
                            data-highlight-line-color="#333" data-tooltip-suffix="Accounts"></span>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                    <div class="block-content block-content-full d-flex justify-content-between">
                        <div class="me-3">
                            <p class="fs-3 fw-light mb-0">
                                {{ number_format($posts->sum('comments')) }}
                            </p>
                            <p class="text-muted mb-0">
                                Comments
                            </p>
                        </div>
                        <div>
                            <i class="fa fa-2x fa-comments text-primary-lighter"></i>
                        </div>
                    </div>
                    <div class="block-content block-content-full overflow-hidden">
                        <!-- Sparkline Container -->
                        <span class="js-sparkline" data-type="line" data-points="[3,5,8,2,1,6,7]" data-width="100%"
                            data-height="140px" data-line-color="#ffb119" data-fill-color="transparent"
                            data-spot-color="transparent" data-min-spot-color="transparent"
                            data-max-spot-color="transparent" data-highlight-spot-color="#ffb119"
                            data-highlight-line-color="#ffb119" data-tooltip-suffix="Tickets"></span>
                    </div>
                </a>
            </div>
            <!-- END Charts -->

            <!-- More Stats -->
            <div class="col-md-12">
                <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="py-3 border-end">
                                    <div class="item item-circle bg-body-light mx-auto">
                                        <i class="fa fa-thumbs-up text-primary"></i>
                                    </div>
                                    <p class="fs-3 fw-light mt-3 mb-0">
                                        {{ number_format($posts->sum('likes_external')) }}
                                    </p>
                                    <p class="text-muted mb-0">
                                        Ex. Likes
                                    </p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="py-3 border-end">
                                    <div class="item item-circle bg-body-light mx-auto">
                                        <i class="fa fa-eye text-primary"></i>
                                    </div>
                                    <p class="fs-3 fw-light mt-3 mb-0">
                                        {{ number_format($posts->sum('views_external')) }}
                                    </p>
                                    <p class="text-muted mb-0">
                                        Ex. Views
                                    </p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="py-3">
                                    <div class="item item-circle bg-body-light mx-auto">
                                        <i class="fa fa-comments text-primary"></i>
                                    </div>
                                    <p class="fs-3 fw-light mt-3 mb-0">
                                        {{ number_format($posts->sum('comment_external')) }}
                                    </p>
                                    <p class="text-muted mb-0">
                                        Ex. Comments
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

             


            {{-- <div class="col-md-6">
          <a class="block block-rounded block-link-pop" href="javascript:void(0)">
            <div class="block-content block-content-full">
              <div class="row text-center">
                <div class="col-4">
                  <div class="py-3 border-end">
                    <div class="item item-circle bg-body-light mx-auto">
                      <i class="fa fa-briefcase text-primary"></i>
                    </div>
                    <p class="fs-3 fw-light mt-3 mb-0">
                      61
                    </p>
                    <p class="text-muted mb-0">
                      Projects
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="py-3 border-end">
                    <div class="item item-circle bg-body-light mx-auto">
                      <i class="fa fa-paint-brush text-primary"></i>
                    </div>
                    <p class="fs-3 fw-light mt-3 mb-0">
                      6
                    </p>
                    <p class="text-muted mb-0">
                      Themes
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="py-3">
                    <div class="item item-circle bg-body-light mx-auto">
                      <i class="fa fa-wrench text-primary"></i>
                    </div>
                    <p class="fs-3 fw-light mt-3 mb-0">
                      15
                    </p>
                    <p class="text-muted mb-0">
                      Plugins
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div> --}}
            <!-- END More Stats -->
        </div>
    </div>
    <!-- END Page Content -->

    <!-- END Main Container -->
@endsection
