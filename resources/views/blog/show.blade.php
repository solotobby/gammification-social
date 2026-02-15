<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Payhankey | Monetize your posts, comments and views to earn daily</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Payhankey | Monetize your posts, comments and views to earn daily" />
    <meta name="keywords" content="money, facebook. twitter, instagram premium, marketing, multipurpose" />
    <meta content="Themesbrand" name="author" />
    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <!-- css -->

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7484162262282358"
        crossorigin="anonymous"></script>

    <link href="{{ asset('asset/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('asset/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('asset/css/style.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/brands.min.css"
        integrity="sha512-DJLNx+VLY4aEiEQFjiawXaiceujj5GA7lIY8CHCIGQCBPfsEG0nGz1edb4Jvw1LR7q031zS5PpPqFuPA8ihlRA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10842521152"></script>

    <link rel="stylesheet" id="css-main" href="{{ asset('src/assets/css/dashmix.css') }}">

    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'AW-10842521152');
    </script>

    <style>
        body {
            background-color: #E0E0E0;
            /* light milk color */
        }

        .logos img {
            max-width: 100%;
            height: 100%;
            display: block;
            margin: 0 auto;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo-container .logos {
            flex: 1 1 10%;
            margin: 10px;
        }
    </style>


</head>

<body>

    <!-- light-dark mode -->

    {{-- <a href="javascript: void(0);" id="light-dark-mode" class="mode-btn text-white rounded-end">
        <i class="mdi mdi-sun-compass bx-spin mode-light"></i>
        <i class="mdi mdi-moon-waning-crescent mode-dark"></i>
    </a> --}}

    <!-- Loader -->
    {{-- <div id="preloader">
        <div id="status">
            <div class="spinner"></div>
        </div>
    </div> --}}

    <!--Navbar Start-->

    <nav class="navbar navbar-expand-lg fixed-top navbar-custom sticky sticky-dark" id="navbar">
        <div class="container">
            <!-- LOGO -->
            <a class="navbar-brand logo" href="{{ url('/') }}">
                <img src="{{ asset('logo.png') }}" alt="" class="logo-dark" height="34" />
                <img src="{{ asset('logo.png') }}" alt="" class="logo-light" height="34" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <i class="mdi mdi-menu"></i>
            </button>

            @include('layouts.rsc.header')



        </div>
    </nav>
    <!-- Navbar End -->





    <!-- Main Container -->
    <main id="main-container">

        <!-- Hero -->
        <div class="bg-image" style="background-image: url({{ $blog->cover_image }});">
            <div class="bg-black-75">
                <div class="content content-top content-full text-center">
                    <h1 class="fw-bold text-white mt-5 mb-3">
                        {{ $blog->title }}
                    </h1>
                    {{-- <h2 class="h3 fw-normal text-white-75 mb-5">Building a new web platform.</h2> --}}
                    <p>
                        <span class="badge rounded-pill bg-primary fs-base px-3 py-2 me-2 m-1">
                            <i class="fa fa-user-circle me-1"></i> by Payhankey
                        </span>
                        <span class="badge rounded-pill bg-primary fs-base px-3 py-2 m-1">
                            {{-- <i class="fa fa-clock me-1"></i> {{$blog->blogCategory->name}} --}}
                            <i class="fa fa-dot-circle me-1"></i> {{ $blog->blogCategory->name }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <!-- END Hero -->

        <!-- Page Content -->
        <div class="content content-full">
            <div class="row justify-content-center">
                <div class="col-sm-8 py-5">
                    <!-- Story -->
                    <!-- Magnific Popup (.js-gallery class is initialized in Helpers.jqMagnific()) -->
                    <!-- For more info and examples you can check out http://dimsemenov.com/plugins/magnific-popup/ -->
                    <article class="js-gallery story">
                        {!! $blog->content !!}
                    </article>
                    <!-- END Story -->

                    <!-- Actions -->
                    <div class="mt-5 d-flex justify-content-between push">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-alt-secondary" data-bs-toggle="tooltip"
                                title="Like Story">
                                <i class="fa fa-eye text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-alt-secondary" data-bs-toggle="tooltip"
                                title="Recommend">
                                {{ $blog->views }}
                            </button>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-alt-secondary dropdown-toggle"
                                id="dropdown-blog-story" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fa fa-share-alt opacity-50 me-1"></i> Share
                            </button>
                            <div class="dropdown-menu dropdown-menu-end fs-sm" aria-labelledby="dropdown-blog-story">
                                <a class="dropdown-item" href="javascript:void(0)">
                                    <i class="fab fa-fw fa-facebook me-1"></i> Facebook
                                </a>
                                <a class="dropdown-item" href="javascript:void(0)">
                                    <i class="fab fa-fw fa-twitter me-1"></i> Twitter
                                </a>
                                <a class="dropdown-item" href="javascript:void(0)">
                                    <i class="fab fa-fw fa-linkedin me-1"></i> LinkedIn
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- END Actions -->

                    <!-- Comments -->
                    {{-- 
                    <div class="px-4 pt-4 rounded bg-body-extra-light">
                        <p class="fs-sm">
                        <i class="fa fa-thumbs-up text-info"></i>
                        <i class="fa fa-heart text-danger"></i>
                        <a class="fw-semibold" href="javascript:void(0)">Albert Ray</a>,
                        <a class="fw-semibold" href="javascript:void(0)">Carol Ray</a>,
                        <a class="fw-semibold" href="javascript:void(0)">and 72 others</a>
                        </p>
                        <form action="be_pages_blog_story.html" method="POST" onsubmit="return false;">
                        <input type="text" class="form-control form-control-alt" placeholder="Write a comment..">
                        </form>
                        <div class="pt-3 fs-sm">
                        <div class="d-flex">
                            <a class="flex-shrink-0 img-link me-2" href="javascript:void(0)">
                            <img class="img-avatar img-avatar32 img-avatar-thumb" src="assets/media/avatars/avatar6.jpg" alt="">
                            </a>
                            <div class="flex-grow-1">
                            <p class="mb-1">
                                <a class="fw-semibold" href="javascript:void(0)">Lori Grant</a>
                                Vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit.
                            </p>
                            <p>
                                <a class="me-1" href="javascript:void(0)">Like</a>
                                <a href="javascript:void(0)">Comment</a>
                            </p>
                            <div class="d-flex">
                                <a class="flex-shrink-0 img-link me-2" href="javascript:void(0)">
                                <img class="img-avatar img-avatar32 img-avatar-thumb" src="assets/media/avatars/avatar10.jpg" alt="">
                                </a>
                                <div class="flex-grow-1">
                                <p class="mb-1">
                                    <a class="fw-semibold" href="javascript:void(0)">Jack Estrada</a>
                                    Odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
                                </p>
                                <p>
                                    <a class="me-1" href="javascript:void(0)">Like</a>
                                    <a href="javascript:void(0)">Comment</a>
                                </p>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <a class="flex-shrink-0 img-link me-2" href="javascript:void(0)">
                            <img class="img-avatar img-avatar32 img-avatar-thumb" src="assets/media/avatars/avatar10.jpg" alt="">
                            </a>
                            <div class="flex-grow-1">
                            <p class="mb-1">
                                <a class="fw-semibold" href="javascript:void(0)">Ryan Flores</a>
                                Leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices.
                            </p>
                            <p>
                                <a class="me-1" href="javascript:void(0)">Like</a>
                                <a href="javascript:void(0)">Comment</a>
                            </p>
                            <div class="d-flex">
                                <a class="flex-shrink-0 img-link me-2" href="javascript:void(0)">
                                <img class="img-avatar img-avatar32 img-avatar-thumb" src="assets/media/avatars/avatar14.jpg" alt="">
                                </a>
                                <div class="flex-grow-1">
                                <p class="mb-1">
                                    <a class="fw-semibold" href="javascript:void(0)">Carl Wells</a>
                                    Odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
                                </p>
                                <p>
                                    <a class="me-1" href="javascript:void(0)">Like</a>
                                    <a href="javascript:void(0)">Comment</a>
                                </p>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div> 
                    --}}
                    <!-- END Comments -->
                    <div class="row items-push">
                        <h4>Related Posts</h4>

                        @foreach ($suggestions as $blog)
                            <div class="col-lg-4">
                                <a class="block block-rounded block-link-pop h-100 mb-0"
                                    href="{{ url('blog/' . $blog->slug) }}">
                                    {{-- <img class="img-fluid" src="{{ asset('src/assets/media/photos/photo21@2x.jpg')}}" alt=""> --}}
                                    <img class="img-fluid" src="{{ $blog->cover_image }}" alt="">
                                    <div class="block-content">
                                        <h4 class="mb-1">{{ $blog->title }}</h4>
                                        <p class="fs-sm">
                                            <span class="text-primary">Payhankey</span> on
                                            {{ \Carbon\Carbon::parse($blog->created_at)->format('F d, Y') }}
                                            {{-- · <em class="text-muted">9 min</em> --}}
                                        </p>
                                        <p>
                                            {!! $blog->excerpt !!}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach

                    </div>
                </div>


            </div>
        </div>
        <!-- END Page Content -->
    </main>
    <!-- END Main Container -->


    <!-- javascript -->
    <script src="{{ asset('asset/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('asset/js/smooth-scroll.polyfills.min.js') }}"></script>
    <script src="{{ asset('asset/js/gumshoe.polyfills.min.js') }}"></script>
    <!-- feather icon -->
    <script src="{{ asset('asset/js/feather.js') }}"></script>
    <!-- unicons icon -->
    <script src="{{ asset('asset/js/unicons.js') }}"></script>
    <!-- Main Js -->
    <script src="{{ asset('asset/js/app.js') }}"></script>

</body>

</html>
