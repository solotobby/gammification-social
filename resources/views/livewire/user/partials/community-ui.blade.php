@once
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@endonce

<style>
    /* ── Shared community UI (list + details + feed) ─────────────── */
    .communities-page,
    .community-show-page {
        --pk-violet: #5A4FDC;
        --pk-violet-dark: #4338CA;
        --pk-violet-tint: rgba(90, 79, 220, .10);
        --pk-violet-glow: rgba(90, 79, 220, .28);
        --pk-mint: #1FAE64;
        --pk-mint-tint: rgba(31, 174, 100, .12);
        --pk-mint-line: #CBEBDA;
        --pk-gold: #E3A421;
        --pk-red: #EF4444;
        --pk-red-tint: #FDECEC;
        --pk-ink: #0F1117;
        --pk-gray-700: #3D4254;
        --pk-gray-500: #8B90A5;
        --pk-gray-400: #AEB2C2;
        --pk-line: rgba(15, 17, 23, .08);
        --pk-line-strong: rgba(15, 17, 23, .12);
        --pk-r-sm: 10px;
        --pk-r-md: 14px;
        --pk-r-lg: 18px;
        --pk-r-pill: 999px;
        --pk-shadow: 0 1px 2px rgba(15, 17, 23, .04);
        --pk-shadow-md: 0 8px 32px rgba(15, 17, 23, .07), 0 2px 8px rgba(15, 17, 23, .04);
        --pk-shadow-lg: 0 20px 48px rgba(15, 17, 23, .09);
        --pk-fb-bg: #F0F2F5;
        --pk-x-line: #eff3f4;
        font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;
        color: var(--pk-ink);
        position: relative;
        -webkit-font-smoothing: antialiased;
    }

    .pk-ui-bg {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(ellipse 60% 50% at 5% 0%, rgba(90, 79, 220, .08), transparent 55%),
            radial-gradient(ellipse 50% 40% at 95% 15%, rgba(31, 174, 100, .06), transparent 50%);
    }

    .pk-ui-inner { position: relative; z-index: 1; }

    /* Cards */
    .communities-page .pk-card,
    .community-show-page .pk-card {
        border-color: var(--pk-line);
        box-shadow: var(--pk-shadow-md);
        border-radius: var(--pk-r-lg);
    }

    /* Banner (list page) */
    .communities-page .pk-banner {
        background: linear-gradient(135deg, rgba(90,79,220,.12) 0%, rgba(31,174,100,.08) 100%);
        border: 1px solid rgba(90,79,220,.15);
    }
    .communities-page .pk-banner-ic {
        border-radius: 14px;
        background: linear-gradient(135deg, var(--pk-violet), var(--pk-violet-dark));
        box-shadow: 0 4px 16px var(--pk-violet-glow);
    }

    /* Buttons */
    .communities-page .pk-btn-violet,
    .community-show-page .pk-btn-violet {
        background: linear-gradient(135deg, var(--pk-violet), var(--pk-violet-dark));
        box-shadow: 0 4px 14px var(--pk-violet-glow);
        border-radius: var(--pk-r-md);
    }

    .communities-page .pk-btn-outline,
    .community-show-page .pk-btn-outline {
        border-radius: var(--pk-r-md);
    }

    /* Search + chips */
    .communities-page .pk-search-row,
    .community-show-page .pk-search-row {
        background: #fff;
        border: 1px solid var(--pk-line);
        box-shadow: var(--pk-shadow);
    }
    .communities-page .pk-search-row:focus-within,
    .community-show-page .pk-search-row:focus-within {
        border-color: rgba(90,79,220,.35);
        box-shadow: 0 0 0 3px var(--pk-violet-tint);
    }
    .communities-page .pk-f-chip.pk-sel,
    .community-show-page .pk-f-chip.pk-sel {
        box-shadow: 0 2px 8px var(--pk-violet-glow);
    }

    /* ── Details: Hero ───────────────────────────────────────────── */
    .community-show-page .pk-hero {
        overflow: visible;
    }
    .community-show-page .pk-hero-banner {
        height: clamp(140px, 28vw, 200px);
        border-radius: var(--pk-r-lg) var(--pk-r-lg) 0 0;
    }
    .community-show-page .pk-hero-banner::after {
        background: linear-gradient(to bottom, transparent 30%, rgba(255,255,255,.15) 100%);
        opacity: 1;
    }
    .community-show-page .pk-hero-logo {
        border-radius: 22px;
    }
    .community-show-page .pk-hero-name {
        font-size: clamp(1.25rem, 3.5vw, 1.55rem);
        letter-spacing: -.03em;
    }
    .community-show-page .pk-hero-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid var(--pk-line);
    }
    .community-show-page .pk-hero-stat b {
        display: block;
        font-family: 'DM Mono', ui-monospace, monospace;
        font-size: 1.05rem;
        font-weight: 500;
        line-height: 1.2;
    }
    .community-show-page .pk-hero-stat span {
        font-size: .72rem;
        color: var(--pk-gray-500);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    /* Tabs — sticky */
    .community-show-page .pk-tabs-wrap {
        position: sticky;
        top: 0;
        z-index: 20;
        background: rgba(248,249,252,.92);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        margin: 0 -4px 18px;
        padding: 0 4px;
        border-bottom: 1px solid var(--pk-line);
    }
    .community-show-page .pk-tabs { margin-bottom: 0; border-bottom: none; }
    .community-show-page .pk-tab.pk-sel {
        color: var(--pk-violet);
        border-bottom-color: transparent;
    }
    .community-show-page .pk-tab.pk-sel::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 16px;
        right: 16px;
        height: 3px;
        background: var(--pk-violet);
        border-radius: 3px 3px 0 0;
    }
    .community-show-page .pk-tab {
        position: relative;
    }

    /* Composer */
    .community-show-page .pk-composer {
        padding: 16px 18px;
        margin-bottom: 14px;
    }
    .community-show-page .pk-comp-field {
        background: var(--pk-fb-bg);
        border-radius: 24px;
        padding: 12px 18px;
    }
    .community-show-page .pk-comp-field:focus-within {
        background: #fff;
        box-shadow: 0 0 0 2px var(--pk-violet-tint), var(--pk-shadow-md);
    }
    .community-show-page .pk-ph-av {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: .85rem;
    }
    .community-show-page .pk-comp-bar {
        border-top: none;
        margin-top: 10px;
        padding-top: 0;
    }
    .community-show-page .pk-comp-tool:hover {
        background: var(--pk-violet-tint);
        color: var(--pk-violet);
    }

    /* Feed posts */
    .community-show-page .pk-card.pk-standalone {
        border-radius: var(--pk-r-lg);
        margin-bottom: 14px;
        border: 1px solid var(--pk-x-line);
        box-shadow: var(--pk-shadow-md);
        overflow: hidden;
    }

    .community-show-page .pk-header {
        padding: 14px 16px 0;
    }
    .community-show-page .pk-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(15,17,23,.10);
    }
    .community-show-page .pk-name {
        font-family: 'Instrument Sans', sans-serif;
        font-weight: 700;
        font-size: 15px;
        color: var(--pk-ink);
    }
    .community-show-page .pk-handle,
    .community-show-page .pk-time {
        font-size: 13px;
        color: var(--pk-gray-500);
    }
    .community-show-page .pk-body {
        padding: 8px 16px 0 72px;
    }
    .community-show-page .pk-texth,
    .community-show-page .pk-text {
        font-size: 15px;
        line-height: 1.55;
        color: var(--pk-ink);
    }

    .community-show-page .pk-media {
        margin: 12px 16px 0;
        border-radius: var(--pk-r-md);
        border: 1px solid var(--pk-x-line);
        overflow: hidden;
    }

    .community-show-page .pk-actions {
        padding: 4px 12px 6px 56px;
        border-top: 1px solid var(--pk-x-line);
        margin-top: 10px;
    }
    .community-show-page .pk-action {
        font-weight: 500;
        font-size: 13px;
        border-radius: var(--pk-r-pill);
    }
    .community-show-page .pk-action.pk-liked {
        color: #f91880;
    }
    .community-show-page .pk-action.pk-liked svg {
        fill: #f91880;
        stroke: #f91880;
    }
    .community-show-page .pk-action.pk-like:hover {
        color: #f91880;
        background: rgba(249,24,128,.08);
    }

    /* Comments */
    .community-show-page .pk-comments {
        padding: 12px 16px 14px;
        background: var(--pk-fb-bg);
        border-top: 1px solid var(--pk-x-line);
    }
    .community-show-page .pk-fb-comment {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }
    .community-show-page .pk-fb-comment-av {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .community-show-page .pk-fb-comment-bubble {
        background: #fff;
        border-radius: 18px;
        padding: 8px 14px;
        box-shadow: 0 1px 2px rgba(15,17,23,.06);
        flex: 1;
        min-width: 0;
    }
    .community-show-page .pk-fb-comment-name {
        font-weight: 700;
        font-size: 13px;
        color: var(--pk-ink);
        text-decoration: none;
    }
    .community-show-page .pk-fb-comment-name:hover { text-decoration: underline; }
    .community-show-page .pk-fb-comment-time {
        font-size: 11px;
        color: var(--pk-gray-500);
        margin-left: 6px;
    }
    .community-show-page .pk-fb-comment-text {
        font-size: 14px;
        color: var(--pk-gray-700);
        margin: 2px 0 0;
        line-height: 1.45;
        word-break: break-word;
    }
    .community-show-page .pk-comment-input-row input {
        background: #fff;
        border: none;
        box-shadow: 0 1px 2px rgba(15,17,23,.06);
        padding: 10px 16px;
        border-radius: 20px;
        font-size: .84rem;
    }
    .community-show-page .pk-comment-input-row input:focus {
        box-shadow: 0 0 0 2px var(--pk-violet-tint), 0 1px 4px rgba(15,17,23,.08);
    }

    /* Share menu */
    .community-show-page .pk-share-menu {
        border-radius: var(--pk-r-md);
        box-shadow: var(--pk-shadow-lg);
        border: 1px solid var(--pk-line);
    }
    .community-show-page .pk-icon-btn {
        border-radius: 12px;
    }

    /* Empty states */
    .communities-page .pk-empty,
    .community-show-page .pk-empty {
        border-radius: var(--pk-r-lg);
        background: linear-gradient(180deg, #fff, var(--pk-fb-bg));
    }

    .community-show-page .pk-earn {
        font-family: 'DM Mono', ui-monospace, monospace;
        font-size: 12px;
        border-radius: 20px;
    }

    .community-show-page .pk-stat-val {
        font-family: 'DM Mono', ui-monospace, monospace;
    }

    .communities-page .pk-sec-head h3 {
        letter-spacing: -.02em;
    }

    @media (max-width: 575.98px) {
        .community-show-page .pk-hero-stats {
            justify-content: center;
        }
    }
</style>
