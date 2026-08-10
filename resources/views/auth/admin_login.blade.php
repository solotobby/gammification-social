<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure access · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #0F1117;
            color: #fff;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .gate-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            color: #0F1117;
            border-radius: 16px;
            padding: 32px 28px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .35);
        }

        .gate-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .gate-brand-mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #5A4FDC, #4338CA);
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            font-size: .9rem;
        }

        .gate-brand span {
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: -.02em;
        }

        .gate-card h1 {
            margin: 0 0 6px;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .gate-card p.lead {
            margin: 0 0 22px;
            font-size: .9rem;
            color: #64748B;
            line-height: 1.5;
        }

        .gate-expiry {
            font-size: .78rem;
            color: #94A3B8;
            margin-bottom: 20px;
            padding: 10px 12px;
            background: #F8FAFC;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
        }

        .field { margin-bottom: 16px; }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-size: .875rem;
            font-weight: 600;
        }

        .field input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            font-family: inherit;
            font-size: .95rem;
            background: #fff;
        }

        .field input:focus {
            outline: none;
            border-color: #5A4FDC;
            box-shadow: 0 0 0 3px rgba(90, 79, 220, .15);
        }

        .field-error {
            display: block;
            margin-top: 6px;
            font-size: .8rem;
            color: #B91C1C;
        }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: .875rem;
            margin-bottom: 18px;
        }

        .btn-submit {
            width: 100%;
            margin-top: 8px;
            padding: 13px 16px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #5A4FDC, #4338CA);
            color: #fff;
            font-family: inherit;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(90, 79, 220, .28);
        }

        .btn-submit:hover { opacity: .95; }

        .gate-foot {
            margin-top: 20px;
            font-size: .75rem;
            color: #94A3B8;
            text-align: center;
            line-height: 1.45;
        }
    </style>
</head>
<body>
    <div class="gate-card">
        <div class="gate-brand">
            <div class="gate-brand-mark">PK</div>
            <span>{{ config('app.name') }}</span>
        </div>

        <h1>Admin sign in</h1>
        <p class="lead">This link is single-use and bound to your current network session.</p>

        @if ($expiresAt ?? null)
            <div class="gate-expiry">
                Link expires {{ $expiresAt->timezone(config('app.timezone'))->format('M j, Y g:i A T') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('dinky.reg') }}" autocomplete="off" novalidate>
            @csrf
            <input type="hidden" name="gate_code" value="{{ old('gate_code', $gateCode) }}">

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    required autofocus autocomplete="username">
                @error('email')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Sign in to admin</button>
        </form>

        <p class="gate-foot">
            Unauthorized access is logged. If you did not request this page, close it immediately.
        </p>
    </div>
</body>
</html>
