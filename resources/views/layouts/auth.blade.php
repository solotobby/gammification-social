<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Payhankey')</title>
<meta name="description" content="@yield('meta_description', 'Payhankey — monetize your posts, comments and views.')">
<link rel="shortcut icon" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('rsc/styles.css') }}">
<link rel="stylesheet" href="{{ asset('rsc/auth-scaffold.css') }}">
@stack('head')
</head>
<body class="auth-scaffold">
<div class="auth-scaffold__page">
    <a class="auth-scaffold__logo" href="{{ url('/') }}">
        <img src="{{ asset('logo.png') }}" alt="Payhankey">
    </a>
    <div class="auth-scaffold__card">
        @yield('content')
    </div>
    <p class="auth-scaffold__home">
        <a href="{{ url('/') }}">← Back to website</a>
    </p>
</div>
<script src="{{ asset('rsc/app.js') }}"></script>
@stack('scripts')
</body>
</html>
