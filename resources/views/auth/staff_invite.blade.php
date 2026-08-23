@extends('layouts.auth')

@section('title', 'Accept staff invite · Payhankey')

@section('content')
    <h1>Join as staff</h1>
    <p class="auth-scaffold__lead">
        Create your password to activate staff access for
        <strong>{{ $invite->email }}</strong>.
    </p>

    @if ($errors->any())
        <div class="alert alert--error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ url('staff/invite/'.$invite->token) }}">
        @csrf
        <div class="field">
            <label for="name">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $invite->name) }}" required autofocus>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" value="{{ $invite->email }}" disabled>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
        </div>
        <button class="btn btn--primary btn--block auth-scaffold__submit" type="submit">Activate staff account</button>
    </form>
@endsection
