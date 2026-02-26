<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Payhankey | How Payhankey Works</title>
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
        </div>
    </nav>
    <!-- Navbar End -->






    <section class="section feather-bg-img" style="background-image: url(asset/images/features-bg-img-1.png)">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12 align-self-center">
                    <p class="font-weight-medium text-uppercase mb-2 mt-2">
                        <i class="mdi mdi-chart-bubble h2 text-primary me-1 align-middle"></i> List of Payhankey Top
                        Earners
                    </p>
                    {{-- <h3 class="font-weight-semibold line-height-1_4 mb-4">
                        Privacy Policy
                    </h3> --}}

                    <div class="accordion" id="monthlyTopEarnersAccordion">

                        @foreach ($topEarners as $monthKey => $earners)
                            @php
                                $collapseId = 'collapse' . str_replace('-', '', $monthKey);
                                $headingId = 'heading' . str_replace('-', '', $monthKey);
                                $isFirst = $loop->first;
                                $monthFormatted = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->format('F Y');
                            @endphp

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="{{ $headingId }}">
                                    <button class="accordion-button {{ !$isFirst ? 'collapsed' : '' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                        aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                        aria-controls="{{ $collapseId }}">

                                        <b>{{ $monthFormatted }} — Top 10 Paid Earners</b>
                                    </button>
                                </h2>

                                <div id="{{ $collapseId }}"
                                    class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                                    aria-labelledby="{{ $headingId }}" data-bs-parent="#monthlyTopEarnersAccordion">

                                    <div class="accordion-body">

                                        @if ($earners->count())
                                            <ul class="list-group list-group-flush">
                                                @foreach ($earners as $earner)
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="badge bg-primary me-2">
                                                                #{{ $earner->rank_position ?? $loop->iteration }}
                                                            </span>
                                                            @<span>{{ $earner->username }}</span>
                                                        </div>

                                                        
                                                            ₦{{ number_format($earner->total_paid, 2) }}
                                                       
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted mb-0">No earners found for this month.</p>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>


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


    <!-- Footer Start -->
    <section class="footer" style="background-image: url(images/footer-bg.png)">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-5">
                        <img src="images/logo-light.png" alt="" class="" height="24">
                        <p class="text-white-50 my-4 font-size-15">
                            Payhankey allows you to make short posts, facts, quizzes and teasers. Earn signup bonus and
                            referral bonuses when you refer your friends.
                        </p>
                        <ul class="list-inline footer-social-icon-content">
                            <li class="list-inline-item me-4">
                                <a href="https://www.tiktok.com/@payhankeyofficial?_t=8nKWNLIq65o&_r=1"
                                    class="footer-social-icon" target="_blank">
                                    <i class="fab fa-tiktok"></i>
                                </a>
                            </li>
                            <li class="list-inline-item me-4">
                                <a href="https://www.instagram.com/payhankey_official?utm_source=qr&igsh=M3ZzdjN3MHUxOXZk"
                                    class="footer-social-icon" target="_blank">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </li>
                            <li class="list-inline-item me-4">
                                <a href="https://www.facebook.com/profile.php?id=61561454191408&mibextid=ZbWKwL"
                                    class="footer-social-icon" target="_blank">
                                    <i class="fab fa-facebook"></i>
                                </a>
                            </li>
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
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © Payhankey.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->



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
