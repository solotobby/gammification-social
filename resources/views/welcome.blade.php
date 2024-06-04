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
    <link rel="shortcut icon" href="{{asset('favicon.png')}}">
    <!-- css -->
    <link href="{{asset('asset/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('asset/css/materialdesignicons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('asset/css/style.min.css')}}" rel="stylesheet" type="text/css" />
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
                    {{-- <li class="nav-item">
                        <a data-scroll href="#pricing" class="nav-link">Access Code</a>
                    </li> --}}
                    <li class="nav-item">
                        <a data-scroll href="#faq" class="nav-link">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="#partner" class="nav-link">Become a Partner</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="#contact" class="nav-link">Contact Us</a>
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

    <!-- Hero Start -->
    <section class="hero-1-bg" style="background-image: url(asset/images/hero-1-bg-img.png)" id="home">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-6">
                    <h1 class="hero-1-title fw-bold text-shadow mb-4">Monetize your Posts</h1>
                    <div class="w-75 mb-5 mb-lg-0">
                        <p class="text-muted mb-5 pb-5 font-size-17">The social media that pays you for every posts, views, comments and likes. Withdraw your earnings anytime. Minimum withdrawal of $20.</p>
                        {{-- <p><span class="font-size-20 me-2">🥳️</span>Join our mailing list to receive updates before creating an account.</p>
                        <div class="subscribe-form">
                            <form action="#">
                                <input type="text" placeholder="Enter email...">
                                <button type="submit" class="btn btn-primary">Subscribe</button>
                            </form>
                        </div> --}}
                    </div>
                </div>
                <div class="col-lg-6 col-md-10">
                    <div class=" mt-5 mt-lg-0">
                        <img src="{{asset('asset/images/hero-1-img.png')}}" alt="" class="img-fluid d-block mx-auto">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero End -->

    <!-- Why Choose Us Start -->
    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 align-self-center">
                    <div class="mb-4 mb-lg-0">
                        <div class="p-2 bg-soft-primary d-inline-block rounded mb-4">
                            <div class="icon-xxl uim-icon-primary"><i class="uim uim-cube"></i></div>
                        </div>
                        <h3 class="">Why Choose Us ?</h3>
                        <p class="text-muted mb-4">Payhankey pays you for every engagement you get on short posts, facts, quizzes and teasers. You earn from your FIRST post.</p>
                        <a href="{{ url('/#pricing') }}" class="btn btn-outline-primary">Get Started</a>
                    </div>
                </div>
                <div class="col-lg-8 align-self-center">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="wc-box rounded text-center wc-box-primary p-4 mt-md-5">
                                <div class="wc-box-icon">
                                    <i class="mdi mdi-cash"></i>
                                </div>
                                <h5 class="fw-bold mb-2 wc-title mt-4">Earn on the Go</h5>
                                <p class="text-muted mb-0 font-size-15 wc-subtitle">Earn up to $5 signup bonus. Make money from every post.</p>
                            </div>
                            <div class="wc-box rounded text-center wc-box-primary p-4">
                                <div class="wc-box-icon">
                                    <i class="mdi mdi-trending-up"></i>
                                </div>
                                <h5 class="fw-bold mb-2 wc-title mt-4">Grow Your Revenue</h5>
                                <p class="text-muted mb-0 font-size-15 wc-subtitle">Earn up to $3 on every friend you refer to Payhankey.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wc-box rounded text-center wc-box-primary p-4">
                                <div class="wc-box-icon">
                                    <i class="mdi mdi-chart-line"></i>
                                </div>
                                <h5 class="fw-bold mb-2 wc-title mt-4">Analytics</h5>
                                <p class="text-muted mb-0 font-size-15 wc-subtitle">Monitor your growth and Revenue on the go</p>
                            </div>
                            <div class="wc-box rounded text-center wc-box-primary p-4">
                                <div class="wc-box-icon">
                                    <i class="mdi mdi-account-cash"></i>
                                </div>
                                <h5 class="fw-bold mb-2 wc-title mt-4">Withdraw Anytime</h5>
                                <p class="text-muted mb-0 font-size-15 wc-subtitle">Withdraw your earnings via Paypal or USDT.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Why Choose Us End -->

    <!-- Features Start -->
    <section class="section bg-light feather-bg-img" style="background-image: url('src/images/features-bg-img.png');"
        id="features">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="text-center mb-5">
                        <h3 class="title mb-3">Awesome Features</h3>
                        <p class="text-muted font-size-15">
                            We are removing every monetization barrier for all creators globally.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6 align-self-center">
                    <div class="mb-4 mb-lg-0">
                        <img src="{{asset('asset/images/features-img.png')}}" alt="" class="img-fluid d-block mx-auto">
                    </div>
                </div>

                <div class="col-lg-5 offset-lg-1 align-self-center">
                    <p class="font-weight-medium text-uppercase mb-2"><i
                            class="mdi mdi-chart-bubble h2 text-primary me-1 align-middle"></i> Earning Made Easy</p>
                    <h3 class="font-weight-semibold line-height-1_4 mb-4">
                       
                        We pay you for your <b>Likes</b>, <b>Comments</b>
                       and <b>Views</b></h3>
                    <p class="text-muted font-size-15 mb-4">Payhankey allows you to make short posts, facts, quizzes and teasers. Earn signup bonus and referral bonuses when you refer your friends.</p>
                    {{-- <p class="text-muted mb-2"><i class="icon-xs me-1" data-feather="layout"></i> Donec pede justo
                        fringilla vel nec.</p>
                    <p class="text-muted"><i class="icon-xs me-1" data-feather="life-buoy"></i> Cras ultricies mi eu
                        turpis hendrerit fringilla.</p> --}}
                    <div class="mt-5">
                        <a href="{{ url('/#pricing')}}" class="btn btn-primary me-2">Learn More</a>
                        <a href="{{ url('register') }}" class="btn btn-soft-primary">Get Started</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section feather-bg-img" style="background-image: url(asset/images/features-bg-img-1.png)" id="partner">
        <div class="container">
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif


            <div class="row justify-content-center">
                <div class="col-lg-5 align-self-center">
                    <p class="font-weight-medium text-uppercase mb-2"><i
                            class="mdi mdi-chart-bubble h2 text-primary me-1 align-middle"></i> Become our Partner </p>
                    {{-- <h3 class="font-weight-semibold line-height-1_4 mb-4">We do the work you <b>stay focused</b> on
                        <b>your customers</b>.
                    </h3> --}}
                    <h3 class="title mb-3">Earn more as a Partner</h3>

                    <p class="text-muted font-size-15 mb-4">
                        As a partner, you can buy bulk access codes at a discounted price and resell to your friends and family. This makes you earn affiliate commissions and partner commissions. Our partners earn up to $500 daily.
                    </p>
                    
                    <div class="mt-5">
                        <a href="" target="_blank" data-bs-toggle="modal" data-bs-target="#exampleModalCenter-1" class="btn btn-primary me-2">Become a Partner </a>
                        {{-- <a href="" target="_blank" class="btn btn-soft-primary">Pay in Naira  </a> --}}
                    </div>
                    
                </div>
                <div class="col-lg-6 offset-lg-1 align-self-center">
                    <div class="mt-4 mt-lg-0">
                        <img src="{{asset('asset/images/features-img-1.png')}}" alt="" class="img-fluid d-block mx-auto">
                    </div>
                </div>

                    <!-- Button trigger modal -->

  
               



            </div>
        </div>
    </section>

    <!-- Features End -->

    <!-- Pricing Start -->
    <section class="section bg-light" id="pricing">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h3 class="title mb-3">How do you want to start?</h3>
                        <p class="text-muted font-size-15"> Start earning daily on Payhankey 
                            instead of waiting for many subscribers, followers or unending watch hours requirement 
                            for monetization somewhere else.</p>
                            <p>
                                <strong>Get an Access Code to Get Started</strong>
                            </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="pricing-box rounded text-center p-4">
                        <div class="pricing-icon-bg my-4">
                            <i class="mdi mdi-account h1"></i>
                        </div>
                        <h4 class="title mb-3">Beginner</h4>
                        <h1 class="fw-bold mb-0"><b><sup class="h4 me-2 fw-bold">$</sup>5</b></h1>
                        <p class="text-muted font-weight-semibold">Access fee</p>
                        <ul class="list-unstyled pricing-item mb-4">
                            <li class="text-muted">SignUp Bonus: $1</li>
                            <li class="text-muted">Referral Bonus: $1</li>
                            <li class="text-muted">every 1,000 views: $1</li>
                            <li class="text-muted">every 1,000 likes: $0.4</li>
                            <li class="text-muted">every 1,000 comments: $0.5</li>
                        </ul>
                        <a href="{{ url('access/code/beginner') }}" class="btn btn-outline-primary pr-btn">Get Access Code</a>
                        <div class="mt-4">
                            <div class="hero-bottom-img">
                                <img src="{{asset('asset/images/pricing-bottom-bg.png')}}" alt="" class="img-fluid d-block mx-auto">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="pricing-box rounded text-center active p-4">
                        <div class="pricing-icon-bg my-4">
                            <i class="mdi mdi-account-multiple h1"></i>
                        </div>
                        <h4 class="title mb-3">Creator(Popular)</h4>
                        <h1 class="fw-bold mb-0"><b><sup class="h4 me-2 fw-bold">$</sup>10</b></h1>
                        <p class="text-muted font-weight-semibold">Access fee</p>
                        <ul class="list-unstyled pricing-item mb-4">
                            <li class="text-muted">SignUp Bonus: $2</li>
                            <li class="text-muted">Referral Bonus: $3</li>
                            <li class="text-muted">every 1,000 views: $2</li>
                            <li class="text-muted">every 1,000 likes: $0.6</li>
                            <li class="text-muted">every 1,000 comments: $0.7</li>
                        </ul>
                        <a href="{{ url('access/code/creator') }}" class="btn btn-primary pr-btn">Get Access Code</a>
                        <div class="mt-4">
                            <div class="hero-bottom-img">
                                <img src="images/pricing-bottom-bg.png" alt="" class="img-fluid d-block mx-auto">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="pricing-box rounded text-center p-4">
                        <div class="pricing-icon-bg my-4">
                            <i class="mdi mdi-account-group h1"></i>
                        </div>
                        <h4 class="title mb-3">Influencer</h4>
                        <h1 class="fw-bold mb-0"><b><sup class="h4 me-2 fw-bold">$</sup>20</b></h1>
                        <p class="text-muted font-weight-semibold">Access fee</p>
                        <ul class="list-unstyled pricing-item mb-4">
                            <li class="text-muted">SignUp Bonus: $5</li>
                            <li class="text-muted">Referral Bonus: $5</li>
                            <li class="text-muted">every 1,000 views: $4</li>
                            <li class="text-muted">every 1,000 likes: $0.7</li>
                            <li class="text-muted">every 1,000 comments: $1</li>
                        </ul>
                        <a href="{{ url('access/code/influencer') }}" class="btn btn-outline-primary pr-btn">Get Access Code</a>
                        <div class="mt-4">
                            <div class="hero-bottom-img">
                                <img src="images/pricing-bottom-bg.png" alt="" class="img-fluid d-block mx-auto">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Pricing End -->

   

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
    <section class="section bg-light" id="contact">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="text-center mb-5">
                        <h3 class="title mb-3">Contact Us</h3>
                        <p class="text-muted font-size-15">
                            We'll love to hear from you and learn how best to assist you monetize your posts and content. Kindly send us a mail and we will be glad to help.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                
                <div class="col-lg-6 align-self-center">
                    <div class="contact-detail text-muted ms-lg-5">

                        <p class=""><i class="icon-xs icon me-1" data-feather="mail"></i> :
                            <span>support@payhankey.com</span>
                        </p>
                       
                        <p class=""><i class="icon-xs icon me-1" data-feather="map-pin"></i> : <span>
                            1309 Coffeen Avenue, 1200 Sheridan Wyoming, 82801</span>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Us End -->

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

    <!-- login Modal Start -->
    {{-- <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content login-page">
                <div class="modal-body">
                    <div class="text-center">
                        <h3 class="title mb-4">Welcome To Lezir</h3>
                        <h4 class="text-uppercase text-primary"><b>Login</b></h4>
                    </div>
                    <div class="login-form mt-4">
                        <form>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Username / Email</label>
                                <input type="email" class="form-control" id="exampleInputEmail1"
                                    placeholder="Youremail@gmail.com">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password</label>
                                <input type="password" class="form-control" id="exampleInputPassword1"
                                    placeholder="Password">
                            </div>
                            <a href="#" class="float-end text-muted font-size-15">Forgot Password.?</a>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck1">
                                <label class="custom-control-label font-size-15" for="customCheck1">Remember Me</label>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Login <i class="icon-size-15 icon ms-1"
                                        data-feather="arrow-right-circle"></i></button>
                            </div>
                        </form>
                        <div class="position-relative text-center mt-4">
                            <span class="login-border"></span>
                            <p class="social-login text-muted font-size-17">Social Login</p>
                        </div>
                        <div class="text-center">
                            <ul class="list-inline mt-2 mb-3">
                                <li class="list-inline-item me-3"><a href="#" class="login-social-icon icon-primary"><i
                                            class="icon-xs" data-feather="facebook"></i></a></li>
                                <li class="list-inline-item me-3"><a href="#" class="login-social-icon icon-info"><i
                                            class="icon-xs" data-feather="twitter"></i></a></li>
                                <li class="list-inline-item me-3"><a href="#" class="login-social-icon icon-danger"><i
                                            class="icon-xs" data-feather="instagram"></i></a></li>
                                <li class="list-inline-item"><a href="#" class="login-social-icon icon-success"><i
                                            class="icon-xs" data-feather="linkedin"></i></a></li>
                            </ul>
                            <p class="text-muted mb-0">New User? <a href="#" class="text-primary">Signup</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- login Modal End -->

    <!-- Register Modal Start-->
    <div class="modal fade" id="exampleModalCenter-1" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content login-page">
                <div class="modal-body">
                    <div class="text-center">
                        <h3 class="title mb-4">Welcome To Payhankey</h3>
                        <h4 class="text-uppercase text-primary"><b>Register as Partner</b></h4>
                    </div>
                    <div class="login-form mt-4">
                        <form action="{{ route('partner') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputUsername">Full Name</label>
                                <input type="text" class="form-control" name="name" id="exampleInputUsername"
                                    placeholder="Enter Name" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail2">Email</label>
                                <input type="email" class="form-control" name="email" id="exampleInputEmail2"
                                    placeholder="Youremail@gmail.com" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail2">Phone Number</label>
                                <input type="text" class="form-control" name="phone" id="exampleInputEmail2"
                                    placeholder="Youremail@gmail.com" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail2">Govt. issued Id. Card</label>
                                    <select class="form-control" name="identification" required>
                                        <option>National Identity Number (NIN)</option>
                                        <option>Passport</option>
                                    </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail2">Country</label>
                                    <select class="form-control" name="country" required>
                                        <option>Nigeria</option>
                                        <option>Germany</option>
                                    </select>
                            </div>
                            {{-- <div class="form-group">
                                <label for="exampleInputPassword2">Password</label>
                                <input type="password" class="form-control" id="exampleInputPassword2"
                                    placeholder="Password">
                            </div>
                            <a href="#" class="float-end text-muted font-size-15">Forgot Password.?</a>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck2">
                                <label class="custom-control-label font-size-15" for="customCheck2">Remember Me</label>
                            </div> --}}
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Register <i class="icon-size-15 icon ms-1"
                                        data-feather="arrow-right-circle"></i></button>
                            </div>
                        </form>
                        {{-- <div class="position-relative text-center mt-4">
                            <span class="login-border"></span>
                            <p class="social-login text-muted font-size-17">Social Login</p>
                        </div>
                        <div class="text-center">
                            <ul class="list-inline mt-2 mb-3">
                                <li class="list-inline-item me-3"><a href="#" class="login-social-icon icon-primary"><i
                                            class="icon-xs" data-feather="facebook"></i></a></li>
                                <li class="list-inline-item me-3"><a href="#" class="login-social-icon icon-info"><i
                                            class="icon-xs" data-feather="twitter"></i></a></li>
                                <li class="list-inline-item me-3"><a href="#" class="login-social-icon icon-danger"><i
                                            class="icon-xs" data-feather="instagram"></i></a></li>
                                <li class="list-inline-item"><a href="#" class="login-social-icon icon-success"><i
                                            class="icon-xs" data-feather="linkedin"></i></a></li>
                            </ul>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Register Modal Start-->


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

</body>

</html>