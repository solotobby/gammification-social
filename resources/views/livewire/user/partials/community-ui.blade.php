@once
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@endonce

<style>
    /* ── Shared community UI (list + details + feed) ─────────────── */
    .communities-page,
    .community-show-page,
    #createCommunityModal {
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

    .pk-ui-inner {
        position: relative;
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0 0 max(24px, env(safe-area-inset-bottom)) 0;
    }

    @media (min-width: 768px) {
        .pk-ui-inner {
            padding-bottom: 32px;
        }
    }

    /* Global loading bar for Livewire requests */
    .community-show-page .pk-page-loading-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        z-index: 9999;
        background: linear-gradient(90deg, transparent, var(--pk-violet), var(--pk-mint), transparent);
        background-size: 200% 100%;
        animation: pk-page-load 0.9s linear infinite;
        pointer-events: none;
    }

    @keyframes pk-page-load {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .community-show-page form.is-saving,
    #createCommunityModal form.is-saving {
        opacity: .72;
        pointer-events: none;
        transition: opacity .18s ease;
    }

    .community-show-page .pk-tab-panel {
        animation: pk-panel-in .22s ease;
    }

    @keyframes pk-panel-in {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: none; }
    }

    .community-show-page .pk-tabs {
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        scroll-snap-type: x proximity;
    }

    .community-show-page .pk-tabs::-webkit-scrollbar {
        display: none;
    }

    .community-show-page .pk-tab {
        scroll-snap-align: start;
        white-space: nowrap;
        touch-action: manipulation;
    }

    @media (max-width: 575.98px) {
        .community-show-page .pk-settings-section {
            padding: 16px 14px !important;
        }

        .community-show-page .pk-field input,
        .community-show-page .pk-field textarea,
        .community-show-page .pk-field select,
        .communities-page .pk-field input,
        .communities-page .pk-field textarea,
        .communities-page .pk-field select,
        #createCommunityModal .pk-field input,
        #createCommunityModal .pk-field textarea,
        #createCommunityModal .pk-field select {
            font-size: 16px;
        }

        .community-show-page .pk-btn,
        #createCommunityModal .pk-btn {
            min-height: 44px;
        }

        .community-show-page .pk-status-opt,
        #createCommunityModal .pk-status-opt {
            padding: 14px 12px;
        }
    }

    .community-show-page .pk-field.is-invalid input,
    .community-show-page .pk-field.is-invalid select,
    .community-show-page .pk-field.is-invalid textarea,
    .communities-page .pk-field.is-invalid input,
    .communities-page .pk-field.is-invalid select,
    .communities-page .pk-field.is-invalid textarea,
    #createCommunityModal .pk-field.is-invalid input,
    #createCommunityModal .pk-field.is-invalid select,
    #createCommunityModal .pk-field.is-invalid textarea {
        border-color: var(--pk-red);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, .12);
    }

    .community-show-page .pk-settings-footer {
        z-index: 30;
        margin-top: 8px;
        padding: 12px 0 max(12px, env(safe-area-inset-bottom));
        background: rgba(255, 255, 255, .94);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-top: 1px solid var(--pk-line);
        box-shadow: 0 -6px 24px rgba(15, 17, 23, .06);
    }

    .community-show-page .pk-btn .pk-spinner,
    #createCommunityModal .pk-btn .pk-spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, .35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: pk-spin .65s linear infinite;
        vertical-align: -2px;
        margin-right: 6px;
    }

    @keyframes pk-spin {
        to { transform: rotate(360deg); }
    }

    @media (prefers-reduced-motion: reduce) {
        .community-show-page .pk-tab-panel,
        .community-show-page .pk-price-field,
        .community-show-page .pk-page-loading-bar {
            animation: none;
        }
    }

    .community-show-page [x-cloak] { display: none !important; }

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

    /* ── Details: Facebook-style hero ─────────────────────────── */
    .community-show-page .pk-hero {
        overflow: hidden;
        margin-bottom: 12px;
    }

    .community-show-page .pk-tabs-bar {
        margin-bottom: 16px;
        padding: 0;
        overflow: hidden;
    }

    .community-show-page .pk-tabs-bar .pk-tabs-wrap {
        padding: 0 clamp(12px, 2vw, 18px);
        background: #fff;
    }

    .community-show-page .pk-tabs-bar .pk-tabs {
        margin-bottom: 0;
        border-bottom: 1px solid var(--pk-line);
    }

    .community-show-page .pk-tabs-bar .pk-tab {
        position: relative;
        padding: 14px 16px;
        font-size: .9375rem;
        font-weight: 600;
        color: var(--pk-gray-500);
    }

    .community-show-page .pk-tabs-bar .pk-tab:hover {
        color: var(--pk-ink);
        background: rgba(15, 17, 23, .03);
    }

    .community-show-page .pk-tabs-bar .pk-tab.pk-sel {
        color: var(--pk-violet);
    }

    .community-show-page .pk-tabs-bar .pk-tab.pk-sel::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 12px;
        right: 12px;
        height: 3px;
        background: var(--pk-violet);
        border-radius: 3px 3px 0 0;
    }

    .community-show-page .pk-feed-stack {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .community-show-page .pk-feed-post {
        border-radius: var(--pk-r-lg);
        overflow: hidden;
        margin-bottom: 0;
    }

    .community-show-page .pk-text a,
    .community-show-page .pk-text .pk-tag,
    .community-show-page .pk-text .pk-mention {
        color: #1d9bf0;
        text-decoration: none;
    }

    .community-show-page .pk-text a:hover,
    .community-show-page .pk-text .pk-tag:hover,
    .community-show-page .pk-text .pk-mention:hover {
        text-decoration: underline;
    }

    .community-show-page .pk-see-more {
        display: inline;
        background: none;
        border: none;
        padding: 0;
        margin: 0 0 0 6px;
        color: #5A4FDC;
        font-size: inherit;
        font-weight: 700;
        line-height: inherit;
        cursor: pointer;
        font-family: inherit;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .community-show-page .pk-see-more:hover {
        color: #4338ca;
    }

    .community-show-page .pk-link-preview {
        margin: 0;
        border-radius: 0;
        border-left: none;
        border-right: none;
    }

    .community-show-page [x-cloak] {
        display: none !important;
    }

    /* legacy: tabs inside hero (unused) */
    .community-show-page .pk-hero .pk-tabs-wrap {
        position: sticky;
        top: 0;
        z-index: 20;
        margin: 0;
        padding: 0 clamp(8px, 2vw, 16px);
        background: #fff;
        border-top: 1px solid var(--pk-line);
        border-radius: 0 0 var(--pk-r-lg) var(--pk-r-lg);
    }

    .community-show-page .pk-hero .pk-tabs {
        margin-bottom: 0;
        border-bottom: none;
    }

    .community-show-page .pk-hero .pk-tab {
        padding: 14px 16px;
        font-size: .9375rem;
        font-weight: 600;
        color: var(--pk-gray-500);
    }

    .community-show-page .pk-hero .pk-tab:hover {
        background: rgba(15, 17, 23, .04);
        border-radius: 8px 8px 0 0;
    }

    .community-show-page .pk-hero .pk-tab.pk-sel {
        color: var(--pk-violet);
    }

    .community-show-page .pk-hero .pk-tab.pk-sel::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 12px;
        right: 12px;
        height: 3px;
        background: var(--pk-violet);
        border-radius: 3px 3px 0 0;
    }

    .community-show-page .pk-hero .pk-tab {
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
