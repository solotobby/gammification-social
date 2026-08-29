@php
    $navUser = auth()->user();
    $navLevel = userLevel($navUser?->id);
    $navAvatar = $navUser?->avatar ?: asset('src/assets/media/avatars/avatar13.jpg');
    $navName = $navUser?->name ?? 'Account';
@endphp

<style>
    #page-header.pk-header {
        --n-ink: #111827;
        --n-mute: #6b7280;
        --n-line: #e5e7eb;
        --n-hover: #f3f4f6;
        --n-accent: #5A4FDC;
        height: 52px;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        background: #fff !important;
        border-bottom: 1px solid var(--n-line);
        box-shadow: none;
        z-index: 1045 !important;
        overflow: visible !important;
    }

    /* Beat Dashmix .content-header { margin:0 auto } shrink-wrap on mobile */
    #page-container > #page-header.pk-header .content-header,
    #page-header.pk-header .content-header {
        width: 100% !important;
        max-width: none !important;
        height: 52px;
        min-height: 52px;
        margin: 0 !important;
        padding: 0 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        background: #fff !important;
        box-sizing: border-box;
    }

    @media (min-width: 768px) {
        #page-header.pk-header .content-header { padding: 0 20px; }
    }

    @media (min-width: 1200px) {
        #page-container.main-content-narrow > #page-header.pk-header .content-header {
            width: 100% !important;
        }
    }

    .pk-n {
        display: flex;
        align-items: center;
        gap: 4px;
        min-width: 0;
    }

    .pk-n-right {
        margin-left: auto;
        gap: 2px;
    }

    .pk-n-btn {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #374151;
        display: inline-grid;
        place-items: center;
        cursor: pointer;
        text-decoration: none;
        flex: none;
        padding: 0;
        transition: background .12s ease, color .12s ease;
    }

    .pk-n-btn:hover,
    .pk-n-btn:focus-visible {
        background: var(--n-hover);
        color: var(--n-ink);
        outline: none;
        text-decoration: none;
    }

    .pk-n-btn i { font-size: 15px; line-height: 1; }

    .pk-n-brand {
        display: inline-flex;
        align-items: center;
        margin-left: 2px;
        padding: 4px 6px;
        border-radius: 6px;
        text-decoration: none;
        color: var(--n-ink);
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.02em;
        line-height: 1;
    }

    .pk-n-brand:hover {
        color: var(--n-ink);
        text-decoration: none;
        background: var(--n-hover);
    }

    .pk-n-bell,
    .pk-n-msg {
        position: relative;
    }

    .pk-n-count {
        position: absolute;
        top: 2px;
        right: 0;
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        line-height: 16px;
        text-align: center;
        border: 1.5px solid #fff;
        pointer-events: none;
    }

    .pk-n-dot {
        position: absolute;
        top: 7px;
        right: 7px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ef4444;
        border: 1.5px solid #fff;
    }

    .pk-n-avatar-btn {
        width: 36px;
        height: 36px;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: transparent;
        cursor: pointer;
        display: inline-grid;
        place-items: center;
        margin-left: 2px;
    }

    .pk-n-avatar-btn:hover img,
    .pk-n-avatar-btn:focus-visible img {
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(90, 79, 220, .35);
        outline: none;
    }

    .pk-n-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        background: #e5e7eb;
    }

    #page-header.pk-header .content-header,
    #page-header.pk-header .pk-n-right {
        overflow: visible !important;
    }

    [data-pk-dropdown] {
        position: relative;
    }

    [data-pk-dropdown-menu] {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        left: auto;
        z-index: 1050;
    }

    [data-pk-dropdown-menu].pk-dropdown-open {
        display: block !important;
    }

    .pk-n-menu {
        min-width: 220px;
        margin-top: 0 !important;
        padding: 6px;
        border: 1px solid var(--n-line);
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(17, 24, 39, .1);
    }

    .pk-n-menu-top {
        padding: 8px 10px 10px;
        border-bottom: 1px solid var(--n-line);
        margin-bottom: 4px;
    }

    .pk-n-menu-top strong {
        display: block;
        font-size: 13px;
        font-weight: 650;
        color: var(--n-ink);
        line-height: 1.25;
    }

    .pk-n-menu-top span {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        color: var(--n-mute);
    }

    .pk-n-menu .dropdown-item {
        border-radius: 6px;
        padding: 8px 10px;
        font-size: 13px;
        font-weight: 500;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pk-n-menu .dropdown-item i {
        width: 16px;
        text-align: center;
        color: var(--n-mute);
        font-size: 13px;
    }

    .pk-n-menu .dropdown-item:hover,
    .pk-n-menu .dropdown-item:focus {
        background: var(--n-hover);
        color: var(--n-ink);
    }

    .pk-n-menu .dropdown-divider {
        margin: 4px 6px;
        border-color: var(--n-line);
        opacity: 1;
    }

    .pk-n-menu .is-danger { color: #b91c1c; }
    .pk-n-menu .is-danger:hover {
        background: #fef2f2;
        color: #b91c1c;
    }
    .pk-n-menu .is-danger:hover i { color: #b91c1c; }

    .pk-n-notify {
        min-width: min(300px, 92vw);
        margin-top: 0 !important;
        padding: 0;
        border: 1px solid var(--n-line);
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(17, 24, 39, .1);
        overflow: hidden;
    }

    .pk-n-notify-h {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        border-bottom: 1px solid var(--n-line);
        font-size: 13px;
        font-weight: 650;
        color: var(--n-ink);
        background: #fff;
    }

    .pk-n-notify-h button {
        border: 0;
        background: transparent;
        color: var(--n-accent);
        font-size: 12px;
        font-weight: 600;
        padding: 0;
        cursor: pointer;
    }

    .pk-n-notify-list {
        list-style: none;
        margin: 0;
        padding: 4px 0;
        max-height: 320px;
        overflow-y: auto;
    }

    .pk-n-notify-list a {
        display: block;
        padding: 10px 14px;
        text-decoration: none;
        color: var(--n-ink);
    }

    .pk-n-notify-list a:hover { background: var(--n-hover); }

    .pk-n-notify-list .t {
        font-size: 13px;
        font-weight: 550;
        line-height: 1.35;
    }

    .pk-n-notify-list .m {
        margin-top: 2px;
        font-size: 11px;
        color: var(--n-mute);
    }

    .pk-n-notify-empty {
        padding: 28px 14px;
        text-align: center;
        color: var(--n-mute);
        font-size: 13px;
    }
</style>

<header id="page-header" class="pk-header">
    <div class="content-header">
        <div class="pk-n">
            <button type="button"
                class="pk-n-btn"
                data-toggle="layout"
                data-action="sidebar_toggle"
                aria-label="Open menu">
                <i class="fa fa-bars"></i>
            </button>

            <a href="{{ url('home') }}" class="pk-n-brand">Payhankey</a>
        </div>

        <div class="pk-n pk-n-right">
            <a href="{{ url('search/user') }}" class="pk-n-btn" aria-label="Search">
                <i class="fa fa-search"></i>
            </a>

            @auth
                @if ($navUser->hasRole('user'))
                    <livewire:user.navbar-messages />
                @endif
                <livewire:user.navbar-notifications />
            @endauth

            <div class="dropdown d-inline-flex" data-pk-dropdown>
                <button type="button"
                    class="pk-n-avatar-btn"
                    id="page-header-user-dropdown"
                    data-pk-dropdown-toggle
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-label="Account menu">
                    <img src="{{ $navAvatar }}" alt="" class="pk-n-avatar">
                </button>

                <div class="dropdown-menu dropdown-menu-end pk-n-menu"
                    data-pk-dropdown-menu
                    aria-labelledby="page-header-user-dropdown">
                    <div class="pk-n-menu-top">
                        <strong>{{ $navName }}</strong>
                        <span>{{ '@' . $navUser->username }} · {{ $navLevel }}</span>
                    </div>
                    <a class="dropdown-item" href="{{ url('profile/' . $navUser->username) }}">
                        <i class="far fa-user"></i> Profile
                    </a>
                    <a class="dropdown-item" href="{{ url('wallets') }}">
                        <i class="fa fa-wallet"></i> Wallet
                    </a>
                    <a class="dropdown-item" href="{{ url('analytics') }}">
                        <i class="si si-bar-chart"></i> Analytics
                    </a>
                    <a class="dropdown-item" href="{{ url('upgrade') }}">
                        <i class="fa fa-arrow-up"></i> Upgrade
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ url('settings') }}">
                        <i class="si si-settings"></i> Settings
                    </a>
                    <a class="dropdown-item is-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="far fa-arrow-alt-circle-left"></i> Sign out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
