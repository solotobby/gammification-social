@extends('layouts.auth')

@section('title', 'Verify email · Payhankey')

@section('content')
    <div class="auth-verify__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="M22 7l-10 7L2 7"/>
        </svg>
    </div>

    <h1>Verify your email</h1>
    <p class="auth-scaffold__lead">
        Thanks for joining Payhankey. We sent a verification link to
        @auth
            <strong class="auth-verify__email">{{ auth()->user()->email }}</strong>.
        @else
            your email address.
        @endauth
        Open it to activate your account and start posting.
    </p>

    @if (session('resent') || session('message'))
        <div class="alert alert--success">
            A fresh verification link has been sent. Check your inbox.
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert--error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="btn btn--primary btn--block auth-scaffold__submit" type="submit">
            Resend verification email
        </button>
    </form>

    <ul class="auth-verify__tips">
        <li>Links expire after 60 minutes — request a new one if needed.</li>
        <li>Check your spam or promotions folder if you don't see it.</li>
        <li>Add <strong>{{ config('mail.from.address', 'hello@payhankey.com') }}</strong> to your contacts.</li>
    </ul>

    @auth
        <form method="POST" action="{{ route('logout') }}" class="auth-verify__logout">
            @csrf
            <button type="submit">Log out and use a different account</button>
        </form>
    @endauth
@endsection
