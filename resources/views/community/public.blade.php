{{-- resources/views/public/community-show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $community->name }} — {{ config('app.name') }}</title>

    {{-- ---- social share meta ---- --}}
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Mono&display=swap" rel="stylesheet">
</head>
<body>

<div class="pk-public-page">
    <style>
        .pk-public-page {
            --pk-violet: #5A4FDC;
            --pk-violet-dark: #4B41C4;
            --pk-violet-tint: #EEECFC;
            --pk-mint: #1FAE64;
            --pk-mint-tint: #E6F7EE;
            --pk-mint-line: #CBEBDA;
            --pk-gold: #E3A421;
            --pk-red: #EF4444;
            --pk-ink: #171B24;
            --pk-gray-700: #4B5163;
            --pk-gray-500: #8A8FA3;
            --pk-gray-400: #AEB2C2;
            --pk-line: #E7E8F0;
            --pk-line-strong: #DADCE9;
            --pk-r-sm: 8px;
            --pk-r-md: 12px;
            --pk-r-lg: 14px;
            --pk-r-pill: 999px;
            --pk-shadow: 0 1px 2px rgba(23, 27, 36, .04);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--pk-ink);
            background: #F7F7FB;
            margin: 0;
            min-height: 100vh;
        }

        .pk-public-page * { box-sizing: border-box }

        .pk-public-page .pk-wrap {
            max-width: 620px;
            margin: 0 auto;
            padding: 32px 16px 60px
        }

        .pk-public-page .pk-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            font-size: .92rem;
            color: var(--pk-violet);
            text-decoration: none;
            margin-bottom: 20px
        }

        .pk-public-page .pk-brand svg { width: 20px; height: 20px }

        .pk-public-page .pk-card {
            background: #fff;
            border: 1px solid var(--pk-line);
            border-radius: var(--pk-r-lg);
            box-shadow: var(--pk-shadow);
            overflow: hidden
        }

        /* ---- hero ---- */
        .pk-public-page .pk-hero-banner {
            height: 170px;
            position: relative;
            background: linear-gradient(120deg, #15103A, #5A4FDC 55%, #1FAE64 140%);
            background-size: cover;
            background-position: center
        }

        .pk-public-page .pk-hero-banner::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, .14) 1.5px, transparent 1.5px);
            background-size: 16px 16px;
            opacity: .4
        }

        .pk-public-page .pk-hero-body { padding: 0 24px 24px; text-align: center }

        .pk-public-page .pk-hero-logo {
            width: 92px;
            height: 92px;
            border-radius: 20px;
            margin: -40px auto 0;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            font-size: 1.5rem;
            border: 5px solid #fff;
            box-shadow: 0 10px 22px -8px rgba(23, 27, 36, .28);
            overflow: hidden
        }

        .pk-public-page .pk-hero-logo img { width: 100%; height: 100%; object-fit: cover }

        .pk-public-page .pk-hero-name {
            font-size: 1.35rem;
            font-weight: 800;
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap
        }

        .pk-public-page .pk-hero-meta {
            font-size: .84rem;
            color: var(--pk-gray-500);
            margin-top: 4px
        }

        .pk-public-page .pk-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .7rem;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: var(--pk-r-pill);
            white-space: nowrap
        }

        .pk-public-page .pk-status-pill svg { width: 11px; height: 11px }
        .pk-public-page .pk-status-public { background: var(--pk-violet-tint); color: var(--pk-violet) }
        .pk-public-page .pk-status-private { background: #EEF0F4; color: var(--pk-gray-700) }
        .pk-public-page .pk-status-paid { background: var(--pk-mint-tint); color: #0D7A45 }
        .pk-public-page .pk-status-approval { background: #FCF1DA; color: #946409 }

        .pk-public-page .pk-hero-desc {
            font-size: .9rem;
            color: var(--pk-gray-700);
            line-height: 1.6;
            margin: 16px auto 0;
            max-width: 46ch
        }

        .pk-public-page .pk-stat-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 22px;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--pk-line)
        }

        .pk-public-page .pk-stat b {
            display: block;
            font-size: 1.1rem;
            font-weight: 800;
            font-family: 'Space Mono', ui-monospace, monospace
        }

        .pk-public-page .pk-stat span {
            font-size: .74rem;
            color: var(--pk-gray-500);
            font-weight: 600
        }

        .pk-public-page .pk-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 20px;
            border-radius: var(--pk-r-md);
            font-weight: 700;
            font-size: .92rem;
            border: none;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            width: 100%
        }

        .pk-public-page .pk-btn svg { width: 16px; height: 16px }
        .pk-public-page .pk-btn-violet { background: var(--pk-violet); color: #fff; margin-top: 22px }
        .pk-public-page .pk-btn-violet:hover { background: var(--pk-violet-dark) }

        .pk-public-page .pk-cta-sub {
            font-size: .78rem;
            color: var(--pk-gray-500);
            margin-top: 8px
        }

        /* ---- about card ---- */
        .pk-public-page .pk-about-card { padding: 18px 20px; margin-top: 14px }
        .pk-public-page .pk-about-card h3 { font-size: .9rem; font-weight: 800; margin: 0 0 10px }
        .pk-public-page .pk-about-row {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            border-top: 1px solid var(--pk-line);
            font-size: .84rem
        }
        .pk-public-page .pk-about-row:first-of-type { border-top: none }
        .pk-public-page .pk-about-row .pk-lbl { color: var(--pk-gray-500); font-weight: 600 }

        /* ---- share ---- */
        .pk-public-page .pk-share-card { padding: 18px 20px; margin-top: 14px }
        .pk-public-page .pk-share-card h3 { font-size: .9rem; font-weight: 800; margin: 0 0 12px }

        .pk-public-page .pk-share-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap
        }

        .pk-public-page .pk-share-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #fff;
            text-decoration: none;
            flex: none;
            transition: transform .15s
        }

        .pk-public-page .pk-share-btn:hover { transform: translateY(-2px) }
        .pk-public-page .pk-share-btn svg { width: 18px; height: 18px }

        .pk-public-page .pk-share-whatsapp { background: #25D366 }
        .pk-public-page .pk-share-x { background: #171B24 }
        .pk-public-page .pk-share-facebook { background: #1877F2 }
        .pk-public-page .pk-share-linkedin { background: #0A66C2 }
        .pk-public-page .pk-share-telegram { background: #229ED9 }

        .pk-public-page .pk-copy-input {
            display: flex;
            align-items: center;
            gap: 9px;
            background: #F7F7FB;
            border: 1.3px solid var(--pk-line-strong);
            border-radius: var(--pk-r-sm);
            padding: 10px 12px;
            color: var(--pk-gray-700);
            font-size: .82rem;
            margin-top: 12px
        }

        .pk-public-page .pk-copy-input svg { width: 15px; height: 15px; flex: none; color: var(--pk-gray-500) }
        .pk-public-page .pk-copy-input span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1
        }

        .pk-public-page .pk-copy-btn {
            flex: none;
            background: none;
            border: none;
            color: var(--pk-violet);
            font-weight: 700;
            font-size: .78rem;
            cursor: pointer;
            font-family: inherit
        }

        .pk-public-page .pk-footer {
            text-align: center;
            font-size: .78rem;
            color: var(--pk-gray-400);
            margin-top: 24px
        }

        .pk-public-page .pk-footer a { color: var(--pk-violet); text-decoration: none; font-weight: 700 }

        @media (max-width: 480px) {
            .pk-public-page .pk-hero-logo { width: 76px; height: 76px }
        }
    </style>

    <div class="pk-wrap">

        <a href="{{ url('/') }}" class="pk-brand">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                <path d="M13.5 2.5c1 3-1 4.5-2 6-1.3 2-1.5 3.5-.5 5a3 3 0 0 0 5.7-1.2c.9.9 1.3 2 1.3 3.2a6 6 0 0 1-12 0c0-4 2.6-6 3.3-8.4.4-1.4.2-3-.8-4.6Z" />
            </svg>
            {{ config('app.name') }}
        </a>

        <div class="pk-card">
            <div class="pk-hero-banner"
                @if ($community->banner) style="background-image:url('{{ Illuminate\Support\Facades\Storage::disk('spaces')->url($community->banner) }}')" @endif>
            </div>

            <div class="pk-hero-body">
                <div class="pk-hero-logo" style="background:{{ $community->color }}">
                    @if ($community->image)
                        <img src="{{ Illuminate\Support\Facades\Storage::disk('spaces')->url($community->image) }}" alt="{{ $community->name }}">
                    @else
                        {{ $community->initials }}
                    @endif
                </div>

                <div class="pk-hero-name">
                    {{ $community->name }}

                    @switch($community->type)
                        @case('public')
                            <span class="pk-status-pill pk-status-public">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z" />
                                </svg>
                                Public
                            </span>
                        @break

                        @case('private')
                            <span class="pk-status-pill pk-status-private">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                    <rect x="5" y="11" width="14" height="9" rx="2" />
                                    <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                </svg>
                                Invite only
                            </span>
                        @break

                        @case('paid')
                            <span class="pk-status-pill pk-status-paid">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                    <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round" />
                                </svg>
                                {{ $community->currency === 'NGN' ? '₦' : $community->currency.' ' }}{{ number_format($community->member_charge, 2) }}{{ $community->price_suffix }}
                            </span>
                        @break

                        @case('approval')
                            <span class="pk-status-pill pk-status-approval">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M12 7v5l3 2" />
                                </svg>
                                Approval required
                            </span>
                        @break
                    @endswitch
                </div>

                <div class="pk-hero-meta">
                    {{ $community->category->name ?? 'Uncategorised' }} · Admin: {{ $community->user->name ?? 'Unknown' }}
                </div>

                @if ($community->description)
                    <p class="pk-hero-desc">{{ $community->description }}</p>
                @endif

                <div class="pk-stat-row">
                    <div class="pk-stat">
                        <b>{{ number_format($community->members_count) }}</b>
                        <span>Member{{ $community->members_count === 1 ? '' : 's' }}</span>
                    </div>
                    <div class="pk-stat">
                        <b>{{ $community->created_at->format('M Y') }}</b>
                        <span>Founded</span>
                    </div>
                </div>

                <a href="{{ route('community.show', $community) }}" class="pk-btn pk-btn-violet">
                    @switch($community->type)
                        @case('public') Join this community @break
                        @case('paid') View subscription details @break
                        @case('approval') Request to join @break
                        @default View community
                    @endswitch
                </a>
                <div class="pk-cta-sub">
                    @if (auth()->guest())
                        You'll need a free {{ config('app.name') }} account to continue.
                    @else
                        Opens in {{ config('app.name') }}.
                    @endif
                </div>
            </div>
        </div>

        <div class="pk-card pk-about-card">
            <h3>About this community</h3>
            <div class="pk-about-row"><span class="pk-lbl">Category</span><span>{{ $community->category->name ?? 'Uncategorised' }}</span></div>
            <div class="pk-about-row"><span class="pk-lbl">Type</span><span>{{ ucfirst($community->type) }}</span></div>
            <div class="pk-about-row"><span class="pk-lbl">Members</span><span>{{ number_format($community->members_count) }}</span></div>
            <div class="pk-about-row"><span class="pk-lbl">Created</span><span>{{ $community->created_at->format('F Y') }}</span></div>
        </div>

        <div class="pk-card pk-share-card">
            <h3>Share this community</h3>
            <div class="pk-share-row">
                @php
                    $shareUrl = urlencode(url()->current());
                    $shareText = urlencode('Check out '.$community->name.' on '.config('app.name'));
                @endphp

                <a class="pk-share-btn pk-share-whatsapp" target="_blank" rel="noopener"
                    href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" aria-label="Share on WhatsApp">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.28-1.39a9.9 9.9 0 0 0 4.76 1.21h.01c5.46 0 9.9-4.45 9.9-9.91C22 6.45 17.5 2 12.04 2Zm5.8 14.05c-.24.68-1.4 1.3-1.93 1.38-.5.08-1.12.11-1.8-.11-.42-.13-.96-.31-1.65-.6-2.9-1.25-4.8-4.17-4.94-4.36-.14-.2-1.18-1.57-1.18-3 0-1.42.75-2.12 1.02-2.41.26-.28.57-.35.76-.35.19 0 .38 0 .55.01.18.01.41-.07.64.49.24.57.81 1.98.88 2.12.07.14.12.31.02.5-.09.19-.14.31-.28.48-.14.16-.29.36-.42.49-.14.14-.28.29-.12.57.16.28.72 1.19 1.55 1.93 1.06.95 1.96 1.24 2.24 1.38.28.14.44.12.6-.07.16-.19.68-.79.87-1.06.19-.28.37-.23.62-.14.26.09 1.63.77 1.91.91.28.14.47.21.53.33.07.12.07.68-.17 1.36Z"/></svg>
                </a>

                <a class="pk-share-btn pk-share-x" target="_blank" rel="noopener"
                    href="https://twitter.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}" aria-label="Share on X">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-7.2 8.2L23.3 22H16.6l-5.2-6.8L5.4 22H2.3l7.7-8.8L1 2h6.9l4.7 6.2L18.9 2Zm-1.2 18h1.7L7.4 4h-1.8l12.1 16Z"/></svg>
                </a>

                <a class="pk-share-btn pk-share-facebook" target="_blank" rel="noopener"
                    href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" aria-label="Share on Facebook">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7.5H16l.4-3H13.5V8.4c0-.87.24-1.46 1.5-1.46H16.5V4.3c-.26-.03-1.14-.1-2.16-.1-2.14 0-3.6 1.3-3.6 3.7v2.6H8.5v3h2.24V21h2.76Z"/></svg>
                </a>

                <a class="pk-share-btn pk-share-linkedin" target="_blank" rel="noopener"
                    href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" aria-label="Share on LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3.5A1.96 1.96 0 1 0 5.27 7.42 1.96 1.96 0 0 0 5.25 3.5ZM20.45 20h-3.37v-5.98c0-1.43-.03-3.26-1.99-3.26-2 0-2.3 1.56-2.3 3.16V20H9.42V8.5h3.24v1.57h.05c.45-.86 1.56-1.77 3.2-1.77 3.43 0 4.06 2.26 4.06 5.19V20Z"/></svg>
                </a>

                <a class="pk-share-btn pk-share-telegram" target="_blank" rel="noopener"
                    href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}" aria-label="Share on Telegram">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="m21.9 4.3-3 15c-.2.9-.8 1.1-1.6.7l-4.5-3.3-2.2 2.1c-.2.2-.4.4-.8.4l.3-4.3 7.9-7.1c.3-.3-.1-.5-.5-.2l-9.7 6.1-4.2-1.3c-.9-.3-.9-.9.2-1.3L20.6 3.4c.8-.3 1.5.2 1.3.9Z"/></svg>
                </a>
            </div>

            <div class="pk-copy-input">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.5.5l2-2a5 5 0 0 0-7-7l-1.5 1.5" />
                    <path d="M14 11a5 5 0 0 0-7.5-.5l-2 2a5 5 0 0 0 7 7l1.5-1.5" />
                </svg>
                <span id="pk-share-url">{{ url()->current() }}</span>
                <button type="button" class="pk-copy-btn" onclick="pkCopyLink()">Copy</button>
            </div>
        </div>

        <div class="pk-footer">
            Powered by <a href="{{ url('/') }}">{{ config('app.name') }}</a>
        </div>
    </div>
</div>

<script>
    function pkCopyLink() {
        const url = document.getElementById('pk-share-url').textContent;
        navigator.clipboard.writeText(url).then(() => {
            const btn = document.querySelector('.pk-copy-btn');
            const original = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(() => { btn.textContent = original; }, 1500);
        });
    }
</script>

</body>
</html>