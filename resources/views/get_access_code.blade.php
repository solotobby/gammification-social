<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Pawo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Premium Bootstrap 4 Landing Page Template" />
    <meta name="keywords" content="bootstrap 4, premium, marketing, multipurpose" />
    <meta content="Themesbrand" name="author" />
    <!-- favicon -->
    <link rel="shortcut icon" href="{{asset('favicon.png')}}">
    <!-- css -->
    <link href="{{asset('asset/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('asset/css/materialdesignicons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('asset/css/style.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
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
                <img src="{{asset('logo.png')}}" alt="" class="logo-dark" height="34" />
                <img src="{{asset('logo.png')}}" alt="" class="logo-light" height="34" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <i class="mdi mdi-menu"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mx-auto navbar-center" id="navbar-navlist">
                    <li class="nav-item">
                        <a data-scroll href="{{ url('/') }}" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="#features" class="nav-link">Features</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="{{ url('#pricing') }}" class="nav-link">Pricing</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a data-scroll href="{{ url('#blog') }}" class="nav-link">Blog</a>
                    </li> --}}
                    <li class="nav-item">
                        <a data-scroll href="{{ url('#contact') }}#contact" class="nav-link">Contact Us</a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-center">
                    <li class="nav-item">
                        <a href="{{ url('login') }}" class="nav-link">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('register') }}" class="nav-link">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

   

    <!-- Blog start -->
    {{-- <section class="section blog" id="blog">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="text-center mb-5">
                        <h3 class="title mb-3">Latest News</h3>
                        <p class="text-muted font-size-15">Et harum quidem rerum facilis est et expedita distinctio nam
                            libero tempore cum soluta nobis eligendi cumque.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="blog-box mb-4 mb-lg-0">
                        <img src="images/blog/img-1.jpg" alt="" class="img-fluid d-block mx-auto rounded">
                        <ul class="list-inline text-muted text-uppercase font-size-15 font-weight-medium mt-3 mb-2">
                            <li class="list-inline-item me-3"><i class="icon-size-15 icon me-1"
                                    data-feather="calendar"></i> April 10 2020</li>
                            <li class="list-inline-item"><i class="icon-size-15 icon me-1" data-feather="user"></i>
                                Admin</li>
                        </ul>
                        <a href="#" class="fw-bold h5">Best Traveling Place</a>
                        <p class="text-muted font-size-15">Integer ante arcu accumsan a consectetuer eget posuere mauris
                            praesent adipiscing phasellus ullamcorper ipsum rutrum punc.</p>
                        <a href="#" class="text-primary  font-weight-semibold">Learn More <span
                                class="ms-2 right-icon">&#8594;</span></a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog-box blog-box mb-4 mb-lg-0">
                        <img src="images/blog/img-2.jpg" alt="" class="img-fluid d-block mx-auto rounded">
                        <ul class="list-inline text-muted text-uppercase font-size-15 font-weight-medium mt-3 mb-2">
                            <li class="list-inline-item me-3"><i class="icon-size-15 icon me-1"
                                    data-feather="calendar"></i> April 11 2020</li>
                            <li class="list-inline-item"><i class="icon-size-15 icon me-1" data-feather="user"></i>
                                Admin</li>
                        </ul>
                        <a href="#" class="fw-bold h5">Private Meeting Room</a>
                        <p class="text-muted font-size-15">Integer ante arcu accumsan a consectetuer eget posuere mauris
                            praesent adipiscing phasellus ullamcorper ipsum rutrum punc.</p>
                        <a href="#" class="text-primary   font-weight-semibold">Learn More <span
                                class="ms-2 right-icon">&#8594;</span></a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog-box blog-box mb-4 mb-lg-0">
                        <img src="images/blog/img-3.jpg" alt="" class="img-fluid d-block mx-auto rounded">
                        <ul class="list-inline text-muted text-uppercase font-size-15 font-weight-medium mt-3 mb-2">
                            <li class="list-inline-item me-3"><i class="icon-size-15 icon me-1"
                                    data-feather="calendar"></i> April 12 2020</li>
                            <li class="list-inline-item"><i class="icon-size-15 icon me-1" data-feather="user"></i>
                                Admin</li>
                        </ul>
                        <a href="#" class="fw-bold h5">The Best Business Ideas</a>
                        <p class="text-muted font-size-15">Integer ante arcu accumsan a consectetuer eget posuere mauris
                            praesent adipiscing phasellus ullamcorper ipsum rutrum punc.</p>
                        <a href="#" class="text-primary  font-weight-semibold">Learn More <span
                                class="ms-2 right-icon">&#8594;</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Blog End -->

    <!-- Contact Us Start -->
  

    <section class="section feather-bg-img" style="background-image: url(asset/images/features-bg-img-1.png)">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 align-self-center">
                    <p class="font-weight-medium text-uppercase mb-2"><i
                            class="mdi mdi-chart-bubble h2 text-primary me-1 align-middle"></i> Early Bird Offer </p>
                    {{-- <h3 class="font-weight-semibold line-height-1_4 mb-4">We do the work you <b>stay focused</b> on
                        <b>your customers</b>.
                    </h3> --}}
                    <h3 class="title mb-3">Get Access Code for {{ ucfirst($level) }}</h3>

                    <p class="text-muted font-size-15 mb-4">
                        An Access code is your unique passkey to 
                        create an account on Payhankey. Once payment is successful and confirmed, 
                        your access code will be sent to your registered email address.
                    </p>
                    
                    <div class="mt-5">
                        <a href="{{ $dollarlink }}" target="_blank" class="btn btn-primary me-2">Pay in Dollar - ${{ $amountDollar }}</a>
                        <a href="{{ $nairalink }}" target="_blank" class="btn btn-soft-primary">Pay in Naira - &#8358;{{ $amountNaira }}</a>
                    </div>
                    <hr>
                    <div class="">
                        {{-- <p class="text-muted font-size-15 mb-4">Pay  ${{ $amountDollar }} to the USDT wallet address below</p> --}}
                        <a href="{{ $nairalink }}" target="_blank" class="btn btn-soft-primary" data-toggle="modal" data-target="#exampleModal">Pay with USDT - ${{ $amountDollar }}</a>
                    </div>
                </div>
                <div class="col-lg-6 offset-lg-1 align-self-center">
                    <div class="mt-4 mt-lg-0">
                        <img src="{{asset('asset/images/features-img-1.png')}}" alt="" class="img-fluid d-block mx-auto">
                    </div>
                </div>

                    <!-- Button trigger modal -->

  
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Pay with USDT - ${{ $amountDollar }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                            Copy the wallet address below to make a payment of ${{ $amountDollar }}. <b>Only USDT (TRC20) is acceptable with the Address</b>
                            <br>
                            
                            <b><i>TDq4Lg25Vbr9BxZpsWc1WcuW2UmuqnnSZZ</i></b>

                            <button type="button" onclick="copyToClipboard('TDq4Lg25Vbr9BxZpsWc1WcuW2UmuqnnSZZ')" class="btn btn-xs btn-primary mt-3" data-bs-dismiss="modal">Copy USDT Address</button>
                            <br><br>
                            Send evidence of payment to support@payhankey.com immediately after payment.
                        </div>
                        <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    </div>
                </div>



            </div>
        </div>
    </section>
    <!-- Contact Us End -->

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Address copied to clipboard');
            }, function(err) {
                alert('Could not copy text: ', err);
            });
        }
    </script>


    <!-- Footer Start -->
    <section class="footer" style="background-image: url(images/footer-bg.png)">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-5">
                        <img src="images/logo-light.png" alt="" class="" height="24">
                        <p class="text-white-50 my-4 font-size-15">
                            Payhankey allows you to make short posts, facts, quizzes and teasers. Earn signup bonus and referral bonuses when you refer your friends.
                        </p>
                        <ul class="list-inline footer-social-icon-content">
                            <li class="list-inline-item me-4"><a href="#" class="footer-social-icon facebook"><i
                                        class="" data-feather="facebook"></i></a></li>
                            <li class="list-inline-item me-4"><a href="#" class="footer-social-icon"><i class=""
                                        data-feather="twitter"></i></a></li>
                            <li class="list-inline-item me-4"><a href="#" class="footer-social-icon"><i class=""
                                        data-feather="instagram"></i></a></li>
                            <li class="list-inline-item "><a href="#" class="footer-social-icon"><i class=""
                                        data-feather="linkedin"></i></a></li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
    <div class="footer-alt py-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="text-white-50 font-size-15 mb-0">
                            <script>document.write(new Date().getFullYear())</script> © Payhankey.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

   

    <!-- javascript -->
    <script src="{{asset('asset/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('asset/js/smooth-scroll.polyfills.min.js')}}"></script>
    <script src="{{asset('asset/js/gumshoe.polyfills.min.js')}}"></script>
    <!-- feather icon -->
    <script src="{{asset('asset/js/feather.js')}}"></script>
    <!-- unicons icon -->
    <script src="{{asset('asset/js/unicons.js')}}"></script>
    <!-- Main Js -->
    <script src="{{asset('asset/js/app.js')}}"></script>
    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>

</html>