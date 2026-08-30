@once
<style>
    [x-cloak] { display: none !important; }

    .pkoin-wrap { margin-bottom: 20px; }

    .pkoin-hero {
        background: linear-gradient(135deg, #1a1625 0%, #2d2640 45%, #3d2f1f 100%);
        border-radius: var(--pk-r);
        padding: clamp(20px, 4vw, 28px);
        color: #fff;
        margin-bottom: 16px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(245, 158, 11, .2);
    }

    .pkoin-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 90% 10%, rgba(245, 158, 11, .18), transparent 42%);
        pointer-events: none;
    }

    .pkoin-hero-top {
        position: relative;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .pkoin-brand {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .pkoin-coin {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        filter: drop-shadow(0 4px 12px rgba(245, 158, 11, .35));
    }

    .pkoin-coin svg { width: 100%; height: 100%; display: block; }

    .pkoin-kicker {
        margin: 0 0 4px;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: rgba(253, 230, 138, .85);
    }

    .pkoin-balance {
        margin: 0;
        font-size: clamp(1.6rem, 4vw, 2rem);
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.1;
    }

    .pkoin-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pkoin-btn-light {
        background: rgba(255, 255, 255, .08) !important;
        border-color: rgba(255, 255, 255, .2) !important;
        color: #fff !important;
    }

    .pkoin-btn-light:hover {
        background: rgba(255, 255, 255, .14) !important;
        color: #fff !important;
    }

    .pkoin-btn-light:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .pkoin-stats {
        position: relative;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 14px;
    }

    @media (min-width: 768px) {
        .pkoin-stats { grid-template-columns: repeat(4, 1fr); }
        .pkoin-stats--3 { grid-template-columns: repeat(3, 1fr); }
    }

    .pkoin-stat {
        background: rgba(255, 255, 255, .06);
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: var(--pk-r-sm);
        padding: 12px 14px;
    }

    .pkoin-stat-label {
        display: block;
        font-size: .72rem;
        font-weight: 600;
        color: rgba(255, 255, 255, .65);
        margin-bottom: 4px;
    }

    .pkoin-stat strong {
        font-size: .92rem;
        font-weight: 800;
    }

    .pkoin-note {
        position: relative;
        margin: 0;
        font-size: .82rem;
        line-height: 1.5;
        color: rgba(255, 255, 255, .72);
    }

    .pkoin-note strong { color: #FDE68A; }

    .pkoin-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .pkoin-tab {
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1.5px solid var(--pk-line);
        background: #fff;
        font-family: inherit;
        font-size: .75rem;
        font-weight: 700;
        color: var(--pk-muted);
        cursor: pointer;
        transition: all .15s ease;
    }

    .pkoin-tab.is-active {
        background: var(--pk-violet-soft);
        border-color: #C7D2FE;
        color: var(--pk-violet-dark);
    }

    .pkoin-tx-body { padding: 0 !important; }

    .pkoin-tx-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .pkoin-tx-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--pk-line);
    }

    .pkoin-tx-item:last-child { border-bottom: none; }

    .pkoin-tx-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        font-size: .95rem;
    }

    .pkoin-tx-icon--topup { background: #EEF2FF; color: #4F46E5; }
    .pkoin-tx-icon--gift_sent { background: #FEF3C7; color: #D97706; }
    .pkoin-tx-icon--gift_received { background: var(--pk-mint-soft); color: var(--pk-mint); }
    .pkoin-tx-icon--convert { background: var(--pk-violet-soft); color: var(--pk-violet); }

    .pkoin-tx-main {
        flex: 1;
        min-width: 0;
    }

    .pkoin-tx-main b {
        display: block;
        font-size: .88rem;
        margin-bottom: 2px;
    }

    .pkoin-tx-main small {
        color: var(--pk-muted);
        font-size: .78rem;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pkoin-tx-right {
        text-align: right;
        flex-shrink: 0;
    }

    .pkoin-tx-amount {
        display: block;
        font-size: .9rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .pkoin-tx-amount.is-credit { color: var(--pk-mint); }
    .pkoin-tx-amount.is-debit { color: var(--pk-ink); }

    .pkoin-tx-right time {
        font-size: .72rem;
        color: var(--pk-muted);
        font-weight: 600;
    }

    /* Modals */
    .pkoin-overlay {
        position: fixed;
        inset: 0;
        z-index: 1055;
        background: rgba(15, 17, 23, .55);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        backdrop-filter: blur(4px);
    }

    .pkoin-modal {
        width: min(100%, 480px);
        max-height: min(92vh, 680px);
        background: #fff;
        border-radius: var(--pk-r);
        box-shadow: 0 24px 48px rgba(15, 17, 23, .2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .pkoin-modal--sm { width: min(100%, 420px); }

    .pkoin-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--pk-line);
    }

    .pkoin-modal-kicker {
        margin: 0 0 4px;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--pk-muted);
    }

    .pkoin-modal-head h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
    }

    .pkoin-close {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 8px;
        background: var(--pk-bg);
        color: var(--pk-muted);
        font-size: 1.4rem;
        line-height: 1;
        cursor: pointer;
        flex-shrink: 0;
    }

    .pkoin-close:hover { background: var(--pk-line); color: var(--pk-ink); }

    .pkoin-steps-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        background: var(--pk-bg);
        border-bottom: 1px solid var(--pk-line);
    }

    .pkoin-step-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #CBD5E1;
        transition: all .2s ease;
    }

    .pkoin-step-dot.is-current {
        width: 24px;
        border-radius: 999px;
        background: var(--pk-violet);
    }

    .pkoin-step-dot.is-done { background: var(--pk-mint); }

    .pkoin-modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }

    .pkoin-modal-lead {
        margin: 0 0 16px;
        font-size: .875rem;
        line-height: 1.55;
        color: var(--pk-muted);
    }

    .pkoin-modal-foot {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        padding: 14px 20px;
        border-top: 1px solid var(--pk-line);
        background: var(--pk-bg);
    }

    .pkoin-preset-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    @media (min-width: 480px) {
        .pkoin-preset-grid { grid-template-columns: repeat(3, 1fr); }
    }

    .pkoin-preset {
        min-height: 48px;
        padding: 10px 12px;
        border-radius: var(--pk-r-sm);
        border: 1.5px solid var(--pk-line);
        background: #fff;
        font-family: inherit;
        font-size: .875rem;
        font-weight: 700;
        color: var(--pk-ink);
        cursor: pointer;
        transition: all .15s ease;
    }

    .pkoin-preset:hover { border-color: #C7D2FE; }
    .pkoin-preset.is-selected {
        border-color: var(--pk-violet);
        background: var(--pk-violet-soft);
        color: var(--pk-violet-dark);
        box-shadow: 0 0 0 3px rgba(90, 79, 220, .12);
    }

    .pkoin-input-prefix {
        position: relative;
    }

    .pkoin-input-prefix span {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 700;
        color: var(--pk-muted);
        pointer-events: none;
    }

    .pkoin-input-prefix .pk-input { padding-left: 32px; }

    .pkoin-preview-card {
        margin-top: 16px;
        padding: 14px 16px;
        border-radius: var(--pk-r-sm);
        background: var(--pk-bg);
        border: 1px solid var(--pk-line);
    }

    .pkoin-preview-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        font-size: .875rem;
    }

    .pkoin-preview-row span { color: var(--pk-muted); font-weight: 600; }
    .pkoin-preview-row b { font-weight: 800; text-align: right; }

    .pkoin-preview-row--muted b { color: var(--pk-muted); font-weight: 700; }

    .pkoin-preview-row--highlight {
        border-top: 1px dashed #CBD5E1;
        margin-top: 4px;
        padding-top: 12px;
    }

    .pkoin-preview-row--highlight span { color: var(--pk-ink); }
    .pkoin-preview-row--highlight b { color: var(--pk-violet-dark); font-size: 1rem; }

    .pkoin-review-hero {
        text-align: center;
        padding: 20px;
        margin-bottom: 16px;
        border-radius: var(--pk-r);
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        border: 1px solid #FCD34D;
    }

    .pkoin-review-pk {
        display: block;
        font-size: 1.75rem;
        font-weight: 800;
        color: #92400E;
        line-height: 1.1;
        margin-bottom: 4px;
    }

    .pkoin-review-hero p {
        margin: 0;
        font-size: .875rem;
        color: #B45309;
        font-weight: 600;
    }

    .pkoin-review-list { margin-bottom: 16px; }

    .pkoin-pay-methods {
        display: grid;
        gap: 10px;
        margin-bottom: 16px;
    }

    .pkoin-pay-option {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        border-radius: var(--pk-r-sm);
        border: 1.5px solid var(--pk-line);
        cursor: pointer;
        transition: all .15s ease;
    }

    .pkoin-pay-option input { display: none; }

    .pkoin-pay-option.is-selected {
        border-color: var(--pk-violet);
        background: var(--pk-violet-soft);
        box-shadow: 0 0 0 3px rgba(90, 79, 220, .1);
    }

    .pkoin-pay-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #fff;
        display: grid;
        place-items: center;
        color: var(--pk-violet);
        font-size: 1rem;
        flex-shrink: 0;
    }

    .pkoin-pay-option b {
        display: block;
        font-size: .9rem;
        margin-bottom: 2px;
    }

    .pkoin-pay-option small {
        color: var(--pk-muted);
        font-size: .78rem;
    }

    .pkoin-pay-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        border-radius: var(--pk-r-sm);
        background: var(--pk-ink);
        color: #fff;
    }

    .pkoin-pay-total span { font-size: .875rem; opacity: .85; }
    .pkoin-pay-total strong { font-size: 1.15rem; }

    .pkoin-success {
        text-align: center;
        padding: 12px 0 8px;
    }

    .pkoin-success-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--pk-violet-soft);
        color: var(--pk-violet);
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        margin: 0 auto 14px;
    }

    .pkoin-success-icon--mint {
        background: var(--pk-mint-soft);
        color: var(--pk-mint);
    }

    .pkoin-success h4 {
        margin: 0 0 8px;
        font-size: 1.15rem;
        font-weight: 800;
    }

    .pkoin-success p {
        margin: 0 0 8px;
        font-size: .875rem;
        color: var(--pk-muted);
        line-height: 1.5;
    }

    .pkoin-success-balance {
        font-size: .9rem !important;
        color: var(--pk-ink) !important;
    }

    .pk-panel-head--split {
        flex-wrap: wrap;
        gap: 12px;
    }

    .pk-panel-head-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-left: auto;
    }

    .pkoin-more-btn {
        min-height: 32px;
        padding: 0 12px;
        font-size: .78rem;
        font-weight: 700;
        gap: 4px;
    }

    .pkoin-tx-more {
        padding: 12px 18px 16px;
        border-top: 1px solid var(--pk-line);
        text-align: center;
    }

    .pkoin-sidebar-overlay {
        position: fixed;
        inset: 0;
        z-index: 1060;
        background: rgba(15, 17, 23, .45);
        backdrop-filter: blur(3px);
        display: flex;
        justify-content: flex-end;
    }

    .pkoin-sidebar {
        width: min(100%, 420px);
        max-width: 100vw;
        height: 100%;
        background: #fff;
        box-shadow: -12px 0 40px rgba(15, 17, 23, .15);
        display: flex;
        flex-direction: column;
        animation: pkoinSidebarIn .22s ease-out;
    }

    @keyframes pkoinSidebarIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }

    .pkoin-sidebar-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--pk-line);
        flex-shrink: 0;
    }

    .pkoin-sidebar-head h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
    }

    .pkoin-sidebar-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--pk-line);
        background: var(--pk-bg);
        flex-shrink: 0;
    }

    .pkoin-sidebar-body {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endonce
