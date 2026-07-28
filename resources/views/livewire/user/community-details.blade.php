<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}

    <div class="community-show-page" x-data="{ tab: 'feed' }">

        @verbatim
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
                    overflow: hidden;
                    margin-bottom: 16px
                }

                .community-show-page .pk-hero-banner {
                    height: 170px;
                    position: relative;
                    background: linear-gradient(120deg, #15103A, #5A4FDC 55%, #1FAE64 140%);
                    background-size: cover;
                    background-position: center
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
                                    {{ getCurrencyCode() }}{{ number_format(convertCurrency($community->member_charge, $community->currency, auth()->user()->wallet->currency),2) }} {{ $community->price_suffix }}
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
                        {{ number_format($community->members()->count()) }} members ·
                        Admin: {{ $community->user->name ?? 'Unknown' }}</div>
                    <p class="pk-hero-desc mx-auto mx-sm-0">{{ $community->description }}</p>
                </div>
                <div class="d-grid d-sm-flex gap-2 align-items-sm-center pt-sm-2">
                    @if ($this->isOwner())
                        <button type="button" class="pk-btn pk-btn-outline" disabled>You own this</button>
                    @elseif ($this->isMember())
                        <button type="button" class="pk-btn pk-btn-violet" disabled>
                            {{ $community->type === 'paid' ? 'Subscribed' : 'Joined' }}</button>
                    @elseif ($community->type === 'public')
                        <button type="button" class="pk-btn pk-btn-violet" wire:click="join"
                            wire:loading.attr="disabled" wire:target="join">Join community</button>
                    @elseif ($community->type === 'paid')
                        @if ($this->hasPendingSubscription())
                            <button type="button" class="pk-btn pk-btn-outline" disabled>Payment pending</button>
                        @else
                            <button type="button" class="pk-btn pk-btn-violet" wire:click="subscribe"
                                wire:loading.attr="disabled"
                                wire:target="subscribe">{{ $this->subscribeLabel() }}</button>
                        @endif
                        {{-- <button type="button" class="pk-btn pk-btn-violet">Subscribe</button> --}}
                    @elseif ($community->type === 'approval')
                        @if ($this->hasPendingRequest())
                            <button type="button" class="pk-btn pk-btn-outline" disabled>Request pending</button>
                        @else
                            <button type="button" class="pk-btn pk-btn-outline" wire:click="requestToJoin"
                                wire:loading.attr="disabled" wire:target="requestToJoin">Request to join</button>
                        @endif
                    @else
                        <button type="button" class="pk-btn pk-btn-outline" disabled>Invite only</button>
                    @endif

                    <button type="button" class="pk-icon-btn mx-auto mx-sm-0" aria-label="Share community"
                        onclick="navigator.clipboard.writeText('{{ url('/c/' . $community->slug) }}'); this.dataset.copied = 'true';">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7M16 6l-4-4-4 4M12 2v13"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    @if ($this->isOwner())
                        <button type="button" class="pk-icon-btn mx-auto mx-sm-0" x-on:click="tab = 'settings'"
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
        <div class="pk-tabs d-flex flex-nowrap overflow-auto">
            <button type="button" class="pk-tab" x-on:click="tab = 'feed'"
                x-bind:class="{ 'pk-sel': tab === 'feed' }">Feed</button>
            <button type="button" class="pk-tab" x-on:click="tab = 'about'"
                x-bind:class="{ 'pk-sel': tab === 'about' }">About</button>
            <button type="button" class="pk-tab" x-on:click="tab = 'members'"
                x-bind:class="{ 'pk-sel': tab === 'members' }">Members</button>
            @if ($this->isOwner())
                <button type="button" class="pk-tab" x-on:click="tab = 'settings'"
                    x-bind:class="{ 'pk-sel': tab === 'settings' }">Settings</button>
            @endif
        </div>

        {{-- ============ FEED TAB ============ --}}
        <div x-show="tab === 'feed'">

            @if ($this->isMember())
                <div class="pk-card pk-composer">
                    <div class="pk-comp-row">
                        <div class="pk-ph-av" style="background:{{ $community->color }}">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                        <div class="pk-comp-field">
                            <textarea rows="2" wire:model.live="content" placeholder="Share something with {{ $community->name }}…"></textarea>
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

            @forelse ($posts as $post)
                <article class="pk-card pk-post-card" wire:key="post-{{ $post->id }}"
                    wire:init="recordView('{{ $post->id }}')">
                    <div class="pk-post-head">
                        <div class="pk-ph-av" style="background:{{ $community->color }}">
                            {{ mb_strtoupper(mb_substr($post->user->name ?? '?', 0, 1)) }}</div>
                        <div class="flex-grow-1">
                            <div class="pk-ph-name">{{ $post->user->name ?? 'Deleted user' }}</div>
                            <div class="pk-ph-time">{{ $post->created_at->diffForHumans() }}</div>
                        </div>
                        @if ($this->isOwnerOrAdmin())
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
                    </div>

                    @if ($post->content)
                        <p class="pk-ph-text">{{ $post->content }}</p>
                    @endif

                    @if ($post->media->count())
                        @php $count = $post->media->count(); @endphp
                        <div class="pk-post-media pk-m{{ min($count, 4) }}">
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

                    <div class="pk-post-actions">
                        <button type="button" class="pk-pa @if ($post->liked_by_me) pk-liked @endif"
                            wire:click="toggleLike('{{ $post->id }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path
                                    d="M12 20s-7-4.5-9.5-9C1 8 2.5 4.5 6 4.5c2 0 3.2 1.2 4 2.3.8-1.1 2-2.3 4-2.3 3.5 0 5 3.5 3.5 6.5C19 15.5 12 20 12 20Z" />
                            </svg>
                            {{ number_format($post->likes_count) }}
                        </button>
                        <span class="pk-pa" style="cursor:default">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M21 11.5a8.4 8.4 0 0 1-9 8.4L3 21l1.1-4.5A8.4 8.4 0 1 1 21 11.5Z"
                                    stroke-linejoin="round" />
                            </svg>
                            {{ number_format($post->comments_count) }}
                        </span>
                        <span class="pk-pa" style="cursor:default">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            {{ number_format($post->views_count) }}
                        </span>
                    </div>

                    <div class="pk-comments">
                        @foreach ($post->comments->take(3) as $comment)
                            <div class="pk-comment-row">
                                <div class="pk-ph-av"
                                    style="width:28px;height:28px;font-size:.7rem;background:{{ $community->color }}">
                                    {{ mb_strtoupper(mb_substr($comment->user->name ?? '?', 0, 1)) }}</div>
                                <div class="pk-comment-bubble">
                                    <b>{{ $comment->user->name ?? 'Deleted user' }}</b>{{ $comment->content }}
                                </div>
                            </div>
                        @endforeach

                        @if ($this->isMember())
                            <div class="pk-comment-input-row">
                                <input type="text" maxlength="500" wire:model="newComment.{{ $post->id }}"
                                    wire:keydown.enter="addComment('{{ $post->id }}')"
                                    placeholder="Write a comment…">
                                <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                    wire:click="addComment('{{ $post->id }}')">Send</button>
                            </div>
                        @endif
                    </div>
                </article>
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
        </div>

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
                            · &#8358;{{ number_format($community->member_charge, 2) }}/mo
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

            @if ($this->isOwner())
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

            <div class="pk-card pk-settings-section">
                <h3>Members</h3>

                @if ($this->canViewMembers())
                    <div class="pk-sub" style="margin-bottom:6px">
                        {{ number_format($community->members()->count()) }} people
                        @if ($this->isOwner())
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

                            @if ($this->isOwner() && $member->id !== $community->user_id)
                                <div class="d-flex gap-2">
                                    @if ($member->pivot->role === 'admin')
                                        <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                            wire:click="demoteToMember('{{ $member->id }}')">Remove admin</button>
                                    @else
                                        <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                            wire:click="promoteToAdmin('{{ $member->id }}')">Make admin</button>
                                    @endif
                                    {{-- <button type="button" class="pk-icon-btn pk-icon-btn-sm"
                                        wire:click="removeMember('{{ $member->id }}')"
                                        aria-label="Remove {{ $member->name }}" title="Remove from community">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" />
                                        </svg>
                                    </button> --}}
                                    <button type="button" class="pk-icon-btn pk-icon-btn-sm pk-icon-danger"
                                        wire:click="banMember('{{ $member->id }}')"
                                        aria-label="Ban {{ $member->name }}" title="Ban from community">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <circle cx="12" cy="12" r="9" />
                                            <path d="m6 6 12 12" stroke-linecap="round" />
                                        </svg>
                                    </button>
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
                        Only the owner and admins can view the member list for this community.
                    </div>
                @endif
            </div>

            @if ($this->isOwner())
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
                    <div class="pk-sub">Square image, shown in cards, search, and the top of this page.</div>
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                        <div class="pk-logo-preview mx-auto mx-sm-0" style="background:{{ $community->color }}">
                            @if ($settingsLogo)
                                <img src="{{ $settingsLogo->temporaryUrl() }}" alt="">
                            @elseif ($community->image)
                                <img src="{{ Illuminate\Support\Facades\Storage::disk('spaces')->url($community->image) }}"
                                    alt="">
                            @else
                                {{ $community->initials }}
                            @endif
                        </div>
                        <div class="d-flex flex-column gap-2 flex-grow-1">
                            <input type="file" wire:model="settingsLogo" accept="image/*">
                            @error('settingsLogo')
                                <div class="pk-field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- banner --}}
                <div class="pk-card pk-settings-section">
                    <h3>Banner image</h3>
                    <div class="pk-sub">Wide cover photo shown at the top of the community page. Recommended
                        1200×360.</div>
                    <div class="pk-banner-preview"
                        style="background-image:url('{{ $settingsBanner ? $settingsBanner->temporaryUrl() : ($community->banner ? Illuminate\Support\Facades\Storage::disk('spaces')->url($community->banner) : '') }}'); background-color:#15103A">
                    </div>
                    <input type="file" wire:model="settingsBanner" accept="image/*">
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
                            <span>{{ url('/c/' . $community->slug) }}</span>
                        </div>
                        <button type="button" class="pk-btn pk-btn-violet pk-btn-sm"
                            onclick="navigator.clipboard.writeText('{{ url('/c/' . $community->slug) }}')">
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
                            <label for="sPrice">Monthly fee</label>
                            <div class="pk-currency-input">
                                <span>&#8358;</span>
                                <input type="number" id="sPrice" min="100" step="50"
                                    wire:model.live.debounce.400ms="settingsMonthlyFee">
                            </div>
                            @error('settingsMonthlyFee')
                                <div class="pk-field-error">{{ $message }}</div>
                            @enderror

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
                                    <div class="pk-fp-row"><span>Members pay</span>
                                        <b>&#8358;{{ number_format($preview['memberCharge'], 2) }}</b>
                                    </div>
                                    <div class="pk-fp-row"><span>Payhankey fee ({{ $platformFeePercent }}%)</span>
                                        <b>&#8358;{{ number_format($preview['platformCut'], 2) }}</b>
                                    </div>
                                    <div class="pk-fp-row pk-fp-total"><span>You receive, per member/month</span>
                                        <b>&#8358;{{ number_format($preview['creatorPayout'], 2) }}</b>
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
                    <div
                        class="pk-danger-row d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2">
                        <div class="pk-dt"><b>Archive community</b><span>Hide it from search. Members keep access,
                                no new joins.</span></div>
                        <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" wire:click="archiveCommunity"
                            onclick="return confirm('Archive this community? It will become invite-only.')">Archive</button>
                    </div>
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

    </div>
</div>
