@once
<style>
        /* ── Reset / scope ──────────────────────────────────────── */
        .pk-card *,
        .pk-card *::before,
        .pk-card *::after {
            box-sizing: border-box;
        }

        /* ── Card shell ─────────────────────────────────────────── */
        .pk-card {
            position: relative;
            background: #fff;
            border: 1px solid #eff3f4;
            border-radius: 0;
            /* X-style: no radius on feed cards */
            margin-bottom: 1px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .pk-card:focus-within,
        .pk-card:has(.pk-menu[open]) {
            z-index: 50;
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
            position: relative;
            z-index: 2;
            overflow: visible;
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
        }

        .pk-earn:hover {
            background: rgba(0, 186, 124, .15);
            color: #00ba7c;
        }

        .pk-earn--static {
            cursor: default;
            pointer-events: none;
        }

        /* Options kebab */
        .pk-header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
            flex-shrink: 0;
            position: relative;
            z-index: 5;
        }

        .pk-menu {
            position: relative;
        }

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
            transition: background .15s, color .15s;
            flex-shrink: 0;
            list-style: none;
        }

        .pk-options-btn::-webkit-details-marker,
        .pk-options-btn::marker {
            display: none;
            content: '';
        }

        .pk-options-btn:hover {
            background: rgba(90, 79, 220, .08);
            color: #5A4FDC;
        }

        .pk-menu[open] .pk-options-btn {
            background: rgba(90, 79, 220, .08);
            color: #5A4FDC;
        }

        .pk-menu-panel {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 210px;
            background: #fff;
            border: 1px solid #eff3f4;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 17, 23, .12);
            padding: 6px;
            z-index: 100;
        }

        .pk-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 12px;
            border: none;
            border-radius: 8px;
            background: none;
            color: #0f1419;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            font-family: inherit;
            text-align: left;
        }

        .pk-menu-item:hover {
            background: #f7f9f9;
        }

        .pk-menu-item i {
            width: 16px;
            text-align: center;
            color: #536471;
        }

        .pk-menu-item--danger {
            color: #dc2626;
        }

        .pk-menu-item--danger i {
            color: #dc2626;
        }

        .pk-menu-item--active {
            color: #5A4FDC;
        }

        .pk-menu-divider {
            height: 1px;
            background: #eff3f4;
            margin: 4px 6px;
        }

        /* ── Body ───────────────────────────────────────────────── */
        .pk-body {
            padding: 10px 16px 0 72px;
        }

        /* 72px = 44px avatar + 12px gap + 16px left pad */

        .pk-text-wrap {
            margin: 0;
        }

        .pk-text {
            font-size: 15px;
            line-height: 1.5;
            color: #0f1419;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            word-break: break-word;
            margin: 0;
            text-align: left;
        }

        .pk-text a,
        .pk-text .pk-tag,
        .pk-text .pk-mention {
            color: #1d9bf0;
            text-decoration: none;
        }

        .pk-text a:hover,
        .pk-text .pk-tag:hover,
        .pk-text .pk-mention:hover {
            text-decoration: underline;
        }

        .pk-see-more {
            display: inline;
            background: none;
            border: none;
            padding: 0;
            margin-left: 6px;
            color: #5A4FDC;
            font-size: inherit;
            font-weight: 700;
            line-height: inherit;
            cursor: pointer;
            font-family: inherit;
            text-decoration: underline;
            text-underline-offset: 2px;
            white-space: nowrap;
        }

        .pk-see-more:hover {
            color: #4338ca;
        }

        /* ── External link embeds (YouTube / Instagram / TikTok) ── */
        .pk-link-embed {
            border: none;
            border-radius: 0;
            margin-top: 0;
            background: #000;
            position: relative;
        }

        .pk-link-embed iframe {
            display: block;
            width: 100%;
            border: 0;
            background: #000;
        }

        .pk-link-embed--youtube iframe {
            aspect-ratio: 16 / 9;
            min-height: 220px;
        }

        .pk-link-embed--instagram iframe {
            aspect-ratio: 9 / 16;
            max-height: 540px;
            min-height: 420px;
        }

        .pk-link-embed--tiktok iframe {
            aspect-ratio: 9 / 16;
            max-height: 580px;
            min-height: 460px;
        }

        .pk-link-embed-fallback {
            display: block;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #536471;
            text-decoration: none;
            background: #f7f9f9;
            border-top: 1px solid #eff3f4;
        }

        .pk-link-embed-fallback:hover {
            color: #1d9bf0;
            background: #eff8ff;
        }

        /* ── Generic URL preview card (X-style) ── */
        .pk-link-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 0;
            padding: 12px 16px;
            border: none;
            border-top: 1px solid #eff3f4;
            border-radius: 0;
            background: #f7f9f9;
            text-decoration: none;
            color: inherit;
            overflow: hidden;
            transition: background .15s;
        }

        .pk-link-card:hover {
            background: #eff8ff;
        }

        .pk-link-preview {
            margin-top: 0;
        }

        .pk-link-preview .pk-link-embed,
        .pk-link-preview .pk-link-card {
            width: 100%;
        }

        .pk-link-card-icon {
            flex: none;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #eff3f4;
            display: grid;
            place-items: center;
            color: #536471;
        }

        .pk-link-card-icon svg {
            width: 18px;
            height: 18px;
        }

        .pk-link-card-body {
            min-width: 0;
            flex: 1;
        }

        .pk-link-card-host {
            font-size: 13px;
            font-weight: 700;
            color: #536471;
            line-height: 1.3;
        }

        .pk-link-card-path {
            margin-top: 2px;
            font-size: 15px;
            font-weight: 400;
            color: #0f1419;
            line-height: 1.35;
            word-break: break-word;
        }

        .pk-text a.pk-link {
            word-break: break-all;
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

        /* ── Media — Facebook full-bleed ─────────────────────────── */
        .pk-media {
            margin: 8px 0 0;
            border-radius: 0;
            overflow: hidden;
            border: none;
            border-top: 1px solid #ced0d4;
            border-bottom: 1px solid #ced0d4;
        }

        .fb-img-grid {
            display: grid;
            gap: 2px;
            background: #000;
        }

        .fb-img-grid.n1 { grid-template-columns: 1fr; }
        .fb-img-grid.n1 .fb-img-cell { height: min(500px, 70vw); max-height: 500px; }
        .fb-img-grid.n2 { grid-template-columns: 1fr 1fr; }
        .fb-img-grid.n2 .fb-img-cell { height: 300px; }
        .fb-img-grid.n3 { grid-template-columns: 1fr 1fr; }
        .fb-img-grid.n3 .fb-img-cell:first-child { grid-row: span 2; min-height: 300px; }
        .fb-img-grid.n3 .fb-img-cell { height: 150px; }
        .fb-img-grid.n4 { grid-template-columns: 1fr 1fr; }
        .fb-img-grid.n4 .fb-img-cell { height: 220px; }

        .fb-img-cell {
            position: relative;
            overflow: hidden;
            background: #1c1e21;
            cursor: pointer;
        }
        .fb-img-cell:focus-visible { outline: 2px solid #1877f2; outline-offset: -2px; }

        .fb-img-cell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: filter .2s;
        }

        .fb-img-cell:hover img { filter: brightness(.92); }

        .fb-img-more {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            pointer-events: none;
        }

        /* FB video attachment */
        .fb-video {
            position: relative;
            display: block;
            background: #1c1e21;
            text-decoration: none;
            overflow: hidden;
            max-height: 500px;
        }

        .fb-video img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            display: block;
            transition: filter .2s;
        }

        .fb-video:hover img { filter: brightness(.85); }

        .fb-video-placeholder {
            height: 320px;
            background: #1c1e21;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fb-video-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s;
        }

        .fb-video:hover .fb-video-overlay { background: rgba(0, 0, 0, .35); }

        .fb-play {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, .95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .35);
            transition: transform .15s;
        }

        .fb-video:hover .fb-play { transform: scale(1.06); }

        .fb-video-pill {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(0, 0, 0, .65);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .fb-video-dur {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: rgba(0, 0, 0, .75);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 8px;
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
            transition: background .15s, color .15s;
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
            animation: pk-pop .25s ease;
        }

        @keyframes pk-pop {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.4);
            }

            100% {
                transform: scale(1);
            }
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

        [x-cloak] {
            display: none !important;
        }

        /* ── Edit post modal ────────────────────────────────────── */
        .pk-edit-overlay {
            position: fixed;
            inset: 0;
            z-index: 10060;
            background: rgba(15, 17, 23, .45);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .pk-edit-panel {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 18px 48px rgba(15, 17, 23, .22);
            padding: 18px 18px 16px;
        }

        .pk-edit-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 16px;
            color: #0f1419;
        }

        .pk-edit-close {
            border: none;
            background: #eff3f4;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
            color: #0f1419;
        }

        .pk-edit-textarea {
            width: 100%;
            border: 1px solid #eff3f4;
            border-radius: 12px;
            padding: 12px 14px;
            font: inherit;
            font-size: 15px;
            line-height: 1.45;
            resize: vertical;
            min-height: 120px;
            outline: none;
        }

        .pk-edit-textarea:focus {
            border-color: #5A4FDC;
            box-shadow: 0 0 0 3px rgba(90, 79, 220, .12);
        }

        .pk-edit-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 14px;
        }

        .pk-edit-btn {
            border: none;
            border-radius: 999px;
            padding: 9px 16px;
            font: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .pk-edit-btn--ghost {
            background: #eff3f4;
            color: #0f1419;
        }

        .pk-edit-btn--primary {
            background: #5A4FDC;
            color: #fff;
        }

        .pk-edit-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
        }
</style>
@endonce
