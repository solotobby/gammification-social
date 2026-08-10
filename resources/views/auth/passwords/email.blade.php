@extends('layouts.auth')

@section('title', 'Forgot password · Payhankey')

@section('content')
    <a class="auth-scaffold__back" href="{{ url('/login') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Back to log in
    </a>

    <h1>Forgot your password?</h1>
    <p class="auth-scaffold__lead">No problem. Enter your email and we'll send you a password reset link.</p>

    @if (session('status'))
        <div class="alert alert--success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert--error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            @error('email')
                <div class="field__hint" style="color:var(--rose)">{{ $message }}</div>
            @enderror
        </div>
        <button class="btn btn--primary btn--block auth-scaffold__submit" type="submit">Email password reset link</button>
    </form>
@endsection
