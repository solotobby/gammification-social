<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Payhankey | Get Paid for the Posts You Already Make')</title>
<meta name="description" content="@yield('meta_description', 'Payhankey is the social platform that pays you for likes, comments and views — no followers needed. Free signup, welcome bonus, withdraw from $1.')">
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
