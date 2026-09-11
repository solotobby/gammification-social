<div>
    <style>
        :root {
            --pk-boost-primary: #6D28D9;
            --pk-boost-primary-dark: #5B21B6;
            --pk-boost-primary-light: #7C3AED;
            --pk-boost-primary-subtle: #FAF5FF;
            --pk-boost-border: #E2E8F0;
            --pk-boost-card-bg: #FFFFFF;
            --pk-boost-dark: #0F172A;
        }

        .pkb-back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #64748B;
            text-decoration: none;
            padding: 14px 0 16px;
            transition: color 0.15s ease;
        }
        .pkb-back-link:hover {
            color: var(--pk-boost-primary);
        }

        /* ── HERO HEADER ── */
        .pkb-hero-banner {
            background: linear-gradient(135deg, #0B0F19 0%, #171335 45%, #2E1065 100%);
            border-radius: 18px;
            padding: 32px 28px;
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }
        .pkb-hero-banner::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.35) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .pkb-hero-badge-strip {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .pkb-pill-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #E2E8F0;
            backdrop-filter: blur(4px);
        }
        .pkb-pill-tag.is-rate {
            background: rgba(16, 185, 129, 0.18);
            border-color: rgba(16, 185, 129, 0.35);
            color: #6EE7B7;
        }
        .pkb-hero-title {
            font-size: clamp(1.5rem, 3.5vw, 2.1rem);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.02em;
            margin: 0 0 10px;
            color: #FFFFFF;
        }
        .pkb-hero-subtitle {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.55;
            max-width: 680px;
            margin: 0 0 18px;
        }
        .pkb-hero-post-preview {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            max-width: 720px;
        }
        .pkb-hero-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
            background: #4C1D95;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #FFFFFF;
        }
        .pkb-hero-snippet-text {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        /* ── GRID LAYOUT ── */
        .pkb-layout-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            margin-bottom: 40px;
        }
        @media (min-width: 992px) {
            .pkb-layout-grid {
                grid-template-columns: 1.45fr 1fr;
                align-items: start;
            }
        }

        /* ── STEP CARDS ── */
        .pkb-step-card {
            background: #FFFFFF;
            border: 1px solid var(--pk-boost-border);
            border-radius: 16px;
            padding: 22px 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .pkb-step-card:hover {
            border-color: #CBD5E1;
        }
        .pkb-step-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .pkb-step-badge-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .pkb-step-num {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--pk-boost-primary-subtle);
            color: var(--pk-boost-primary);
            border: 1px solid rgba(109, 40, 217, 0.2);
            font-size: 0.78rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pkb-step-head h3 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
            letter-spacing: -0.01em;
        }
        .pkb-step-head p {
            font-size: 0.8rem;
            color: #64748B;
            margin: 2px 0 0;
        }

        /* ── FORM ELEMENTS ── */
        .pkb-url-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        .pkb-url-icon {
            position: absolute;
            left: 14px;
            color: #94A3B8;
            font-size: 0.95rem;
            pointer-events: none;
        }
        .pkb-url-input {
            width: 100%;
            height: 48px;
            padding: 0 14px 0 42px;
            border-radius: 12px;
            border: 1.5px solid #CBD5E1;
            font-size: 0.92rem;
            color: #0F172A;
            background: #FFFFFF;
            transition: all 0.2s ease;
        }
        .pkb-url-input:focus {
            outline: none;
            border-color: var(--pk-boost-primary);
            box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.12);
        }

        /* CTA PILLS */
        .pkb-cta-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
            display: block;
        }
        .pkb-cta-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .pkb-cta-btn {
            padding: 8px 14px;
            border-radius: 999px;
            border: 1.5px solid #E2E8F0;
            background: #F8FAFC;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }
        .pkb-cta-btn:hover {
            border-color: #CBD5E1;
            background: #FFFFFF;
            color: #0F172A;
        }
        .pkb-cta-btn.is-active {
            border-color: var(--pk-boost-primary);
            background: var(--pk-boost-primary-subtle);
            color: var(--pk-boost-primary);
            box-shadow: 0 0 0 1px var(--pk-boost-primary) inset;
        }

        /* ── PLATFORM CARDS ── */
        .pkb-platform-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (max-width: 540px) {
            .pkb-platform-grid {
                grid-template-columns: 1fr;
            }
        }
        .pkb-platform-box {
            border: 1.5px solid #E2E8F0;
            border-radius: 14px;
            padding: 16px 16px;
            background: #FFFFFF;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            user-select: none;
            position: relative;
        }
        .pkb-platform-box:hover {
            border-color: #CBD5E1;
            background: #F8FAFC;
        }
        .pkb-platform-box.is-selected {
            border-color: var(--pk-boost-primary);
            background: #FAF5FF;
            box-shadow: 0 4px 14px rgba(109, 40, 217, 0.08);
        }
        .pkb-platform-check {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid #CBD5E1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
            background: #FFFFFF;
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 800;
            transition: all 0.15s ease;
        }
        .pkb-platform-box.is-selected .pkb-platform-check {
            background: var(--pk-boost-primary);
            border-color: var(--pk-boost-primary);
        }
        .pkb-platform-details strong {
            display: block;
            font-size: 0.92rem;
            color: #0F172A;
            font-weight: 800;
            margin-bottom: 2px;
        }
        .pkb-platform-details p {
            font-size: 0.76rem;
            color: #64748B;
            line-height: 1.4;
            margin: 0;
        }
        .pkb-platform-badge {
            display: inline-block;
            margin-top: 6px;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            background: #EDE9FE;
            color: #6D28D9;
        }

        /* ── PACKAGES GRID (3 PK / click) ── */
        .pkb-pricing-rate-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 10px;
            background: #F1F5F9;
            margin-bottom: 14px;
            font-size: 0.82rem;
            color: #334155;
        }
        .pkb-pricing-rate-strip strong {
            color: var(--pk-boost-primary);
            font-weight: 800;
        }
        .pkb-packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(125px, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }
        .pkb-pkg-card {
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            padding: 14px 10px;
            background: #FFFFFF;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }
        .pkb-pkg-card:hover {
            border-color: #CBD5E1;
            transform: translateY(-1px);
        }
        .pkb-pkg-card.is-active {
            border-color: var(--pk-boost-primary);
            background: #FAF5FF;
            box-shadow: 0 0 0 1px var(--pk-boost-primary) inset, 0 4px 12px rgba(109, 40, 217, 0.1);
        }
        .pkb-pkg-clicks {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.1;
        }
        .pkb-pkg-pk {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--pk-boost-primary);
            margin-top: 4px;
        }
        .pkb-pkg-naira {
            font-size: 0.72rem;
            color: #64748B;
            margin-top: 2px;
        }

        /* CUSTOM CLICKS FIELD */
        .pkb-custom-wrap {
            border: 1px dashed #CBD5E1;
            border-radius: 12px;
            padding: 12px 16px;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .pkb-custom-wrap label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin: 0;
        }
        .pkb-custom-input-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pkb-custom-field {
            width: 100px;
            height: 38px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1.5px solid #CBD5E1;
            text-align: center;
            font-weight: 700;
            font-size: 0.92rem;
            color: #0F172A;
            background: #FFFFFF;
        }
        .pkb-custom-field:focus {
            outline: none;
            border-color: var(--pk-boost-primary);
        }

        /* ── MONETIZATION NOTICE ── */
        .pkb-monetization-callout {
            border-radius: 12px;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.82rem;
            line-height: 1.45;
            color: #92400E;
            margin-bottom: 20px;
        }
        .pkb-monetization-callout strong {
            color: #78350F;
        }

        /* ── LAUNCH ACTION BAR ── */
        .pkb-action-bar {
            background: #FFFFFF;
            border: 1px solid var(--pk-boost-border);
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .pkb-cost-display strong {
            display: block;
            font-size: 1.5rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.1;
        }
        .pkb-cost-display span {
            font-size: 0.82rem;
            color: #64748B;
        }
        .pkb-launch-btn {
            background: linear-gradient(135deg, #6D28D9 0%, #7C3AED 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(109, 40, 217, 0.35);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .pkb-launch-btn:hover {
            background: linear-gradient(135deg, #5B21B6 0%, #6D28D9 100%);
            box-shadow: 0 6px 18px rgba(109, 40, 217, 0.45);
            transform: translateY(-1px);
        }
        .pkb-launch-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ── STICKY PREVIEW CARD ── */
        .pkb-sticky-panel {
            position: sticky;
            top: 24px;
        }
        .pkb-preview-card {
            background: #FFFFFF;
            border: 1px solid var(--pk-boost-border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
            margin-bottom: 20px;
        }
        .pkb-preview-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--pk-boost-border);
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pkb-preview-header h4 {
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .pkb-preview-badge {
            font-size: 0.72rem;
            font-weight: 700;
            color: #6D28D9;
            background: #EDE9FE;
            padding: 2px 8px;
            border-radius: 999px;
        }

        /* SOCIAL MOCKUP INTERIOR */
        .pkb-mockup-body {
            padding: 18px;
        }
        .pkb-mockup-author {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .pkb-mockup-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background: #6D28D9;
            color: #FFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.88rem;
        }
        .pkb-mockup-author-info strong {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.9rem;
            color: #0F172A;
        }
        .pkb-mockup-author-info span {
            font-size: 0.76rem;
            color: #64748B;
        }
        .pkb-sponsored-tag {
            font-size: 0.68rem;
            font-weight: 700;
            color: #B45309;
            background: #FEF3C7;
            padding: 1px 6px;
            border-radius: 4px;
            margin-left: 4px;
        }
        .pkb-mockup-text {
            font-size: 0.88rem;
            line-height: 1.45;
            color: #1E293B;
            margin-bottom: 14px;
        }
        .pkb-mockup-image-box {
            border-radius: 12px;
            overflow: hidden;
            background: #F1F5F9;
            max-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }
        .pkb-mockup-image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* SPONSORED CTA BAR IN MOCKUP */
        .pkb-mockup-cta-strip {
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            transition: all 0.15s ease;
        }
        .pkb-mockup-cta-strip:hover {
            border-color: #CBD5E1;
            background: #F1F5F9;
        }
        .pkb-mockup-url-hint {
            font-size: 0.75rem;
            color: #64748B;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 170px;
        }
        .pkb-mockup-action-btn {
            background: var(--pk-boost-primary);
            color: #FFFFFF;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 0.78rem;
            font-weight: 700;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 6px rgba(109, 40, 217, 0.25);
        }

        /* ── IMPACT STATS CARD ── */
        .pkb-impact-card {
            background: #FFFFFF;
            border: 1px solid var(--pk-boost-border);
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        }
        .pkb-impact-card h4 {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0F172A;
            margin: 0 0 14px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .pkb-impact-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #F1F5F9;
            font-size: 0.82rem;
        }
        .pkb-impact-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .pkb-impact-row span {
            color: #64748B;
        }
        .pkb-impact-row strong {
            color: #0F172A;
            font-weight: 700;
        }

        /* ── SUCCESS SCREEN ── */
        .pkb-success-wrap {
            background: #FFFFFF;
            border: 1px solid var(--pk-boost-border);
            border-radius: 20px;
            padding: 48px 32px;
            text-align: center;
            max-width: 640px;
            margin: 0 auto 40px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }
        .pkb-success-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #FAF5FF;
            color: var(--pk-boost-primary);
            font-size: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 16px rgba(109, 40, 217, 0.15);
        }
        .pkb-success-wrap h2 {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 10px;
        }
        .pkb-success-wrap p {
            font-size: 0.95rem;
            color: #64748B;
            line-height: 1.5;
            max-width: 480px;
            margin: 0 auto 24px;
        }
        .pkb-success-summary-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 16px 20px;
            text-align: left;
            margin-bottom: 28px;
            font-size: 0.85rem;
        }
        .pkb-success-summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            color: #334155;
        }
        .pkb-success-summary-row:not(:last-child) {
            border-bottom: 1px solid #EDF2F7;
        }
        .pkb-success-summary-row span {
            color: #64748B;
        }
        .pkb-success-btns {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .pkb-btn-secondary {
            background: #F1F5F9;
            color: #334155;
            border: 1px solid #CBD5E1;
            border-radius: 12px;
            padding: 12px 22px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .pkb-btn-secondary:hover {
            background: #E2E8F0;
            color: #0F172A;
        }
    </style>

    <div
        class="row"
        x-data="{
            step: 'configure',
            isSubmitting: false,
            serverError: '',
            targetUrl: '',
            selectedCta: 'Shop Now',
            selectedClicks: 100,
            customClicks: 100,
            isCustom: false,
            userSpendablePk: {{ $spendablePk }},
            platformWarning: false,

            platforms: {
                payhankey: true,
                partner: true
            },

            ctaOptions: [
                'Shop Now',
                'Learn More',
                'Order on WhatsApp',
                'Visit Website',
                'Sign Up',
                'Get Offer',
                'Contact Us',
                'Download'
            ],

            packages: [
                { clicks: 10 },
                { clicks: 25 },
                { clicks: 50 },
                { clicks: 100 },
                { clicks: 250 },
                { clicks: 500 },
                { clicks: 1000 },
                { clicks: 5000 }
            ],

            get ratePerClick() {
                return 3;
            },

            togglePlatform(key) {
                if (this.platforms[key]) {
                    const active = Object.values(this.platforms).filter(Boolean).length;
                    if (active <= 1) {
                        this.platformWarning = true;
                        setTimeout(() => this.platformWarning = false, 2500);
                        return;
                    }
                    this.platforms[key] = false;
                } else {
                    this.platforms[key] = true;
                    this.platformWarning = false;
                }
            },

            selectPackage(clicks) {
                this.isCustom = false;
                this.selectedClicks = clicks;
            },

            get totalClicks() {
                if (this.isCustom) {
                    return Math.max(100, parseInt(this.customClicks) || 100);
                }
                return this.selectedClicks;
            },

            get totalPkCost() {
                return this.totalClicks * this.ratePerClick;
            },

            get totalNairaCost() {
                return this.totalPkCost * 10;
            },

            get canAfford() {
                return this.userSpendablePk >= this.totalPkCost;
            },

            get deficit() {
                return Math.max(0, this.totalPkCost - this.userSpendablePk);
            },

            get platformText() {
                if (this.platforms.payhankey && this.platforms.partner) {
                    return 'Payhankey Feed & Partner Websites';
                } else if (this.platforms.payhankey) {
                    return 'Payhankey Feed Only';
                } else if (this.platforms.partner) {
                    return 'Partner Websites Only';
                }
                return 'Selected Networks';
            },

            get targetDomain() {
                try {
                    const trimmed = (this.targetUrl || '').trim();
                    if (!trimmed) return 'yourwebsite.com';
                    const url = new URL(trimmed.startsWith('http') ? trimmed : 'https://' + trimmed);
                    return url.hostname.replace('www.', '');
                } catch(e) {
                    return 'yourwebsite.com';
                }
            },

            async launchCampaign() {
                this.serverError = '';
                const trimmedUrl = (this.targetUrl || '').trim();
                if (!trimmedUrl) {
                    this.serverError = 'Please provide a destination URL (e.g. https://yourwebsite.com).';
                    return;
                }
                this.isSubmitting = true;

                try {
                    const payload = {
                        target_url: this.targetUrl,
                        cta: this.selectedCta,
                        clicks: this.totalClicks,
                        platforms: this.platforms
                    };

                    const res = await this.$wire.boostPost(payload);

                    if (res && res.status === 'success') {
                        const boostRecord = {
                            postId: '{{ $post->id }}',
                            boostId: res.boost?.id,
                            isBoosted: true,
                            clicks: this.totalClicks,
                            platforms: this.platforms,
                            platformText: this.platformText,
                            cta: this.selectedCta,
                            url: this.targetUrl,
                            pkCost: this.totalPkCost,
                            timestamp: Date.now()
                        };
                        sessionStorage.setItem('pk_boosted_{{ $post->id }}', JSON.stringify(boostRecord));
                        window.dispatchEvent(new CustomEvent('pk-post-boosted', { detail: boostRecord }));

                        this.step = 'success';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        this.serverError = res?.message || 'Failed to activate boost. Please check your balance and try again.';
                    }
                } catch (err) {
                    this.serverError = err.message || 'An unexpected error occurred while launching your campaign.';
                } finally {
                    this.isSubmitting = false;
                }
            }
        }"
    >
        <div class="col-12 ph-feed-wrap">

            <!-- Back Navigation -->
            <a href="{{ url('timeline/' . $post->id) }}" class="pkb-back-link" wire:navigate>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to post
            </a>

            <!-- HERO BANNER -->
            <div class="pkb-hero-banner">
                <div class="pkb-hero-badge-strip">
                    <span class="pkb-pill-tag">
                        <span>🚀</span> Payhankey Ad Studio
                    </span>
                    <span class="pkb-pill-tag is-rate">
                        <span>✓</span> 3 PayKoin (₦30) / Click
                    </span>
                    <span class="pkb-pill-tag">
                        <span>🎯</span> Guaranteed Delivery
                    </span>
                </div>

                <h1 class="pkb-hero-title">Supercharge Your Post's Reach & Traffic</h1>
                <p class="pkb-hero-subtitle">
                    Transform your post into an eye-catching sponsored advertisement across Payhankey and verified Partner Websites. Guaranteed results—only pay when real visitors click your link.
                </p>

                <!-- Post Snippet Context -->
                <div class="pkb-hero-post-preview">
                    @if ($authorAvatar)
                        <img src="{{ $authorAvatar }}" alt="{{ $authorName }}" class="pkb-hero-avatar">
                    @else
                        <div class="pkb-hero-avatar">{{ strtoupper(substr($authorName, 0, 1)) }}</div>
                    @endif
                    <div class="pkb-hero-snippet-text">
                        <strong>{{ $authorName }}</strong>:
                        {{ $postExcerpt ?: 'Media post' }}
                    </div>
                </div>
            </div>

            <!-- SUCCESS VIEW -->
            <template x-if="step === 'success'">
                <div class="pkb-success-wrap">
                    <div class="pkb-success-icon">🚀</div>
                    <h2>Campaign Successfully Launched!</h2>
                    <p>
                        Your post is now actively sponsored across <strong x-text="platformText"></strong>. Traffic and link clicks will start flowing immediately.
                    </p>

                    <div class="pkb-success-summary-box">
                        <div class="pkb-success-summary-row">
                            <span>Target Link:</span>
                            <strong style="word-break:break-all" x-text="targetUrl"></strong>
                        </div>
                        <div class="pkb-success-summary-row">
                            <span>Button Label:</span>
                            <strong x-text="selectedCta"></strong>
                        </div>
                        <div class="pkb-success-summary-row">
                            <span>Click Budget:</span>
                            <strong x-text="`${totalClicks.toLocaleString()} Guaranteed Clicks`"></strong>
                        </div>
                        <div class="pkb-success-summary-row">
                            <span>Cost:</span>
                            <strong style="color:var(--pk-boost-primary)" x-text="`${totalPkCost.toLocaleString()} PayKoin (₦${totalNairaCost.toLocaleString()})`"></strong>
                        </div>
                        <div class="pkb-success-summary-row">
                            <span>Campaign Status:</span>
                            <strong style="color:#059669">Active & Delivering</strong>
                        </div>
                    </div>

                    <div class="pkb-success-btns">
                        <a
                            href="{{ url('post/timeline/' . $post->id . '/analytics?tab=boost') }}"
                            class="pkb-launch-btn"
                            style="text-decoration:none"
                            wire:navigate
                        >
                            <span>View Live Analytics</span>
                            <span>→</span>
                        </a>
                        <a
                            href="{{ url('timeline') }}"
                            class="pkb-btn-secondary"
                            wire:navigate
                        >
                            Back to Timeline
                        </a>
                    </div>
                </div>
            </template>

            <!-- CONFIGURATION LAYOUT (2 Columns) -->
            <template x-if="step === 'configure'">
                <div class="pkb-layout-grid">

                    <!-- LEFT COLUMN: STUDIO CONFIGURATOR -->
                    <div>

                        <!-- STEP 1: DESTINATION & ACTION BUTTON -->
                        <div class="pkb-step-card">
                            <div class="pkb-step-head">
                                <div class="pkb-step-badge-title">
                                    <span class="pkb-step-num">1</span>
                                    <div>
                                        <h3>Target Link & Call-to-Action</h3>
                                        <p>Where users go when they click your post's action button</p>
                                    </div>
                                </div>
                            </div>

                            <div class="pkb-url-input-wrap">
                                <span class="pkb-url-icon">🔗</span>
                                <input
                                    type="url"
                                    class="pkb-url-input"
                                    placeholder="https://yourwebsite.com/product"
                                    x-model="targetUrl"
                                >
                            </div>

                            <span class="pkb-cta-label">Select Button Action (CTA)</span>
                            <div class="pkb-cta-grid">
                                <template x-for="cta in ctaOptions" :key="cta">
                                    <button
                                        type="button"
                                        class="pkb-cta-btn"
                                        :class="{ 'is-active': selectedCta === cta }"
                                        @click="selectedCta = cta"
                                        x-text="cta"
                                    ></button>
                                </template>
                            </div>
                        </div>

                        <!-- STEP 2: DUAL PLATFORM SELECTION -->
                        <div class="pkb-step-card">
                            <div class="pkb-step-head">
                                <div class="pkb-step-badge-title">
                                    <span class="pkb-step-num">2</span>
                                    <div>
                                        <h3>Advertising Networks</h3>
                                        <p>Choose where to distribute your sponsored post</p>
                                    </div>
                                </div>
                            </div>

                            <div class="pkb-platform-grid">
                                <!-- Payhankey Feed -->
                                <div
                                    class="pkb-platform-box"
                                    :class="{ 'is-selected': platforms.payhankey }"
                                    @click="togglePlatform('payhankey')"
                                >
                                    <div class="pkb-platform-check">
                                        <span x-show="platforms.payhankey">✓</span>
                                    </div>
                                    <div class="pkb-platform-details">
                                        <strong>Payhankey Feed</strong>
                                        <p>Native in-feed placement on creator timelines and discovery feeds.</p>
                                        <span class="pkb-platform-badge">🔥 Highly Active Audience</span>
                                    </div>
                                </div>

                                <!-- Partner Websites -->
                                <div
                                    class="pkb-platform-box"
                                    :class="{ 'is-selected': platforms.partner }"
                                    @click="togglePlatform('partner')"
                                >
                                    <div class="pkb-platform-check">
                                        <span x-show="platforms.partner">✓</span>
                                    </div>
                                    <div class="pkb-platform-details">
                                        <strong>Partner Websites</strong>
                                        <p>High-volume click placements across our external partner web network.</p>
                                        <span class="pkb-platform-badge" style="background:#ECFDF5;color:#059669">🌐 Guaranteed Traffic</span>
                                    </div>
                                </div>
                            </div>

                            <div x-show="platformWarning" style="font-size:0.75rem;color:#DC2626;margin-top:8px">
                                * At least one advertising platform must remain selected.
                            </div>
                        </div>

                        <!-- STEP 3: CLICK BUDGET & PACKAGES -->
                        <div class="pkb-step-card">
                            <div class="pkb-step-head">
                                <div class="pkb-step-badge-title">
                                    <span class="pkb-step-num">3</span>
                                    <div>
                                        <h3>Click Budget & Packages</h3>
                                        <p>Select your desired guaranteed clicks or enter custom amount</p>
                                    </div>
                                </div>
                            </div>

                            <div class="pkb-pricing-rate-strip">
                                <span>Fixed rate per click:</span>
                                <strong>3 PayKoin (₦30) / Click</strong>
                            </div>

                            <div class="pkb-packages-grid">
                                <template x-for="pkg in packages" :key="pkg.clicks">
                                    <div
                                        class="pkb-pkg-card"
                                        :class="{ 'is-active': !isCustom && selectedClicks === pkg.clicks }"
                                        @click="selectPackage(pkg.clicks)"
                                    >
                                        <div class="pkb-pkg-clicks" x-text="`${pkg.clicks.toLocaleString()} Clicks`"></div>
                                        <div class="pkb-pkg-pk" x-text="`${(pkg.clicks * 3).toLocaleString()} PK`"></div>
                                        <div class="pkb-pkg-naira" x-text="`₦${(pkg.clicks * 30).toLocaleString()}`"></div>
                                    </div>
                                </template>
                            </div>

                            <!-- Custom Clicks Option -->
                            <div class="pkb-custom-wrap">
                                <label for="pkbCustomClicks">Or enter custom clicks (min. 100):</label>
                                <div class="pkb-custom-input-group">
                                    <input
                                        id="pkbCustomClicks"
                                        type="number"
                                        min="100"
                                        max="50000"
                                        step="10"
                                        class="pkb-custom-field"
                                        x-model.number="customClicks"
                                        @focus="isCustom = true"
                                        @input="isCustom = true"
                                        @blur="if (!customClicks || customClicks < 100) customClicks = 100"
                                    >
                                    <span style="font-size:0.8rem;font-weight:700;color:var(--pk-boost-primary)">Clicks</span>
                                </div>
                            </div>
                        </div>



                        <!-- Error Callout -->
                        <div x-show="serverError" style="background:#FEF2F2;border:1px solid #FCA5A5;color:#B91C1C;padding:12px 16px;border-radius:12px;font-size:0.85rem;margin-bottom:14px;display:flex;align-items:center;gap:8px" x-cloak>
                            <span>⚠️</span>
                            <span x-text="serverError"></span>
                        </div>

                        <!-- STEP 5: ACTION & SUMMARY BAR -->
                        <div class="pkb-action-bar">
                            <div class="pkb-cost-display">
                                <strong x-text="`${totalPkCost.toLocaleString()} PayKoin`"></strong>
                                <span x-text="`₦${totalNairaCost.toLocaleString()} · ${totalClicks.toLocaleString()} guaranteed clicks`"></span>
                            </div>

                            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                                <div style="font-size:0.8rem;color:#64748B;text-align:right">
                                    <div>Balance: <strong x-text="`${userSpendablePk} PK`"></strong></div>
                                    <template x-if="totalPkCost > userSpendablePk">
                                        <a href="{{ url('wallets') }}" style="color:#DC2626;font-weight:700;text-decoration:underline" wire:navigate>
                                            Top up PayKoin
                                        </a>
                                    </template>
                                </div>

                                <button
                                    type="button"
                                    class="pkb-launch-btn"
                                    :disabled="isSubmitting || totalPkCost > userSpendablePk"
                                    @click="launchCampaign()"
                                >
                                    <span x-show="!isSubmitting">Launch Boost Campaign</span>
                                    <span x-show="isSubmitting">Activating Boost...</span>
                                    <span x-show="!isSubmitting">→</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: STICKY LIVE AD PREVIEW -->
                    <div class="pkb-sticky-panel">

                        <!-- LIVE AD PREVIEW CARD -->
                        <div class="pkb-preview-card">
                            <div class="pkb-preview-header">
                                <h4>Live Ad Simulation</h4>
                                <span class="pkb-preview-badge">Live Preview</span>
                            </div>

                            <div class="pkb-mockup-body">
                                <div class="pkb-mockup-author">
                                    @if ($authorAvatar)
                                        <img src="{{ $authorAvatar }}" alt="{{ $authorName }}" class="pkb-mockup-avatar">
                                    @else
                                        <div class="pkb-mockup-avatar">{{ strtoupper(substr($authorName, 0, 1)) }}</div>
                                    @endif
                                    <div class="pkb-mockup-author-info">
                                        <strong>
                                            {{ $authorName }}
                                            <span class="pkb-sponsored-tag">Sponsored</span>
                                        </strong>
                                        <span>&#64;{{ $authorUsername }} · Just now</span>
                                    </div>
                                </div>

                                <div class="pkb-mockup-text">
                                    {{ $postExcerpt ?: 'Check out our latest update!' }}
                                </div>

                                @if ($firstImage)
                                    <div class="pkb-mockup-image-box">
                                        <img src="{{ $firstImage }}" alt="Ad post image">
                                    </div>
                                @endif

                                <!-- SPONSORED CTA BAR -->
                                <div class="pkb-mockup-cta-strip">
                                    <div>
                                        <div style="font-size:0.7rem;font-weight:700;color:#94A3B8;text-transform:uppercase">Destination</div>
                                        <div class="pkb-mockup-url-hint" x-text="targetDomain"></div>
                                    </div>
                                    <div class="pkb-mockup-action-btn">
                                        <span x-text="selectedCta"></span>
                                        <span>↗</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PROJECTED CAMPAIGN IMPACT -->
                        <div class="pkb-impact-card">
                            <h4>Estimated Campaign Impact</h4>
                            <div class="pkb-impact-row">
                                <span>Guaranteed Clicks</span>
                                <strong style="color:var(--pk-boost-primary)" x-text="`${totalClicks.toLocaleString()} Clicks`"></strong>
                            </div>
                            <div class="pkb-impact-row">
                                <span>Est. Ad Impressions</span>
                                <strong x-text="`~${(totalClicks * 65).toLocaleString()} – ${(totalClicks * 110).toLocaleString()}`"></strong>
                            </div>
                            <div class="pkb-impact-row">
                                <span>Est. Click Rate (CTR)</span>
                                <strong>~2.8% – 4.2%</strong>
                            </div>
                            <div class="pkb-impact-row">
                                <span>Rate / Link Click</span>
                                <strong>3 PK (₦30)</strong>
                            </div>
                            <div class="pkb-impact-row">
                                <span>Target Networks</span>
                                <strong x-text="platformText"></strong>
                            </div>
                        </div>

                        <!-- TRUST POINTS -->
                        <div style="margin-top:16px;font-size:0.78rem;color:#64748B;line-height:1.6">
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                                <span style="color:#059669">✓</span> <strong>Anti-bot protection</strong>: Only verified human visits are counted.
                            </div>
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                                <span style="color:#059669">✓</span> <strong>Real-time analytics</strong>: Track clicks and CTR in your post analytics.
                            </div>
                            <div style="display:flex;align-items:center;gap:6px">
                                <span style="color:#059669">✓</span> <strong>Dual network synergy</strong>: Maximizes reach across social & web.
                            </div>
                        </div>

                    </div>

                </div>
            </template>

        </div>

        @if (userLevel() === 'Basic')
            @include('layouts.upgrade')
        @endif

        @if (auth()->user()->email_verified_at == null)
            @include('layouts.accesscode_verification')
        @else
            @include('layouts.onboarding')
        @endif

    </div>
</div>
