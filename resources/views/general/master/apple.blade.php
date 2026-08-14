@extends('general.master.body')

@section('body_class', 'page-landing-apple')

@push('head')
<link rel="stylesheet" href="{{ versioned_asset('rsc/landing-apple.css') }}">
@endpush

@section('content')
<div class="landing--apple">
@yield('apple_content')
</div>
@endsection
