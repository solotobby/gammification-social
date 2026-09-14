@php
    $bnavUser = auth()->user();
    $bnavAvatar = $bnavUser?->avatar ?: asset('src/assets/media/avatars/avatar13.jpg');
    $bnavProfileUrl = $bnavUser ? url('profile/' . $bnavUser->username) : url('login');

    $isHome = request()->is('home') 
        || request()->is('user/home') 
        || request()->is('timeline*') 
        || request()->is('dashboard-timeline') 
        || request()->is('new-timeline');

    $isCommunities = request()->routeIs('community*') 
        || request()->is('community*') 
        || request()->is('c/*')
        || request()->is('communities');

    $isRolls = request()->routeIs('rolls.*') 
        || request()->is('rolls*');

    $isWallet = request()->is('wallets*') 
        || request()->is('transaction/list*');

    $isProfile = $bnavUser && (
        request()->is('profile/' . $bnavUser->username) 
        || request()->is('profile/' . $bnavUser->username . '/*')
    );
@endphp

<style>
    /* ═══════════════════════════════════════════════════════════════
       MOBILE BOTTOM NAVIGATION — Payhankey Custom Mobile Design
       ═══════════════════════════════════════════════════════════════ */
    .pk-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 1040;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border-top: 1px solid rgba(229, 231, 235, 0.9);
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
        padding-bottom: env(safe-area-inset-bottom, 0px);
        user-select: none;
        -webkit-user-select: none;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, border-color 0.2s ease;
    }

    /* Dark mode */
    #page-container.dark-mode .pk-bottom-nav,
    .dark-mode .pk-bottom-nav {
        background: rgba(18, 24, 38, 0.94);
        border-top-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.35);
    }

    .pk-bottom-nav-inner {
        display: flex;
        align-items: stretch;
        justify-content: space-around;
        height: 56px;
        max-width: 540px;
        margin: 0 auto;
        padding: 0 4px;
        box-sizing: border-box;
    }

    .pk-bnav-item {
        position: relative;
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #64748b;
        text-decoration: none !important;
        padding: 6px 2px 4px 2px;
        border-radius: 8px;
        transition: color 0.16s ease, transform 0.12s ease;
        -webkit-tap-highlight-color: transparent;
    }

    .pk-bnav-item:active {
        transform: scale(0.92);
    }

    .pk-bnav-item:hover {
        color: #334155;
        text-decoration: none;
    }

    #page-container.dark-mode .pk-bnav-item,
    .dark-mode .pk-bnav-item {
        color: #94a3b8;
    }

    #page-container.dark-mode .pk-bnav-item:hover,
    .dark-mode .pk-bnav-item:hover {
        color: #f1f5f9;
    }

    /* Top active indicator line */
    .pk-bnav-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 3px;
        border-radius: 0 0 4px 4px;
        background: transparent;
        transition: width 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease;
    }

    .pk-bnav-item.active::before {
        width: 22px;
        background: #5A4FDC;
        box-shadow: 0 2px 8px rgba(90, 79, 220, 0.45);
    }

    #page-container.dark-mode .pk-bnav-item.active::before,
    .dark-mode .pk-bnav-item.active::before {
        background: #818cf8;
        box-shadow: 0 2px 8px rgba(129, 140, 248, 0.5);
    }

    /* Icon Box */
    .pk-bnav-icon-box {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 24px;
        margin-bottom: 2px;
    }

    .pk-bnav-icon {
        font-size: 18px;
        line-height: 1;
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.16s ease;
    }

    /* Label */
    .pk-bnav-label {
        font-size: 10.5px;
        font-weight: 550;
        line-height: 1.15;
        letter-spacing: -0.01em;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        max-width: 100%;
        transition: color 0.16s ease, font-weight 0.16s ease;
    }

    /* Active State */
    .pk-bnav-item.active {
        color: #5A4FDC;
    }

    .pk-bnav-item.active .pk-bnav-icon {
        transform: scale(1.12);
        color: #5A4FDC;
    }

    .pk-bnav-item.active .pk-bnav-label {
        color: #5A4FDC;
        font-weight: 700;
    }

    #page-container.dark-mode .pk-bnav-item.active,
    #page-container.dark-mode .pk-bnav-item.active .pk-bnav-icon,
    #page-container.dark-mode .pk-bnav-item.active .pk-bnav-label,
    .dark-mode .pk-bnav-item.active,
    .dark-mode .pk-bnav-item.active .pk-bnav-icon,
    .dark-mode .pk-bnav-item.active .pk-bnav-label {
        color: #818cf8;
    }

    /* ── Rolls Tab Distinctive Style ── */
    .pk-bnav-rolls .pk-bnav-icon {
        font-size: 21px;
    }

    .pk-bnav-rolls.active .pk-bnav-icon {
        color: #fe2c55;
        background: linear-gradient(135deg, #fe2c55 10%, #5A4FDC 90%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        transform: scale(1.18);
    }

    .pk-bnav-rolls.active::before {
        background: linear-gradient(90deg, #fe2c55, #5A4FDC);
        box-shadow: 0 2px 8px rgba(254, 44, 85, 0.45);
    }

    /* ── Profile Avatar ── */
    .pk-bnav-avatar-wrap {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        padding: 1px;
        border: 1.5px solid #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        transition: border-color 0.16s ease, transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .pk-bnav-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        display: block;
    }

    .pk-bnav-item.active .pk-bnav-avatar-wrap {
        border-color: #5A4FDC;
        border-width: 2px;
        transform: scale(1.12);
        box-shadow: 0 0 0 2px rgba(90, 79, 220, 0.22);
    }

    #page-container.dark-mode .pk-bnav-item.active .pk-bnav-avatar-wrap,
    .dark-mode .pk-bnav-item.active .pk-bnav-avatar-wrap {
        border-color: #818cf8;
        box-shadow: 0 0 0 2px rgba(129, 140, 248, 0.25);
    }

    /* ── Responsive Viewport Rules ── */
    @media (max-width: 991.98px) {
        body {
            padding-bottom: calc(58px + env(safe-area-inset-bottom, 0px)) !important;
        }

        #page-container {
            padding-bottom: 0 !important;
        }

        #page-footer {
            padding-bottom: 12px;
            margin-bottom: 0;
        }

        /* Lift any floating action button above the bottom nav */
        .float {
            bottom: calc(76px + env(safe-area-inset-bottom, 0px)) !important;
            right: 20px !important;
            width: 52px !important;
            height: 52px !important;
            font-size: 24px !important;
        }
        .float .my-float {
            margin-top: 14px !important;
        }
    }

    /* Completely hide on desktop/tablet where sidebar is visible */
    @media (min-width: 992px) {
        .pk-bottom-nav {
            display: none !important;
        }
    }
</style>

<nav class="pk-bottom-nav d-lg-none" aria-label="Mobile Bottom Navigation">
    <div class="pk-bottom-nav-inner">
        {{-- 1. Home --}}
        <a href="{{ url('home') }}" class="pk-bnav-item{{ $isHome ? ' active' : '' }}" aria-label="Home">
            <span class="pk-bnav-icon-box">
                <i class="fa fa-home pk-bnav-icon"></i>
            </span>
            <span class="pk-bnav-label">Home</span>
        </a>

        {{-- 2. Communities --}}
        <a href="{{ route('community') }}" class="pk-bnav-item{{ $isCommunities ? ' active' : '' }}" aria-label="Communities">
            <span class="pk-bnav-icon-box">
                <i class="fa fa-users pk-bnav-icon"></i>
            </span>
            <span class="pk-bnav-label">Communities</span>
        </a>

        {{-- 3. Rolls --}}
        <a href="{{ route('rolls.random') }}" class="pk-bnav-item pk-bnav-rolls{{ $isRolls ? ' active' : '' }}" aria-label="Rolls">
            <span class="pk-bnav-icon-box">
                <i class="fa fa-circle-play pk-bnav-icon"></i>
            </span>
            <span class="pk-bnav-label">Rolls</span>
        </a>

        {{-- 4. Wallet --}}
        <a href="{{ url('wallets') }}" class="pk-bnav-item{{ $isWallet ? ' active' : '' }}" aria-label="Wallet">
            <span class="pk-bnav-icon-box">
                <i class="fa fa-wallet pk-bnav-icon"></i>
            </span>
            <span class="pk-bnav-label">Wallet</span>
        </a>

        {{-- 5. Profile --}}
        <a href="{{ $bnavProfileUrl }}" class="pk-bnav-item{{ $isProfile ? ' active' : '' }}" aria-label="Profile">
            <span class="pk-bnav-icon-box">
                @if ($bnavUser && $bnavUser->avatar)
                    <span class="pk-bnav-avatar-wrap">
                        <img src="{{ $bnavAvatar }}" alt="" class="pk-bnav-avatar">
                    </span>
                @else
                    <i class="far fa-user pk-bnav-icon"></i>
                @endif
            </span>
            <span class="pk-bnav-label">Profile</span>
        </a>
    </div>
</nav>
