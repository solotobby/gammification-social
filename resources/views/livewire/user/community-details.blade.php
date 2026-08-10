<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}

    @include('livewire.user.partials.community-ui')

    <div class="community-show-page" x-data="{ tab: 'feed', shareOpen: false }"
        @settings-saved.window="tab = 'feed'"
        @keydown.escape.window="shareOpen = false">

        <div class="pk-ui-bg" aria-hidden="true"></div>
        <div class="pk-ui-inner">

        @verbatim

            <style>
                /* ── Reset / scope ──────────────────────────────────────── */
                .pk-card *,
                .pk-card *::before,
                .pk-card *::after {
                    box-sizing: border-box;
                }

                /* ── Card shell ─────────────────────────────────────────── */
                .pk-card {
                    background: #fff;
                    border: 1px solid #eff3f4;
                    border-radius: 0;
                    /* X-style: no radius on feed cards */
                    margin-bottom: 1px;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                }

                .pk-card+.pk-card {
                    border-top: none;
                }

                /* Round only when it's a standalone detail card */
                .pk-card.pk-standalone {
                    border-radius: 12px;
                    margin-bottom: 12px;
                    border: 1px solid #eff3f4;
                }

                /* ── Header ─────────────────────────────────────────────── */
                .pk-header {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                    padding: 14px 16px 0;
                }

                .pk-avatar-col {
                    flex-shrink: 0;
                }

                .pk-avatar {
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    object-fit: cover;
                    display: block;
                }

                .pk-name-row {
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 4px;
                    line-height: 1.2;
                }

                .pk-name {
                    font-size: 15px;
                    font-weight: 700;
                    color: #0f1419;
                    text-decoration: none;
                }

                .pk-name:hover {
                    text-decoration: underline;
                    color: #0f1419;
                }

                /* Verified badge — creator = blue, influencer = brand purple */
                .pk-tick {
                    width: 18px;
                    height: 18px;
                    flex-shrink: 0;
                }

                /* Influencer crown label */
                .pk-influencer-label {
                    font-size: 10px;
                    font-weight: 700;
                    letter-spacing: .05em;
                    text-transform: uppercase;
                    color: #5A4FDC;
                    background: rgba(90, 79, 220, .08);
                    border-radius: 4px;
                    padding: 1px 5px;
                }

                .pk-handle-row {
                    display: flex;
                    align-items: center;
                    gap: 4px;
                    margin-top: 1px;
                }

                .pk-handle {
                    font-size: 14px;
                    color: #536471;
                    text-decoration: none;
                }

                .pk-handle:hover {
                    text-decoration: underline;
                    color: #536471;
                }

                .pk-sep {
                    color: #ccd3d8;
                    font-size: 13px;
                }

                .pk-time {
                    font-size: 14px;
                    color: #536471;
                }

                /* Earnings pill — owner only */
                .pk-earn {
                    font-size: 12px;
                    font-weight: 600;
                    color: #00ba7c;
                    background: rgba(0, 186, 124, .08);
                    border-radius: 20px;
                    padding: 2px 8px;
                    white-space: nowrap;
                    text-decoration: none;
                    border: none;
                    cursor: pointer;
                    font-family: inherit;
                    line-height: 1.4;
                }

                .pk-earn:hover {
                    background: rgba(0, 186, 124, .15);
                    color: #00ba7c;
                }

                /* Options kebab */
                .pk-options-btn {
                    background: none;
                    border: none;
                    padding: 6px;
                    border-radius: 50%;
                    color: #536471;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: *background* .15s, color .15s;
                    margin-left: auto;
                    flex-shrink: 0;
                }

                .pk-options-btn:hover {
                    background: rgba(90, 79, 220, .08);
                    color: #5A4FDC;
                }

                /* ── Body ───────────────────────────────────────────────── */
                .pk-body {
                    padding: 10px 16px 0 72px;
                }

                /* 72px = 44px avatar + 12px gap + 16px left pad */

                .pk-text {
                    font-size: 15px;
                    line-height: 1.55;
                    color: #0f1419;
                    white-space: pre-wrap;
                    word-break: break-word;
                    margin: 0;
                }

                .pk-text a {
                    color: #1d9bf0;
                    text-decoration: none;
                }

                .pk-text a:hover {
                    text-decoration: underline;
                }

                .pk-see-more {
                    background: none;
                    border: none;
                    padding: 0;
                    color: #1d9bf0;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    font-family: inherit;
                    margin-left: 4px;
                }

                .pk-see-more:hover {
                    text-decoration: underline;
                }

                /* ── Trend tags ─────────────────────────────────────────── */
                /* Signature: left-border rule — editorial eyebrow, not a chip cloud */
                .pk-trends {
                    margin-top: 10px;
                    padding-left: 10px;
                    border-left: 2px solid #5A4FDC;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 6px;
                    align-items: center;
                }

                .pk-trend {
                    font-size: 13px;
                    font-weight: 700;
                    color: #5A4FDC;
                    text-decoration: none;
                    letter-spacing: -.01em;
                }

                .pk-trend:hover {
                    text-decoration: underline;
                    color: #5A4FDC;
                }

                /* ── Media ──────────────────────────────────────────────── */
                /* Sits edge-to-edge below the indented body */
                .pk-media {
                    margin: 12px 16px 0 72px;
                    border-radius: 14px;
                    overflow: hidden;
                    border: 1px solid #eff3f4;
                }

                /* Image grid — Facebook-style */
                .pk-img-grid {
                    display: grid;
                    gap: 2px;
                    background: #000;
                }

                .pk-img-grid.n1 {
                    grid-template-columns: 1fr;
                }

                .pk-img-grid.n1 .pk-img-cell {
                    height: 360px;
                }

                .pk-img-grid.n2 {
                    grid-template-columns: 1fr 1fr;
                }

                .pk-img-grid.n2 .pk-img-cell {
                    height: 280px;
                }

                .pk-img-grid.n3 {
                    grid-template-columns: 1fr 1fr;
                }

                .pk-img-grid.n3 .pk-img-cell:first-child {
                    grid-row: span 2;
                    height: 100%;
                    min-height: 280px;
                }

                .pk-img-grid.n3 .pk-img-cell {
                    height: 200px;
                }

                .pk-img-grid.n4 {
                    grid-template-columns: 1fr 1fr;
                }

                .pk-img-grid.n4 .pk-img-cell {
                    height: 200px;
                }

                .pk-img-cell {
                    position: relative;
                    overflow: hidden;
                    background: #0f1419;
                    cursor: pointer;
                }

                .pk-img-cell img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                    transition: transform .25s ease;
                }

                .pk-img-cell:hover img {
                    transform: scale(1.03);
                }

                .pk-img-more {
                    position: absolute;
                    inset: 0;
                    background: rgba(0, 0, 0, .55);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #fff;
                    font-size: 26px;
                    font-weight: 700;
                    text-decoration: none;
                    letter-spacing: -.02em;
                }

                /* Video thumbnail */
                .pk-video {
                    position: relative;
                    background: #000;
                    cursor: pointer;
                    overflow: hidden;
                    display: block;
                    text-decoration: none;
                }

                .pk-video img {
                    width: 100%;
                    max-height: 380px;
                    object-fit: cover;
                    display: block;
                    transition: transform .25s;
                }

                .pk-video:hover img {
                    transform: scale(1.02);
                }

                .pk-video-placeholder {
                    height: 280px;
                    background: #111827;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .pk-video-overlay {
                    position: absolute;
                    inset: 0;
                    background: rgba(0, 0, 0, .25);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: *background* .2s;
                }

                .pk-video:hover .pk-video-overlay {
                    background: rgba(0, 0, 0, .42);
                }

                .pk-play {
                    width: 60px;
                    height: 60px;
                    background: rgba(255, 255, 255, .93);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: transform .15s;
                    box-shadow: 0 2px 12px rgba(0, 0, 0, .3);
                }

                .pk-video:hover .pk-play {
                    transform: scale(1.08);
                }

                .pk-video-pill {
                    position: absolute;
                    top: 10px;
                    left: 10px;
                    background: #f02849;
                    color: #fff;
                    font-size: 10px;
                    font-weight: 800;
                    padding: 3px 8px;
                    border-radius: 4px;
                    text-transform: uppercase;
                    letter-spacing: .06em;
                }

                .pk-video-dur {
                    position: absolute;
                    bottom: 10px;
                    right: 10px;
                    background: rgba(0, 0, 0, .7);
                    color: #fff;
                    font-size: 12px;
                    font-weight: 600;
                    padding: 2px 7px;
                    border-radius: 4px;
                }

                /* ── Action bar ─────────────────────────────────────────── */
                .pk-actions {
                    display: flex;
                    padding: 4px 8px 4px 64px;
                    gap: 0;
                    margin-top: 8px;
                    border-top: 1px solid #eff3f4;
                }

                .pk-action {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 6px;
                    padding: 9px 6px;
                    border: none;
                    background: transparent;
                    border-radius: 99px;
                    font-size: 13px;
                    font-weight: 400;
                    color: #536471;
                    cursor: pointer;
                    transition: *background* .15s, color .15s;
                    font-family: inherit;
                    text-decoration: none;
                    min-width: 0;
                    white-space: nowrap;
                }

                .pk-action:hover {
                    color: #1d9bf0;
                    background: rgba(29, 155, 240, .08);
                }

                .pk-action.pk-like:hover {
                    color: #f91880;
                    background: rgba(249, 24, 128, .08);
                }

                .pk-action.pk-share:hover {
                    color: #5A4FDC;
                    background: rgba(90, 79, 220, .08);
                }

                .pk-action.pk-view {
                    cursor: default;
                }

                .pk-action.pk-view:hover {
                    background: rgba(0, 186, 124, .08);
                    color: #00ba7c;
                }

                .pk-action.pk-liked {
                    color: #f91880;
                }

                .pk-action.pk-liked svg {
                    fill: #f91880;
                    stroke: #f91880;
                }

                /* ── Comments ───────────────────────────────────────────── */
                .pk-comments {
                    padding: 10px 16px 14px;
                    border-top: 1px solid #eff3f4;
                    background: #f7f8fa;
                }

                /* ── Share modal overrides ──────────────────────────────── */
                .pk-modal-header {
                    background: linear-gradient(120deg, #5A4FDC 0%, #7c6ef0 100%);
                    border: none;
                    border-radius: 0;
                    padding: 16px 20px;
                }

                .pk-share-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 8px 14px;
                    border-radius: 8px;
                    font-size: 13px;
                    font-weight: 600;
                    text-decoration: none;
                    border: none;
                    cursor: pointer;
                    transition: opacity .15s;
                }

                .pk-share-btn:hover {
                    opacity: .85;
                }
            </style>
            <style>
                .community-show-page {
                    --pk-violet: #5A4FDC;
                    --pk-violet-dark: #4B41C4;
                    --pk-violet-tint: #EEECFC;
                    --pk-mint: #1FAE64;
                    --pk-mint-tint: #E6F7EE;
                    --pk-mint-line: #CBEBDA;
                    --pk-gold: #E3A421;
                    --pk-red: #EF4444;
                    --pk-red-tint: #FDECEC;
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
                }

                .community-show-page * {
                    box-sizing: border-box
                }

                .community-show-page .pk-card {
                    background: #fff;
                    border: 1px solid var(--pk-line);
                    border-radius: var(--pk-r-lg);
                    box-shadow: var(--pk-shadow)
                }

                /* ---- hero: banner + logo ---- */
                .community-show-page .pk-hero {
                    overflow: visible;
                    margin-bottom: 16px
                }

                .community-show-page .pk-hero-banner {
                    overflow: hidden;
                    border-radius: var(--pk-r-lg) var(--pk-r-lg) 0 0;
                    height: 170px;
                    position: relative;
                    background: linear-gradient(120deg, #15103A, #5A4FDC 55%, #1FAE64 140%);
                    background-size: cover;
                    background-position: center
                }

                .community-show-page .pk-hero-actions {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    flex-wrap: wrap;
                    position: relative;
                    z-index: 2;
                }

                .community-show-page .pk-hero-banner::after {
                    content: "";
                    position: absolute;
                    inset: 0;
                    background-image: radial-gradient(rgba(255, 255, 255, .14) 1.5px, transparent 1.5px);
                    background-size: 16px 16px;
                    opacity: .4
                }

                .community-show-page .pk-hero-body {
                    padding: 0 22px 20px
                }

                .community-show-page .pk-hero-logo {
                    width: 92px;
                    height: 92px;
                    border-radius: 20px;
                    margin-top: -40px;
                    flex: none;
                    display: grid;
                    place-items: center;
                    color: #fff;
                    font-weight: 800;
                    font-size: 1.5rem;
                    border: 5px solid #fff;
                    box-shadow: 0 10px 22px -8px rgba(23, 27, 36, .28);
                    overflow: hidden
                }

                .community-show-page .pk-hero-logo img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover
                }

                .community-show-page .pk-hero-name {
                    font-size: 1.3rem;
                    font-weight: 800;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    flex-wrap: wrap
                }

                .community-show-page .pk-hero-meta {
                    font-size: .84rem;
                    color: var(--pk-gray-500);
                    margin-top: 3px
                }

                .community-show-page .pk-hero-desc {
                    font-size: .88rem;
                    color: var(--pk-gray-700);
                    margin-top: 8px;
                    max-width: 56ch;
                    line-height: 1.55
                }

                .community-show-page .pk-tick {
                    width: 15px;
                    height: 15px;
                    flex: none
                }

                .community-show-page .pk-status-pill {
                    flex: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    font-size: .7rem;
                    font-weight: 700;
                    padding: 5px 10px;
                    border-radius: var(--pk-r-pill);
                    white-space: nowrap
                }

                .community-show-page .pk-status-pill svg {
                    width: 11px;
                    height: 11px
                }

                .community-show-page .pk-status-public {
                    background: var(--pk-violet-tint);
                    color: var(--pk-violet)
                }

                .community-show-page .pk-status-private {
                    background: #EEF0F4;
                    color: var(--pk-gray-700)
                }

                .community-show-page .pk-status-paid {
                    background: var(--pk-mint-tint);
                    color: #0D7A45
                }

                .community-show-page .pk-status-approval {
                    background: #FCF1DA;
                    color: #946409
                }

                /* ---- buttons ---- */
                .community-show-page .pk-btn {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    padding: 10px 18px;
                    border-radius: var(--pk-r-md);
                    font-weight: 700;
                    font-size: .86rem;
                    transition: .15s;
                    white-space: nowrap;
                    border: none;
                    cursor: pointer;
                    font-family: inherit
                }

                .community-show-page .pk-btn svg {
                    width: 15px;
                    height: 15px;
                    flex: none
                }

                .community-show-page .pk-btn-violet {
                    background: var(--pk-violet);
                    color: #fff
                }

                .community-show-page .pk-btn-violet:hover {
                    background: var(--pk-violet-dark)
                }

                .community-show-page .pk-btn-outline {
                    background: #fff;
                    border: 1.3px solid var(--pk-line-strong);
                    color: var(--pk-gray-700)
                }

                .community-show-page .pk-btn-outline:hover {
                    border-color: var(--pk-violet);
                    color: var(--pk-violet)
                }

                .community-show-page .pk-btn[disabled] {
                    opacity: .5;
                    pointer-events: none
                }

                .community-show-page .pk-btn-danger {
                    background: var(--pk-red-tint);
                    color: #B91C1C
                }

                .community-show-page .pk-btn-danger:hover {
                    background: #FADADA
                }

                .community-show-page .pk-btn-text {
                    background: none;
                    color: var(--pk-gray-500);
                    font-weight: 700;
                    font-size: .82rem;
                    padding: 6px 4px
                }

                .community-show-page .pk-btn-text:hover {
                    color: var(--pk-red)
                }

                .community-show-page .pk-search-row {
                    display: flex;
                    align-items: center;
                    gap: 9px;
                    background: #F4F5F9;
                    border-radius: var(--pk-r-pill);
                    padding: 9px 16px;
                    color: var(--pk-gray-500)
                }

                .community-show-page .pk-search-row svg {
                    width: 16px;
                    height: 16px;
                    flex: none
                }

                .community-show-page .pk-search-row input {
                    border: none;
                    outline: none;
                    font-family: inherit;
                    font-size: .86rem;
                    width: 100%;
                    background: none;
                    color: var(--pk-ink)
                }

                .community-show-page .pk-btn-sm {
                    padding: 7px 14px;
                    font-size: .78rem
                }

                .community-show-page .pk-icon-btn {
                    width: 40px;
                    height: 40px;
                    border-radius: var(--pk-r-md);
                    background: #fff;
                    border: 1.3px solid var(--pk-line-strong);
                    display: grid;
                    place-items: center;
                    color: var(--pk-gray-700);
                    flex: none;
                    transition: .15s;
                    cursor: pointer
                }

                .community-show-page .pk-icon-btn:hover {
                    border-color: var(--pk-violet);
                    color: var(--pk-violet)
                }

                .community-show-page .pk-icon-btn svg {
                    width: 18px;
                    height: 18px
                }

                .community-show-page .pk-icon-btn.pk-active {
                    background: var(--pk-violet-tint);
                    border-color: var(--pk-violet);
                    color: var(--pk-violet)
                }

                /* ---- tabs ---- */
                .community-show-page .pk-tabs {
                    border-bottom: 1px solid var(--pk-line);
                    margin-bottom: 18px
                }

                .community-show-page .pk-tab {
                    flex: none;
                    padding: 11px 16px;
                    font-weight: 700;
                    font-size: .88rem;
                    color: var(--pk-gray-500);
                    border-bottom: 2px solid transparent;
                    margin-bottom: -1px;
                    cursor: pointer;
                    background: none;
                    border-left: none;
                    border-right: none;
                    border-top: none;
                    font-family: inherit
                }

                .community-show-page .pk-tab:hover {
                    color: var(--pk-ink)
                }

                .community-show-page .pk-tab.pk-sel {
                    color: var(--pk-violet);
                    border-bottom-color: var(--pk-violet)
                }

                /* ---- composer (matches the dashboard composer) ---- */
                .community-show-page .pk-composer {
                    padding: 18px 20px;
                    margin-bottom: 16px
                }

                .community-show-page .pk-comp-row {
                    display: flex;
                    gap: 12px;
                    align-items: flex-start
                }

                .community-show-page .pk-comp-field {
                    flex: 1;
                    min-width: 0
                }

                .community-show-page .pk-comp-field textarea {
                    width: 100%;
                    border: none;
                    outline: none;
                    resize: none;
                    font-family: inherit;
                    font-size: 1rem;
                    color: var(--pk-ink);
                    line-height: 1.5;
                    min-height: 30px;
                    background: none
                }

                .community-show-page .pk-comp-previews {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-top: 10px
                }

                .community-show-page .pk-comp-prev {
                    width: 78px;
                    height: 78px;
                    border-radius: var(--pk-r-md);
                    position: relative;
                    overflow: hidden;
                    background: var(--pk-line)
                }

                .community-show-page .pk-comp-prev img,
                .community-show-page .pk-comp-prev video {
                    width: 100%;
                    height: 100%;
                    object-fit: cover
                }

                .community-show-page .pk-comp-prev .pk-vlbl {
                    position: absolute;
                    bottom: 4px;
                    left: 5px;
                    color: #fff;
                    font-size: .62rem;
                    font-weight: 700;
                    background: rgba(0, 0, 0, .5);
                    padding: 2px 6px;
                    border-radius: 5px
                }

                .community-show-page .pk-comp-prev .pk-x {
                    position: absolute;
                    top: 4px;
                    right: 4px;
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    background: rgba(23, 27, 36, .6);
                    color: #fff;
                    display: grid;
                    place-items: center;
                    font-size: .8rem;
                    line-height: 1;
                    border: none;
                    cursor: pointer
                }

                .community-show-page .pk-comp-bar {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    margin-top: 14px;
                    padding-top: 12px;
                    border-top: 1px solid var(--pk-line)
                }

                .community-show-page .pk-comp-tool {
                    display: inline-flex;
                    align-items: center;
                    gap: 7px;
                    padding: 8px 12px;
                    border-radius: var(--pk-r-pill);
                    font-weight: 600;
                    font-size: .84rem;
                    color: var(--pk-gray-700);
                    cursor: pointer;
                    background: none;
                    border: none
                }

                .community-show-page .pk-comp-tool:hover {
                    background: #F4F5F9;
                    color: var(--pk-violet)
                }

                .community-show-page .pk-comp-tool svg {
                    width: 18px;
                    height: 18px
                }

                .community-show-page .pk-comp-post {
                    margin-left: auto
                }

                .community-show-page .pk-field-error {
                    color: var(--pk-red);
                    font-size: .76rem;
                    margin-top: 5px
                }

                .community-show-page .pk-field-hint {
                    color: var(--pk-muted, #6b7280);
                    font-size: .74rem;
                    margin-top: 5px
                }

                /* ---- feed post card ---- */
                .community-show-page .pk-post-card {
                    padding: 16px 18px;
                    margin-bottom: 12px
                }

                .community-show-page .pk-post-head {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 8px
                }

                .community-show-page .pk-ph-av {
                    width: 38px;
                    height: 38px;
                    border-radius: 50%;
                    display: grid;
                    place-items: center;
                    color: #fff;
                    font-weight: 700;
                    font-size: .82rem;
                    flex: none
                }
                .community-show-page .pk-stat-card {
                    padding: 16px 18px;
                    height: 100%;
                }

                .community-show-page .pk-stat-lbl {
                    font-size: .78rem;
                    color: var(--pk-gray-500);
                    margin-bottom: 4px;
                }

                .community-show-page .pk-stat-val {
                    font-size: 1.35rem;
                    font-weight: 800;
                    line-height: 1.2;
                }

                .community-show-page .pk-stat-good { color: #0D7A45; }
                .community-show-page .pk-stat-warn { color: #946409; }

                .community-show-page .pk-stat-sub {
                    font-size: .76rem;
                    color: var(--pk-gray-500);
                    margin-top: 4px;
                }

                .community-show-page .pk-f-chip {
                    flex: none;
                    font-size: .8rem;
                    font-weight: 700;
                    padding: 8px 14px;
                    border-radius: var(--pk-r-pill);
                    background: #fff;
                    border: 1px solid var(--pk-line);
                    color: var(--pk-gray-700);
                    transition: .15s;
                    cursor: pointer;
                    font-family: inherit
                }

                .community-show-page .pk-f-chip:hover {
                    border-color: var(--pk-violet)
                }

                .community-show-page .pk-f-chip.pk-sel {
                    background: var(--pk-violet);
                    border-color: var(--pk-violet);
                    color: #fff
                }

                .community-show-page .pk-pay-col {
                    flex: none;
                    min-width: 140px
                }

                .community-show-page .pk-pay-amt {
                    font-family: 'Space Mono', ui-monospace, monospace;
                    font-weight: 700;
                    font-size: .92rem;
                    line-height: 1.3
                }

                .community-show-page .pk-pay-meta {
                    font-size: .74rem;
                    color: var(--pk-gray-500);
                    line-height: 1.45
                }

                .community-show-page .pk-sub-status {
                    display: inline-block;
                    font-size: .68rem;
                    font-weight: 700;
                    padding: 3px 9px;
                    border-radius: var(--pk-r-pill);
                    margin-top: 4px
                }

                .community-show-page .pk-sub-status.pk-sub-active {
                    background: var(--pk-mint-tint);
                    color: #0D7A45
                }

                .community-show-page .pk-payout-table th {
                    font-size: .78rem;
                    color: var(--pk-gray-500);
                    font-weight: 600;
                }

                .community-show-page .pk-ph-name {
                    font-weight: 700;
                    font-size: .88rem;
                    display: flex;
                    align-items: center;
                    gap: 5px
                }

                .community-show-page .pk-ph-time {
                    font-size: .76rem;
                    color: var(--pk-gray-500)
                }

                .community-show-page .pk-ph-text {
                    font-size: .89rem;
                    color: var(--pk-gray-700);
                    line-height: 1.55;
                    white-space: pre-line
                }

                .community-show-page .pk-post-media {
                    display: grid;
                    gap: 4px;
                    margin-top: 12px;
                    border-radius: var(--pk-r-md);
                    overflow: hidden
                }

                .community-show-page .pk-post-media.pk-m1 {
                    grid-template-columns: 1fr
                }

                .community-show-page .pk-post-media.pk-m2,
                .community-show-page .pk-post-media.pk-m4 {
                    grid-template-columns: 1fr 1fr
                }

                .community-show-page .pk-post-media.pk-m3 {
                    grid-template-columns: 2fr 1fr;
                    grid-template-rows: 1fr 1fr
                }

                .community-show-page .pk-post-media.pk-m3 .pk-media-item:first-child {
                    grid-row: 1 / 3
                }

                .community-show-page .pk-media-item {
                    position: relative;
                    aspect-ratio: 4/3;
                    background: var(--pk-line-strong)
                }

                .community-show-page .pk-media-item img,
                .community-show-page .pk-media-item video {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block
                }

                .community-show-page .pk-media-more {
                    position: absolute;
                    inset: 0;
                    background: rgba(23, 27, 36, .55);
                    color: #fff;
                    font-weight: 800;
                    font-size: 1.3rem;
                    display: grid;
                    place-items: center
                }

                .community-show-page .pk-post-actions {
                    display: flex;
                    align-items: center;
                    gap: 4px;
                    margin-top: 10px;
                    padding-top: 8px;
                    border-top: 1px solid var(--pk-line)
                }

                .community-show-page .pk-pa {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 7px 11px;
                    border-radius: var(--pk-r-pill);
                    font-weight: 600;
                    font-size: .82rem;
                    color: var(--pk-gray-700);
                    background: none;
                    border: none;
                    cursor: pointer
                }

                .community-show-page .pk-pa:hover {
                    background: #F4F5F9;
                    color: var(--pk-violet)
                }

                .community-show-page .pk-pa.pk-liked {
                    color: var(--pk-red)
                }

                .community-show-page .pk-pa.pk-liked svg {
                    fill: var(--pk-red);
                    stroke: var(--pk-red)
                }

                .community-show-page .pk-comments {
                    margin-top: 10px
                }

                .community-show-page .pk-comment-row {
                    display: flex;
                    gap: 8px;
                    padding: 7px 0
                }

                .community-show-page .pk-comment-bubble {
                    background: #F4F5F9;
                    border-radius: var(--pk-r-md);
                    padding: 7px 11px;
                    font-size: .82rem;
                    flex: 1
                }

                .community-show-page .pk-comment-bubble b {
                    font-size: .8rem;
                    display: block;
                    margin-bottom: 1px
                }

                .community-show-page .pk-comment-input-row {
                    display: flex;
                    gap: 8px;
                    align-items: center;
                    margin-top: 8px
                }

                .community-show-page .pk-comment-input-row input {
                    flex: 1;
                    border: 1.3px solid var(--pk-line-strong);
                    border-radius: var(--pk-r-pill);
                    padding: 8px 14px;
                    font-family: inherit;
                    font-size: .82rem;
                    outline: none;
                    background: #F7F7FB
                }

                .community-show-page .pk-comment-input-row input:focus {
                    border-color: var(--pk-violet);
                    background: #fff
                }

                .community-show-page .pk-load-more-row {
                    margin-top: 14px;
                    display: flex;
                    justify-content: center
                }

                /* ---- feed/about/members placeholders ---- */
                .community-show-page .pk-placeholder-card {
                    padding: 16px 18px;
                    margin-bottom: 12px
                }

                .community-show-page .pk-empty {
                    text-align: center;
                    padding: 40px 20px;
                    color: var(--pk-gray-500);
                    background: #fff;
                    border: 1px dashed var(--pk-line-strong);
                    border-radius: var(--pk-r-lg)
                }

                .community-show-page .pk-about-row {
                    padding: 11px 0;
                    border-top: 1px solid var(--pk-line);
                    font-size: .86rem
                }

                .community-show-page .pk-about-row:first-child {
                    border-top: none
                }

                .community-show-page .pk-about-row .pk-lbl {
                    color: var(--pk-gray-500);
                    font-weight: 600
                }

                .community-show-page .pk-member-row {
                    padding: 11px 0;
                    border-top: 1px solid var(--pk-line)
                }

                .community-show-page .pk-member-row:first-child {
                    border-top: none
                }

                .community-show-page .pk-member-info .pk-n {
                    font-weight: 700;
                    font-size: .86rem;
                    display: flex;
                    align-items: center;
                    gap: 5px
                }

                .community-show-page .pk-member-info .pk-h {
                    font-size: .76rem;
                    color: var(--pk-gray-500)
                }

                .community-show-page .pk-role-badge {
                    font-size: .68rem;
                    font-weight: 700;
                    padding: 3px 9px;
                    border-radius: var(--pk-r-pill);
                    background: var(--pk-violet-tint);
                    color: var(--pk-violet);
                    flex: none
                }

                .community-show-page .pk-role-badge.pk-member-role {
                    background: #EEF0F4;
                    color: var(--pk-gray-700)
                }

                .community-show-page .pk-icon-btn-sm {
                    width: 32px;
                    height: 32px;
                    border-radius: var(--pk-r-sm)
                }

                .community-show-page .pk-icon-btn-sm svg {
                    width: 15px;
                    height: 15px
                }

                .community-show-page .pk-icon-danger:hover {
                    border-color: var(--pk-red);
                    color: var(--pk-red);
                    background: var(--pk-red-tint)
                }

                /* ---- shareable link ---- */
                .community-show-page .pk-link-banner {
                    background: var(--pk-mint-tint);
                    border-color: var(--pk-mint-line)
                }

                .community-show-page .pk-link-banner h3 {
                    color: #0D7A45
                }

                .community-show-page .pk-copy-input {
                    display: flex;
                    align-items: center;
                    gap: 9px;
                    background: #fff;
                    border: 1.3px solid var(--pk-mint-line);
                    border-radius: var(--pk-r-sm);
                    padding: 10px 12px;
                    color: var(--pk-gray-700);
                    font-size: .86rem
                }

                .community-show-page .pk-copy-input svg {
                    width: 15px;
                    height: 15px;
                    flex: none;
                    color: #0D7A45
                }

                .community-show-page .pk-copy-input span {
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap
                }

                /* ---- settings ---- */
                .community-show-page .pk-settings-section {
                    padding: 20px 22px;
                    margin-bottom: 16px
                }

                .community-show-page .pk-settings-section h3 {
                    font-size: .98rem;
                    font-weight: 800;
                    margin-bottom: 3px
                }

                .community-show-page .pk-settings-section .pk-sub {
                    font-size: .8rem;
                    color: var(--pk-gray-500);
                    margin-bottom: 16px
                }

                .community-show-page .pk-logo-preview {
                    width: 64px;
                    height: 64px;
                    border-radius: 18px;
                    color: #fff;
                    display: grid;
                    place-items: center;
                    font-weight: 800;
                    font-size: 1.2rem;
                    flex: none;
                    overflow: hidden
                }

                .community-show-page .pk-logo-preview img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover
                }

                .community-show-page .pk-banner-preview {
                    height: 120px;
                    border-radius: var(--pk-r-md);
                    background-size: cover;
                    background-position: center;
                    margin-bottom: 14px
                }

                .community-show-page .pk-field {
                    margin-bottom: 16px
                }

                .community-show-page .pk-field:last-child {
                    margin-bottom: 0
                }

                .community-show-page .pk-field label {
                    display: block;
                    font-size: .8rem;
                    font-weight: 700;
                    margin-bottom: 6px;
                    color: var(--pk-ink)
                }

                .community-show-page .pk-field .pk-cnt {
                    font-weight: 600;
                    color: var(--pk-gray-400);
                    float: right
                }

                .community-show-page .pk-field input[type=text],
                .community-show-page .pk-field input[type=number],
                .community-show-page .pk-field input[type=file],
                .community-show-page .pk-field textarea,
                .community-show-page .pk-field select {
                    width: 100%;
                    border: 1.3px solid var(--pk-line-strong);
                    border-radius: var(--pk-r-sm);
                    padding: 10px 12px;
                    font-family: inherit;
                    font-size: .88rem;
                    color: var(--pk-ink);
                    outline: none;
                    transition: .15s;
                    background: #F7F7FB
                }

                .community-show-page .pk-field input:focus,
                .community-show-page .pk-field textarea:focus,
                .community-show-page .pk-field select:focus {
                    border-color: var(--pk-violet);
                    background: #fff
                }

                .community-show-page .pk-field textarea {
                    resize: vertical;
                    min-height: 76px;
                    line-height: 1.5
                }

                .community-show-page .pk-field select {
                    appearance: none;
                    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%235A4FDC" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>');
                    background-repeat: no-repeat;
                    background-position: right 12px center;
                    background-size: 15px
                }

                .community-show-page .pk-price-field {
                    margin-top: 12px;
                    padding: 14px;
                    background: var(--pk-mint-tint);
                    border: 1px solid var(--pk-mint-line);
                    border-radius: var(--pk-r-md)
                }

                .community-show-page .pk-price-field label {
                    color: #0D7A45
                }

                .community-show-page .pk-currency-input {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background: #fff;
                    border: 1.3px solid var(--pk-line-strong);
                    border-radius: var(--pk-r-sm);
                    padding: 0 12px
                }

                .community-show-page .pk-currency-input span {
                    color: var(--pk-gray-500);
                    font-weight: 700
                }

                .community-show-page .pk-currency-input input {
                    border: none;
                    background: none;
                    padding: 10px 0;
                    width: 100%
                }

                .community-show-page .pk-billing-toggle {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                    margin-bottom: 12px
                }

                .community-show-page .pk-billing-opt {
                    flex: 1;
                    min-width: 130px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 10px 12px;
                    border: 1.3px solid var(--pk-line-strong);
                    border-radius: var(--pk-r-sm);
                    font-size: .82rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: .15s;
                    background: #fff
                }

                .community-show-page .pk-billing-opt input {
                    accent-color: #0D7A45;
                    flex: none
                }

                .community-show-page .pk-billing-opt.pk-sel {
                    border-color: #0D7A45;
                    background: #fff;
                    box-shadow: inset 0 0 0 1px #0D7A45
                }

                .community-show-page .pk-fee-note {
                    display: flex;
                    align-items: flex-start;
                    gap: 8px;
                    margin-top: 10px;
                    font-size: .78rem;
                    color: var(--pk-gray-600);
                    line-height: 1.45
                }

                .community-show-page .pk-fee-note svg {
                    width: 16px;
                    height: 16px;
                    flex: none;
                    margin-top: 1px;
                    color: #0D7A45
                }

                .community-show-page .pk-upload-actions {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                    align-items: center
                }

                .community-show-page .pk-upload-btn {
                    position: relative;
                    overflow: hidden;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px
                }

                .community-show-page .pk-upload-btn input[type=file] {
                    position: absolute;
                    inset: 0;
                    opacity: 0;
                    cursor: pointer;
                    width: 100%
                }

                .community-show-page .pk-upload-hint {
                    font-size: .76rem;
                    color: var(--pk-gray-500);
                    margin-top: 6px
                }

                .community-show-page .pk-upload-loading {
                    font-size: .78rem;
                    color: var(--pk-violet);
                    font-weight: 600
                }

                .community-show-page .pk-share-wrap {
                    position: relative
                }

                .community-show-page .pk-share-menu {
                    position: absolute;
                    top: calc(100% + 8px);
                    right: 0;
                    min-width: 240px;
                    background: #fff;
                    border: 1px solid var(--pk-line);
                    border-radius: var(--pk-r-md);
                    box-shadow: 0 12px 32px rgba(23, 27, 36, .12);
                    padding: 8px;
                    z-index: 100;
                    transform-origin: top right;
                }

                .community-show-page .pk-share-menu-header {
                    font-size: .72rem;
                    font-weight: 700;
                    color: var(--pk-gray-500);
                    padding: 4px 10px 8px;
                    text-transform: uppercase;
                    letter-spacing: .04em;
                }

                .community-show-page .pk-share-url-preview {
                    font-size: .72rem;
                    color: var(--pk-gray-500);
                    padding: 0 10px 8px;
                    word-break: break-all;
                    line-height: 1.4;
                }

                .community-show-page .pk-share-item {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    width: 100%;
                    padding: 9px 10px;
                    border: none;
                    background: none;
                    border-radius: var(--pk-r-sm);
                    font-size: .84rem;
                    font-weight: 600;
                    color: var(--pk-ink);
                    text-decoration: none;
                    cursor: pointer;
                    font-family: inherit;
                    text-align: left
                }

                .community-show-page .pk-share-item:hover {
                    background: var(--pk-violet-tint)
                }

                .community-show-page .pk-share-item svg {
                    width: 18px;
                    height: 18px;
                    flex: none
                }

                .community-show-page .pk-share-item.pk-share-wa { color: #25D366 }
                .community-show-page .pk-share-item.pk-share-x { color: #0f1419 }
                .community-show-page .pk-share-item.pk-share-fb { color: #1877F2 }
                .community-show-page .pk-share-item.pk-share-li { color: #0A66C2 }
                .community-show-page .pk-share-item.pk-share-tg { color: #229ED9 }

                .community-show-page .pk-fee-payer-row {
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                    margin-top: 12px
                }

                .community-show-page .pk-fee-payer-opt {
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                    padding: 10px 11px;
                    border: 1.3px solid var(--pk-mint-line);
                    border-radius: var(--pk-r-sm);
                    background: #fff;
                    cursor: pointer
                }

                .community-show-page .pk-fee-payer-opt.pk-sel {
                    border-color: #0D7A45;
                    box-shadow: 0 0 0 1px #0D7A45 inset
                }

                .community-show-page .pk-fee-payer-opt input {
                    margin-top: 3px;
                    accent-color: #0D7A45;
                    flex: none
                }

                .community-show-page .pk-fee-payer-opt b {
                    font-size: .84rem;
                    display: block
                }

                .community-show-page .pk-fee-payer-opt span span {
                    font-size: .76rem;
                    color: var(--pk-gray-500)
                }

                .community-show-page .pk-fee-preview {
                    margin-top: 12px;
                    padding: 12px 14px;
                    background: #fff;
                    border: 1px dashed var(--pk-mint-line);
                    border-radius: var(--pk-r-sm)
                }

                .community-show-page .pk-fp-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    font-size: .82rem;
                    padding: 5px 0;
                    color: var(--pk-gray-700)
                }

                .community-show-page .pk-fp-row b {
                    font-family: 'Space Mono', ui-monospace, monospace;
                    color: var(--pk-ink)
                }

                .community-show-page .pk-fp-row.pk-fp-total {
                    border-top: 1px solid var(--pk-mint-line);
                    margin-top: 3px;
                    padding-top: 8px
                }

                .community-show-page .pk-fp-row.pk-fp-total b {
                    color: #0D7A45;
                    font-size: .92rem
                }

                .community-show-page .pk-status-opt {
                    display: flex;
                    align-items: flex-start;
                    gap: 11px;
                    padding: 11px 12px;
                    border: 1.3px solid var(--pk-line-strong);
                    border-radius: var(--pk-r-md);
                    cursor: pointer;
                    transition: .15s;
                    margin-bottom: 8px
                }

                .community-show-page .pk-status-opt:last-child {
                    margin-bottom: 0
                }

                .community-show-page .pk-status-opt:hover {
                    border-color: var(--pk-gray-400)
                }

                .community-show-page .pk-status-opt.pk-sel {
                    border-color: var(--pk-violet);
                    background: var(--pk-violet-tint)
                }

                .community-show-page .pk-status-opt input {
                    margin-top: 3px;
                    accent-color: var(--pk-violet);
                    flex: none
                }

                .community-show-page .pk-status-opt .pk-so-ic {
                    width: 30px;
                    height: 30px;
                    border-radius: var(--pk-r-sm);
                    display: grid;
                    place-items: center;
                    flex: none
                }

                .community-show-page .pk-status-opt .pk-so-ic svg {
                    width: 14px;
                    height: 14px
                }

                .community-show-page .pk-status-opt .pk-so-txt b {
                    font-size: .86rem;
                    display: block
                }

                .community-show-page .pk-status-opt .pk-so-txt span {
                    font-size: .76rem;
                    color: var(--pk-gray-500)
                }

                .community-show-page .pk-danger {
                    border-color: #F5C6C6
                }

                .community-show-page .pk-danger h3 {
                    color: #B91C1C
                }

                .community-show-page .pk-danger-row {
                    padding: 12px 0;
                    border-top: 1px solid #F5C6C6
                }

                .community-show-page .pk-danger-row:first-of-type {
                    border-top: none;
                    padding-top: 0
                }

                .community-show-page .pk-danger-row .pk-dt b {
                    display: block;
                    font-size: .88rem
                }

                .community-show-page .pk-danger-row .pk-dt span {
                    font-size: .78rem;
                    color: var(--pk-gray-500)
                }

                .community-show-page .pk-settings-footer {
                    position: sticky;
                    bottom: 0;
                    padding: 14px 0 4px;
                    background: linear-gradient(rgba(244, 245, 249, 0), #F4F5F9 30%)
                }

                .community-show-page .pk-alert {
                    padding: 11px 14px;
                    border-radius: var(--pk-r-md);
                    font-size: .84rem;
                    margin-bottom: 14px
                }

                .community-show-page .pk-alert-success {
                    background: var(--pk-mint-tint);
                    color: #0D7A45;
                    border: 1px solid var(--pk-mint-line)
                }

                .community-show-page .pk-alert-error {
                    background: var(--pk-red-tint);
                    color: #B91C1C;
                    border: 1px solid #F5C6C6
                }

                @media (max-width: 575.98px) {
                    .community-show-page .pk-hero-logo {
                        width: 76px;
                        height: 76px;
                        margin-left: auto;
                        margin-right: auto
                    }
                }
            </style>
        @endverbatim

        @if (session('status'))
            <div class="pk-alert pk-alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="pk-alert pk-alert-error">{{ session('error') }}</div>
        @endif

        {{-- ============ HERO: BANNER + LOGO ============ --}}
        <div class="pk-card pk-hero">
            <div class="pk-hero-banner"
                @if ($community->banner) style="background-image:url('{{ Illuminate\Support\Facades\Storage::disk('spaces')->url($community->banner) }}')" @endif>
            </div>
            <div class="pk-hero-body d-flex flex-column flex-sm-row align-items-sm-end gap-3 text-center text-sm-start">
                <div class="pk-hero-logo" style="background:{{ $community->color }}">
                    @if ($community->image)
                        <img src="{{ Illuminate\Support\Facades\Storage::disk('spaces')->url($community->image) }}"
                            alt="{{ $community->name }}">
                    @else
                        {{ $community->initials }}
                    @endif
                </div>
                <div class="flex-grow-1 pt-sm-2">
                    <div class="pk-hero-name justify-content-center justify-content-sm-start">
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
                                        {{-- <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"
                                            stroke-linecap="round" /> --}}
                                    </svg>
                                    {{ getCurrencyCode() }}{{ number_format(convertCurrency($community->member_charge, $community->currency, auth()->user()->wallet->currency), 2) }}
                                    {{ $community->price_suffix }}
                                    {{-- &#8358;{{ number_format($community->member_charge, 2) }}/mo --}}
                                </span>
                            @break

                            @case('approval')
                                <span class="pk-status-pill pk-status-approval">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 2" />
                                    </svg>
                                    Approval
                                </span>
                            @break
                        @endswitch
                    </div>
                    <div class="pk-hero-meta">{{ $community->category->name ?? 'Uncategorised' }} ·
                        Admin: {{ $community->user->name ?? 'Unknown' }}</div>
                    <div class="pk-hero-stats">
                        <div class="pk-hero-stat">
                            <b>{{ number_format($community->members_count) }}</b>
                            <span>Members</span>
                        </div>
                        <div class="pk-hero-stat">
                            <b>{{ number_format($community->posts_count ?? 0) }}</b>
                            <span>Posts</span>
                        </div>
                        <div class="pk-hero-stat">
                            <b>{{ $community->created_at->format('M Y') }}</b>
                            <span>Founded</span>
                        </div>
                    </div>
                    @if ($community->description)
                        <p class="pk-hero-desc mx-auto mx-sm-0">{{ $community->description }}</p>
                    @endif
                </div>
                <div class="pk-hero-actions d-grid d-sm-flex gap-2 align-items-sm-center pt-sm-2 ms-sm-auto">
                    @if ($this->isOwner())
                        <button type="button" class="pk-btn pk-btn-outline" disabled>You own this</button>
                        @if ($community->type === 'paid')
                            <button type="button" class="pk-earn" x-on:click="tab = 'earnings'"
                                title="View earnings dashboard">
                                {{ $this->formatCommunityMoney($this->ownerEarningsTotal()) }}
                            </button>
                        @endif
                    @elseif ($this->isMember())
                        <button type="button" class="pk-btn pk-btn-violet" disabled>
                            {{ $community->type === 'paid' ? 'Subscribed' : 'Joined' }}</button>
                        @if (! $this->isOwner())
                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" wire:click="leaveCommunity"
                                wire:loading.attr="disabled" wire:target="leaveCommunity"
                                onclick="return confirm('Leave this community?')">Leave</button>
                        @endif
                    @elseif ($community->type === 'public')
                        <button type="button" class="pk-btn pk-btn-violet" wire:click="join"
                            wire:loading.attr="disabled" wire:target="join">Join community</button>
                    @elseif ($community->type === 'paid')
                        @if ($this->userSubscriptionStatus($community->id) === 'active')
                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" disabled>Joined</button>
                        @elseif ($this->userSubscriptionStatus($community->id) === 'pending')
                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" disabled>Pending</button>
                        @else
                            <a href="{{ url('community/payment/' . $community->id) }}"
                                class="pk-btn pk-btn-violet pk-btn-sm">
                                {{ $community->billing_type === 'one_off' ? 'Pay Once' : 'Subscribe' }}
                            </a>
                        @endif
                    @elseif ($community->type === 'approval')
                        @if ($this->hasPendingRequest())
                            <button type="button" class="pk-btn pk-btn-outline" disabled>Request
                                pending</button>
                        @else
                            <button type="button" class="pk-btn pk-btn-outline" wire:click="requestToJoin"
                                wire:loading.attr="disabled" wire:target="requestToJoin">Request to
                                join</button>
                        @endif
                    @elseif ($community->type === 'private')
                        @if ($this->hasPendingInvite())
                            <button type="button" class="pk-btn pk-btn-violet" wire:click="acceptInvite"
                                wire:loading.attr="disabled" wire:target="acceptInvite">Accept invitation</button>
                        @else
                            <button type="button" class="pk-btn pk-btn-outline" disabled>Invite only</button>
                        @endif
                    @else
                        <button type="button" class="pk-btn pk-btn-outline" disabled>Invite only</button>
                    @endif

                    @php
                        $publicShareUrl = route('community.public', $community);
                        $shareText = 'Check out ' . $community->name . ' on ' . config('app.name');
                        $encodedShareText = urlencode($shareText);
                        $encodedShareUrl = urlencode($publicShareUrl);
                    @endphp

                    <div class="pk-share-wrap mx-auto mx-sm-0">
                        <button type="button" class="pk-icon-btn"
                            x-bind:class="{ 'pk-active': shareOpen }"
                            aria-label="Share community"
                            aria-expanded="false"
                            x-bind:aria-expanded="shareOpen.toString()"
                            x-on:click.stop="shareOpen = !shareOpen">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7M16 6l-4-4-4 4M12 2v13"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="pk-share-menu"
                            x-show="shareOpen"
                            x-on:click.outside="shareOpen = false"
                            style="display:none">
                            <div class="pk-share-menu-header">Share community</div>
                            <div class="pk-share-url-preview">{{ $publicShareUrl }}</div>

                            <button type="button" class="pk-share-item"
                                x-show="typeof navigator !== 'undefined' && !!navigator.share"
                                x-on:click="
                                    navigator.share(@js(['title' => $community->name, 'text' => $shareText, 'url' => $publicShareUrl]))
                                        .then(() => shareOpen = false)
                                        .catch(() => {});
                                ">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                    <circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" />
                                    <path d="m8.6 13.5 6.8 3.9M15.4 6.6 8.6 10.5" />
                                </svg>
                                <span>Share via device…</span>
                            </button>

                            <a class="pk-share-item pk-share-wa" target="_blank" rel="noopener"
                                href="https://wa.me/?text={{ $encodedShareText }}%20{{ $encodedShareUrl }}"
                                x-on:click="shareOpen = false">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.28-1.39a9.9 9.9 0 0 0 4.76 1.21h.01c5.46 0 9.9-4.45 9.9-9.91C22 6.45 17.5 2 12.04 2Z"/></svg>
                                WhatsApp
                            </a>
                            <a class="pk-share-item pk-share-x" target="_blank" rel="noopener"
                                href="https://twitter.com/intent/tweet?text={{ $encodedShareText }}&url={{ $encodedShareUrl }}"
                                x-on:click="shareOpen = false">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-7.2 8.2L23.3 22H16.6l-5.2-6.8L5.4 22H2.3l7.7-8.8L1 2h6.9l4.7 6.2L18.9 2Z"/></svg>
                                X (Twitter)
                            </a>
                            <a class="pk-share-item pk-share-fb" target="_blank" rel="noopener"
                                href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedShareUrl }}"
                                x-on:click="shareOpen = false">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7.5H16l.4-3H13.5V8.4c0-.87.24-1.46 1.5-1.46H16.5V4.3c-.26-.03-1.14-.1-2.16-.1-2.14 0-3.6 1.3-3.6 3.7v2.6H8.5v3h2.24V21h2.76Z"/></svg>
                                Facebook
                            </a>
                            <a class="pk-share-item pk-share-li" target="_blank" rel="noopener"
                                href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedShareUrl }}"
                                x-on:click="shareOpen = false">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3.5A1.96 1.96 0 1 0 5.27 7.42 1.96 1.96 0 0 0 5.25 3.5ZM20.45 20h-3.37v-5.98c0-1.43-.03-3.26-1.99-3.26-2 0-2.3 1.56-2.3 3.16V20H9.42V8.5h3.24v1.57h.05c.45-.86 1.56-1.77 3.2-1.77 3.43 0 4.06 2.26 4.06 5.19V20Z"/></svg>
                                LinkedIn
                            </a>
                            <a class="pk-share-item pk-share-tg" target="_blank" rel="noopener"
                                href="https://t.me/share/url?url={{ $encodedShareUrl }}&text={{ $encodedShareText }}"
                                x-on:click="shareOpen = false">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="m21.9 4.3-3 15c-.2.9-.8 1.1-1.6.7l-4.5-3.3-2.2 2.1c-.2.2-.4.4-.8.4l.3-4.3 7.9-7.1c.3-.3-.1-.5-.5-.2l-9.7 6.1-4.2-1.3c-.9-.3-.9-.9.2-1.3L20.6 3.4c.8-.3 1.5.2 1.3.9Z"/></svg>
                                Telegram
                            </a>
                            <button type="button" class="pk-share-item"
                                x-on:click="
                                    navigator.clipboard.writeText(@js($publicShareUrl));
                                    $el.querySelector('.pk-copy-label').textContent = 'Copied!';
                                    setTimeout(() => { $el.querySelector('.pk-copy-label').textContent = 'Copy public link'; shareOpen = false; }, 1200);
                                ">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                    <rect x="9" y="9" width="12" height="12" rx="2" />
                                    <path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1" />
                                </svg>
                                <span class="pk-copy-label">Copy public link</span>
                            </button>
                        </div>
                    </div>

                    @if ($this->isOwner())
                        <button type="button" class="pk-icon-btn mx-auto mx-sm-0" x-on:click="tab = 'settings'; shareOpen = false"
                            x-bind:class="{ 'pk-active': tab === 'settings' }" aria-label="Community settings">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3" />
                                <path
                                    d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-2.7 1.1V21a2 2 0 0 1-4 0v-.1A1.6 1.6 0 0 0 6.6 19l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A1.6 1.6 0 0 0 3 13.4H3a2 2 0 0 1 0-4h.1A1.6 1.6 0 0 0 4.6 6.8L4.5 6.7a2 2 0 1 1 2.8-2.8l.1.1A1.6 1.6 0 0 0 10 5V3a2 2 0 0 1 4 0v.1a1.6 1.6 0 0 0 2.7 1.1l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0 1.1 2.7H21a2 2 0 0 1 0 4h-.1a1.6 1.6 0 0 0-1.5 1.2Z" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============ TABS ============ --}}
        <div class="pk-tabs-wrap">
        <div class="pk-tabs d-flex flex-nowrap overflow-auto">
            <button type="button" class="pk-tab" x-on:click="tab = 'feed'"
                x-bind:class="{ 'pk-sel': tab === 'feed' }">Feed</button>
            <button type="button" class="pk-tab" x-on:click="tab = 'about'"
                x-bind:class="{ 'pk-sel': tab === 'about' }">About</button>
            <button type="button" class="pk-tab" x-on:click="tab = 'members'"
                x-bind:class="{ 'pk-sel': tab === 'members' }">Members</button>
            @if ($this->isOwner() && $community->type === 'paid')
                <button type="button" class="pk-tab" x-on:click="tab = 'earnings'"
                    x-bind:class="{ 'pk-sel': tab === 'earnings' }">Earnings</button>
            @endif
            @if ($this->isOwnerOrAdmin())
                <button type="button" class="pk-tab" x-on:click="tab = 'analytics'"
                    x-bind:class="{ 'pk-sel': tab === 'analytics' }">Analytics</button>
            @endif
            @if ($this->isOwner())
                <button type="button" class="pk-tab" x-on:click="tab = 'settings'"
                    x-bind:class="{ 'pk-sel': tab === 'settings' }">Settings</button>
            @endif
        </div>
        </div>

        {{-- ============ FEED TAB ============ --}}
        <div x-show="tab === 'feed'">

            @if ($this->isMember())
                <div class="pk-card pk-composer">
                    <div class="pk-comp-row">
                        <div class="pk-ph-av" style="background:{{ $community->color }}">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                        <div class="pk-comp-field">
                            <textarea rows="2" wire:model.live="content" placeholder="What's on your mind? Share with {{ $community->name }}…"></textarea>
                            @error('content')
                                <div class="pk-field-error">{{ $message }}</div>
                            @enderror

                            @if (count($media))
                                <div class="pk-comp-previews">
                                    @foreach ($media as $index => $file)
                                        <div class="pk-comp-prev">
                                            @if (str_starts_with($file->getMimeType(), 'video'))
                                                <video muted>
                                                    <source src="{{ $file->temporaryUrl() }}">
                                                </video>
                                                <span class="pk-vlbl">Video</span>
                                            @else
                                                <img src="{{ $file->temporaryUrl() }}" alt="">
                                            @endif
                                            <button type="button" class="pk-x"
                                                wire:click="removeMedia({{ $index }})"
                                                aria-label="Remove">&times;</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @error('media')
                                <div class="pk-field-error">{{ $message }}</div>
                            @enderror
                            @error('media.*')
                                <div class="pk-field-error">{{ $message }}</div>
                            @enderror

                            <div class="pk-comp-bar">
                                <label class="pk-comp-tool" style="margin:0">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="3" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <path d="m21 15-5-5L5 21" />
                                    </svg>
                                    Photo/Video
                                    <input type="file" wire:model="media" multiple accept="image/*,video/*"
                                        style="display:none">
                                </label>

                                <button type="button" class="pk-btn pk-btn-violet pk-comp-post"
                                    wire:click="publishPost" wire:loading.attr="disabled"
                                    wire:target="publishPost,media">
                                    <span wire:loading.remove wire:target="publishPost">Post</span>
                                    <span wire:loading wire:target="publishPost">Posting…</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($this->canViewFeed())
                <div class="pk-search-row" style="margin-bottom:14px">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="postSearch"
                        placeholder="Search posts by content or author…">
                </div>
            @endif

            @if (! $this->canViewFeed())
                <div class="pk-card pk-settings-section text-center py-4">
                    <div class="pk-sub" style="margin-bottom:0">
                        <b>{{ $this->feedGateTitle() }}</b><br>
                        {{ $this->feedGateMessage() }}
                    </div>
                </div>
            @elseif ($posts->isEmpty() && $postSearch !== '')
                <div class="pk-empty">
                    <b>No posts match "{{ $postSearch }}"</b>
                    Try a different search term.
                </div>
            @else
            @forelse ($posts as $post)
                <div class="pk-card pk-standalone" wire:init="recordView('{{ $post->id }}')">

                    <div class="pk-header">
                        <div class="pk-avatar-col">
                            <a href="#">
                                <img class="pk-avatar" src="{{$post->user->avatar ?? ''}}"
                                    alt="{{ $post->user->name ?? 'Deleted user' }}">
                            </a>
                        </div>

                        <div style="flex:1;min-width:0">
                            <div class="pk-name-row">
                                <a class="pk-name" href="#">{{ $post->user->name ?? 'Deleted user' }}</a>
                                {{-- <svg class="pk-tick" viewBox="0 0 22 22" fill="none" aria-label="Verified">
                                    <circle cx="11" cy="11" r="11" fill="#1d9bf0" />
                                    <path d="M7 11l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg> --}}
                            </div>

                            <div class="pk-handle-row">
                                <a class="pk-handle"
                                    href="#">@<span>{{ $post->user->username ?? 'Deleted user' }}</span></a>
                                <span class="pk-sep">·</span>
                                <span class="pk-time">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        @if ($this->canDeletePost($post->id))
                            <button type="button" class="pk-icon-btn pk-icon-btn-sm pk-icon-danger"
                                wire:click="deletePost('{{ $post->id }}')"
                                onclick="return confirm('Delete this post? This can\'t be undone.')"
                                aria-label="Delete post" title="Delete post">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path
                                        d="M4 7h16M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3m-7 0 1 13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1l1-13"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        @endif

                        {{-- <div class="dropdown">
                            <a href="#" class="pk-earn">₦2,500.00</a>
                        </div> --}}
                    </div>

                    <div class="pk-body">
                        <p class="pk-texth">
                            {!! $post->content !!}
                        </p>

                    </div>

                    @if ($post->media->count())
                        @php $count = $post->media->count(); @endphp
                        <div class="pk-media pk-m{{ min($count, 4) }}">
                            @foreach ($post->media->take(4) as $i => $item)
                                <div class="pk-media-item">
                                    @if ($item->is_video)
                                        <video src="{{ $item->url }}" controls></video>
                                    @else
                                        <img src="{{ $item->url }}" alt="">
                                    @endif
                                    @if ($i === 3 && $count > 4)
                                        <div class="pk-media-more">+{{ $count - 4 }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- <div class="pk-media">
                        <div class="pk-img-grid n4">
                            <div class="pk-img-cell">
                                <a href="#">
                                    <img src="https://picsum.photos/900/700?random=21" alt="Post image">
                                </a>
                            </div>
                            <div class="pk-img-cell">
                                <a href="#">
                                    <img src="https://picsum.photos/900/700?random=22" alt="Post image">
                                </a>
                            </div>
                            <div class="pk-img-cell">
                                <a href="#">
                                    <img src="https://picsum.photos/900/700?random=23" alt="Post image">
                                </a>
                            </div>
                            <div class="pk-img-cell">
                                <a href="#">
                                    <img src="https://picsum.photos/900/700?random=24" alt="Post image">
                                </a>
                            </div>
                        </div>
                    </div> --}}

                    <div class="pk-actions">
                        <button class="pk-action pk-like @if ($post->liked_by_me) pk-liked @endif"
                            wire:click="toggleLike('{{ $post->id }}')">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                            </svg>
                            {{ number_format($post->likes_count) }}
                        </button>

                        <a class="pk-action" href="#">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                            </svg>
                            {{ number_format($post->comments_count) }}
                        </a>

                        <span class="pk-action pk-view">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            {{ number_format($post->views_count) }}
                        </span>

                        {{-- <button class="pk-action pk-share" data-bs-toggle="modal" data-bs-target="#pk-share-demo">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="18" cy="5" r="3" />
                                <circle cx="6" cy="12" r="3" />
                                <circle cx="18" cy="19" r="3" />
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
                            </svg>
                            Share
                        </button> --}}
                    </div>

                    <div class="pk-comments">

                        @if ($this->isMember())
                            <div class="pk-comment-input-row">
                                <input type="text" maxlength="500" wire:model="newComment.{{ $post->id }}"
                                    wire:keydown.enter="addComment('{{ $post->id }}')"
                                    placeholder="Write a comment…">
                                <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                    wire:click="addComment('{{ $post->id }}')">Send</button>
                            </div>
                        @endif


                        <!-- Comment Section -->
                        <div class="pt-1">
                            @foreach ($post->comments->take(3) as $comment)
                                <div class="pk-fb-comment" wire:key="comment-{{ $comment->id }}">
                                    <img class="pk-fb-comment-av"
                                        src="{{ $comment->user->avatar ?? '' }}"
                                        alt="{{ $comment->user->name ?? 'User' }}">
                                    <div class="pk-fb-comment-bubble">
                                        <div class="d-flex align-items-start justify-content-between gap-2">
                                            <div>
                                                <a class="pk-fb-comment-name" href="#">
                                                    {{ $comment->user->name ?? 'Deleted user' }}
                                                </a>
                                                <span class="pk-fb-comment-time">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                                <p class="pk-fb-comment-text mb-0">{{ $comment->content }}</p>
                                            </div>
                                            @if ($this->canDeleteComment($comment->id))
                                                <button type="button" class="pk-icon-btn pk-icon-btn-sm pk-icon-danger"
                                                    wire:click="deleteComment('{{ $comment->id }}')"
                                                    onclick="return confirm('Delete this comment?')"
                                                    aria-label="Delete comment" title="Delete comment">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                                        <path d="M4 7h16M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" stroke-linecap="round" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>


                    </div>
                </div>

               
            @empty
                <div class="pk-empty">
                    <b>No posts yet.</b>
                    {{ $this->isMember() ? 'Be the first to share something.' : 'Join to start the conversation.' }}
                </div>
            @endforelse

            @if ($posts->hasMorePages())
                <div class="pk-load-more-row">
                    <button type="button" class="pk-btn pk-btn-outline" wire:click="loadMorePosts"
                        wire:loading.attr="disabled" wire:target="loadMorePosts">
                        <span wire:loading.remove wire:target="loadMorePosts">Load more posts</span>
                        <span wire:loading wire:target="loadMorePosts">Loading…</span>
                    </button>
                </div>
            @endif
            @endif

        </div>

        {{-- ============ ANALYTICS TAB (owner/admin) ============ --}}
        @if ($this->isOwnerOrAdmin())
            <div x-show="tab === 'analytics'">
                @livewire('user.community-analytics-dashboard', ['community' => $community], key('community-analytics-' . $community->id))
            </div>
        @endif

        {{-- ============ EARNINGS TAB (paid communities, owner only) ============ --}}
        @if ($this->isOwner() && $community->type === 'paid')
            <div x-show="tab === 'earnings'">
                @livewire('user.community-payout-dashboard', ['community' => $community], key('community-payout-' . $community->id))
            </div>
        @endif

        {{-- ============ ABOUT TAB ============ --}}
        <div x-show="tab === 'about'">
            <div class="pk-card pk-settings-section">
                <h3>About this community</h3>
                <p class="pk-sub" style="margin-bottom:14px">{{ $community->description }}</p>
                <div class="pk-about-row d-flex flex-column flex-sm-row gap-1 gap-sm-3">
                    <span class="pk-lbl flex-sm-shrink-0" style="min-width:120px">Category</span>
                    <span>{{ $community->category->name ?? 'Uncategorised' }}</span>
                </div>
                <div class="pk-about-row d-flex flex-column flex-sm-row gap-1 gap-sm-3">
                    <span class="pk-lbl flex-sm-shrink-0" style="min-width:120px">Status</span>
                    <span>
                        {{ ucfirst($community->type) }}
                        @if ($community->type === 'paid')
                            · {{ getCurrencyCode() }}{{ number_format(convertCurrency($community->member_charge, $community->currency, auth()->user()->wallet->currency), 2) }}{{ $community->price_suffix }}
                        @endif
                    </span>
                </div>
                <div class="pk-about-row d-flex flex-column flex-sm-row gap-1 gap-sm-3">
                    <span class="pk-lbl flex-sm-shrink-0" style="min-width:120px">Created</span>
                    <span>{{ $community->created_at->format('F Y') }}</span>
                </div>
                <div class="pk-about-row d-flex flex-column flex-sm-row gap-1 gap-sm-3">
                    <span class="pk-lbl flex-sm-shrink-0" style="min-width:120px">Members</span>
                    <span>{{ number_format($community->members()->count()) }}</span>
                </div>
                <div class="pk-about-row d-flex flex-column flex-sm-row gap-1 gap-sm-3">
                    <span class="pk-lbl flex-sm-shrink-0" style="min-width:120px">Admin</span>
                    <span>{{ $community->user->name ?? 'Unknown' }}</span>
                </div>
            </div>
        </div>

        {{-- ============ MEMBERS TAB ============ --}}
        <div x-show="tab === 'members'">

            @if ($this->isOwnerOrAdmin())
                @if ($community->type == 'approval')
                    <div class="pk-card pk-settings-section">
                        <h3>Pending join requests</h3>
                        <div class="pk-sub" style="margin-bottom:6px">People waiting for you to approve or deny their
                            request to join.</div>

                        @forelse ($pendingRequests as $req)
                            <div class="pk-member-row d-flex flex-wrap align-items-center gap-2"
                                wire:key="request-{{ $req->id }}" x-data="{ denying: false }">
                                <div class="pk-ph-av" style="background:{{ $community->color }}">
                                    {{ mb_strtoupper(mb_substr($req->user->name ?? '?', 0, 1)) }}</div>
                                <div class="flex-grow-1" style="min-width:160px">
                                    <div class="pk-n">{{ $req->user->name ?? 'Unknown user' }}</div>
                                    <div class="pk-h">{{ $req->user->email ?? '' }} · requested
                                        {{ $req->created_at->diffForHumans() }}</div>
                                </div>

                                <div class="d-flex gap-2" x-show="!denying">
                                    <button type="button" class="pk-btn pk-btn-violet pk-btn-sm"
                                        wire:click="approveRequest('{{ $req->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="approveRequest('{{ $req->id }}')">Approve</button>
                                    <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                        x-on:click="denying = true">Deny</button>
                                </div>

                                <div class="d-flex flex-wrap gap-2 w-100" x-show="denying" style="display:none">
                                    <input type="text" x-ref="reason_{{ $req->id }}" maxlength="255"
                                        placeholder="Reason for denying (optional)" class="flex-grow-1"
                                        style="min-width:160px;border:1.3px solid var(--pk-line-strong);border-radius:var(--pk-r-sm);padding:8px 10px;font-size:.82rem;font-family:inherit">
                                    <button type="button" class="pk-btn pk-btn-danger pk-btn-sm"
                                        x-on:click="$wire.denyRequest('{{ $req->id }}', $refs['reason_{{ $req->id }}'].value); denying = false"
                                        wire:loading.attr="disabled" wire:target="denyRequest">Confirm deny</button>
                                    <button type="button" class="pk-btn-text"
                                        x-on:click="denying = false">Cancel</button>
                                </div>
                            </div>
                        @empty
                            <div class="pk-sub" style="margin-bottom:0">No pending requests right now.</div>
                        @endforelse
                    </div>
                @endif
            @endif

            @if ($this->isOwnerOrAdmin() && $community->type === 'private')
                <div class="pk-card pk-settings-section">
                    <h3>Invite members</h3>
                    <div class="pk-sub" style="margin-bottom:14px">
                        Private communities are hidden from search. Invite people by username or email, or share an invite link.
                    </div>

                    <div class="pk-field" style="margin-bottom:16px">
                        <label for="inviteIdentifier">Invite by username or email</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="text" id="inviteIdentifier" wire:model="inviteIdentifier"
                                placeholder="e.g. johndoe or user@email.com"
                                class="flex-grow-1"
                                style="min-width:200px;border:1.3px solid var(--pk-line-strong);border-radius:var(--pk-r-sm);padding:10px 12px;font-size:.88rem;font-family:inherit">
                            <button type="button" class="pk-btn pk-btn-violet pk-btn-sm" wire:click="inviteMember"
                                wire:loading.attr="disabled" wire:target="inviteMember">Send invite</button>
                        </div>
                        @error('inviteIdentifier')
                            <div class="pk-field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pk-field" style="margin-bottom:8px">
                        <label>Invite link</label>
                        @if ($inviteLinkUrl)
                            <div class="pk-copy-input d-flex flex-wrap align-items-center gap-2"
                                style="border:1.3px solid var(--pk-line-strong);border-radius:var(--pk-r-sm);padding:8px 12px;margin-top:6px">
                                <span class="flex-grow-1 text-break" style="font-size:.82rem;color:var(--pk-gray-700)"
                                    id="pk-invite-url">{{ $inviteLinkUrl }}</span>
                                <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                    onclick="navigator.clipboard.writeText(document.getElementById('pk-invite-url').textContent); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000)">Copy</button>
                            </div>
                            <div class="d-flex flex-wrap gap-2" style="margin-top:10px">
                                <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" wire:click="generateInviteLink"
                                    wire:loading.attr="disabled" wire:target="generateInviteLink">Regenerate link</button>
                                <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" wire:click="revokeInviteLink"
                                    wire:loading.attr="disabled" wire:target="revokeInviteLink"
                                    onclick="return confirm('Revoke this invite link? Existing links will stop working.')">Revoke link</button>
                            </div>
                        @else
                            <button type="button" class="pk-btn pk-btn-violet pk-btn-sm" style="margin-top:6px"
                                wire:click="generateInviteLink" wire:loading.attr="disabled"
                                wire:target="generateInviteLink">Generate invite link</button>
                        @endif
                    </div>

                    @if ($pendingDirectInvites->isNotEmpty())
                        <div style="margin-top:18px">
                            <div class="pk-sub" style="margin-bottom:8px"><b>Pending invitations</b></div>
                            @foreach ($pendingDirectInvites as $inv)
                                <div class="pk-member-row d-flex flex-wrap align-items-center gap-2"
                                    wire:key="invite-{{ $inv->id }}">
                                    <div class="pk-ph-av" style="background:{{ $community->color }}">
                                        {{ mb_strtoupper(mb_substr($inv->user->name ?? '?', 0, 1)) }}</div>
                                    <div class="flex-grow-1" style="min-width:140px">
                                        <div class="pk-n">{{ $inv->user->name ?? 'Unknown' }}</div>
                                        <div class="pk-h">{{ $inv->user->email ?? '' }} · sent {{ $inv->created_at->diffForHumans() }}</div>
                                    </div>
                                    <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                        wire:click="revokeDirectInvite('{{ $inv->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="revokeDirectInvite('{{ $inv->id }}')">Revoke</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div class="pk-card pk-settings-section">
                <h3>Members</h3>

                @if ($this->canViewMembers())
                    <div class="pk-sub" style="margin-bottom:6px">
                        {{ number_format($community->members()->count()) }} people
                        @if ($this->isOwnerOrAdmin())
                            · admin actions below
                        @endif
                    </div>

                    <div class="pk-search-row" style="margin-bottom:14px">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="memberSearch"
                            placeholder="Search members by name or email…">
                    </div>

                    @forelse ($members as $member)
                        <div class="pk-member-row d-flex flex-wrap align-items-center gap-2"
                            wire:key="member-{{ $member->id }}">
                            <div class="pk-ph-av" style="background:{{ $community->color }}">
                                {{ mb_strtoupper(mb_substr($member->name ?? '?', 0, 1)) }}</div>
                            <div class="flex-grow-1" style="min-width:140px">
                                <div class="pk-n">{{ $member->name }}</div>
                                <div class="pk-h">{{ $member->email }}</div>
                            </div>
                            <span class="pk-role-badge @if ($member->pivot->role === 'member') pk-member-role @endif">
                                {{ ucfirst($member->pivot->role) }}</span>

                            @if ($member->id !== $community->user_id)
                                <div class="d-flex gap-2">
                                    @if ($this->isOwner())
                                        @if ($member->pivot->role === 'admin')
                                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                                wire:click="demoteToMember('{{ $member->id }}')">Remove admin</button>
                                        @else
                                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                                wire:click="promoteToAdmin('{{ $member->id }}')">Make admin</button>
                                        @endif
                                    @endif
                                    @if ($this->isOwnerOrAdmin())
                                    <button type="button" class="pk-icon-btn pk-icon-btn-sm pk-icon-danger"
                                        wire:click="banMember('{{ $member->id }}')"
                                        aria-label="Ban {{ $member->name }}" title="Ban from community">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <circle cx="12" cy="12" r="9" />
                                            <path d="m6 6 12 12" stroke-linecap="round" />
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="pk-empty">
                            {{ $memberSearch !== '' ? 'No members match your search.' : 'No members yet.' }}</div>
                    @endforelse

                    @if ($members && $members->hasMorePages())
                        <div class="pk-load-more-row">
                            <button type="button" class="pk-btn pk-btn-outline" wire:click="loadMoreMembers"
                                wire:loading.attr="disabled" wire:target="loadMoreMembers">
                                <span wire:loading.remove wire:target="loadMoreMembers">Load more members</span>
                                <span wire:loading wire:target="loadMoreMembers">Loading…</span>
                            </button>
                        </div>
                    @endif
                @else
                    <div class="pk-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <rect x="5" y="11" width="14" height="9" rx="2" />
                            <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                        </svg>
                        <b>Members list is private</b>
                        Only the owner, admins, and members can view the member list for this community.
                    </div>
                @endif
            </div>

            @if ($this->isOwnerOrAdmin())
                <div class="pk-card pk-settings-section">
                    <h3>Banned members</h3>
                    <div class="pk-sub" style="margin-bottom:6px">People removed from this community can't rejoin
                        unless unbanned.</div>

                    @forelse ($bannedMembers as $banned)
                        <div class="pk-member-row d-flex flex-wrap align-items-center gap-2"
                            wire:key="banned-{{ $banned->id }}">
                            <div class="pk-ph-av" style="background:var(--pk-gray-400)">
                                {{ mb_strtoupper(mb_substr($banned->name ?? '?', 0, 1)) }}</div>
                            <div class="flex-grow-1" style="min-width:140px">
                                <div class="pk-n">{{ $banned->name }}</div>
                                <div class="pk-h">{{ $banned->email }}</div>
                            </div>
                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                wire:click="unbanMember('{{ $banned->id }}')">Unban</button>
                        </div>
                    @empty
                        <div class="pk-sub" style="margin-bottom:0">No banned members.</div>
                    @endforelse
                </div>
            @endif
        </div>

        {{-- ============ SETTINGS TAB (owner only) ============ --}}
        @if ($this->isOwner())
            <div x-show="tab === 'settings'">

                {{-- logo --}}
                <div class="pk-card pk-settings-section">
                    <h3>Community logo</h3>
                    <div class="pk-sub">Square image, shown in cards, search, and the top of this page. JPG, PNG or WebP · max 4 MB.</div>
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                        <div class="pk-logo-preview mx-auto mx-sm-0" style="background:{{ $community->color }}">
                            @if ($settingsLogo)
                                <img src="{{ $settingsLogo->temporaryUrl() }}" alt="Logo preview">
                            @elseif ($community->image)
                                <img src="{{ Illuminate\Support\Facades\Storage::disk('spaces')->url($community->image) }}"
                                    alt="{{ $community->name }} logo">
                            @else
                                {{ $community->initials }}
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="pk-upload-actions">
                                <label class="pk-btn pk-btn-violet pk-btn-sm pk-upload-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                        <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                                    </svg>
                                    {{ $community->image ? 'Replace logo' : 'Upload logo' }}
                                    <input type="file" wire:model="settingsLogo" accept="image/jpeg,image/png,image/webp,image/gif">
                                </label>
                                @if ($settingsLogo)
                                    <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                        wire:click="clearPendingLogo">Cancel</button>
                                @elseif ($community->image)
                                    <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                        wire:click="removeLogo"
                                        wire:confirm="Remove the community logo?">Remove</button>
                                @endif
                            </div>
                            <div class="pk-upload-hint" wire:loading wire:target="settingsLogo">
                                <span class="pk-upload-loading">Uploading logo…</span>
                            </div>
                            @error('settingsLogo')
                                <div class="pk-field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- banner --}}
                <div class="pk-card pk-settings-section">
                    <h3>Banner image</h3>
                    <div class="pk-sub">Wide cover photo shown at the top of the community page. Recommended 1200×360 · max 6 MB.</div>
                    <div class="pk-banner-preview"
                        style="background-image:url('{{ $settingsBanner ? $settingsBanner->temporaryUrl() : ($community->banner ? Illuminate\Support\Facades\Storage::disk('spaces')->url($community->banner) : '') }}'); background-color:#15103A">
                    </div>
                    <div class="pk-upload-actions">
                        <label class="pk-btn pk-btn-violet pk-btn-sm pk-upload-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                            </svg>
                            {{ $community->banner ? 'Replace banner' : 'Upload banner' }}
                            <input type="file" wire:model="settingsBanner" accept="image/jpeg,image/png,image/webp,image/gif">
                        </label>
                        @if ($settingsBanner)
                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                wire:click="clearPendingBanner">Cancel</button>
                        @elseif ($community->banner)
                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                wire:click="removeBanner"
                                wire:confirm="Remove the banner image?">Remove</button>
                        @endif
                    </div>
                    <div class="pk-upload-hint" wire:loading wire:target="settingsBanner">
                        <span class="pk-upload-loading">Uploading banner…</span>
                    </div>
                    @error('settingsBanner')
                        <div class="pk-field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- details --}}
                <div class="pk-card pk-settings-section">
                    <h3>Community details</h3>
                    <div class="pk-sub">Shown across the app wherever this community appears.</div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="pk-field">
                                <label for="sName">Community name</label>
                                <input type="text" id="sName" maxlength="255" wire:model.blur="settingsName">
                                @error('settingsName')
                                    <div class="pk-field-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="pk-field">
                                <label for="sCat">Category</label>
                                <select id="sCat" wire:model="settingsCategoryId">
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('settingsCategoryId')
                                    <div class="pk-field-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pk-field">
                        <label for="sDesc">Description
                            <span class="pk-cnt">{{ strlen($settingsDescription) }}/1000</span></label>
                        <textarea id="sDesc" maxlength="1000" wire:model.live.debounce.300ms="settingsDescription"></textarea>
                        @error('settingsDescription')
                            <div class="pk-field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pk-upload-hint" style="margin-top:4px">
                        Renaming updates your public link slug automatically (currently <code>/c/{{ $community->slug }}</code>).
                    </div>
                </div>

                {{-- shareable community link --}}
                <div class="pk-card pk-settings-section pk-link-banner">
                    <h3>Community link</h3>
                    <div class="pk-sub">Anyone with this link can view the community's public info page — even if
                        it's private, paid, or approval-based.</div>
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <div class="pk-copy-input flex-grow-1">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10 13a5 5 0 0 0 7.5.5l2-2a5 5 0 0 0-7-7l-1.5 1.5" />
                                <path d="M14 11a5 5 0 0 0-7.5-.5l-2 2a5 5 0 0 0 7 7l1.5-1.5" />
                            </svg>
                            <span>{{ route('community.public', $community) }}</span>
                        </div>
                        <button type="button" class="pk-btn pk-btn-violet pk-btn-sm"
                            onclick="navigator.clipboard.writeText('{{ route('community.public', $community) }}'); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="9" y="9" width="12" height="12" rx="2" />
                                <path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1" />
                            </svg>
                            Copy
                        </button>
                    </div>
                </div>

                {{-- privacy & access --}}
                <div class="pk-card pk-settings-section">
                    <h3>Privacy &amp; access</h3>
                    <div class="pk-sub">Controls who can find and join this community.</div>

                    <label class="pk-status-opt @if ($settingsType === 'public') pk-sel @endif">
                        <input type="radio" name="sStatus" value="public" wire:model.live="settingsType">
                        <span class="pk-so-ic" style="background:var(--pk-violet-tint);color:var(--pk-violet)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z" />
                            </svg>
                        </span>
                        <span class="pk-so-txt"><b>Public</b><span>Anyone can find and join instantly.</span></span>
                    </label>

                    <label class="pk-status-opt @if ($settingsType === 'private') pk-sel @endif">
                        <input type="radio" name="sStatus" value="private" wire:model.live="settingsType">
                        <span class="pk-so-ic" style="background:#EEF0F4;color:var(--pk-gray-700)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                <rect x="5" y="11" width="14" height="9" rx="2" />
                                <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                            </svg>
                        </span>
                        <span class="pk-so-txt"><b>Private (invite only)</b><span>Hidden from search — only people
                                you invite can join.</span></span>
                    </label>

                    <label class="pk-status-opt @if ($settingsType === 'paid') pk-sel @endif">
                        <input type="radio" name="sStatus" value="paid" wire:model.live="settingsType">
                        <span class="pk-so-ic" style="background:var(--pk-mint-tint);color:#0D7A45">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"
                                    stroke-linecap="round" />
                            </svg>
                        </span>
                        <span class="pk-so-txt"><b>Paid</b><span>Members pay a monthly fee to join.</span></span>
                    </label>

                    @if ($settingsType === 'paid')
                        <div class="pk-price-field">
                            <label>How should members pay?</label>
                            <div class="pk-billing-toggle">
                                <label class="pk-billing-opt @if ($settingsBillingType === 'one_off') pk-sel @endif">
                                    <input type="radio" name="sBillingType" value="one_off"
                                        wire:model.live="settingsBillingType">
                                    One-off payment
                                </label>
                                @if (userBaseCurrency() !== 'NGN' || $community->billing_type === 'subscription')
                                    <label class="pk-billing-opt @if ($settingsBillingType === 'subscription') pk-sel @endif">
                                        <input type="radio" name="sBillingType" value="subscription"
                                            wire:model.live="settingsBillingType">
                                        Subscription
                                    </label>
                                @endif
                            </div>
                            @error('settingsBillingType')
                                <div class="pk-field-error">{{ $message }}</div>
                            @enderror

                            @if ($settingsBillingType === 'subscription' && (userBaseCurrency() !== 'NGN' || $community->billing_type === 'subscription'))
                                <div class="pk-field" style="margin-bottom:12px">
                                    <label for="sInterval">Billing interval</label>
                                    <select id="sInterval" wire:model.live="settingsBillingInterval">
                                        @foreach ($billingIntervals as $key => $meta)
                                            <option value="{{ $key }}">{{ $meta['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('settingsBillingInterval')
                                        <div class="pk-field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            @php($settingsCurrency = $community->currency ?? userBaseCurrency())
                            <label for="sPrice">
                                {{ $settingsBillingType === 'one_off' ? 'Price (one-time)' : 'Price per billing cycle' }}
                            </label>
                            <div class="pk-currency-input">
                                <span>{{ getCurrencyCode($settingsCurrency) }}</span>
                                <input type="number" id="sPrice"
                                    min="{{ communityMinimumPrice($settingsCurrency) }}"
                                    step="{{ $settingsCurrency === 'USD' ? 1 : 50 }}"
                                    wire:model.live.debounce.400ms="settingsMonthlyFee">
                            </div>
                            <div class="pk-field-hint">Minimum {{ getCurrencyCode($settingsCurrency) }}{{ number_format(communityMinimumPrice($settingsCurrency), $settingsCurrency === 'USD' ? 2 : 0) }}</div>
                            @error('settingsMonthlyFee')
                                <div class="pk-field-error">{{ $message }}</div>
                            @enderror

                            <div class="pk-fee-note">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 16v-4M12 8h.01" />
                                </svg>
                                <span>{{ config('app.name') }} charges a <strong>{{ $platformFeePercent }}%</strong>
                                    platform fee on every payment into this community.</span>
                            </div>

                            <div class="pk-fee-payer-row">
                                <label class="pk-fee-payer-opt @if ($settingsFeePayer === 'creator') pk-sel @endif">
                                    <input type="radio" name="sFeePayer" value="creator"
                                        wire:model.live="settingsFeePayer">
                                    <span><b>I'll cover the {{ $platformFeePercent }}% fee</b>
                                        <span>Deducted from what you receive.</span></span>
                                </label>
                                <label class="pk-fee-payer-opt @if ($settingsFeePayer === 'members') pk-sel @endif">
                                    <input type="radio" name="sFeePayer" value="members"
                                        wire:model.live="settingsFeePayer">
                                    <span><b>My members will cover it</b>
                                        <span>Added on top of the price above.</span></span>
                                </label>
                            </div>
                            @error('settingsFeePayer')
                                <div class="pk-field-error">{{ $message }}</div>
                            @enderror

                            @php($preview = $this->settingsFeePreview())
                            @if ($preview)
                                <div class="pk-fee-preview">
                                    <div class="pk-fp-row"><span>Members pay{{ $preview['suffix'] }}</span>
                                        <b>{{ getCurrencyCode() }}{{ number_format($preview['memberCharge'], 2) }}</b>
                                    </div>
                                    <div class="pk-fp-row"><span>{{ config('app.name') }} fee ({{ $platformFeePercent }}%)</span>
                                        <b>{{ getCurrencyCode() }}{{ number_format($preview['platformCut'], 2) }}</b>
                                    </div>
                                    <div class="pk-fp-row pk-fp-total"><span>You receive{{ $preview['suffix'] }}</span>
                                        <b>{{ getCurrencyCode() }}{{ number_format($preview['creatorPayout'], 2) }}</b>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <label class="pk-status-opt @if ($settingsType === 'approval') pk-sel @endif">
                        <input type="radio" name="sStatus" value="approval" wire:model.live="settingsType">
                        <span class="pk-so-ic" style="background:#FCF1DA;color:#946409">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7v5l3 2" />
                            </svg>
                        </span>
                        <span class="pk-so-txt"><b>Approval required</b><span>Visible to everyone, but joining needs
                                admin acceptance.</span></span>
                    </label>
                </div>

                {{-- danger zone --}}
                <div class="pk-card pk-settings-section pk-danger">
                    <h3>Danger zone</h3>
                    @if ($community->archived_at)
                        <div class="pk-danger-row d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2">
                            <div class="pk-dt"><b>Community archived</b><span>Hidden from discovery since
                                    {{ $community->archived_at->format('M j, Y') }}. Existing members keep access.</span></div>
                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" wire:click="unarchiveCommunity">Restore</button>
                        </div>
                    @else
                        <div class="pk-danger-row d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2">
                            <div class="pk-dt"><b>Archive community</b><span>Hide from search and block new joins.
                                    Existing members keep access.</span></div>
                            <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" wire:click="archiveCommunity"
                                onclick="return confirm('Archive this community?')">Archive</button>
                        </div>
                    @endif
                    <div
                        class="pk-danger-row d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2">
                        <div class="pk-dt"><b>Delete community</b><span>Permanently removes it for all
                                {{ number_format($community->members()->count()) }} members. This can't be
                                undone.</span></div>
                        <button type="button" class="pk-btn pk-btn-danger pk-btn-sm" wire:click="deleteCommunity"
                            onclick="return confirm('This permanently deletes the community. Continue?')">Delete
                            community</button>
                    </div>
                </div>

                <div class="pk-settings-footer d-grid d-sm-flex gap-2 justify-content-sm-end">
                    <button type="button" class="pk-btn pk-btn-outline" x-on:click="tab = 'about'">Cancel</button>
                    <button type="button" class="pk-btn pk-btn-violet" wire:click="saveSettings"
                        wire:loading.attr="disabled" wire:target="saveSettings">
                        <span wire:loading.remove wire:target="saveSettings">Save changes</span>
                        <span wire:loading wire:target="saveSettings">Saving…</span>
                    </button>
                </div>
            </div>
        @endif

        </div>{{-- /.pk-ui-inner --}}
    </div>
</div>
