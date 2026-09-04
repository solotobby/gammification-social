<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Rolls · {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @livewireStyles
    @include('partials.google-ads-tag')

    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #000 !important;
            overflow: hidden !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 100vh;
            min-height: 100dvh;
            min-height: -webkit-fill-available;
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: 100%;
        }
    </style>
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
