<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $community->name }} — {{ config('app.name') }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($community->description), 160) }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $community->name }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($community->description), 160) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    @if ($community->image)
        <meta property="og:image" content="{{ Illuminate\Support\Facades\Storage::disk('spaces')->url($community->image) }}">
    @elseif ($community->banner)
        <meta property="og:image" content="{{ Illuminate\Support\Facades\Storage::disk('spaces')->url($community->banner) }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $community->name }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($community->description), 160) }}">
    @if ($community->image)
        <meta name="twitter:image" content="{{ Illuminate\Support\Facades\Storage::disk('spaces')->url($community->image) }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

@php
    $shareUrl = url()->current();
    $shareText = 'Check out ' . $community->name . ' on ' . config('app.name');
    $encodedUrl = urlencode($shareUrl);
    $encodedText = urlencode($shareText);
    $priceLabel = $community->type === 'paid' && $community->member_charge
        ? getCurrencyCode($community->currency) . number_format($community->member_charge, 2) . $community->price_suffix
        : null;
    $bannerUrl = $community->banner
        ? Illuminate\Support\Facades\Storage::disk('spaces')->url($community->banner)
        : null;
    $logoUrl = $community->image
        ? Illuminate\Support\Facades\Storage::disk('spaces')->url($community->image)
        : null;
@endphp

<div class="cp">
<style>
    .cp {
        --violet: #5A4FDC;
        --violet-deep: #4338CA;
        --violet-soft: rgba(90, 79, 220, .10);
        --violet-glow: rgba(90, 79, 220, .28);
        --mint: #1FAE64;
        --mint-soft: rgba(31, 174, 100, .12);
        --ink: #0F1117;
        --ink-2: #3D4254;
        --muted: #8B90A5;
        --line: rgba(15, 17, 23, .08);
        --surface: #FFFFFF;
        --surface-2: #F8F9FC;
        --radius: 16px;
        --radius-lg: 24px;
        --shadow-sm: 0 1px 2px rgba(15, 17, 23, .04);
        --shadow-md: 0 8px 32px rgba(15, 17, 23, .07), 0 2px 8px rgba(15, 17, 23, .04);
        --shadow-lg: 0 24px 64px rgba(15, 17, 23, .10), 0 4px 16px rgba(15, 17, 23, .05);
        --ease: cubic-bezier(.22, 1, .36, 1);
        font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;
        color: var(--ink);
        background: var(--surface-2);
        margin: 0;
        min-height: 100vh;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }

    .cp *, .cp *::before, .cp *::after { box-sizing: border-box }
    .cp a { color: inherit; text-decoration: none }

    /* ── Ambient background ── */
    .cp .cp-bg {
        position: fixed;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }
    .cp .cp-bg::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 55% at 15% -5%, rgba(90, 79, 220, .14), transparent 60%),
            radial-gradient(ellipse 55% 45% at 90% 10%, rgba(31, 174, 100, .10), transparent 55%),
            radial-gradient(ellipse 50% 40% at 50% 100%, rgba(90, 79, 220, .06), transparent 60%);
    }
    .cp .cp-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: .55;
        animation: cp-float 18s ease-in-out infinite;
    }
    .cp .cp-orb-1 {
        width: 420px; height: 420px;
        background: rgba(90, 79, 220, .18);
        top: -120px; left: -80px;
        animation-delay: 0s;
    }
    .cp .cp-orb-2 {
        width: 320px; height: 320px;
        background: rgba(31, 174, 100, .14);
        top: 40%; right: -100px;
        animation-delay: -6s;
    }
    .cp .cp-orb-3 {
        width: 260px; height: 260px;
        background: rgba(90, 79, 220, .10);
        bottom: 10%; left: 20%;
        animation-delay: -12s;
    }

    @keyframes cp-float {
        0%, 100% { transform: translate(0, 0) scale(1) }
        33% { transform: translate(24px, -18px) scale(1.04) }
        66% { transform: translate(-16px, 12px) scale(.97) }
    }

    /* ── Shell ── */
    .cp .cp-shell {
        position: relative;
        z-index: 1;
        max-width: 680px;
        margin: 0 auto;
        padding: clamp(20px, 5vw, 40px) 20px 120px;
    }

    /* ── Nav ── */
    .cp .cp-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: clamp(24px, 5vw, 36px);
        animation: cp-rise .7s var(--ease) both;
    }
    .cp .cp-logo-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: .9rem;
        letter-spacing: -.01em;
        color: var(--ink);
        transition: opacity .2s;
    }
    .cp .cp-logo-link:hover { opacity: .75 }
    .cp .cp-logo-mark {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--violet), var(--mint));
        display: grid;
        place-items: center;
        box-shadow: 0 4px 12px var(--violet-glow);
    }
    .cp .cp-logo-mark svg { width: 16px; height: 16px; color: #fff }

    .cp .cp-nav-badge {
        font-size: .72rem;
        font-weight: 600;
        color: var(--muted);
        background: var(--surface);
        border: 1px solid var(--line);
        padding: 6px 12px;
        border-radius: 999px;
        backdrop-filter: blur(8px);
    }

    /* ── Hero card ── */
    .cp .cp-hero {
        background: var(--surface);
        border-radius: var(--radius-lg);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: cp-rise .8s var(--ease) .1s both;
    }

    .cp .cp-banner {
        height: clamp(160px, 32vw, 220px);
        position: relative;
        background: linear-gradient(135deg, #120E2E 0%, #2A2270 45%, #0F3D2A 100%);
        background-size: cover;
        background-position: center;
    }
    .cp .cp-banner::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 40%, rgba(255,255,255,.95) 100%);
    }
    .cp .cp-banner.has-image::after {
        background: linear-gradient(to bottom, rgba(15,17,23,.15) 0%, rgba(255,255,255,.92) 100%);
    }

    .cp .cp-hero-inner {
        padding: 0 clamp(20px, 4vw, 32px) clamp(24px, 4vw, 32px);
        margin-top: -52px;
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .cp .cp-avatar {
        width: 96px;
        height: 96px;
        border-radius: 22px;
        margin: 0 auto;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 1.6rem;
        color: #fff;
        border: 4px solid var(--surface);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        animation: cp-scale-in .6s var(--ease) .25s both;
    }
    .cp .cp-avatar img { width: 100%; height: 100%; object-fit: cover }

    .cp .cp-title {
        margin: 18px 0 0;
        font-size: clamp(1.5rem, 4.5vw, 2rem);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.15;
        animation: cp-rise .7s var(--ease) .35s both;
    }

    .cp .cp-badges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 12px;
        animation: cp-rise .7s var(--ease) .42s both;
    }

    .cp .cp-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: .72rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 999px;
        letter-spacing: .01em;
    }
    .cp .cp-badge svg { width: 12px; height: 12px; flex-shrink: 0 }
    .cp .cp-badge-public { background: var(--violet-soft); color: var(--violet-deep) }
    .cp .cp-badge-private { background: #EEF0F5; color: var(--ink-2) }
    .cp .cp-badge-paid { background: var(--mint-soft); color: #0A7040 }
    .cp .cp-badge-approval { background: #FEF3DC; color: #92600A }
    .cp .cp-badge-cat {
        background: var(--surface-2);
        color: var(--muted);
        border: 1px solid var(--line);
    }

    .cp .cp-subtitle {
        margin: 10px 0 0;
        font-size: .88rem;
        color: var(--muted);
        font-weight: 500;
        animation: cp-rise .7s var(--ease) .48s both;
    }

    .cp .cp-desc {
        margin: 18px auto 0;
        max-width: 48ch;
        font-size: .95rem;
        color: var(--ink-2);
        line-height: 1.65;
        animation: cp-rise .7s var(--ease) .54s both;
    }

    /* ── Stats ── */
    .cp .cp-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1px;
        background: var(--line);
        border-radius: var(--radius);
        overflow: hidden;
        margin-top: 28px;
        animation: cp-rise .7s var(--ease) .6s both;
    }
    .cp .cp-stat {
        background: var(--surface-2);
        padding: 16px 12px;
        text-align: center;
    }
    .cp .cp-stat-val {
        display: block;
        font-family: 'DM Mono', ui-monospace, monospace;
        font-size: 1.15rem;
        font-weight: 500;
        letter-spacing: -.02em;
        color: var(--ink);
        line-height: 1.2;
    }
    .cp .cp-stat-lbl {
        display: block;
        font-size: .7rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-top: 4px;
    }

    /* ── CTA ── */
    .cp .cp-cta-wrap {
        margin-top: 24px;
        animation: cp-rise .7s var(--ease) .66s both;
    }
    .cp .cp-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 15px 24px;
        border-radius: 14px;
        font-family: inherit;
        font-size: .95rem;
        font-weight: 600;
        letter-spacing: -.01em;
        color: #fff;
        border: none;
        cursor: pointer;
        background: linear-gradient(135deg, var(--violet) 0%, var(--violet-deep) 100%);
        box-shadow: 0 4px 20px var(--violet-glow), inset 0 1px 0 rgba(255,255,255,.15);
        transition: transform .2s var(--ease), box-shadow .2s var(--ease);
        position: relative;
        overflow: hidden;
    }
    .cp .cp-cta::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.18) 50%, transparent 60%);
        transform: translateX(-100%);
        transition: transform .6s var(--ease);
    }
    .cp .cp-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px var(--violet-glow), inset 0 1px 0 rgba(255,255,255,.15);
    }
    .cp .cp-cta:hover::before { transform: translateX(100%) }
    .cp .cp-cta svg { width: 18px; height: 18px; flex-shrink: 0 }

    .cp .cp-cta-note {
        margin-top: 10px;
        font-size: .78rem;
        color: var(--muted);
        text-align: center;
    }

    /* ── Section cards ── */
    .cp .cp-section {
        margin-top: 16px;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: clamp(20px, 4vw, 24px);
        box-shadow: var(--shadow-sm);
        opacity: 0;
        transform: translateY(20px);
        transition: opacity .6s var(--ease), transform .6s var(--ease);
    }
    .cp .cp-section.cp-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .cp .cp-section-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .cp .cp-section-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--violet-soft);
        color: var(--violet);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .cp .cp-section-icon svg { width: 18px; height: 18px }
    .cp .cp-section-icon.cp-icon-share { background: var(--mint-soft); color: var(--mint) }

    .cp .cp-section-title {
        margin: 0;
        font-size: .95rem;
        font-weight: 700;
        letter-spacing: -.02em;
    }
    .cp .cp-section-sub {
        margin: 1px 0 0;
        font-size: .78rem;
        color: var(--muted);
    }

    /* ── About rows ── */
    .cp .cp-rows { display: grid; gap: 0 }
    .cp .cp-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 11px 0;
        border-top: 1px solid var(--line);
        font-size: .86rem;
    }
    .cp .cp-row:first-child { border-top: none; padding-top: 0 }
    .cp .cp-row-lbl { color: var(--muted); font-weight: 500 }
    .cp .cp-row-val { font-weight: 600; text-align: right }

    /* ── Share ── */
    .cp .cp-share-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .cp .cp-share {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        color: #fff;
        transition: transform .2s var(--ease), box-shadow .2s;
        box-shadow: var(--shadow-sm);
    }
    .cp .cp-share:hover {
        transform: translateY(-3px) scale(1.04);
        box-shadow: var(--shadow-md);
    }
    .cp .cp-share svg { width: 20px; height: 20px }
    .cp .cp-share-wa { background: #25D366 }
    .cp .cp-share-x { background: #111318 }
    .cp .cp-share-fb { background: #1877F2 }
    .cp .cp-share-li { background: #0A66C2 }
    .cp .cp-share-tg { background: #229ED9 }

    .cp .cp-copy {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        padding: 12px 14px;
        background: var(--surface-2);
        border: 1px solid var(--line);
        border-radius: 12px;
        font-size: .82rem;
        color: var(--ink-2);
    }
    .cp .cp-copy span {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-family: 'DM Mono', ui-monospace, monospace;
        font-size: .78rem;
    }
    .cp .cp-copy-btn {
        flex-shrink: 0;
        border: none;
        background: var(--violet-soft);
        color: var(--violet-deep);
        font-family: inherit;
        font-size: .76rem;
        font-weight: 600;
        padding: 7px 14px;
        border-radius: 8px;
        cursor: pointer;
        transition: background .2s, transform .15s;
    }
    .cp .cp-copy-btn:hover { background: rgba(90, 79, 220, .18) }
    .cp .cp-copy-btn.cp-copied {
        background: var(--mint-soft);
        color: #0A7040;
    }

    /* ── Footer ── */
    .cp .cp-footer {
        text-align: center;
        margin-top: 32px;
        font-size: .78rem;
        color: var(--muted);
        opacity: 0;
        animation: cp-rise .7s var(--ease) .9s both;
    }
    .cp .cp-footer a {
        color: var(--violet);
        font-weight: 600;
        transition: opacity .2s;
    }
    .cp .cp-footer a:hover { opacity: .75 }

    /* ── Sticky mobile CTA ── */
    .cp .cp-sticky {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 50;
        padding: 14px 20px calc(14px + env(safe-area-inset-bottom));
        background: rgba(255, 255, 255, .88);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid var(--line);
        transform: translateY(100%);
        transition: transform .4s var(--ease);
    }
    .cp .cp-sticky.cp-sticky-show { transform: translateY(0) }

    @media (max-width: 560px) {
        .cp .cp-sticky { display: block }
        .cp .cp-shell { padding-bottom: 100px }
        .cp .cp-avatar { width: 80px; height: 80px; font-size: 1.3rem }
        .cp .cp-stats { grid-template-columns: 1fr 1fr 1fr }
        .cp .cp-stat { padding: 14px 8px }
        .cp .cp-stat-val { font-size: 1rem }
    }

    /* ── Animations ── */
    @keyframes cp-rise {
        from { opacity: 0; transform: translateY(16px) }
        to { opacity: 1; transform: translateY(0) }
    }
    @keyframes cp-scale-in {
        from { opacity: 0; transform: scale(.85) }
        to { opacity: 1; transform: scale(1) }
    }

    @media (prefers-reduced-motion: reduce) {
        .cp .cp-orb { animation: none }
        .cp .cp-nav, .cp .cp-hero, .cp .cp-title, .cp .cp-badges,
        .cp .cp-subtitle, .cp .cp-desc, .cp .cp-stats, .cp .cp-cta-wrap,
        .cp .cp-avatar, .cp .cp-footer { animation: none; opacity: 1; transform: none }
        .cp .cp-section { opacity: 1; transform: none; transition: none }
        .cp .cp-cta::before { display: none }
        .cp .cp-share:hover, .cp .cp-cta:hover { transform: none }
    }
</style>

{{-- Ambient background --}}
<div class="cp-bg" aria-hidden="true">
    <div class="cp-orb cp-orb-1"></div>
    <div class="cp-orb cp-orb-2"></div>
    <div class="cp-orb cp-orb-3"></div>
</div>

<div class="cp-shell">

    <nav class="cp-nav">
        <a href="{{ url('/') }}" class="cp-logo-link">
            <span class="cp-logo-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M13.5 2.5c1 3-1 4.5-2 6-1.3 2-1.5 3.5-.5 5a3 3 0 0 0 5.7-1.2c.9.9 1.3 2 1.3 3.2a6 6 0 0 1-12 0c0-4 2.6-6 3.3-8.4.4-1.4.2-3-.8-4.6Z"/>
                </svg>
            </span>
            {{ config('app.name') }}
        </a>
        <span class="cp-nav-badge">Community</span>
    </nav>

    {{-- Hero --}}
    <article class="cp-hero">
        <div class="cp-banner @if($bannerUrl) has-image @endif"
            @if($bannerUrl) style="background-image:url('{{ $bannerUrl }}')" @endif></div>

        <div class="cp-hero-inner">
            <div class="cp-avatar" style="background:{{ $community->color }}">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $community->name }}">
                @else
                    {{ $community->initials }}
                @endif
            </div>

            <h1 class="cp-title">{{ $community->name }}</h1>

            <div class="cp-badges">
                @switch($community->type)
                    @case('public')
                        <span class="cp-badge cp-badge-public">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18"/></svg>
                            Public
                        </span>
                    @break
                    @case('private')
                        <span class="cp-badge cp-badge-private">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            Invite only
                        </span>
                    @break
                    @case('paid')
                        <span class="cp-badge cp-badge-paid">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round"/></svg>
                            {{ $priceLabel ?? 'Paid' }}
                        </span>
                    @break
                    @case('approval')
                        <span class="cp-badge cp-badge-approval">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            Approval required
                        </span>
                    @break
                @endswitch
                <span class="cp-badge cp-badge-cat">{{ $community->category->name ?? 'Uncategorised' }}</span>
            </div>

            <p class="cp-subtitle">Led by {{ $community->user->name ?? 'Unknown' }}</p>

            @if ($community->description)
                <p class="cp-desc">{{ $community->description }}</p>
            @endif

            <div class="cp-stats">
                <div class="cp-stat">
                    <span class="cp-stat-val cp-count" data-target="{{ $community->members_count }}">0</span>
                    <span class="cp-stat-lbl">Members</span>
                </div>
                <div class="cp-stat">
                    <span class="cp-stat-val cp-count" data-target="{{ $community->posts_count ?? 0 }}">0</span>
                    <span class="cp-stat-lbl">Posts</span>
                </div>
                <div class="cp-stat">
                    <span class="cp-stat-val">{{ $community->created_at->format('M Y') }}</span>
                    <span class="cp-stat-lbl">Founded</span>
                </div>
            </div>

            <div class="cp-cta-wrap">
                <a href="{{ route('community.show', $community) }}" class="cp-cta">
                    @switch($community->type)
                        @case('public')
                            Join this community
                        @break
                        @case('paid')
                            View &amp; subscribe
                        @break
                        @case('approval')
                            Request to join
                        @break
                        @default
                            Open in {{ config('app.name') }}
                    @endswitch
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <p class="cp-cta-note">
                    @guest
                        Free {{ config('app.name') }} account required
                    @else
                        Continue in the app to participate
                    @endguest
                </p>
            </div>
        </div>
    </article>

    {{-- About --}}
    <section class="cp-section cp-reveal">
        <div class="cp-section-head">
            <div class="cp-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 10v6M12 7h.01"/></svg>
            </div>
            <div>
                <h2 class="cp-section-title">About</h2>
                <p class="cp-section-sub">Community details</p>
            </div>
        </div>
        <div class="cp-rows">
            <div class="cp-row"><span class="cp-row-lbl">Type</span><span class="cp-row-val">{{ ucfirst($community->type) }}</span></div>
            <div class="cp-row"><span class="cp-row-lbl">Category</span><span class="cp-row-val">{{ $community->category->name ?? '—' }}</span></div>
            @if ($priceLabel)
                <div class="cp-row"><span class="cp-row-lbl">Price</span><span class="cp-row-val">{{ $priceLabel }}</span></div>
            @endif
            @if ($community->billing_label)
                <div class="cp-row"><span class="cp-row-lbl">Billing</span><span class="cp-row-val">{{ $community->billing_label }}</span></div>
            @endif
            <div class="cp-row"><span class="cp-row-lbl">Created</span><span class="cp-row-val">{{ $community->created_at->format('F j, Y') }}</span></div>
        </div>
    </section>

    {{-- Share --}}
    <section class="cp-section cp-reveal">
        <div class="cp-section-head">
            <div class="cp-section-icon cp-icon-share">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 3.9M15.4 6.6l-6.8 3.9"/></svg>
            </div>
            <div>
                <h2 class="cp-section-title">Share</h2>
                <p class="cp-section-sub">Spread the word</p>
            </div>
        </div>
        <div class="cp-share-grid">
            <a class="cp-share cp-share-wa" target="_blank" rel="noopener"
                href="https://wa.me/?text={{ $encodedText }}%20{{ $encodedUrl }}" aria-label="WhatsApp">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.28-1.39a9.9 9.9 0 0 0 4.76 1.21h.01c5.46 0 9.9-4.45 9.9-9.91C22 6.45 17.5 2 12.04 2Z"/></svg>
            </a>
            <a class="cp-share cp-share-x" target="_blank" rel="noopener"
                href="https://twitter.com/intent/tweet?text={{ $encodedText }}&url={{ $encodedUrl }}" aria-label="X">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-7.2 8.2L23.3 22H16.6l-5.2-6.8L5.4 22H2.3l7.7-8.8L1 2h6.9l4.7 6.2L18.9 2Z"/></svg>
            </a>
            <a class="cp-share cp-share-fb" target="_blank" rel="noopener"
                href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" aria-label="Facebook">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7.5H16l.4-3H13.5V8.4c0-.87.24-1.46 1.5-1.46H16.5V4.3c-.26-.03-1.14-.1-2.16-.1-2.14 0-3.6 1.3-3.6 3.7v2.6H8.5v3h2.24V21h2.76Z"/></svg>
            </a>
            <a class="cp-share cp-share-li" target="_blank" rel="noopener"
                href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" aria-label="LinkedIn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3.5A1.96 1.96 0 1 0 5.27 7.42 1.96 1.96 0 0 0 5.25 3.5ZM20.45 20h-3.37v-5.98c0-1.43-.03-3.26-1.99-3.26-2 0-2.3 1.56-2.3 3.16V20H9.42V8.5h3.24v1.57h.05c.45-.86 1.56-1.77 3.2-1.77 3.43 0 4.06 2.26 4.06 5.19V20Z"/></svg>
            </a>
            <a class="cp-share cp-share-tg" target="_blank" rel="noopener"
                href="https://t.me/share/url?url={{ $encodedUrl }}&text={{ $encodedText }}" aria-label="Telegram">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="m21.9 4.3-3 15c-.2.9-.8 1.1-1.6.7l-4.5-3.3-2.2 2.1c-.2.2-.4.4-.8.4l.3-4.3 7.9-7.1c.3-.3-.1-.5-.5-.2l-9.7 6.1-4.2-1.3c-.9-.3-.9-.9.2-1.3L20.6 3.4c.8-.3 1.5.2 1.3.9Z"/></svg>
            </a>
        </div>
        <div class="cp-copy">
            <span id="cp-share-url">{{ $shareUrl }}</span>
            <button type="button" class="cp-copy-btn" onclick="cpCopyLink(this)">Copy link</button>
        </div>
    </section>

    <footer class="cp-footer">
        Powered by <a href="{{ url('/') }}">{{ config('app.name') }}</a>
    </footer>
</div>

{{-- Mobile sticky CTA --}}
<div class="cp-sticky" id="cp-sticky">
    <a href="{{ route('community.show', $community) }}" class="cp-cta">
        @switch($community->type)
            @case('public') Join now @break
            @case('paid') Subscribe @break
            @case('approval') Request access @break
            @default Open community
        @endswitch
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
</div>

<script>
(function () {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* Count-up animation */
    function animateCount(el) {
        const target = parseInt(el.dataset.target, 10) || 0;
        if (reduced || target === 0) { el.textContent = target.toLocaleString(); return; }
        const duration = 900;
        const start = performance.now();
        function tick(now) {
            const p = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * eased).toLocaleString();
            if (p < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }

    const counters = document.querySelectorAll('.cp-count');
    if (reduced) {
        counters.forEach(el => { el.textContent = (parseInt(el.dataset.target, 10) || 0).toLocaleString(); });
    } else {
        const counterObs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) { animateCount(e.target); counterObs.unobserve(e.target); }
            });
        }, { threshold: 0.5 });
        counters.forEach(el => counterObs.observe(el));
    }

    /* Scroll reveal for sections */
    const sections = document.querySelectorAll('.cp-reveal');
    if (reduced) {
        sections.forEach(s => s.classList.add('cp-visible'));
    } else {
        const revealObs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('cp-visible'); revealObs.unobserve(e.target); }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        sections.forEach(s => revealObs.observe(s));
    }

    /* Sticky bar: show when hero CTA scrolls out of view */
    const sticky = document.getElementById('cp-sticky');
    const cta = document.querySelector('.cp-cta-wrap');
    if (sticky && cta && window.innerWidth <= 560) {
        const stickyObs = new IntersectionObserver(([e]) => {
            sticky.classList.toggle('cp-sticky-show', !e.isIntersecting);
        }, { threshold: 0 });
        stickyObs.observe(cta);
    }
})();

function cpCopyLink(btn) {
    const url = document.getElementById('cp-share-url').textContent;
    navigator.clipboard.writeText(url).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Copied!';
        btn.classList.add('cp-copied');
        setTimeout(() => { btn.textContent = orig; btn.classList.remove('cp-copied'); }, 1800);
    });
}
</script>
</div>

</body>
</html>
