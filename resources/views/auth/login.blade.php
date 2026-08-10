@extends('layouts.auth')

@section('title', 'Log in · Payhankey')

@section('content')
    <h1>Log in</h1>
    <p class="auth-scaffold__lead">Enter your email and password to access your account.</p>

    @if ($errors->any())
        <div class="alert alert--error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert--error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login.user') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <div class="field__wrap">
                <input id="password" type="password" name="password" required autocomplete="current-password">
                <button class="field__toggle" type="button" aria-label="Show password">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>
        <div class="auth-scaffold__actions">
            <label class="auth-scaffold__check" style="margin:0">
                <input type="checkbox" name="remember"> Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot your password?</a>
            @endif
        </div>
        <button class="btn btn--primary btn--block auth-scaffold__submit" type="submit">Log in</button>
    </form>

    <p class="auth-scaffold__foot">Don't have an account? <a href="{{ url('/register') }}">Register</a></p>
@endsection
