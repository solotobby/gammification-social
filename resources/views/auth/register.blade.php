@extends('layouts.auth')

@section('title', 'Register · Payhankey')

@section('content')
    <h1>Register</h1>
    <p class="auth-scaffold__lead">
        @if (session('community_join_intent'))
            Create your free account to continue to <strong>{{ session('community_join_intent') }}</strong>.
        @else
            Create your free account to start earning from your content.
        @endif
    </p>

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

    <form method="POST" action="{{ route('reg.user') }}">
        @csrf
        <div class="field">
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
        </div>
        <div class="field">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username">
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
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
        </div>
        <div class="field">
            <label for="referral_code">Referral code <span style="color:var(--ink-faint);font-weight:400">(optional)</span></label>
            <input id="referral_code" type="text" name="referral_code" value="{{ old('referral_code', $ref ?? '') }}">
        </div>
        <label class="auth-scaffold__check">
            <input type="checkbox" required>
            I agree to the <a href="{{ url('/terms/conditions') }}">Terms</a> and <a href="{{ url('/privacy/policy') }}">Privacy Policy</a>
        </label>
        <button class="btn btn--primary btn--block auth-scaffold__submit" type="submit">Register</button>
    </form>

    <p class="auth-scaffold__foot">Already registered? <a href="{{ url('/login') }}">Log in</a></p>
@endsection
