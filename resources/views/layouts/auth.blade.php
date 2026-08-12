<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Payhankey')</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="@yield('meta_description', 'Payhankey — monetize your posts, comments and views.')">
<link rel="shortcut icon" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('rsc/styles.css') }}">
<link rel="stylesheet" href="{{ asset('rsc/auth-scaffold.css') }}">
@stack('head')
</head>
<body class="auth-scaffold">
<div class="auth-scaffold__page">
    <a class="auth-scaffold__logo" href="{{ url('/') }}">
        <img src="{{ asset('logo.png') }}" alt="Payhankey">
    </a>
    <div class="auth-scaffold__card">
        @yield('content')
    </div>
    <p class="auth-scaffold__home">
        <a href="{{ url('/') }}">← Back to website</a>
    </p>
</div>
<script src="{{ asset('rsc/app.js') }}"></script>
<script>
(() => {
    const csrfUrl = @json(route('csrf.token'));
    const meta = document.querySelector('meta[name="csrf-token"]');

    async function refreshCsrfToken() {
        try {
            const res = await fetch(csrfUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store',
            });
            if (!res.ok) return;
            const data = await res.json();
            if (!data.token) return;

            if (meta) meta.setAttribute('content', data.token);
            document.querySelectorAll('input[name="_token"]').forEach((input) => {
                input.value = data.token;
            });
        } catch (_) {
            // Ignore network blips; submit will still get a friendly redirect if needed.
        }
    }

    // Keep tokens alive on idle auth pages and refresh when the tab is focused again.
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') refreshCsrfToken();
    });
    window.addEventListener('focus', refreshCsrfToken);
    setInterval(refreshCsrfToken, 10 * 60 * 1000);

    document.querySelectorAll('form[method="post"], form[method="POST"]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.csrfReady === '1') {
                form.dataset.csrfReady = '';
                return;
            }

            event.preventDefault();
            refreshCsrfToken().finally(() => {
                form.dataset.csrfReady = '1';
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    HTMLFormElement.prototype.submit.call(form);
                }
            });
        });
    });
})();
</script>
@stack('scripts')
</body>
</html>
