@once
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endonce

<style>
    .pk-app {
        --pk-violet: #5A4FDC;
        --pk-violet-dark: #4338CA;
        --pk-violet-soft: #EEECFC;
        --pk-mint: #1FAE64;
        --pk-mint-soft: #E6F7EE;
        --pk-gold: #E3A421;
        --pk-ink: #0F1117;
        --pk-muted: #64748B;
        --pk-line: #E2E8F0;
        --pk-bg: #F8FAFC;
        --pk-white: #FFFFFF;
        --pk-r: 14px;
        --pk-r-sm: 10px;
        --pk-shadow: 0 4px 24px rgba(15, 17, 23, .06);
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: var(--pk-ink);
    }

    .pk-app * { box-sizing: border-box; }

    .pk-app-hero {
        background: linear-gradient(135deg, #4B41C4 0%, #5A4FDC 50%, #7C3AED 100%);
        border-radius: var(--pk-r);
        padding: clamp(24px, 5vw, 36px);
        color: #fff;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .pk-app-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 15%, rgba(255,255,255,.12), transparent 45%);
        pointer-events: none;
    }

    .pk-app-hero-inner { position: relative; }

    .pk-app-kicker {
        display: inline-block;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        opacity: .85;
        margin-bottom: 8px;
    }

    .pk-app-hero h1 {
        font-size: clamp(1.35rem, 4vw, 1.85rem);
        font-weight: 800;
        margin: 0 0 8px;
        letter-spacing: -.02em;
    }

    .pk-app-hero p {
        margin: 0;
        font-size: .95rem;
        line-height: 1.55;
        color: rgba(255,255,255,.88);
        max-width: 620px;
    }

    .pk-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    @media (min-width: 768px) {
        .pk-stat-grid { grid-template-columns: repeat(4, 1fr); }
    }

    .pk-stat-card {
        background: var(--pk-white);
        border: 1px solid var(--pk-line);
        border-radius: var(--pk-r);
        padding: 16px;
        box-shadow: var(--pk-shadow);
    }

    .pk-stat-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 1rem;
        margin-bottom: 10px;
    }

    .pk-stat-card-value {
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.1;
        margin: 0 0 4px;
    }

    .pk-stat-card-label {
        font-size: .78rem;
        color: var(--pk-muted);
        font-weight: 600;
        margin: 0;
        line-height: 1.35;
    }

    .pk-panel {
        background: var(--pk-white);
        border: 1px solid var(--pk-line);
        border-radius: var(--pk-r);
        box-shadow: var(--pk-shadow);
        overflow: hidden;
        margin-bottom: 16px;
    }

    .pk-panel-head {
        padding: 16px 18px;
        border-bottom: 1px solid var(--pk-line);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .pk-panel-head h2 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
    }

    .pk-panel-body { padding: 18px; }

    .pk-alert {
        padding: 12px 14px;
        border-radius: var(--pk-r-sm);
        font-size: .875rem;
        font-weight: 600;
        margin-bottom: 16px;
        line-height: 1.5;
    }

    .pk-alert--success { background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; }
    .pk-alert--info { background: var(--pk-violet-soft); color: var(--pk-violet-dark); border: 1px solid #C7D2FE; }
    .pk-alert--warn { background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; }
    .pk-alert--error { background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; }

    .pk-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 38px;
        padding: 0 16px;
        border-radius: var(--pk-r-sm);
        border: none;
        font-family: inherit;
        font-size: .875rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all .15s ease;
    }

    .pk-btn--primary {
        background: linear-gradient(135deg, var(--pk-violet), var(--pk-violet-dark));
        color: #fff;
        box-shadow: 0 6px 16px rgba(90,79,220,.25);
    }

    .pk-btn--primary:hover { color: #fff; transform: translateY(-1px); }

    .pk-btn--ghost {
        background: #fff;
        color: var(--pk-ink);
        border: 1.5px solid var(--pk-line);
    }

    .pk-btn--ghost:hover { background: var(--pk-bg); color: var(--pk-ink); }

    .pk-field { margin-bottom: 16px; }

    .pk-label {
        display: block;
        margin-bottom: 6px;
        font-size: .875rem;
        font-weight: 600;
    }

    .pk-input, .pk-select, .pk-textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid var(--pk-line);
        border-radius: var(--pk-r-sm);
        font-family: inherit;
        font-size: .9rem;
        background: #fff;
    }

    .pk-input:focus, .pk-select:focus, .pk-textarea:focus {
        outline: none;
        border-color: var(--pk-violet);
        box-shadow: 0 0 0 3px rgba(90,79,220,.12);
    }

    .pk-input:read-only, .pk-input:disabled {
        background: var(--pk-bg);
        color: var(--pk-muted);
    }

    .pk-error { display: block; margin-top: 6px; font-size: .8rem; color: #B91C1C; }
    .pk-hint { display: block; margin-top: 6px; font-size: .8rem; color: var(--pk-muted); }

    .pk-table-wrap { overflow-x: auto; }

    .pk-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }

    .pk-table th {
        text-align: left;
        padding: 12px 14px;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--pk-muted);
        border-bottom: 1px solid var(--pk-line);
        background: var(--pk-bg);
    }

    .pk-table td {
        padding: 14px;
        border-bottom: 1px solid var(--pk-line);
        vertical-align: middle;
    }

    .pk-table tr:last-child td { border-bottom: none; }

    .pk-user-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pk-user-row img, .pk-user-row .pk-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        background: var(--pk-violet-soft);
    }

    .pk-user-row b { display: block; font-size: .9rem; }
    .pk-user-row small { color: var(--pk-muted); }

    .pk-empty {
        text-align: center;
        padding: 48px 20px;
        color: var(--pk-muted);
    }

    .pk-empty h3 { color: var(--pk-ink); margin: 0 0 8px; font-size: 1.1rem; }

    .pk-copy-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        padding: 12px 14px;
        border-radius: var(--pk-r-sm);
        background: var(--pk-bg);
        border: 1px dashed #CBD5E1;
        margin-bottom: 16px;
    }

    .pk-copy-bar code {
        flex: 1;
        min-width: 0;
        word-break: break-all;
        font-size: .82rem;
        color: var(--pk-ink);
        background: transparent;
    }

    .pk-steps { display: grid; gap: 12px; }

    .pk-step {
        display: flex;
        gap: 14px;
        padding: 16px;
        border-radius: var(--pk-r);
        background: var(--pk-bg);
        border: 1px solid var(--pk-line);
    }

    .pk-step-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--pk-violet-soft);
        color: var(--pk-violet-dark);
        font-weight: 800;
        font-size: .85rem;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .pk-step h3 { margin: 0 0 4px; font-size: .95rem; }
    .pk-step p { margin: 0; font-size: .875rem; color: var(--pk-muted); line-height: 1.55; }

    .pk-prose { font-size: .925rem; line-height: 1.7; color: #334155; }
    .pk-prose h3 { font-size: 1rem; margin: 20px 0 8px; color: var(--pk-ink); }
    .pk-prose p { margin: 0 0 12px; }
    .pk-prose ul { margin: 0 0 12px; padding-left: 20px; }

    .pk-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 18px;
        border-top: 1px solid var(--pk-line);
        flex-wrap: wrap;
    }

    .pk-pg-info { font-size: .8rem; color: var(--pk-muted); }

    .pk-pg-btns { display: flex; gap: 6px; }

    .pk-pg-btn {
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 8px;
        border: 1px solid var(--pk-line);
        background: #fff;
        font-family: inherit;
        font-size: .82rem;
        font-weight: 700;
        cursor: pointer;
    }

    .pk-pg-btn:disabled { opacity: .45; cursor: not-allowed; }

    .pk-detail-list { list-style: none; margin: 0; padding: 0; }

    .pk-detail-list li {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--pk-line);
        font-size: .875rem;
    }

    .pk-detail-list li:last-child { border-bottom: none; }
    .pk-detail-list span { color: var(--pk-muted); }
    .pk-detail-list b { text-align: right; }

    /* Blog cards (internal user/blog) */
    .pk-blog-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .pk-blog-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pk-blog-chip {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1.5px solid var(--pk-line);
        background: #fff;
        font-family: inherit;
        font-size: .8rem;
        font-weight: 700;
        color: var(--pk-muted);
        cursor: pointer;
        transition: all .15s ease;
    }

    .pk-blog-chip:hover { border-color: #C7D2FE; color: var(--pk-violet-dark); }
    .pk-blog-chip.is-active {
        background: var(--pk-violet-soft);
        border-color: #C7D2FE;
        color: var(--pk-violet-dark);
    }

    .pk-blog-search {
        position: relative;
        min-width: min(100%, 260px);
        flex: 1;
        max-width: 320px;
    }

    .pk-blog-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--pk-muted);
        font-size: .85rem;
        pointer-events: none;
    }

    .pk-blog-search input {
        width: 100%;
        padding: 10px 12px 10px 36px;
        border: 1px solid var(--pk-line);
        border-radius: var(--pk-r-sm);
        font-family: inherit;
        font-size: .875rem;
        background: #fff;
    }

    .pk-blog-search input:focus {
        outline: none;
        border-color: var(--pk-violet);
        box-shadow: 0 0 0 3px rgba(90,79,220,.12);
    }

    .pk-blog-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    @media (min-width: 640px) {
        .pk-blog-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (min-width: 1024px) {
        .pk-blog-grid { grid-template-columns: repeat(3, 1fr); }
    }

    .pk-blog-card {
        background: var(--pk-white);
        border: 1px solid var(--pk-line);
        border-radius: var(--pk-r);
        overflow: hidden;
        box-shadow: var(--pk-shadow);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .pk-blog-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(15,17,23,.08);
    }

    .pk-blog-card-link {
        display: block;
        text-decoration: none;
        color: inherit;
        height: 100%;
    }

    .pk-blog-card-cover {
        height: 168px;
        background: linear-gradient(135deg, var(--pk-violet), #7C3AED);
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .pk-blog-card-cat {
        position: absolute;
        left: 12px;
        bottom: 12px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(255,255,255,.92);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--pk-violet-dark);
    }

    .pk-blog-card-body { padding: 16px; }

    .pk-blog-card-body h3 {
        margin: 0 0 8px;
        font-size: .98rem;
        font-weight: 800;
        line-height: 1.35;
        color: var(--pk-ink);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .pk-blog-card-body p {
        margin: 0 0 12px;
        font-size: .84rem;
        line-height: 1.55;
        color: var(--pk-muted);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .pk-blog-card-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        font-size: .75rem;
        font-weight: 600;
        color: var(--pk-muted);
    }

    .pk-blog-card-avatar {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--pk-violet), var(--pk-violet-dark));
        color: #fff;
        font-size: .58rem;
        font-weight: 800;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    /* User search (search/user) */
    .pk-search-page { margin-bottom: 20px; }

    .pk-search-head {
        margin-bottom: 20px;
    }

    .pk-search-head h1 {
        margin: 0 0 6px;
        font-size: clamp(1.35rem, 3vw, 1.65rem);
        font-weight: 800;
        letter-spacing: -.02em;
        color: var(--pk-ink);
    }

    .pk-search-head p {
        margin: 0;
        font-size: .9rem;
        color: var(--pk-muted);
        line-height: 1.5;
    }

    .pk-search-bar {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--pk-white);
        border: 1px solid var(--pk-line);
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(15, 17, 23, .04);
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .pk-search-bar:focus-within {
        border-color: #CBD5E1;
        box-shadow: 0 4px 20px rgba(15, 17, 23, .07);
    }

    .pk-search-bar > i {
        position: absolute;
        left: 18px;
        color: #94A3B8;
        font-size: .95rem;
        pointer-events: none;
    }

    .pk-search-bar input {
        flex: 1;
        width: 100%;
        padding: 16px 48px 16px 48px;
        border: none;
        background: transparent;
        font-family: inherit;
        font-size: 1rem;
        color: var(--pk-ink);
    }

    .pk-search-bar input::placeholder { color: #94A3B8; }

    .pk-search-bar input:focus { outline: none; }

    .pk-search-clear {
        position: absolute;
        right: 12px;
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 50%;
        background: var(--pk-bg);
        color: var(--pk-muted);
        cursor: pointer;
        display: grid;
        place-items: center;
        transition: background .15s ease, color .15s ease;
    }

    .pk-search-clear:hover {
        background: #E2E8F0;
        color: var(--pk-ink);
    }

    .pk-search-meta {
        margin-top: 10px;
        font-size: .82rem;
        color: var(--pk-muted);
        min-height: 20px;
    }

    .pk-search-hints {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 14px;
    }

    .pk-search-hints-label {
        font-size: .78rem;
        color: var(--pk-muted);
        font-weight: 600;
    }

    .pk-search-hint {
        padding: 6px 14px;
        border-radius: 999px;
        border: 1px solid var(--pk-line);
        background: var(--pk-white);
        color: var(--pk-ink);
        font-size: .78rem;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: background .15s ease, border-color .15s ease;
    }

    .pk-search-hint:hover {
        background: var(--pk-bg);
        border-color: #CBD5E1;
    }

    .pk-search-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--pk-line);
        transition: background .15s ease;
    }

    .pk-search-row:last-child { border-bottom: none; }
    .pk-search-row:hover { background: #FAFBFC; }

    .pk-search-row-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        background: #F1F5F9;
    }

    .pk-search-row-body {
        flex: 1;
        min-width: 0;
    }

    .pk-search-row-name {
        display: block;
        font-size: .92rem;
        font-weight: 700;
        color: var(--pk-ink);
        text-decoration: none;
        line-height: 1.25;
    }

    .pk-search-row-name:hover { text-decoration: underline; }

    .pk-search-row-handle {
        display: block;
        font-size: .84rem;
        color: var(--pk-muted);
        text-decoration: none;
        margin-top: 1px;
    }

    .pk-search-row-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 4px;
        font-size: .76rem;
        font-weight: 500;
        color: #94A3B8;
    }

    .pk-follow-btn {
        flex-shrink: 0;
        min-width: 92px;
        height: 34px;
        padding: 0 16px;
        border-radius: 999px;
        border: 1px solid var(--pk-ink);
        background: var(--pk-ink);
        color: #fff;
        font-family: inherit;
        font-size: .8rem;
        font-weight: 700;
        cursor: pointer;
        transition: opacity .15s ease, background .15s ease;
    }

    .pk-follow-btn:hover { opacity: .88; }

    .pk-follow-btn--active {
        background: #fff;
        color: var(--pk-ink);
        border-color: var(--pk-line);
    }

    .pk-follow-btn--active:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
        opacity: 1;
    }

    @media (max-width: 640px) {
        .pk-search-row {
            flex-wrap: wrap;
            padding: 14px 16px;
        }

        .pk-follow-btn {
            width: 100%;
            margin-left: 62px;
        }
    }
</style>
