{{-- resources/views/layouts/rolls.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#000000">
    <title>Rolls · {{ config('app.name') }}</title>

    {{-- Match your existing app CSS exactly --}}
    <link rel="stylesheet" href="{{ asset('src/assets/css/oneui.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @livewireStyles

    <style>
        /* Hard-reset body for full-screen video — overrides anything from oneui.min.css */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #000 !important;
            overflow: hidden !important;
            height: 100% !important;
        }
    </style>
</head>
<body>

    {{ $slot }}

    @livewireScripts
</body>
</html>

