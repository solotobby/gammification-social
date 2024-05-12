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
    <link rel="shortcut icon" href="images/favicon.ico">
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
                <img src="{{asset('asset/images/logo-dark.png')}}" alt="" class="logo-dark" height="24" />
                <img src="{{asset('asset/images/logo-light.png')}}" alt="" class="logo-light" height="24" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <i class="mdi mdi-menu"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mx-auto navbar-center" id="navbar-navlist">
                    <li class="nav-item">
                        <a data-scroll href="{{ url('/#home') }}" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="#features" class="nav-link">Features</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="{{ url('/#pricing') }}" class="nav-link">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="{{ url('/#blog') }}" class="nav-link">Blog</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a data-scroll href="{{ url('/#contact') }}#contact" class="nav-link">Contact Us</a>
                    </li> --}}
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
    <section class="section bg-light" id="contact">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="text-center mb-5">
                        <h3 class="title mb-3">Get Access Code</h3>
                        <p class="text-muted font-size-15">Et harum quidem rerum facilis est et expedita distinctio nam
                            libero tempore cum soluta nobis eligendi cumque.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-7 align-self-center">
                    <div class="custom-form mb-5 mb-lg-0">
                        <form method="post" action="{{ url('process/access/code') }}" name="myForm" onsubmit="validateForm()">
                            @csrf
                            <p id="error-msg"></p>
                            <div id="simple-msg"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name*</label>
                                        <input name="name" id="name" type="text" class="form-control"
                                            placeholder="Your name..." required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address*</label>
                                        <input name="email" id="email" type="email" class="form-control"
                                            placeholder="Your email..." required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="comments">Message*</label>
                                        <textarea name="comments" id="comments" rows="4" class="form-control"
                                            placeholder="Your message..." required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="submit" id="submit" class="btn btn-primary">Get Access Code
                                        <i class="icon-size-15 ms-2 icon" data-feather="send"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5 align-self-center">
                    <div class="contact-detail text-muted ms-lg-5">
                        <p class=""><i class="icon-xs icon me-1" data-feather="mail"></i> :
                            <span>support@website.com</span>
                        </p>
                        {{-- <p class=""><i class="icon-xs icon me-1" data-feather="link"></i> : <span>www.website.com</span>
                        </p> --}}
                        
                        <p class=""><i class="icon-xs icon me-1" data-feather="instagram"></i> : <span>1644 Deer Ridge
                            Drive Rochelle Park, NJ 07662</span>
                        </p>
                        {{-- <p class=""><i class="icon-xs icon me-1" data-feather="phone-call"></i> : <span>(+001) 123 456
                                7890</span></p> --}}
                        {{-- <p class=""><i class="icon-xs icon me-1" data-feather="clock"></i> : <span>9:00 AM - 6:00
                                PM</span></p> --}}
                        <p class=""><i class="icon-xs icon me-1" data-feather="twitter"></i> : <span>1644 Deer Ridge
                                Drive Rochelle Park, NJ 07662</span>
                        </p>
                        <p class=""><i class="icon-xs icon me-1" data-feather="facebook"></i> : <span>1644 Deer Ridge
                            Drive Rochelle Park, NJ 07662</span>
                        </p>
                        <p class=""><i class="icon-xs icon me-1" data-feather="x"></i> : <span>1644 Deer Ridge
                            Drive Rochelle Park, NJ 07662</span>
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
                <div class="col-lg-4">
                    <div class="mb-5">
                        <img src="images/logo-light.png" alt="" class="" height="24">
                        <p class="text-white-50 my-4 font-size-15">Cras ultricies mi eu turpis sit hendrerit fringilla
                            vestibulum ante ipsum primis in faucibus ultrices posuere cubilia.</p>
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
                <div class="col-lg-7 offset-lg-1">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-white text-uppercase mb-3">About Us</h6>
                            <ul class="list-unstyled footer-sub-menu">
                                <li><a href="#" class="footer-link">Works</a></li>
                                <li><a href="#" class="footer-link">Strategy</a></li>
                                <li><a href="#" class="footer-link">Releases</a></li>
                                <li><a href="#" class="footer-link">Press</a></li>
                                <li><a href="#" class="footer-link">Mission</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-white text-uppercase mb-3">Customers</h6>
                            <ul class="list-unstyled footer-sub-menu">
                                <li><a href="#" class="footer-link">Tranding</a></li>
                                <li><a href="#" class="footer-link">Popular</a></li>
                                <li><a href="#" class="footer-link">Customers</a></li>
                                <li><a href="#" class="footer-link">Features</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-white text-uppercase mb-3">Support</h6>
                            <ul class="list-unstyled footer-sub-menu">
                                <li><a href="#" class="footer-link">Developers</a></li>
                                <li><a href="#" class="footer-link">Support</a></li>
                                <li><a href="#" class="footer-link">Customer Service</a></li>
                                <li><a href="#" class="footer-link">Get Started</a></li>
                                <li><a href="#" class="footer-link">Guide</a></li>
                            </ul>
                        </div>
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
                            <script>document.write(new Date().getFullYear())</script> © Lezir. Design By Themesbrand
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

</body>

</html>