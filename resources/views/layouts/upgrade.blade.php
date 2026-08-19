@php
    $baseCurrency = userBaseCurrency();
    $currentLevel = userLevel();
    $disableBasic = in_array($currentLevel, ['Creator', 'Influencer']);
    $isNgn = $baseCurrency === 'NGN';
    $userActiveMode = $currentBillingMode ?? 'subscription';
    $currentMode = $currentMode ?? $userActiveMode;
    $discountRate = 0.1;
    $walletCurrency = auth()->user()->wallet->currency ?? $baseCurrency;
    $currencySymbol = getCurrencyCode();

    $planCopy = [
        'Basic' => [
            'badge' => null,
            'tagline' => 'Your starting point for creating and growing on Payhankey.',
            'cta' => 'Stay on Basic',
            'features' => [
                'Unlimited posts & quizzes',
                'Payhankey Rolls (Videos)',
                'Full dashboard access',
                'Discover and join communities',
            ],
        ],
        'Creator' => [
            'badge' => 'Most popular',
            'tagline' => 'For creators ready to monetize their content and grow their audience.',
            'cta' => 'Continue to checkout',
            'features' => [
                'Everything in Basic',
                'Content monetization',
                'Create & monetize communities',
                'Verified creator badge',
                'Image posting',
                'Priority discovery',
                'AI Creator support tools',
            ],
        ],
        'Influencer' => [
            'badge' => null,
            'tagline' => 'For established creators ready to increase their reach and earning potential.',
            'cta' => 'Continue to checkout',
            'features' => [
                'Everything in Creator',
                'Payhankey Rolls (Videos)',
                'Influencer verification badge',
                'Influencer profile ring',
                'Higher content limits',
                'Top-feed placement',
                'Priority discovery',
                'Advanced creator opportunities',
            ],
        ],
    ];
@endphp

<div class="chk-page">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        .chk-page {
            --chk-ink: #16122E;
            --chk-soft: #4A4570;
            --chk-faint: #8E89AD;
            --chk-line: #E8E5F3;
            --chk-bg: #F8F7FC;
            --chk-card: #FFFFFF;
            --chk-violet: #5A4FDC;
            --chk-violet-deep: #4034B0;
            --chk-mint: #12B886;
            --chk-gold: #D97706;
            --chk-r: 18px;
            --chk-shadow: 0 10px 30px rgba(50, 40, 120, .08);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--chk-ink);
            background:
                radial-gradient(ellipse 80% 50% at 10% -10%, rgba(90, 79, 220, .08), transparent 55%),
                radial-gradient(ellipse 60% 40% at 90% 0%, rgba(18, 184, 134, .06), transparent 50%),
                var(--chk-bg);
            min-height: 100%;
            padding-bottom: 48px;
        }

        .chk-page * { box-sizing: border-box; }

        .chk-wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .chk-hero {
            padding: clamp(28px, 5vw, 48px) 16px 28px;
            text-align: center;
        }

        .chk-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--chk-violet);
            margin-bottom: 12px;
        }

        .chk-hero h1 {
            margin: 0;
            font-size: clamp(1.75rem, 4.5vw, 2.5rem);
            font-weight: 800;
            letter-spacing: -.03em;
            line-height: 1.15;
        }

        .chk-hero p {
            margin: 12px auto 0;
            max-width: 36rem;
            color: var(--chk-soft);
            font-size: 1rem;
            line-height: 1.55;
        }

        .chk-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--chk-line);
            font-size: .875rem;
            font-weight: 600;
            color: var(--chk-soft);
            box-shadow: 0 4px 14px rgba(22, 18, 46, .04);
        }

        .chk-status strong { color: var(--chk-ink); }

        .chk-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: .9rem;
            font-weight: 600;
            margin-bottom: 18px;
        }
        .chk-alert--success { background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; }
        .chk-alert--error { background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; }

        .chk-billing {
            margin: 0 auto 28px;
            max-width: 640px;
            text-align: center;
        }

        .chk-billing-switch {
            display: inline-flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            padding: 6px;
            background: #fff;
            border: 1px solid var(--chk-line);
            border-radius: 999px;
            box-shadow: var(--chk-shadow);
        }

        .chk-billing-switch input { position: absolute; opacity: 0; pointer-events: none; }

        .chk-billing-switch label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            font-size: .875rem;
            font-weight: 700;
            color: var(--chk-soft);
            cursor: pointer;
            transition: .2s ease;
        }

        .chk-billing-switch input:checked + label {
            background: var(--chk-violet);
            color: #fff;
        }

        .chk-save {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            background: var(--chk-mint);
            color: #fff;
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .chk-billing-switch input:checked + label .chk-save {
            background: rgba(255,255,255,.22);
        }

        .chk-billing-note {
            margin: 12px 0 0;
            font-size: .84rem;
            color: var(--chk-faint);
            line-height: 1.5;
        }

        .chk-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            align-items: stretch;
        }

        .chk-plan {
            position: relative;
            display: flex;
            flex-direction: column;
            background: var(--chk-card);
            border: 1.5px solid var(--chk-line);
            border-radius: var(--chk-r);
            box-shadow: 0 4px 18px rgba(22, 18, 46, .04);
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .chk-plan:hover:not(.chk-plan--disabled):not(.chk-plan--active) {
            transform: translateY(-3px);
            box-shadow: var(--chk-shadow);
        }

        .chk-plan--featured {
            border-color: rgba(90, 79, 220, .45);
            box-shadow: 0 16px 40px rgba(90, 79, 220, .14);
        }

        .chk-plan--active {
            border-color: var(--chk-violet);
            box-shadow: 0 0 0 3px rgba(90, 79, 220, .12), var(--chk-shadow);
        }

        .chk-plan--disabled {
            opacity: .55;
            pointer-events: none;
        }

        .chk-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            z-index: 2;
            padding: 5px 10px;
            border-radius: 999px;
            background: var(--chk-violet);
            color: #fff;
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .chk-plan-link {
            display: flex;
            flex-direction: column;
            flex: 1;
            color: inherit;
            text-decoration: none;
            min-height: 100%;
        }

        .chk-plan-top {
            padding: 28px 22px 18px;
            border-bottom: 1px solid var(--chk-line);
        }

        .chk-plan-name {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .chk-plan-tag {
            margin: 8px 0 0;
            font-size: .875rem;
            color: var(--chk-soft);
            line-height: 1.45;
            min-height: 2.6em;
        }

        .chk-price-block { margin-top: 18px; }

        .chk-price {
            margin: 0;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -.04em;
            line-height: 1;
        }

        .chk-price-old {
            margin-right: 8px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--chk-faint);
            text-decoration: line-through;
        }

        .chk-price-meta {
            margin: 8px 0 0;
            font-size: .8125rem;
            color: var(--chk-faint);
            font-weight: 600;
        }

        .chk-discount {
            display: inline-flex;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 8px;
            background: #E4F7F0;
            color: #0F766E;
            font-size: .72rem;
            font-weight: 800;
        }

        .chk-features {
            list-style: none;
            margin: 0;
            padding: 8px 22px;
            flex: 1;
        }

        .chk-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #F1EFF8;
            font-size: .875rem;
            color: var(--chk-soft);
            line-height: 1.4;
        }

        .chk-features li:last-child { border-bottom: none; }

        .chk-features li svg {
            flex: none;
            width: 16px;
            height: 16px;
            margin-top: 2px;
            color: var(--chk-mint);
        }

        .chk-features strong { color: var(--chk-ink); font-weight: 700; }

        .chk-summary {
            margin: 4px 22px 0;
            padding: 12px 14px;
            border-radius: 12px;
            background: #F6F4FD;
            border: 1px solid #E6E2F8;
            font-size: .8rem;
            color: var(--chk-soft);
            line-height: 1.45;
        }

        .chk-summary b { color: var(--chk-ink); }

        .chk-foot {
            padding: 18px 22px 22px;
            margin-top: auto;
        }

        .chk-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            min-height: 48px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: .9rem;
            font-weight: 800;
            border: none;
            text-align: center;
            transition: transform .15s ease, background .15s ease;
        }

        .chk-btn--primary {
            background: var(--chk-violet);
            color: #fff;
            box-shadow: 0 10px 24px rgba(90, 79, 220, .28);
        }
        .chk-btn--primary:hover { background: var(--chk-violet-deep); }

        .chk-btn--gold {
            background: linear-gradient(135deg, #D97706, #F59E0B);
            color: #fff;
            box-shadow: 0 10px 24px rgba(217, 119, 6, .25);
        }

        .chk-btn--outline {
            background: #fff;
            color: var(--chk-violet);
            border: 1.5px solid rgba(90, 79, 220, .35);
        }

        .chk-btn--muted {
            background: #EEF2F7;
            color: var(--chk-faint);
        }

        .chk-trust {
            margin-top: 28px;
            text-align: center;
            color: var(--chk-faint);
            font-size: .875rem;
            font-weight: 600;
        }

        .chk-secure {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px 18px;
            margin-top: 14px;
            font-size: .8rem;
            color: var(--chk-soft);
            font-weight: 600;
        }

        .chk-secure span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .chk-cta {
            margin-top: 36px;
            padding: clamp(24px, 4vw, 36px);
            border-radius: var(--chk-r);
            background: linear-gradient(145deg, #1A1635 0%, #2B2460 55%, #3D3488 100%);
            color: #fff;
            text-align: center;
        }

        .chk-cta h2 {
            margin: 0 0 8px;
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .chk-cta p {
            margin: 0 auto;
            max-width: 34rem;
            color: rgba(255,255,255,.78);
            font-size: .92rem;
            line-height: 1.55;
        }

        .d-none { display: none !important; }

        @media (max-width: 991.98px) {
            .chk-grid {
                grid-template-columns: 1fr;
                max-width: 420px;
                margin-inline: auto;
            }
            .chk-plan--featured { order: -1; }
        }

        @media (max-width: 575.98px) {
            .chk-billing-switch {
                width: 100%;
                border-radius: 16px;
                flex-direction: column;
            }
            .chk-billing-switch label {
                width: 100%;
                justify-content: center;
                border-radius: 12px;
            }
            .chk-plan-top, .chk-features, .chk-foot, .chk-summary { padding-inline: 16px; }
            .chk-summary { margin-inline: 16px; }
        }

        @media (min-width: 992px) {
            .chk-plan--featured { transform: translateY(-6px); }
            .chk-plan--featured:hover { transform: translateY(-9px); }
        }
    </style>

    <section class="chk-hero">
        <div class="chk-kicker">Checkout</div>
        <h1>Choose your creator plan</h1>
        <p>Start free. Upgrade when you want monetization, communities, and higher earning potential. Cancel anytime.</p>
        <div class="chk-status">
            Current plan: <strong>{{ $currentLevel }}</strong>
            @if ($isNgn)
                <span aria-hidden="true">·</span>
                <span>{{ $userActiveMode === 'payg' ? 'Pay as you go' : 'Subscription' }}</span>
            @endif
        </div>
    </section>

    <div class="chk-wrap">
        @if (session()->has('success'))
            <div class="chk-alert chk-alert--success" role="alert">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="chk-alert chk-alert--error" role="alert">{{ session('error') }}</div>
        @endif

        @if ($isNgn)
            <div class="chk-billing">
                <div class="chk-billing-switch" id="billing-toggle" role="group" aria-label="Billing mode">
                    <input type="radio" name="billingMode" id="mode-subscription" autocomplete="off"
                        {{ $currentMode === 'subscription' ? 'checked' : '' }}>
                    <label for="mode-subscription">
                        Direct subscription
                        <span class="chk-save">Save 10%</span>
                    </label>

                    <input type="radio" name="billingMode" id="mode-payg" autocomplete="off"
                        {{ $currentMode === 'payg' ? 'checked' : '' }}>
                    <label for="mode-payg">Pay as you go</label>
                </div>
                <p class="chk-billing-note">
                    <strong>Direct subscription</strong> renews monthly with 10% off.
                    <strong>Pay as you go</strong> is billed each month with no stored subscription.
                </p>
            </div>
        @endif

        <div class="chk-grid">
            @foreach (getLevels() as $level)
                @php
                    $isActive = $currentLevel && $currentLevel === $level->name;
                    $isBasic = $level->name === 'Basic';
                    $isDisabled = $disableBasic && $isBasic;
                    $isFeatured = $level->name === 'Creator';
                    $copy = $planCopy[$level->name] ?? $planCopy['Basic'];

                    $subUrl = url('subscribe/' . $level->id);
                    $paygUrl = url('payg-subscribe/' . $level->id);
                    $currentUrl = $isNgn && $currentMode === 'payg' ? $paygUrl : $subUrl;

                    $basePrice = convertToBaseCurrency($level->amount, $walletCurrency);
                    $discountedPrice = convertToBaseCurrency($level->amount * (1 - $discountRate), $walletCurrency);
                    $regBonus = convertToBaseCurrency($level->reg_bonus, $walletCurrency);
                    $isFree = (float) $level->amount <= 0;
                @endphp

                <article @class([
                    'chk-plan',
                    'chk-plan--featured' => $isFeatured && ! $isDisabled,
                    'chk-plan--active' => $isActive,
                    'chk-plan--disabled' => $isDisabled,
                    'billing-card-active' => $isActive && $isNgn,
                    'billing-card' => ! $isActive && $isNgn,
                ])
                    @if ($isNgn && ! $isActive)
                        data-sub-url="{{ $subUrl }}" data-payg-url="{{ $paygUrl }}"
                    @endif
                    @if ($isActive && $isNgn)
                        data-active-mode="{{ $userActiveMode }}"
                    @endif>

                    @if (! empty($copy['badge']) && ! $isDisabled)
                        <span class="chk-badge">{{ $copy['badge'] }}</span>
                    @endif

                    @if ($isActive)
                        <div class="chk-plan-link">
                            <div class="chk-plan-top">
                                <h3 class="chk-plan-name">{{ $level->name }}</h3>
                                <p class="chk-plan-tag">{{ $copy['tagline'] }}</p>
                                <div class="chk-price-block price-block">
                                    @if ($isFree)
                                        <p class="chk-price">Free</p>
                                        <p class="chk-price-meta">No payment required</p>
                                    @elseif ($isNgn)
                                        <p class="chk-price price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">
                                            <span class="chk-price-old">{{ $currencySymbol }}{{ number_format($basePrice, 2) }}</span>
                                            {{ $currencySymbol }}{{ number_format($discountedPrice, 2) }}
                                        </p>
                                        <span class="chk-discount price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">10% subscription discount</span>
                                        <p class="chk-price price-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">
                                            {{ $currencySymbol }}{{ number_format($basePrice, 2) }}
                                        </p>
                                        <p class="chk-price-meta">per month</p>
                                    @else
                                        <p class="chk-price">{{ $currencySymbol }}{{ number_format($basePrice, 2) }}</p>
                                        <p class="chk-price-meta">per month</p>
                                    @endif
                                </div>
                            </div>

                            <ul class="chk-features">
                                @foreach ($copy['features'] as $feature)
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                                @if (! $isBasic && $regBonus > 0)
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span><strong>{{ $currencySymbol }}{{ number_format($regBonus, 2) }}</strong> upgrade bonus</span>
                                    </li>
                                @endif
                            </ul>

                            <div class="chk-foot">
                                <span class="chk-btn chk-btn--muted active-plan-label {{ ! $isNgn || $currentMode === $userActiveMode ? '' : 'd-none' }}">
                                    Active plan
                                </span>
                            </div>
                        </div>
                    @else
                        @if (! $isDisabled && ! $isBasic)
                            <a href="{{ $currentUrl }}" class="chk-plan-link billing-link">
                        @else
                            <div class="chk-plan-link">
                        @endif

                            <div class="chk-plan-top">
                                <h3 class="chk-plan-name">{{ $level->name }}</h3>
                                <p class="chk-plan-tag">{{ $copy['tagline'] }}</p>
                                <div class="chk-price-block price-block">
                                    @if ($isFree)
                                        <p class="chk-price">Free</p>
                                        <p class="chk-price-meta">No payment required</p>
                                    @elseif ($isNgn)
                                        <p class="chk-price price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">
                                            <span class="chk-price-old">{{ $currencySymbol }}{{ number_format($basePrice, 2) }}</span>
                                            {{ $currencySymbol }}{{ number_format($discountedPrice, 2) }}
                                        </p>
                                        <span class="chk-discount price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">10% subscription discount</span>
                                        <p class="chk-price price-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">
                                            {{ $currencySymbol }}{{ number_format($basePrice, 2) }}
                                        </p>
                                        <p class="chk-price-meta">per month · billed securely</p>
                                    @else
                                        <p class="chk-price">{{ $currencySymbol }}{{ number_format($basePrice, 2) }}</p>
                                        <p class="chk-price-meta">per month · billed securely</p>
                                    @endif
                                </div>
                            </div>

                            <ul class="chk-features">
                                @foreach ($copy['features'] as $feature)
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                                @if (! $isBasic && $regBonus > 0)
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span><strong>{{ $currencySymbol }}{{ number_format($regBonus, 2) }}</strong> upgrade bonus on payment</span>
                                    </li>
                                @endif
                            </ul>

                            @if (! $isBasic && ! $isDisabled)
                                <div class="chk-summary">
                                    You’ll be redirected to a secure payment page to complete
                                    <b>{{ $level->name }}</b>
                                    @if ($isNgn)
                                        (<span class="btn-text-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">subscription</span><span class="btn-text-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">pay-as-you-go</span>)
                                    @endif
                                    .
                                </div>
                            @endif

                            <div class="chk-foot">
                                @if ($isDisabled)
                                    <span class="chk-btn chk-btn--muted">Unavailable</span>
                                @elseif ($isBasic)
                                    <span class="chk-btn chk-btn--outline">{{ $copy['cta'] }}</span>
                                @else
                                    <span @class([
                                        'chk-btn billing-btn-label',
                                        'chk-btn--primary' => $level->name === 'Creator',
                                        'chk-btn--gold' => $level->name === 'Influencer',
                                    ])>
                                        @if ($isNgn)
                                            <span class="btn-text-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">Subscribe &amp; save 10%</span>
                                            <span class="btn-text-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">Continue · Pay as you go</span>
                                        @else
                                            {{ $copy['cta'] }}
                                        @endif
                                    </span>
                                @endif
                            </div>

                        @if (! $isDisabled && ! $isBasic)
                            </a>
                        @else
                            </div>
                        @endif
                    @endif
                </article>
            @endforeach
        </div>

        <p class="chk-trust">No long-term commitment. Cancel anytime.</p>
        <div class="chk-secure">
            <span>Secure checkout</span>
            <span>Local &amp; card payments</span>
            <span>Instant plan activation</span>
        </div>

        <section class="chk-cta">
            <h2>Ready when you are</h2>
            <p>Pick Creator or Influencer, complete checkout, and unlock monetization, communities, and higher earning potential on Payhankey.</p>
        </section>
    </div>

    @if ($isNgn)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var subRadio = document.getElementById('mode-subscription');
                var paygRadio = document.getElementById('mode-payg');
                var upgradeCards = document.querySelectorAll('.billing-card');
                var activeCard = document.querySelector('.billing-card-active');

                function applyMode(mode) {
                    upgradeCards.forEach(function (card) {
                        var link = card.querySelector('.billing-link');
                        if (link) {
                            link.href = mode === 'payg' ? card.dataset.paygUrl : card.dataset.subUrl;
                        }
                    });

                    document.querySelectorAll('.price-subscription').forEach(function (el) {
                        el.classList.toggle('d-none', mode !== 'subscription');
                    });
                    document.querySelectorAll('.price-payg').forEach(function (el) {
                        el.classList.toggle('d-none', mode !== 'payg');
                    });
                    document.querySelectorAll('.btn-text-subscription').forEach(function (el) {
                        el.classList.toggle('d-none', mode !== 'subscription');
                    });
                    document.querySelectorAll('.btn-text-payg').forEach(function (el) {
                        el.classList.toggle('d-none', mode !== 'payg');
                    });

                    if (activeCard) {
                        var activeMode = activeCard.dataset.activeMode;
                        var label = activeCard.querySelector('.active-plan-label');
                        if (label) label.classList.toggle('d-none', mode !== activeMode);
                    }
                }

                if (subRadio) {
                    subRadio.addEventListener('change', function () {
                        if (this.checked) applyMode('subscription');
                    });
                }
                if (paygRadio) {
                    paygRadio.addEventListener('change', function () {
                        if (this.checked) applyMode('payg');
                    });
                }
            });
        </script>
    @endif
</div>
