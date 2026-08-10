@php
    $baseCurrency = userBaseCurrency();
    $currentLevel = userLevel();
    $disableBasic = in_array($currentLevel, ['Creator', 'Influencer']);
    $isNgn = $baseCurrency === 'NGN';
    $userActiveMode = $currentBillingMode ?? 'subscription';
    $currentMode = $currentMode ?? $userActiveMode;
    $discountRate = 0.1;
    $walletCurrency = auth()->user()->wallet->currency;
    $currencySymbol = getCurrencyCode();

    $levelAccent = [
        'Basic' => ['gradient' => 'linear-gradient(135deg,#64748b,#94a3b8)', 'glow' => 'rgba(100,116,139,.25)', 'badge' => null],
        'Creator' => ['gradient' => 'linear-gradient(135deg,#5A4FDC,#7C3AED)', 'glow' => 'rgba(90,79,220,.35)', 'badge' => 'Most popular'],
        'Influencer' => ['gradient' => 'linear-gradient(135deg,#D97706,#F59E0B)', 'glow' => 'rgba(245,158,11,.35)', 'badge' => 'Best value'],
    ];
@endphp

<div class="upg-page">
    @verbatim
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

            .upg-page {
                --upg-violet: #5A4FDC;
                --upg-violet-dark: #4B41C4;
                --upg-violet-soft: #EEECFC;
                --upg-gold: #F59E0B;
                --upg-gold-soft: #FEF3C7;
                --upg-mint: #10B981;
                --upg-ink: #0F172A;
                --upg-muted: #64748B;
                --upg-line: #E2E8F0;
                --upg-bg: #F8FAFC;
                --upg-r: 16px;
                --upg-r-sm: 10px;
                --upg-shadow: 0 4px 24px rgba(15, 23, 42, .06);
                --upg-shadow-lg: 0 20px 50px rgba(90, 79, 220, .12);
                font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
                color: var(--upg-ink);
                background: var(--upg-bg);
                min-height: 100%;
            }

            .upg-page * { box-sizing: border-box; }

            .upg-wrap {
                max-width: 1180px;
                margin: 0 auto;
                padding: 0 16px 48px;
            }

            /* ---- alerts ---- */
            .upg-alert {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                padding: 14px 16px;
                border-radius: var(--upg-r-sm);
                font-size: .9rem;
                font-weight: 600;
                margin-bottom: 20px;
            }
            .upg-alert--success {
                background: #ECFDF5;
                color: #047857;
                border: 1px solid #A7F3D0;
            }
            .upg-alert--error {
                background: #FEF2F2;
                color: #B91C1C;
                border: 1px solid #FECACA;
            }

            /* ---- hero ---- */
            .upg-hero {
                position: relative;
                overflow: hidden;
                border-radius: 0 0 28px 28px;
                margin: 0 -16px 32px;
                padding: clamp(36px, 8vw, 64px) 24px clamp(40px, 9vw, 72px);
                background: linear-gradient(135deg, #4B41C4 0%, #5A4FDC 40%, #7C3AED 100%);
                color: #fff;
                text-align: center;
            }
            .upg-hero::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(circle at 20% 20%, rgba(255,255,255,.12) 0, transparent 45%),
                    radial-gradient(circle at 80% 10%, rgba(255,255,255,.08) 0, transparent 40%),
                    radial-gradient(circle at 50% 100%, rgba(0,0,0,.15) 0, transparent 55%);
                pointer-events: none;
            }
            .upg-hero-inner {
                position: relative;
                max-width: 720px;
                margin: 0 auto;
            }
            .upg-hero-kicker {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 14px;
                border-radius: 999px;
                background: rgba(255,255,255,.14);
                border: 1px solid rgba(255,255,255,.22);
                font-size: .78rem;
                font-weight: 700;
                letter-spacing: .04em;
                text-transform: uppercase;
                margin-bottom: 16px;
                backdrop-filter: blur(8px);
            }
            .upg-hero h1 {
                font-size: clamp(1.75rem, 5vw, 2.75rem);
                font-weight: 800;
                line-height: 1.15;
                margin: 0 0 12px;
                letter-spacing: -.02em;
            }
            .upg-hero p {
                font-size: clamp(.95rem, 2.5vw, 1.125rem);
                line-height: 1.6;
                color: rgba(255,255,255,.88);
                margin: 0 auto 20px;
                max-width: 540px;
            }
            .upg-current-pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 18px;
                border-radius: 999px;
                background: rgba(255,255,255,.95);
                color: var(--upg-violet-dark);
                font-size: .875rem;
                font-weight: 700;
                box-shadow: 0 8px 24px rgba(0,0,0,.12);
            }
            .upg-current-pill svg { width: 16px; height: 16px; }

            /* ---- billing toggle ---- */
            .upg-billing {
                text-align: center;
                margin-bottom: 28px;
            }
            .upg-billing-switch {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 5px;
                border-radius: 999px;
                background: #fff;
                border: 1px solid var(--upg-line);
                box-shadow: var(--upg-shadow);
            }
            .upg-billing-switch input { position: absolute; opacity: 0; pointer-events: none; }
            .upg-billing-switch label {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 18px;
                border-radius: 999px;
                font-size: .84rem;
                font-weight: 700;
                color: var(--upg-muted);
                cursor: pointer;
                transition: all .2s ease;
                white-space: nowrap;
            }
            .upg-billing-switch input:checked + label {
                background: var(--upg-violet);
                color: #fff;
                box-shadow: 0 4px 14px rgba(90,79,220,.35);
            }
            .upg-save-badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 999px;
                background: var(--upg-mint);
                color: #fff;
                font-size: .68rem;
                font-weight: 800;
                letter-spacing: .02em;
            }
            .upg-billing-note {
                max-width: 560px;
                margin: 12px auto 0;
                font-size: .82rem;
                line-height: 1.55;
                color: var(--upg-muted);
            }

            /* ---- plan grid ---- */
            .upg-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                align-items: stretch;
            }

            .upg-plan {
                position: relative;
                display: flex;
                flex-direction: column;
                background: #fff;
                border: 1.5px solid var(--upg-line);
                border-radius: var(--upg-r);
                box-shadow: var(--upg-shadow);
                overflow: hidden;
                transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            }
            .upg-plan:hover:not(.upg-plan--disabled):not(.upg-plan--active) {
                transform: translateY(-4px);
                box-shadow: var(--upg-shadow-lg);
                border-color: rgba(90,79,220,.35);
            }
            .upg-plan--featured {
                border-color: rgba(90,79,220,.45);
                box-shadow: var(--upg-shadow-lg);
            }
            .upg-plan--featured .upg-plan-top { padding-top: 28px; }
            .upg-plan--active {
                border-color: var(--upg-violet);
                box-shadow: 0 0 0 3px rgba(90,79,220,.12), var(--upg-shadow-lg);
            }
            .upg-plan--disabled {
                opacity: .55;
                pointer-events: none;
                filter: grayscale(.2);
            }

            .upg-popular {
                position: absolute;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                padding: 6px 16px;
                border-radius: 0 0 12px 12px;
                background: linear-gradient(135deg, #5A4FDC, #7C3AED);
                color: #fff;
                font-size: .72rem;
                font-weight: 800;
                letter-spacing: .06em;
                text-transform: uppercase;
                z-index: 2;
            }

            .upg-plan-link {
                display: flex;
                flex-direction: column;
                flex: 1;
                color: inherit;
                text-decoration: none;
            }

            .upg-plan-top {
                padding: 24px 22px 18px;
                text-align: center;
                border-bottom: 1px solid var(--upg-line);
            }
            .upg-plan-icon {
                width: 48px;
                height: 48px;
                border-radius: 14px;
                display: grid;
                place-items: center;
                margin: 0 auto 14px;
                color: #fff;
                font-size: 1.1rem;
            }
            .upg-plan-name {
                font-size: 1.25rem;
                font-weight: 800;
                margin: 0 0 4px;
                letter-spacing: -.01em;
            }
            .upg-plan-tag {
                font-size: .8rem;
                color: var(--upg-muted);
                margin: 0;
            }

            .upg-price-block { margin-top: 16px; }
            .upg-price {
                font-size: clamp(1.75rem, 4vw, 2.25rem);
                font-weight: 800;
                line-height: 1.1;
                letter-spacing: -.03em;
                margin: 0;
            }
            .upg-price-old {
                display: block;
                font-size: .85rem;
                font-weight: 600;
                color: var(--upg-muted);
                text-decoration: line-through;
                margin-bottom: 2px;
            }
            .upg-price-meta {
                font-size: .82rem;
                color: var(--upg-muted);
                margin: 6px 0 0;
            }
            .upg-discount-pill {
                display: inline-block;
                margin-top: 6px;
                padding: 3px 10px;
                border-radius: 999px;
                background: #ECFDF5;
                color: #047857;
                font-size: .72rem;
                font-weight: 800;
            }

            .upg-features {
                flex: 1;
                padding: 18px 22px;
                list-style: none;
                margin: 0;
            }
            .upg-features li {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                padding: 9px 0;
                font-size: .875rem;
                line-height: 1.45;
                color: #334155;
                border-bottom: 1px dashed #EEF2F7;
            }
            .upg-features li:last-child { border-bottom: none; }
            .upg-features li svg {
                width: 18px;
                height: 18px;
                flex-shrink: 0;
                margin-top: 1px;
                color: var(--upg-mint);
            }
            .upg-features li strong { color: var(--upg-ink); }

            .upg-plan-foot {
                padding: 18px 22px 22px;
                margin-top: auto;
            }
            .upg-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                padding: 14px 20px;
                border-radius: 12px;
                border: none;
                font-family: inherit;
                font-size: .9rem;
                font-weight: 700;
                cursor: pointer;
                transition: all .2s ease;
                text-decoration: none;
            }
            .upg-btn--primary {
                background: linear-gradient(135deg, #5A4FDC, #6D28D9);
                color: #fff;
                box-shadow: 0 8px 20px rgba(90,79,220,.3);
            }
            .upg-btn--primary:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 28px rgba(90,79,220,.38);
                color: #fff;
            }
            .upg-btn--gold {
                background: linear-gradient(135deg, #D97706, #F59E0B);
                color: #fff;
                box-shadow: 0 8px 20px rgba(245,158,11,.3);
            }
            .upg-btn--muted {
                background: #F1F5F9;
                color: var(--upg-muted);
                cursor: default;
            }
            .upg-btn--outline {
                background: #fff;
                color: var(--upg-violet);
                border: 1.5px solid var(--upg-violet);
            }

            /* ---- perks ---- */
            .upg-perks {
                margin-top: 56px;
                padding: clamp(32px, 6vw, 48px) 0;
            }
            .upg-section-head {
                text-align: center;
                max-width: 620px;
                margin: 0 auto 36px;
            }
            .upg-section-head h2 {
                font-size: clamp(1.4rem, 4vw, 1.875rem);
                font-weight: 800;
                margin: 0 0 10px;
                letter-spacing: -.02em;
            }
            .upg-section-head p {
                font-size: .95rem;
                color: var(--upg-muted);
                line-height: 1.6;
                margin: 0;
            }
            .upg-perk-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
            .upg-perk {
                background: #fff;
                border: 1px solid var(--upg-line);
                border-radius: var(--upg-r);
                padding: 24px 20px;
                text-align: center;
                transition: transform .2s ease, box-shadow .2s ease;
            }
            .upg-perk:hover {
                transform: translateY(-3px);
                box-shadow: var(--upg-shadow);
            }
            .upg-perk-icon {
                width: 52px;
                height: 52px;
                border-radius: 14px;
                display: grid;
                place-items: center;
                margin: 0 auto 14px;
                font-size: 1.25rem;
            }
            .upg-perk h3 {
                font-size: 1rem;
                font-weight: 700;
                margin: 0 0 8px;
            }
            .upg-perk p {
                font-size: .84rem;
                color: var(--upg-muted);
                line-height: 1.55;
                margin: 0;
            }

            /* ---- cta band ---- */
            .upg-cta {
                margin-top: 40px;
                padding: clamp(28px, 5vw, 40px);
                border-radius: var(--upg-r);
                background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4C1D95 100%);
                color: #fff;
                text-align: center;
            }
            .upg-cta h2 {
                font-size: clamp(1.25rem, 3.5vw, 1.625rem);
                font-weight: 800;
                margin: 0 0 8px;
            }
            .upg-cta p {
                font-size: .92rem;
                color: rgba(255,255,255,.78);
                margin: 0;
                max-width: 520px;
                margin-inline: auto;
                line-height: 1.55;
            }

            .d-none { display: none !important; }

            @media (max-width: 991.98px) {
                .upg-grid { grid-template-columns: 1fr; max-width: 420px; margin-inline: auto; }
                .upg-plan--featured { order: -1; }
                .upg-perk-grid { grid-template-columns: 1fr; max-width: 420px; margin-inline: auto; }
            }

            @media (max-width: 575.98px) {
                .upg-wrap { padding-inline: 12px; }
                .upg-hero { margin-inline: -12px; border-radius: 0 0 20px 20px; padding-inline: 16px; }
                .upg-billing-switch {
                    flex-direction: column;
                    width: 100%;
                    max-width: 320px;
                    border-radius: var(--upg-r-sm);
                }
                .upg-billing-switch label {
                    width: 100%;
                    justify-content: center;
                    border-radius: var(--upg-r-sm);
                }
                .upg-plan-top, .upg-features, .upg-plan-foot { padding-inline: 16px; }
            }

            @media (min-width: 992px) {
                .upg-plan--featured { transform: scale(1.03); z-index: 1; }
                .upg-plan--featured:hover { transform: scale(1.03) translateY(-4px); }
            }
        </style>
    @endverbatim

    {{-- Hero --}}
    <section class="upg-hero">
        <div class="upg-hero-inner">
            <div class="upg-hero-kicker">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14">
                    <path d="m12 19V5M5 12l7-7 7 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Level up your account
            </div>
            <h1>Turn your content into income</h1>
            <p>Upgrade to Creator or Influencer to monetize posts, unlock withdrawals, and grow your audience with premium tools.</p>
            <div class="upg-current-pill">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Current plan: <strong>{{ $currentLevel }}</strong>
            </div>
        </div>
    </section>

    <div class="upg-wrap">

        @if (session()->has('success'))
            <div class="upg-alert upg-alert--success" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="upg-alert upg-alert--error" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01" stroke-linecap="round"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if ($isNgn)
            <div class="upg-billing">
                <div class="upg-billing-switch" id="billing-toggle" role="group" aria-label="Billing mode">
                    <input type="radio" name="billingMode" id="mode-subscription" autocomplete="off"
                        {{ $currentMode === 'subscription' ? 'checked' : '' }}>
                    <label for="mode-subscription">
                        <i class="fa fa-refresh"></i>
                        Direct subscription
                        <span class="upg-save-badge">Save 10%</span>
                    </label>

                    <input type="radio" name="billingMode" id="mode-payg" autocomplete="off"
                        {{ $currentMode === 'payg' ? 'checked' : '' }}>
                    <label for="mode-payg">
                        <i class="fa fa-wallet"></i>
                        Pay as you go
                    </label>
                </div>
                <p class="upg-billing-note">
                    <strong>Direct subscription</strong> renews automatically each month with a 10% discount.
                    <strong>Pay as you go</strong> bills monthly with no stored subscription — cancel anytime.
                </p>
            </div>
        @endif

        <div class="upg-grid">
            @foreach (getLevels() as $level)
                @php
                    $isActive = $currentLevel && $currentLevel === $level->name;
                    $isBasic = $level->name === 'Basic';
                    $isDisabled = $disableBasic && $isBasic;
                    $isFeatured = $level->name === 'Creator';
                    $accent = $levelAccent[$level->name] ?? $levelAccent['Basic'];

                    $subUrl = url('subscribe/' . $level->id);
                    $paygUrl = url('payg-subscribe/' . $level->id);
                    $currentUrl = $isNgn && $currentMode === 'payg' ? $paygUrl : $subUrl;

                    $basePrice = convertToBaseCurrency($level->amount, $walletCurrency);
                    $discountedPrice = convertToBaseCurrency($level->amount * (1 - $discountRate), $walletCurrency);

                    $regBonus = convertToBaseCurrency($level->reg_bonus, $walletCurrency);
                    $refBonus = convertToBaseCurrency($level->ref_bonus, $walletCurrency);

                    $planIcons = [
                        'Basic' => 'fa-leaf',
                        'Creator' => 'fa-magic',
                        'Influencer' => 'fa-trophy',
                    ];
                    $planTaglines = [
                        'Basic' => 'Start for free',
                        'Creator' => 'Grow & monetize',
                        'Influencer' => 'Maximum earnings',
                    ];
                @endphp

                <article @class([
                    'upg-plan',
                    'upg-plan--featured' => $isFeatured && ! $isDisabled,
                    'upg-plan--active' => $isActive,
                    'upg-plan--disabled' => $isDisabled,
                    'billing-card-active' => $isActive && $isNgn,
                    'billing-card' => ! $isActive && $isNgn,
                ])
                    @if ($isNgn && ! $isActive)
                        data-sub-url="{{ $subUrl }}" data-payg-url="{{ $paygUrl }}"
                    @endif
                    @if ($isActive && $isNgn)
                        data-active-mode="{{ $userActiveMode }}"
                    @endif>

                    @if ($accent['badge'] && ! $isDisabled)
                        <span class="upg-popular">{{ $accent['badge'] }}</span>
                    @endif

                    @if ($isActive)
                        <div class="upg-plan-link">
                            <div class="upg-plan-top">
                                <div class="upg-plan-icon" style="background: {{ $accent['gradient'] }}; box-shadow: 0 8px 20px {{ $accent['glow'] }};">
                                    <i class="fa {{ $planIcons[$level->name] ?? 'fa-star' }}"></i>
                                </div>
                                <h3 class="upg-plan-name">{{ $level->name }}</h3>
                                <p class="upg-plan-tag">{{ $planTaglines[$level->name] ?? '' }}</p>

                                <div class="upg-price-block price-block">
                                    @if ($isNgn)
                                        <p class="upg-price price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">
                                            <span class="upg-price-old">{{ $currencySymbol }}{{ number_format($basePrice, 2) }}</span>
                                            {{ $currencySymbol }}{{ number_format($discountedPrice, 2) }}
                                        </p>
                                        <span class="upg-discount-pill price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">10% subscription discount</span>
                                        <p class="upg-price price-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">
                                            {{ $currencySymbol }}{{ number_format($basePrice, 2) }}
                                        </p>
                                    @else
                                        <p class="upg-price">{{ $currencySymbol }}{{ number_format($basePrice, 2) }}</p>
                                    @endif
                                    <p class="upg-price-meta">per month</p>
                                </div>
                            </div>

                            <ul class="upg-features">
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span><strong>{{ $currencySymbol }}{{ number_format($regBonus, 2) }}</strong> upgrade bonus</span>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span><strong>{{ $currencySymbol }}{{ number_format($refBonus, 2) }}</strong> referral bonus</span>
                                </li>
                                @if (! $isBasic)
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span><strong>Account monetization</strong> on views, likes & comments</span>
                                    </li>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span><strong>Withdrawals</strong> enabled</span>
                                    </li>
                                @endif
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span><strong>Email support</strong> included</span>
                                </li>
                            </ul>

                            <div class="upg-plan-foot">
                                <span class="upg-btn upg-btn--muted active-plan-label {{ ! $isNgn || $currentMode === $userActiveMode ? '' : 'd-none' }}">
                                    <i class="fa fa-check-circle"></i> Active plan
                                </span>
                            </div>
                        </div>

                    @else
                        @if (! $isDisabled)
                            <a href="{{ $currentUrl }}" class="upg-plan-link billing-link" style="color:inherit">
                        @endif

                        <div class="upg-plan-top">
                            <div class="upg-plan-icon" style="background: {{ $accent['gradient'] }}; box-shadow: 0 8px 20px {{ $accent['glow'] }};">
                                <i class="fa {{ $planIcons[$level->name] ?? 'fa-star' }}"></i>
                            </div>
                            <h3 class="upg-plan-name">{{ $level->name }}</h3>
                            <p class="upg-plan-tag">{{ $planTaglines[$level->name] ?? '' }}</p>

                            <div class="upg-price-block price-block">
                                @if ($isNgn)
                                    <p class="upg-price price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">
                                        <span class="upg-price-old">{{ $currencySymbol }}{{ number_format($basePrice, 2) }}</span>
                                        {{ $currencySymbol }}{{ number_format($discountedPrice, 2) }}
                                    </p>
                                    <span class="upg-discount-pill price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">10% subscription discount</span>
                                    <p class="upg-price price-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">
                                        {{ $currencySymbol }}{{ number_format($basePrice, 2) }}
                                    </p>
                                @else
                                    <p class="upg-price">{{ $currencySymbol }}{{ number_format($basePrice, 2) }}</p>
                                @endif
                                <p class="upg-price-meta">per month</p>
                            </div>
                        </div>

                        <ul class="upg-features">
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span><strong>{{ $currencySymbol }}{{ number_format($regBonus, 2) }}</strong> upgrade bonus</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span><strong>{{ $currencySymbol }}{{ number_format($refBonus, 2) }}</strong> referral bonus</span>
                            </li>
                            @if (! $isBasic)
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span><strong>Account monetization</strong> on views, likes & comments</span>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span><strong>Withdrawals</strong> enabled</span>
                                </li>
                            @endif
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span><strong>Email support</strong> included</span>
                            </li>
                        </ul>

                        <div class="upg-plan-foot">
                            @if ($isDisabled)
                                <span class="upg-btn upg-btn--muted">
                                    <i class="fa fa-ban"></i> Deactivated
                                </span>
                            @else
                                <span @class([
                                    'upg-btn billing-btn-label',
                                    'upg-btn--primary' => $level->name === 'Creator',
                                    'upg-btn--gold' => $level->name === 'Influencer',
                                    'upg-btn--outline' => $level->name === 'Basic',
                                ])>
                                    <i class="fa fa-arrow-up"></i>
                                    @if ($isNgn)
                                        <span class="btn-text-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">Subscribe & save 10%</span>
                                        <span class="btn-text-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">Pay as you go</span>
                                    @else
                                        Upgrade to {{ $level->name }}
                                    @endif
                                </span>
                            @endif
                        </div>

                        @if (! $isDisabled)
                            </a>
                        @endif
                    @endif
                </article>
            @endforeach
        </div>

        @if ($isNgn)
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var subRadio = document.getElementById('mode-subscription');
                    var paygRadio = document.getElementById('mode-payg');
                    var upgradeCards = document.querySelectorAll('.billing-card');
                    var activeCard = document.querySelector('.billing-card-active');

                    function applyMode(mode) {
                        upgradeCards.forEach(function(card) {
                            var link = card.querySelector('.billing-link');
                            if (link) {
                                link.href = mode === 'payg' ? card.dataset.paygUrl : card.dataset.subUrl;
                            }
                        });

                        document.querySelectorAll('.price-subscription').forEach(function(el) {
                            el.classList.toggle('d-none', mode !== 'subscription');
                        });
                        document.querySelectorAll('.price-payg').forEach(function(el) {
                            el.classList.toggle('d-none', mode !== 'payg');
                        });

                        document.querySelectorAll('.btn-text-subscription').forEach(function(el) {
                            el.classList.toggle('d-none', mode !== 'subscription');
                        });
                        document.querySelectorAll('.btn-text-payg').forEach(function(el) {
                            el.classList.toggle('d-none', mode !== 'payg');
                        });

                        if (activeCard) {
                            var activeMode = activeCard.dataset.activeMode;
                            var label = activeCard.querySelector('.active-plan-label');
                            var matchesActive = (mode === activeMode);
                            if (label) label.classList.toggle('d-none', !matchesActive);
                        }
                    }

                    if (subRadio) {
                        subRadio.addEventListener('change', function() {
                            if (this.checked) applyMode('subscription');
                        });
                    }
                    if (paygRadio) {
                        paygRadio.addEventListener('change', function() {
                            if (this.checked) applyMode('payg');
                        });
                    }
                });
            </script>
        @endif

        {{-- Included perks --}}
        <section class="upg-perks">
            <div class="upg-section-head">
                <h2>Everything included when you upgrade</h2>
                <p>Upgrade today and unlock premium benefits at no extra cost — built to help you create, earn, and grow.</p>
            </div>

            <div class="upg-perk-grid">
                <article class="upg-perk">
                    <div class="upg-perk-icon" style="background:#EEF2FF;color:#4F46E5;">
                        <i class="fa fa-phone"></i>
                    </div>
                    <h3>Lifetime support</h3>
                    <p>High-quality email support whenever you need help with your account, payouts, or content.</p>
                </article>
                <article class="upg-perk">
                    <div class="upg-perk-icon" style="background:#ECFDF5;color:#059669;">
                        <i class="fa fa-money"></i>
                    </div>
                    <h3>Unlimited monetizable posts</h3>
                    <p>Earn on views, comments, and likes across unlimited posts — plus longer-form content options.</p>
                </article>
                <article class="upg-perk">
                    <div class="upg-perk-icon" style="background:#FEF3C7;color:#D97706;">
                        <i class="fa fa-photo"></i>
                    </div>
                    <h3>Photos & video posts</h3>
                    <p>Share rich media that engages your audience and keeps your monetization potential high.</p>
                </article>
            </div>
        </section>

        <section class="upg-cta">
            <h2>Ready to grow beyond Basic?</h2>
            <p>Upgrading unlocks monetization, withdrawals, and the tools creators use to expand their reach and build a real audience.</p>
        </section>
    </div>
</div>
