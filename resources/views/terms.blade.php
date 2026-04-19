<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Payhankey | Terms of Service</title>
    <meta name="description"
        content="Payhankey is a content monetization platform where creators earn from engagement without followers. Grow your content, earn payouts, and monetize in Africa.">
    <meta name="author" content="Payhankey">
    <meta name="robots"
        content="index, follow, money, post, posts, comments, comment, views, view, videos, video, content, monetize, monetization, payout, payouts, earn, earnings, africa, nigerian content monetization, social media, content creator, influencer, digital creator, online earnings, content engagement, payhankey platform, content growth, content promotion, content sharing, content discovery, content analytics, content performance, content strategy, content marketing, content tips, content trends, content ideas, content creation, content management, content optimization, content monetization strategies, content monetization tips, content monetization platforms in Africa, content monetization for creators, content monetization for influencers, content monetization for digital creators, content monetization for social media, content monetization for online earnings, content monetization for engagement, content monetization for growth, content monetization for promotion, content monetization for sharing, content monetization for discovery, content monetization for analytics, content monetization for performance, content monetization for strategy, content monetization for marketing, content monetization trends, content monetization ideas, content monetization tips and strategies">

    <!-- Open Graph Meta -->
    <meta property="og:title"
        content="Payhankey is a content monetization platform where creators earn from engagement without followers. Grow your content, earn payouts, and monetize in Africa.">
    <meta property="og:site_name" content="Payhankey">
    <meta property="og:description"
        content="Payhankey is a content monetization platform where creators earn from engagement without followers. Grow your content, earn payouts, and monetize in Africa.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://payhankey.com">
    <meta property="og:image" content="">
    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7484162262282358"
        crossorigin="anonymous"></script>
    <!-- css -->
    <link href="{{ asset('asset/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('asset/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('asset/css/style.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/brands.min.css"
        integrity="sha512-DJLNx+VLY4aEiEQFjiawXaiceujj5GA7lIY8CHCIGQCBPfsEG0nGz1edb4Jvw1LR7q031zS5PpPqFuPA8ihlRA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-E30RCECSBG"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-E30RCECSBG');
    </script>


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
            {{-- <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mx-auto navbar-center" id="navbar-navlist">
                    <li class="nav-item">
                        <a data-scroll href="{{ url('/') }}" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="#features" class="nav-link">Features</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="{{ url('#pricing') }}" class="nav-link">Access Code</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="{{ url('howtoearn') }}" class="nav-link">How to Earn</a>
                    </li>
                    <li class="nav-item">
                        <a data-scroll href="{{ url('privacy/policy') }}" class="nav-link">Privacy Policy</a>
                    </li>
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
            </div> --}}
        </div>
    </nav>
    <!-- Navbar End -->






    <section class="section feather-bg-img" style="background-image: url(asset/images/features-bg-img-1.png)">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12 align-self-center">
                    <p class="font-weight-medium text-uppercase mb-2"><i
                            class="mdi mdi-chart-bubble h2 text-primary me-1 align-middle"></i> Our Terms and Conditions</p>
                    {{-- <h3 class="font-weight-semibold line-height-1_4 mb-4">
                        Privacy Policy
                    </h3> --}}

                    <p class="text-muted font-size-15 mb-4">

                        1. Introduction <br>
                        Welcome to Payhankey, operated by Payhankey Ltd, a digital platform for content creation and
                        engagement. <br>
                        Payhankey Ltd operates as a subsidiary/affiliated platform under Freebyz Technologies Ltd.
                        By accessing or using this platform, you agree to be bound by these Terms and Conditions.
                        <br>

                        2. Eligibility<br>
                        By using Payhankey, you confirm that: <br>
                        You are at least 18 years old or have parental/guardian consent<br>
                        You provide accurate and complete information<br>
                        You will not create duplicate or misleading accounts<br>
                        3. User Accounts<br>
                        You are responsible for maintaining the confidentiality of your account<br>
                        All activities under your account remain your responsibility<br>
                        We reserve the right to suspend accounts that violate our policies<br>
                        4. Content Guidelines<br>
                        You agree to post content that:<br>
                        Is original or properly licensed<br>
                        Complies with applicable laws and regulations<br>
                        Does not contain harmful, misleading, or inappropriate material<br>
                        We reserve the right to review and remove content that violates these guidelines.<br>
                        5. Platform Use<br>
                        You agree NOT to:<br>
                        Use bots, scripts, or automated systems<br>
                        Engage in spam or deceptive practices<br>
                        Attempt to interfere with platform security or operations<br>
                        6. Engagement Integrity<br>
                        All user interactions must be genuine and organic.<br>
                        Artificial engagement, including the use of third-party services, is strictly prohibited.<br>
                        7. Rewards & Participation Disclaimer<br>
                        Payhankey may offer optional programs, campaigns, or rewards to users.<br>
                        However:<br>
                        Participation is voluntary<br>
                        Rewards are not guaranteed<br>
                        Programs are subject to internal policies, verification, and compliance checks<br>
                        Participation does not establish any employment, partnership, or financial obligation<br>
                        8. Intellectual Property<br>
                        You retain ownership of your content<br>
                        By posting, you grant Payhankey a non-exclusive right to use, display, and distribute your
                        content on the platform<br>
                        9. Privacy<br>
                        Your data is handled in accordance with our Privacy Policy.<br>
                        10. Account Suspension & Termination<br>
                        We reserve the right to suspend or terminate accounts that:<br>
                        Violate these Terms<br>
                        Engage in suspicious or harmful activity<br>
                        Compromise platform integrity<br>
                        11. Limitation of Liability<br>
                        Payhankey is provided “as is” without warranties of uninterrupted service or specific outcomes.<br>
                        12. Updates to Terms<br>
                        We may update these Terms at any time. Continued use of the platform indicates acceptance of any
                        updates.<br>
                        13. Contact<br>
                        📧 support@payhankey.com
                        <br>
                
                    </p>

                </div>
                {{-- <div class="col-lg-6 offset-lg-1 align-self-center">
                    <div class="mt-4 mt-lg-0">
                        <img src="{{asset('asset/images/money.png')}}" alt="" class="img-fluid d-block mx-auto">
                    </div>
                </div> --}}



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

    @include('layouts.rsc.footer')

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
    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>

</html>
