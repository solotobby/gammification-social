@extends('layouts.auth')

@section('title', 'Confirm password · Payhankey')

@section('content')
    <h1>Confirm password</h1>
    <p class="auth-scaffold__lead">This is a secure area of the application. Please confirm your password before continuing.</p>

    @if ($errors->any())
        <div class="alert alert--error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="field">
            <label for="password">Password</label>
            <div class="field__wrap">
                <input id="password" type="password" name="password" required autocomplete="current-password" autofocus>
                <button class="field__toggle" type="button" aria-label="Show password">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password')
                <div class="field__hint" style="color:var(--rose)">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn--primary btn--block auth-scaffold__submit" type="submit">Confirm</button>
    </form>
@endsection
