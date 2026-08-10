@extends('layouts.auth')

@section('title', 'Reset password · Payhankey')

@section('content')
    <a class="auth-scaffold__back" href="{{ url('/login') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Back to log in
    </a>

    <h1>Reset password</h1>
    <p class="auth-scaffold__lead">Choose a new password for your account.</p>

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

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus autocomplete="email" readonly>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="field__wrap">
                <input id="password" type="password" name="password" data-strength required autocomplete="new-password">
                <button class="field__toggle" type="button" aria-label="Show password">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <div class="pw-meter" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
            <div class="pw-label">Use 8+ characters with a number and symbol.</div>
            @error('password')
                <div class="field__hint" style="color:var(--rose)">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password-confirm">Confirm password</label>
            <div class="field__wrap">
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
                <button class="field__toggle" type="button" aria-label="Show password">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>

        <button class="btn btn--primary btn--block auth-scaffold__submit" type="submit">Reset password</button>
    </form>
@endsection
