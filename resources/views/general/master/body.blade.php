<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Payhankey | Creator Monetization Platform for Africa')</title>
<meta name="description" content="@yield('meta_description', 'Payhankey is an AI-powered creator monetization platform built for Africa. Create, grow, monetize content, build communities and earn through subscriptions and local payouts.')">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Payhankey">
<meta property="og:title" content="@yield('title', 'Payhankey | Creator Monetization Platform for Africa')">
<meta property="og:description" content="@yield('meta_description', 'Payhankey is an AI-powered creator monetization platform built for Africa. Create, grow, monetize content, build communities and earn through subscriptions and local payouts.')">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('title', 'Payhankey | Creator Monetization Platform for Africa')">
<meta name="twitter:description" content="@yield('meta_description', 'Payhankey is an AI-powered creator monetization platform built for Africa. Create, grow, monetize content, build communities and earn through subscriptions and local payouts.')">
<link rel="shortcut icon" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('rsc/styles.css') }}">
@stack('head')
</head>
<body class="has-site-nav @yield('body_class')" data-page="{{ url()->current() }}">

  @include('general.master.header')

  @yield('content')

  @include('general.master.footer')

<script src="{{ asset('rsc/app.js') }}"></script>
@stack('scripts')
</body>
</html>
