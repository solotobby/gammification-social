@once
<style>
    [x-cloak] { display: none !important; }

    .pk-gift-root { position: relative; }

    /* Gift action button */
    .pk-action.pk-gift:hover,
    .pk-action.pk-gift.is-active {
        color: #D97706;
        background: rgba(245, 158, 11, .1);
    }

    .pk-action.pk-gift.has-gifts {
        color: #B45309;
        font-weight: 600;
    }

    /* Recent gifts strip */
    .pk-gifts-strip {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 8px 16px 4px 64px;
        font-size: 12px;
        color: #536471;
    }

    .pk-gifts-strip-label {
        font-weight: 700;
        color: #92400E;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .pk-gift-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 999px;
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        font-size: 11px;
        font-weight: 600;
        color: #92400E;
    }

    .pk-gift-chip-emoji { font-size: 14px; line-height: 1; }

    /* Gift overlay / modal */
    .pk-gift-overlay {
        position: fixed;
        inset: 0;
        z-index: 1060;
        background: rgba(15, 17, 23, .5);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        backdrop-filter: blur(3px);
    }

    @media (min-width: 640px) {
        .pk-gift-overlay {
            align-items: center;
            padding: 20px;
        }
    }

    .pk-gift-sheet {
        width: 100%;
        max-width: 520px;
        max-height: min(88vh, 640px);
        background: #fff;
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -8px 40px rgba(15, 17, 23, .15);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    @media (min-width: 640px) {
        .pk-gift-sheet {
            border-radius: 18px;
            max-height: min(85vh, 600px);
        }
    }

    .pk-gift-sheet-head {
        padding: 16px 18px;
        border-bottom: 1px solid #E2E8F0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        background: linear-gradient(180deg, #FFFBEB 0%, #fff 100%);
    }

    .pk-gift-sheet-kicker {
        margin: 0 0 2px;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #B45309;
    }

    .pk-gift-sheet-head h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: #0F1117;
        line-height: 1.3;
    }

    .pk-gift-sheet-head small {
        display: block;
        margin-top: 4px;
        font-size: .78rem;
        color: #64748B;
        font-weight: 500;
    }

    .pk-gift-balance {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 10px;
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        flex-shrink: 0;
    }

    .pk-gift-balance strong {
        font-size: .85rem;
        font-weight: 800;
        color: #92400E;
    }

    .pk-gift-balance a {
        font-size: .72rem;
        font-weight: 700;
        color: #5A4FDC;
        text-decoration: none;
    }

    .pk-gift-close {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        background: #F1F5F9;
        color: #64748B;
        font-size: 1.25rem;
        line-height: 1;
        cursor: pointer;
        flex-shrink: 0;
    }

    .pk-gift-cats {
        display: flex;
        gap: 6px;
        padding: 12px 16px;
        overflow-x: auto;
        border-bottom: 1px solid #E2E8F0;
        scrollbar-width: none;
    }

    .pk-gift-cats::-webkit-scrollbar { display: none; }

    .pk-gift-cat {
        flex-shrink: 0;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1.5px solid #E2E8F0;
        background: #fff;
        font-family: inherit;
        font-size: .78rem;
        font-weight: 700;
        color: #64748B;
        cursor: pointer;
        transition: all .15s ease;
    }

    .pk-gift-cat.is-active {
        background: #EEECFC;
        border-color: #C7D2FE;
        color: #4338CA;
    }

    .pk-gift-grid-wrap {
        flex: 1;
        overflow-y: auto;
        padding: 14px 16px 18px;
    }

    .pk-gift-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    @media (min-width: 480px) {
        .pk-gift-grid { grid-template-columns: repeat(5, 1fr); }
    }

    .pk-gift-artifact {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 10px 6px;
        border-radius: 12px;
        border: 1.5px solid transparent;
        background: #F8FAFC;
        cursor: pointer;
        transition: all .15s ease;
        font-family: inherit;
    }

    .pk-gift-artifact:hover {
        background: #FFFBEB;
        border-color: #FDE68A;
        transform: translateY(-2px);
    }

    .pk-gift-artifact:disabled {
        opacity: .45;
        cursor: not-allowed;
        transform: none;
    }

    .pk-gift-artifact-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        background: #fff;
        border: 1px solid #E2E8F0;
        box-shadow: 0 2px 8px rgba(15, 17, 23, .04);
    }

    .pk-gift-artifact--premium .pk-gift-artifact-icon {
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        border-color: #FCD34D;
    }

    .pk-gift-artifact--payhankey .pk-gift-artifact-icon {
        background: linear-gradient(135deg, #EEECFC, #DDD6FE);
        border-color: #C7D2FE;
    }

    .pk-gift-artifact-name {
        font-size: .65rem;
        font-weight: 700;
        color: #0F1117;
        text-align: center;
        line-height: 1.2;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pk-gift-artifact-price {
        font-size: .68rem;
        font-weight: 800;
        color: #D97706;
    }

    .pk-gift-toast {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1070;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 999px;
        background: #0F1117;
        color: #fff;
        font-size: .85rem;
        font-weight: 600;
        box-shadow: 0 8px 32px rgba(15, 17, 23, .25);
        pointer-events: none;
    }

    .pk-gift-toast-emoji { font-size: 1.25rem; }

    .pk-gift-low-balance {
        margin: 0 16px 14px;
        padding: 12px 14px;
        border-radius: 10px;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        font-size: .82rem;
        font-weight: 600;
        color: #B91C1C;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .pk-gift-low-balance a {
        color: #5A4FDC;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    /* Spend celebration */
    .pk-gift-spend-flash {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 4;
        border-radius: inherit;
        opacity: 0;
        background: radial-gradient(circle at 50% 40%, rgba(251, 191, 36, .35), transparent 65%);
        transition: opacity .25s ease;
    }

    .pk-gift-spend-flash.is-active {
        opacity: 1;
        animation: pk-gift-flash 650ms ease forwards;
    }

    @keyframes pk-gift-flash {
        0% { opacity: 0; transform: scale(.96); }
        25% { opacity: 1; }
        100% { opacity: 0; transform: scale(1.02); }
    }

    .pk-gift-balance--spent {
        animation: pk-gift-balance-pulse 900ms ease;
        box-shadow: 0 0 0 0 rgba(245, 158, 11, .45);
    }

    @keyframes pk-gift-balance-pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, .5); }
        35% { transform: scale(1.06); box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    .pk-gift-artifact--sending {
        animation: pk-gift-artifact-send 600ms ease infinite;
    }

    .pk-gift-artifact--sent {
        animation: pk-gift-artifact-sent 700ms ease;
        border-color: #F59E0B !important;
        background: #FEF3C7 !important;
    }

    @keyframes pk-gift-artifact-send {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(.94); opacity: .75; }
    }

    @keyframes pk-gift-artifact-sent {
        0% { transform: scale(1); }
        40% { transform: scale(1.12); }
        100% { transform: scale(1); }
    }

    .pk-gift-burst-layer {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 1065;
        overflow: visible;
    }

    .pk-gift-particle {
        position: fixed;
        font-size: 1.2rem;
        line-height: 1;
        opacity: 0;
        transform: translate(-50%, -50%) scale(.4);
        animation: pk-gift-particle-burst 1.2s ease forwards;
        filter: drop-shadow(0 3px 10px rgba(245, 158, 11, .45));
        z-index: 1066;
    }

    .pk-gift-particle--hero {
        font-size: 2rem;
        animation-duration: 1.4s;
        z-index: 2;
    }

    @keyframes pk-gift-particle-burst {
        0% { opacity: 0; transform: translate(-50%, -50%) scale(.3); }
        15% { opacity: 1; transform: translate(-50%, -50%) scale(1.15); }
        100% {
            opacity: 0;
            transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty))) scale(.7) rotate(18deg);
        }
    }

    .pk-gift-toast--celebrate {
        animation: pk-gift-toast-pop 420ms ease;
        background: linear-gradient(135deg, #92400E, #B45309);
    }

    @keyframes pk-gift-toast-pop {
        0% { transform: translateX(-50%) scale(.85); opacity: 0; }
        60% { transform: translateX(-50%) scale(1.04); opacity: 1; }
        100% { transform: translateX(-50%) scale(1); }
    }

    .pk-gift-float {
        position: fixed;
        right: auto;
        left: 50%;
        bottom: 28%;
        z-index: 1067;
        pointer-events: none;
        animation: pk-gift-float 2.4s cubic-bezier(.22, 1, .36, 1) forwards;
        font-size: 3rem;
        transform: translateX(-50%);
        filter: drop-shadow(0 8px 20px rgba(245, 158, 11, .5));
    }

    @keyframes pk-gift-float {
        0% { opacity: 0; transform: translateY(24px) scale(.5) rotate(-8deg); }
        12% { opacity: 1; transform: translateY(0) scale(1.2) rotate(4deg); }
        55% { opacity: 1; transform: translateY(-90px) scale(1) rotate(-2deg); }
        100% { opacity: 0; transform: translateY(-160px) scale(.75) rotate(6deg); }
    }
</style>
<script>
    window.postGiftTrigger = window.postGiftTrigger || function (config) {
        return {
            postId: config.postId,

            init() {
                window.initPostGiftStore?.();
            },

            openGift() {
                window.dispatchEvent(new CustomEvent('pk-gift-open', {
                    detail: { postId: this.postId },
                }));
            },
        };
    };
    document.addEventListener('livewire:navigated', () => {
        window.dispatchEvent(new CustomEvent('pk-gifts-refresh-all'));
    });
</script>
@endonce
