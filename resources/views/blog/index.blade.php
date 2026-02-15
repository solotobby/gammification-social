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
        background-color: #E0E0E0;  /* light milk color */
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
        <div class="bg-image" style="background-image: url('assets/media/photos/photo9@2x.jpg');">
          <div class="bg-black-50">
            <div class="content content-top content-full text-center">
              <h1 class="fw-bold text-white mt-5 mb-2">
                Check out our latest stories
              </h1>
              <h3 class="fw-normal text-white-75 mb-5">Be inspired and create something amazing today.</h3>
            </div>
          </div>
        </div>
        <!-- END Hero -->

        <!-- Page Content -->
        <div class="content content-full">
          <div class="row items-push">
            <!-- Story -->
            @foreach ($blogs as $blog)
                <div class="col-lg-4">
              <a class="block block-rounded block-link-pop h-100 mb-0" href="{{url('blog/'.$blog->slug)}}">
                {{-- <img class="img-fluid" src="{{ asset('src/assets/media/photos/photo21@2x.jpg')}}" alt=""> --}}
                <img class="img-fluid" src="{{ $blog->cover_image }}" alt="">
                <div class="block-content">
                  <h4 class="mb-1">{{ $blog->title }}</h4>
                  <p class="fs-sm">
                    <span class="text-primary">Payhankey</span> on {{ \Carbon\Carbon::parse($blog->created_at)->format('F d, Y') }} 
                    {{-- · <em class="text-muted">9 min</em> --}}
                  </p>
                  <p>
                    {!!$blog->excerpt!!}
                  </p>
                </div>
              </a>
            </div>
            @endforeach
            
            <!-- END Story -->

          </div>

          <!-- Pagination -->
          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center push">
              <li class="page-item active">
                <a class="page-link" href="javascript:void(0)">1</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript:void(0)">2</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript:void(0)">3</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript:void(0)">4</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript:void(0)">5</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript:void(0)" aria-label="Next">
                  <span aria-hidden="true">
                    <i class="fa fa-angle-right"></i>
                  </span>
                  <span class="visually-hidden">Next</span>
                </a>
              </li>
            </ul>
          </nav>
          <!-- END Pagination -->
        </div>
        <!-- END Page Content -->
      </main>
      <!-- END Main Container -->


 <div class="footer-alt py-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="text-white-50 font-size-15 mb-0">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © Payhankey.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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



