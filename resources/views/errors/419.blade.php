@extends('layouts.auth')

@section('title', 'Session expired · Payhankey')

@section('content')
    <h1>Session expired</h1>
    <p class="auth-scaffold__lead">
        Your login session timed out for security. Refresh the page and try again.
    </p>
    <a class="btn btn--primary btn--block auth-scaffold__submit" href="{{ url('/login') }}">
        Back to login
    </a>
@endsection
