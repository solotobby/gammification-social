<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Sign Up</title>

    <meta name="description" content="Payhankey | Monetize your posts, comments and views to earn daily">
    <meta name="author" content="payhankey">
    <meta name="robots" content="index, follow">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="Payhankey | Monetize your posts, comments and views to earn daily">
    <meta property="og:site_name" content="payhankey">
    <meta property="og:description" content="Payhankey | Monetize your posts, comments and views to earn daily">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://payhankey.com">
    <meta property="og:image" content="">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.png') }}">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- Dashmix framework -->
    <link rel="stylesheet" id="css-main" href="{{ asset('src/assets/css/dashmix.css') }}">
    <!-- END Icons -->

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/xwork.min.css"> -->
    <!-- END Stylesheets -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10842521152"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'AW-10842521152');
    </script>



</head>

<body>

    <div id="page-container">

        <!-- Main Container -->
        <main id="main-container">
            <!-- Page Content -->

            <div class="bg-image" style="background-image: url('{{ asset('src/media/photos/photo14@2x.jpg') }}');">
                <div class="row g-0 justify-content-center bg-black-75">
                    <div class="hero-static col-sm-8 col-md-6 col-xl-4 d-flex align-items-center p-2 px-sm-0">
                        <!-- Sign Up Block -->
                        <div class="block block-transparent block-rounded w-100 mb-0 overflow-hidden">
                            <div
                                class="block-content block-content-full px-lg-5 px-xl-6 py-4 py-md-5 py-lg-6 bg-body-extra-light">
                                <!-- Header -->
                                <div class="mb-2 text-center">
                                    <a class="" href="{{ url('/') }}">
                                        <img src="{{ asset('logo.png') }}" alt="" class="logo-light"
                                            height="54" />
                                        {{-- <span class="text-dark">Dash</span><span class="text-primary">mix</span> --}}
                                    </a>
                                    <p class="text-uppercase fw-bold fs-sm text-muted mt-2">Create New Account</p>
                                </div>

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- END Header -->

                                <!-- Sign Up Form -->
                                <!-- jQuery Validation (.js-validation-signup class is initialized in js/pages/op_auth_signup.min.js which was auto compiled from _js/pages/op_auth_signup.js) -->
                                <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->

                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form method="POST" class="js-validation-signin" action="{{ route('reg.user') }}">
                                    @csrf
                                    <div class="py-3">
                                        <div class="mb-4">
                                            <input type="text"
                                                class="form-control @error('name') is-invalid @enderror form-control-lg form-control-alt"
                                                id="login-username" name="name" placeholder="Enter Full Name" required
                                                autocomplete="name" autofocus>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                         <div class="mb-4">
                                            <input type="text"
                                                class="form-control @error('username') is-invalid @enderror form-control-lg form-control-alt"
                                                id="login-username" name="username" placeholder="Enter your preferred username" required
                                                autocomplete="name" autofocus>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror form-control-lg form-control-alt"
                                                id="login-username" name="email" placeholder="Email address" required>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        {{-- <div class="mb-4">
                                            <input type="text"
                                                class="form-control @error('phone') is-invalid @enderror form-control-lg form-control-alt"
                                                id="login-username" name="phone" placeholder="Phone number" required>
                                            @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div> --}}
                                        <div class="mb-0">
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror form-control-lg form-control-alt"
                                                id="login-password" name="password" placeholder="Password" required>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>



                                    <div class="mb-4">
                                        <input type="text"
                                            class="form-control @error('referral_code') is-invalid @enderror form-control-lg form-control-alt"
                                            id="referral_code" name="referral_code" value="{{ @$ref }}"
                                            placeholder="Referral Code(optional)">
                                        @error('referral_code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <button type="submit" class="btn w-100 btn-lg btn-hero btn-primary">
                                            <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Sign Up
                                        </button>
                                        <p class="mt-3 mb-0 d-lg-flex justify-content-lg-between">
                                            Already Registered?
                                            {{-- <a class="btn btn-sm btn-alt-secondary d-block d-lg-inline-block mb-1" href=""> --}}

                                            {{-- <i class="fa fa-exclamation-triangle opacity-50 me-1"></i> Forgot password --}}
                                            {{-- </a> --}}
                                            <a class="btn btn-sm btn-alt-secondary d-block d-lg-inline-block mb-1"
                                                href="{{ url('login') }}">
                                                <i class="fa fa-sign-in-alt  opacity-50 me-1"></i> Sign In Here
                                            </a>
                                        </p>
                                    </div>
                                </form>

                                <!-- END Sign Up Form -->
                            </div>
                        </div>
                    </div>
                    <!-- END Sign Up Block -->
                </div>


            </div>
            <!-- END Page Content -->
        </main>
        <!-- END Main Container -->
    </div>
    <!-- END Page Container -->

    <!--
      Dashmix JS

      Core libraries and functionality
      webpack is putting everything together at assets/_js/main/app.js
    -->
    <script src="{{ asset('src/assets/js/dashmix.app.min.js') }}"></script>

    <!-- jQuery (required for jQuery Validation plugin) -->
    <script src="{{ asset('src/assets/js/lib/jquery.min.js') }}"></script>

    <!-- Page JS Plugins -->
    <script src="{{ asset('src/assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('src/assets/js/pages/op_auth_signin.min.js') }}"></script>
</body>

</html>
