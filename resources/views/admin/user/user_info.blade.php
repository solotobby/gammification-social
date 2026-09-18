@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        /* Modern Design System Variables & Polish */
        /* Simple & Elegant Hero */
        .dash-user-hero {
            background: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            box-shadow: var(--dash-shadow);
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.5rem;
        }

        .dash-user-hero__main {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
        }

        .dash-user-hero__identity {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            min-width: 280px;
        }

        .dash-user-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }

        .dash-user-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 2px solid var(--dash-border);
            object-fit: cover;
            display: block;
            background: #f1f5f9;
        }

        .dash-user-avatar--fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            text-transform: uppercase;
            letter-spacing: -0.02em;
            border: none;
        }

        .dash-user-status-dot {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2.5px solid var(--dash-surface);
        }
        .dash-user-status-dot--active { background: #10b981; }
        .dash-user-status-dot--shadow { background: #f59e0b; }
        .dash-user-status-dot--blocked { background: #ef4444; }

        .dash-user-hero__meta {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .dash-user-hero__title-row {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            flex-wrap: wrap;
        }

        .dash-user-hero__name {
            font-size: 1.375rem;
            font-weight: 700;
            color: var(--dash-text);
            margin: 0;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            line-height: 1.2;
        }

        .dash-user-hero__details {
            font-size: 0.875rem;
            color: var(--dash-muted);
            display: flex;
            align-items: center;
            gap: 0.625rem;
            flex-wrap: wrap;
        }

        .dash-user-hero__detail-item {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .dash-user-hero__sep {
            color: #cbd5e1;
            font-size: 0.75rem;
        }

        .dash-ref-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.65rem;
            background: #f8fafc;
            border: 1px solid var(--dash-border);
            border-radius: 8px;
            font-size: 0.8125rem;
            color: var(--dash-text);
        }

        .dash-user-hero__actions {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            flex-wrap: wrap;
        }

        .dash-copy-btn {
            background: none;
            border: none;
            padding: 0;
            color: var(--dash-muted);
            cursor: pointer;
            font-size: 0.8125rem;
            transition: color 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .dash-copy-btn:hover { color: var(--dash-accent); }

        /* Metric KPI enhancements */
        .dash-kpi {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.15rem 1.25rem;
            background: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            box-shadow: var(--dash-shadow);
            height: 100%;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }
        .dash-kpi:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
        }

        .dash-kpi__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .dash-kpi__label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dash-muted);
        }

        .dash-kpi__icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .dash-kpi__icon--indigo { background: #eef2ff; color: #4f46e5; }
        .dash-kpi__icon--emerald { background: #ecfdf5; color: #059669; }
        .dash-kpi__icon--amber { background: #fffbeb; color: #d97706; }
        .dash-kpi__icon--sky { background: #f0f9ff; color: #0284c7; }
        .dash-kpi__icon--violet { background: #f5f3ff; color: #7c3aed; }
        .dash-kpi__icon--rose { background: #fff1f2; color: #e11d48; }

        .dash-kpi__value {
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.15;
            color: var(--dash-text);
            margin-bottom: 0.35rem;
        }

        .dash-kpi__hint {
            font-size: 0.8125rem;
            color: var(--dash-muted);
            line-height: 1.35;
        }

        /* Section Headings with decorative accent */
        .dash-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.875rem;
            padding-left: 0.25rem;
        }
        .dash-section-head__title {
            font-size: 0.9375rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dash-muted);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }
        .dash-section-head__title i {
            color: var(--dash-accent);
            font-size: 0.875rem;
        }

        /* Interactive Tabs */
        .dash-nav-tabs {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid var(--dash-border);
            margin-bottom: 1.5rem;
            overflow-x: auto;
            padding-bottom: 0.25rem;
        }

        .dash-nav-tab {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.15rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dash-muted);
            border-radius: 10px;
            cursor: pointer;
            border: 1px solid transparent;
            background: transparent;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .dash-nav-tab:hover {
            color: var(--dash-text);
            background: rgba(99, 102, 241, 0.05);
        }
        .dash-nav-tab.is-active {
            color: var(--dash-accent);
            background: var(--dash-accent-soft);
            border-color: #c7d2fe;
        }

        .dash-tab-pane {
            display: none;
        }
        .dash-tab-pane.is-active {
            display: block;
            animation: fadeIn 0.2s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* DL Lists with refined styling */
        .dash-dl {
            display: grid;
            grid-template-columns: minmax(140px, 35%) 1fr;
            gap: 0.75rem 1rem;
            margin: 0;
            font-size: 0.875rem;
        }
        .dash-dl dt {
            margin: 0;
            font-weight: 600;
            color: var(--dash-muted);
        }
        .dash-dl dd {
            margin: 0;
            word-break: break-word;
            color: var(--dash-text);
        }

        /* Action Forms & Controls */
        .dash-action-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .dash-action-card .dash-card__body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .dash-field label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
        }
        .dash-field {
            margin-bottom: 1rem;
        }
        .dash-field:last-child {
            margin-bottom: 0;
        }

        .dash-select {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: 10px;
            border: 1px solid var(--dash-border);
            font: inherit;
            font-size: 0.875rem;
            background: var(--dash-surface);
            color: var(--dash-text);
        }
        .dash-select:focus {
            outline: none;
            border-color: #a5b4fc;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }

        .dash-btn--full {
            width: 100%;
            justify-content: center;
        }

        .dash-btn--danger {
            background: #e11d48;
            color: #ffffff !important;
            border-color: #e11d48;
        }
        .dash-btn--danger:hover {
            background: #be123c;
            border-color: #be123c;
        }

        .dash-activity-timeline {
            display: flex;
            flex-direction: column;
            gap: 0.875rem;
        }
        .dash-activity-item {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            padding-bottom: 0.875rem;
            border-bottom: 1px solid var(--dash-border);
        }
        .dash-activity-item:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }
        .dash-activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            font-size: 0.8125rem;
            flex-shrink: 0;
            background: #f1f5f9;
            color: var(--dash-muted);
        }

        .dash-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .dash-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        @media (max-width: 1100px) {
            .dash-grid--4, .dash-grid--3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 768px) {
            .dash-grid--4, .dash-grid--3, .dash-grid--2 { grid-template-columns: 1fr; }
            .dash-user-hero__main { flex-direction: column; align-items: flex-start; }
            .dash-user-hero__identity { align-items: center; }
            .dash-user-hero__actions { width: 100%; justify-content: flex-start; }
            .dash-dl { grid-template-columns: 1fr; gap: 0.25rem; }
            .dash-dl dt { font-size: 0.75rem; }
        }
    </style>
@endsection

@section('content')
    @php
        $currency = $user->wallet?->currency ?? 'USD';
        $currencySymbol = getCurrencyCode($currency);
        $planName = $level ?? 'Basic';
        $isInfluencer = $planName === 'Influencer';
        $isCreator = $planName === 'Creator';
        $isPaidPlan = $isInfluencer || $isCreator;

        $subscriptionActive = $subscription
            && $subscription->status === 'active'
            && $subscription->next_payment_date
            && $subscription->next_payment_date->isFuture();

        $statusClass = match ($user->status) {
            'ACTIVE' => 'dash-badge--emerald',
            'SHADOW_BANNED' => 'dash-badge--amber',
            'BLOCKED' => 'dash-badge--rose',
            default => 'dash-badge--gray',
        };

        $statusDotClass = match ($user->status) {
            'ACTIVE' => 'dash-user-status-dot--active',
            'SHADOW_BANNED' => 'dash-user-status-dot--shadow',
            'BLOCKED' => 'dash-user-status-dot--blocked',
            default => 'dash-user-status-dot--active',
        };

        // User Avatar URL or fallback initials
        $hasCustomAvatar = !empty($user->avatar);
        $avatarUrl = $hasCustomAvatar ? $user->avatar : null;
        $nameParts = explode(' ', trim($user->name));
        $initials = strtoupper(substr($nameParts[0] ?? 'U', 0, 1) . substr($nameParts[1] ?? ($nameParts[0] ?? ''), 0, 1));
        if (strlen($initials) < 2) {
            $initials = strtoupper(substr($user->username ?? 'U', 0, 2));
        }

        $userRole = $user->getRoleNames()->first() ?? 'user';
    @endphp

    <div class="content p-0">
        <div class="dash">

            {{-- Flash Alerts --}}
            @if (session('success'))
                <div class="dash-alert dash-alert--success">
                    <i class="fa fa-check-circle" style="margin-right:0.4rem"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">
                    <i class="fa fa-exclamation-triangle" style="margin-right:0.4rem"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Breadcrumb & Back navigation --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;color:var(--dash-muted);">
                    <a href="{{ route('admin.users.index') }}" class="dash-link">Users</a>
                    <span>/</span>
                    <span>Member Profile</span>
                    <span>/</span>
                    <strong style="color:var(--dash-text);">{{ '@' . $user->username }}</strong>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.users.index') }}" class="dash-btn dash-btn--ghost">
                        <i class="fa fa-arrow-left"></i> All users
                    </a>
                </div>
            </div>

            {{-- =============================================================== --}}
            {{-- USER PROFILE HERO CARD (SIMPLE & ELEGANT)                       --}}
            {{-- =============================================================== --}}
            <div class="dash-user-hero">
                <div class="dash-user-hero__main">
                    <div class="dash-user-hero__identity">
                        <div class="dash-user-avatar-wrap">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="dash-user-avatar">
                            @else
                                <div class="dash-user-avatar dash-user-avatar--fallback">{{ $initials }}</div>
                            @endif
                            <span class="dash-user-status-dot {{ $statusDotClass }}" title="Status: {{ $user->status }}"></span>
                        </div>
                        <div class="dash-user-hero__meta">
                            <div class="dash-user-hero__title-row">
                                <h1 class="dash-user-hero__name">
                                    {{ $user->name }}
                                    @if ($isInfluencer || $isCreator)
                                        <svg viewBox="0 0 22 22" style="width:18px;height:18px;display:inline-block;vertical-align:-2px;" fill="none" title="{{ $planName }} Verified">
                                            <circle cx="11" cy="11" r="11" fill="{{ $isInfluencer ? '#6366f1' : '#0284c7' }}" />
                                            <path d="M7 11l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endif
                                </h1>
                                <span class="dash-badge {{ $isInfluencer ? 'dash-badge--indigo' : ($isCreator ? 'dash-badge--indigo' : 'dash-badge--gray') }}">
                                    <i class="fa {{ $isInfluencer ? 'fa-crown' : ($isCreator ? 'fa-star' : 'fa-user') }}" style="margin-right:0.25rem;"></i>
                                    {{ $planName }}
                                </span>
                                <span class="dash-badge {{ $statusClass }}">
                                    <i class="fa fa-circle" style="font-size:0.4rem;margin-right:0.25rem;"></i>
                                    {{ str_replace('_', ' ', $user->status) }}
                                </span>
                                @if ($user->email_verified_at)
                                    <span class="dash-badge dash-badge--emerald" title="Email Verified on {{ $user->email_verified_at->format('M j, Y') }}">
                                        <i class="fa fa-check" style="margin-right:0.25rem;"></i> Verified
                                    </span>
                                @endif
                                @if ($subscription && ! $subscriptionActive)
                                    <span class="dash-badge dash-badge--amber">
                                        <i class="fa fa-clock" style="margin-right:0.25rem;"></i> Expired
                                    </span>
                                @endif
                            </div>

                            <div class="dash-user-hero__details">
                                <span class="dash-user-hero__detail-item">
                                    <strong style="color:var(--dash-text);">{{ '@' . $user->username }}</strong>
                                    <button type="button" class="dash-copy-btn" onclick="copyText('{{ $user->username }}', this)" title="Copy username">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </span>
                                <span class="dash-user-hero__sep">•</span>
                                <span class="dash-user-hero__detail-item">
                                    {{ $user->email }}
                                    <button type="button" class="dash-copy-btn" onclick="copyText('{{ $user->email }}', this)" title="Copy email">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </span>
                                @if ($user->phone)
                                    <span class="dash-user-hero__sep">•</span>
                                    <span class="dash-user-hero__detail-item">
                                        <i class="fa fa-phone" style="font-size:0.75rem;color:var(--dash-muted);"></i>
                                        {{ $user->phone }}
                                    </span>
                                @endif
                                <span class="dash-user-hero__sep">•</span>
                                <span class="dash-user-hero__detail-item dash-muted">
                                    Joined {{ $user->created_at?->format('M j, Y') }} ({{ $user->created_at?->diffForHumans() }})
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="dash-user-hero__actions">
                        @if ($user->referral_code)
                            <div class="dash-ref-tag" title="Referral Code">
                                <span class="dash-muted" style="font-size:0.75rem;">Ref:</span>
                                <strong>{{ $user->referral_code }}</strong>
                                <button type="button" class="dash-copy-btn" onclick="copyText('{{ $user->referral_code }}', this)" title="Copy referral code">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                        @endif
                        <a href="{{ url('profile/' . $user->username) }}" target="_blank" rel="noopener noreferrer" class="dash-btn dash-btn--ghost" title="View live public profile">
                            <i class="fa fa-external-link-alt"></i> Public profile
                        </a>
                        <a href="#actions" onclick="activateTab('actions')" class="dash-btn dash-btn--primary">
                            <i class="fa fa-sliders"></i> Manage account
                        </a>
                    </div>
                </div>
            </div>

            {{-- =============================================================== --}}
            {{-- INTERACTIVE NAVIGATION TABS                                     --}}
            {{-- =============================================================== --}}
            <nav class="dash-nav-tabs" aria-label="User tabs">
                <button type="button" class="dash-nav-tab is-active" data-tab="overview" onclick="activateTab('overview')">
                    <i class="fa fa-chart-pie"></i> Overview & Metrics
                </button>
                <button type="button" class="dash-nav-tab" data-tab="financials" onclick="activateTab('financials')">
                    <i class="fa fa-wallet"></i> Financials & PayKoin
                </button>
                <button type="button" class="dash-nav-tab" data-tab="content" onclick="activateTab('content')">
                    <i class="fa fa-newspaper"></i> Content & Activity
                </button>
                <button type="button" class="dash-nav-tab" data-tab="actions" onclick="activateTab('actions')">
                    <i class="fa fa-sliders"></i> Account Actions
                </button>
            </nav>

            {{-- =============================================================== --}}
            {{-- TAB 1: OVERVIEW & METRICS                                       --}}
            {{-- =============================================================== --}}
            <div id="tab-overview" class="dash-tab-pane is-active">

                {{-- Financial & Wallet Metrics --}}
                <div class="dash-section">
                    <div class="dash-section-head">
                        <h2 class="dash-section-head__title"><i class="fa fa-coins"></i> Wallet & Balances</h2>
                        <a href="#financials" onclick="activateTab('financials')" class="dash-link">View financial details &rarr;</a>
                    </div>
                    <div class="dash-grid dash-grid--4">
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Main Balance</span>
                                <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-wallet"></i></span>
                            </div>
                            <div class="dash-kpi__value">{{ $currencySymbol }}{{ number_format($user->wallet?->balance ?? 0, 2) }}</div>
                            <div class="dash-kpi__hint">Content earnings & registration bonuses</div>
                        </div>
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Referral Balance</span>
                                <span class="dash-kpi__icon dash-kpi__icon--emerald"><i class="fa fa-user-plus"></i></span>
                            </div>
                            <div class="dash-kpi__value">{{ $currencySymbol }}{{ number_format($user->wallet?->referral_balance ?? 0, 2) }}</div>
                            <div class="dash-kpi__hint">{{ number_format($referralsCount) }} referred member(s)</div>
                        </div>
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Promoter Balance</span>
                                <span class="dash-kpi__icon dash-kpi__icon--amber"><i class="fa fa-bullhorn"></i></span>
                            </div>
                            <div class="dash-kpi__value">{{ $currencySymbol }}{{ number_format($user->wallet?->promoter_balance ?? 0, 2) }}</div>
                            <div class="dash-kpi__hint">Ad promotion revenue</div>
                        </div>
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Total Withdrawn</span>
                                <span class="dash-kpi__icon dash-kpi__icon--sky"><i class="fa fa-money-bill-transfer"></i></span>
                            </div>
                            <div class="dash-kpi__value">{{ $currencySymbol }}{{ number_format($totalWithdrawals ?? 0, 2) }}</div>
                            <div class="dash-kpi__hint">Lifetime payouts: ₦{{ number_format($lifetimePayoutsSum, 2) }} ({{ $lifetimePayoutsCount }}x)</div>
                        </div>
                    </div>
                </div>

                {{-- Social & Content Reach Metrics --}}
                <div class="dash-section">
                    <div class="dash-section-head">
                        <h2 class="dash-section-head__title"><i class="fa fa-share-nodes"></i> Audience & Engagement Reach</h2>
                        @if ($isPaidPlan)
                            <a href="{{ route('admin.users.engagement', $user) }}" class="dash-link">Detailed engagement charts &rarr;</a>
                        @endif
                    </div>
                    <div class="dash-grid dash-grid--4">
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Published Posts</span>
                                <span class="dash-kpi__icon dash-kpi__icon--violet"><i class="fa fa-image"></i></span>
                            </div>
                            <div class="dash-kpi__value">{{ number_format($postsCount) }}</div>
                            <div class="dash-kpi__hint">
                                <a href="{{ route('admin.users.posts', $user) }}" class="dash-link">View all user posts</a>
                            </div>
                        </div>
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Post Views</span>
                                <span class="dash-kpi__icon dash-kpi__icon--sky"><i class="fa fa-eye"></i></span>
                            </div>
                            <div class="dash-kpi__value">{{ number_format($viewsReceived) }}</div>
                            <div class="dash-kpi__hint">Views generated by posts</div>
                        </div>
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Likes Received</span>
                                <span class="dash-kpi__icon dash-kpi__icon--rose"><i class="fa fa-heart"></i></span>
                            </div>
                            <div class="dash-kpi__value">{{ number_format($likesReceived) }}</div>
                            <div class="dash-kpi__hint">Total applause on published content</div>
                        </div>
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Comments Received</span>
                                <span class="dash-kpi__icon dash-kpi__icon--amber"><i class="fa fa-comments"></i></span>
                            </div>
                            <div class="dash-kpi__value">{{ number_format($commentsReceived) }}</div>
                            <div class="dash-kpi__hint">{{ number_format($commentsCount) }} comments posted by user</div>
                        </div>
                    </div>
                </div>

                {{-- PayKoin & Digital Assets Quick View --}}
                <div class="dash-section">
                    <div class="dash-section-head">
                        <h2 class="dash-section-head__title"><i class="fa fa-gem"></i> PayKoin & Gifting Economy</h2>
                        <a href="{{ route('admin.paykoin.index', ['tab' => 'transactions', 'q' => $user->username]) }}" class="dash-link">Platform PayKoin &rarr;</a>
                    </div>
                    <div class="dash-grid dash-grid--4">
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Spendable PK</span>
                                <span class="dash-kpi__icon dash-kpi__icon--amber"><i class="fa fa-coins"></i></span>
                            </div>
                            <div class="dash-kpi__value" style="color:#b45309;">{{ number_format($paykoin['spendable']) }} PK</div>
                            <div class="dash-kpi__hint">Balance available to send gifts</div>
                        </div>
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Earned PK</span>
                                <span class="dash-kpi__icon dash-kpi__icon--emerald"><i class="fa fa-hand-holding-dollar"></i></span>
                            </div>
                            <div class="dash-kpi__value" style="color:#059669;">{{ number_format($paykoin['earned']) }} PK</div>
                            <div class="dash-kpi__hint">Convertible to wallet balance</div>
                        </div>
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Gifts Sent / Received</span>
                                <span class="dash-kpi__icon dash-kpi__icon--violet"><i class="fa fa-gift"></i></span>
                            </div>
                            <div class="dash-kpi__value" style="font-size:1.15rem;line-height:1.3">
                                {{ number_format($paykoin['stats']['gifts_sent']) }} sent<br>
                                <span style="font-size:0.95rem;color:var(--dash-muted);">{{ number_format($paykoin['stats']['gifts_received']) }} received</span>
                            </div>
                            <div class="dash-kpi__hint">Live peer gifting activity</div>
                        </div>
                        <div class="dash-kpi">
                            <div class="dash-kpi__top">
                                <span class="dash-kpi__label">Top-ups & Credits</span>
                                <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-arrow-trend-up"></i></span>
                            </div>
                            <div class="dash-kpi__value" style="font-size:1.15rem;line-height:1.3">
                                +{{ number_format($paykoin['stats']['topups']) }} top-up<br>
                                <span style="font-size:0.95rem;color:var(--dash-muted);">+{{ number_format($paykoin['stats']['admin_credits']) }} admin credit</span>
                            </div>
                            <div class="dash-kpi__hint">{{ number_format($paykoin['stats']['converted']) }} PK converted to wallet</div>
                        </div>
                    </div>
                </div>

                {{-- Account Profile & Network Details --}}
                <div class="dash-section dash-grid dash-grid--2">
                    {{-- Account Master Details --}}
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title"><i class="fa fa-id-card" style="color:var(--dash-accent);margin-right:0.4rem;"></i> Account details</h2>
                            <span class="dash-pill"><i class="fa fa-hashtag"></i> ID: {{ substr($user->id, 0, 8) }}…</span>
                        </div>
                        <div class="dash-card__body">
                            <dl class="dash-dl">
                                <dt>User ID</dt>
                                <dd>
                                    <code>{{ $user->id }}</code>
                                    <button type="button" class="dash-copy-btn" onclick="copyText('{{ $user->id }}', this)" title="Copy ID">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </dd>
                                <dt>Full name</dt>
                                <dd>{{ $user->name }}</dd>
                                <dt>Username</dt>
                                <dd>{{ '@' . $user->username }}</dd>
                                <dt>Email address</dt>
                                <dd>{{ $user->email }}</dd>
                                <dt>Phone number</dt>
                                <dd>{{ $user->phone ?: 'Not provided' }}</dd>
                                <dt>Acquisition channel</dt>
                                <dd>{{ $user->heard ?: 'Organic / Direct' }}</dd>
                                <dt>Access code</dt>
                                <dd>{{ $access?->code ?: 'None assigned' }}</dd>
                                <dt>Base currency</dt>
                                <dd><strong>{{ $currency }}</strong> ({{ $currencySymbol }})</dd>
                                <dt>Followers / Following</dt>
                                <dd>{{ number_format($followersCount) }} followers · {{ number_format($followingCount) }} following</dd>
                                <dt>Communities</dt>
                                <dd>{{ number_format($communitiesOwnedCount) }} created · {{ number_format($communitiesJoinedCount) }} joined</dd>
                                <dt>Registered on</dt>
                                <dd>{{ $user->created_at?->format('M j, Y g:i A') }}</dd>
                            </dl>
                        </div>
                    </div>

                    {{-- Withdrawal Method & Banking --}}
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title"><i class="fa fa-building-columns" style="color:var(--dash-accent);margin-right:0.4rem;"></i> Withdrawal method</h2>
                            @if ($withdrawalMethod)
                                <span class="dash-badge dash-badge--emerald"><i class="fa fa-check"></i> Configured</span>
                            @else
                                <span class="dash-badge dash-badge--amber"><i class="fa fa-circle-exclamation"></i> Not set</span>
                            @endif
                        </div>
                        <div class="dash-card__body">
                            @if ($withdrawalMethod)
                                @if ($withdrawalMethod->payment_method === 'usdt')
                                    <dl class="dash-dl">
                                        <dt>Payment method</dt>
                                        <dd><span class="dash-badge dash-badge--indigo">USDT (Crypto)</span></dd>
                                        <dt>Wallet address</dt>
                                        <dd>
                                            <code>{{ maskCode($withdrawalMethod->usdt_wallet) }}</code>
                                            <button type="button" class="dash-copy-btn" onclick="copyText('{{ $withdrawalMethod->usdt_wallet }}', this)">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </dd>
                                        <dt>Last updated</dt>
                                        <dd class="dash-muted">{{ $withdrawalMethod->updated_at?->format('M j, Y') }}</dd>
                                    </dl>
                                @elseif ($withdrawalMethod->payment_method === 'paypal')
                                    <dl class="dash-dl">
                                        <dt>Payment method</dt>
                                        <dd><span class="dash-badge dash-badge--indigo">PayPal</span></dd>
                                        <dt>PayPal email</dt>
                                        <dd>
                                            <code>{{ maskCode($withdrawalMethod->paypal_email) }}</code>
                                            <button type="button" class="dash-copy-btn" onclick="copyText('{{ $withdrawalMethod->paypal_email }}', this)">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </dd>
                                        <dt>Last updated</dt>
                                        <dd class="dash-muted">{{ $withdrawalMethod->updated_at?->format('M j, Y') }}</dd>
                                    </dl>
                                @else
                                    <dl class="dash-dl">
                                        <dt>Account name</dt>
                                        <dd><strong>{{ $withdrawalMethod->account_name }}</strong></dd>
                                        <dt>Bank name</dt>
                                        <dd>{{ $withdrawalMethod->bank_name }}</dd>
                                        <dt>Account number</dt>
                                        <dd>
                                            <code>{{ $withdrawalMethod->account_number }}</code>
                                            <button type="button" class="dash-copy-btn" onclick="copyText('{{ $withdrawalMethod->account_number }}', this)">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </dd>
                                        <dt>Bank code</dt>
                                        <dd>{{ $withdrawalMethod->bank_code ?? '—' }}</dd>
                                        <dt>Last updated</dt>
                                        <dd class="dash-muted">{{ $withdrawalMethod->updated_at?->format('M j, Y') }}</dd>
                                    </dl>
                                @endif
                            @else
                                <div class="dash-empty" style="padding:1.5rem 0;">
                                    <i class="fa fa-credit-card" style="font-size:2rem;color:var(--dash-border);margin-bottom:0.5rem;display:block;"></i>
                                    No withdrawal method configured by the user yet.
                                </div>
                            @endif

                            <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--dash-border);display:flex;justify-content:space-between;align-items:center;">
                                <span class="dash-muted">Total Withdrawals Processed:</span>
                                <strong style="color:var(--dash-text);font-size:1.05rem;">{{ $currencySymbol }}{{ number_format($totalWithdrawals ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Stream Activity --}}
                @if ($recentActivities->isNotEmpty())
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title"><i class="fa fa-clock-rotate-left" style="color:var(--dash-accent);margin-right:0.4rem;"></i> Live activity feed</h2>
                            <span class="dash-muted">Last {{ $recentActivities->count() }} action(s)</span>
                        </div>
                        <div class="dash-card__body">
                            <div class="dash-activity-timeline">
                                @foreach ($recentActivities as $act)
                                    <div class="dash-activity-item">
                                        <div class="dash-activity-icon">
                                            @if ($act->event === 'comment')
                                                <i class="fa fa-comment text-primary"></i>
                                            @elseif ($act->event === 'views')
                                                <i class="fa fa-eye text-info"></i>
                                            @elseif ($act->event === 'like')
                                                <i class="fa fa-heart text-danger"></i>
                                            @elseif ($act->event === 'post')
                                                <i class="fa fa-pen-to-square text-success"></i>
                                            @else
                                                <i class="fa fa-bolt"></i>
                                            @endif
                                        </div>
                                        <div style="flex:1;">
                                            <div style="font-size:0.875rem;font-weight:600;color:var(--dash-text);">
                                                User triggered <code>{{ $act->event }}</code> event
                                            </div>
                                            <div class="dash-muted" style="font-size:0.75rem;">
                                                {{ \Carbon\Carbon::parse($act->created_at)->format('M j, Y g:i A') }}
                                                ({{ \Carbon\Carbon::parse($act->created_at)->diffForHumans() }})
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- =============================================================== --}}
            {{-- TAB 2: FINANCIALS & PAYKOIN                                     --}}
            {{-- =============================================================== --}}
            <div id="tab-financials" class="dash-tab-pane">

                {{-- PayKoin Summary + Admin Credit Form --}}
                <div class="dash-section dash-grid dash-grid--2">
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title"><i class="fa fa-gem" style="color:#d97706;margin-right:0.4rem;"></i> PayKoin ledger breakdown</h2>
                            <span class="dash-badge dash-badge--amber">{{ number_format($paykoin['total']) }} PK Total</span>
                        </div>
                        <div class="dash-card__body">
                            <dl class="dash-dl">
                                <dt>Spendable balance</dt>
                                <dd><strong class="dash-pk">{{ number_format($paykoin['spendable']) }} PK</strong></dd>
                                <dt>Earned from gifts</dt>
                                <dd><strong style="color:#059669;">{{ number_format($paykoin['earned']) }} PK</strong></dd>
                                <dt>Lifetime top-ups</dt>
                                <dd>+{{ number_format($paykoin['stats']['topups']) }} PK</dd>
                                <dt>Admin credits</dt>
                                <dd>+{{ number_format($paykoin['stats']['admin_credits']) }} PK</dd>
                                <dt>Gifts sent</dt>
                                <dd class="dash-muted">−{{ number_format($paykoin['stats']['gifts_sent']) }} PK</dd>
                                <dt>Gifts received</dt>
                                <dd>+{{ number_format($paykoin['stats']['gifts_received']) }} PK</dd>
                                <dt>Converted to wallet</dt>
                                <dd class="dash-muted">−{{ number_format($paykoin['stats']['converted']) }} PK</dd>
                            </dl>
                        </div>
                    </div>

                    @if (isAdmin())
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title"><i class="fa fa-circle-plus" style="color:var(--dash-accent);margin-right:0.4rem;"></i> Credit PayKoin</h2>
                            <span class="dash-pill">Admin action</span>
                        </div>
                        <div class="dash-card__body">
                            <p class="dash-muted" style="margin:0 0 1rem;">
                                Add spendable PayKoin directly to <strong>{{ '@' . $user->username }}</strong>. Requires your validation code.
                            </p>
                            <form method="POST" action="{{ route('admin.users.paykoin.credit', $user) }}" class="dash-form"
                                onsubmit="return confirm('Credit PayKoin to {{ $user->username }}?');">
                                @csrf
                                <div class="dash-field">
                                    <label for="pk_amount">Amount (PK)</label>
                                    <input type="number" id="pk_amount" name="pk_amount" class="dash-input" min="1" max="1000000" step="1" required placeholder="e.g. 500">
                                </div>
                                <div class="dash-field">
                                    <label for="paykoin-note">Reason / Note (optional)</label>
                                    <input type="text" id="paykoin-note" name="note" class="dash-input" maxlength="255" placeholder="e.g. Community promotion reward">
                                </div>
                                <div class="dash-field">
                                    <label for="paykoin-validation-code">Validation code</label>
                                    <input type="text" id="paykoin-validation-code" name="validationCode" class="dash-input" required autocomplete="off" placeholder="Enter validation code">
                                </div>
                                <button type="submit" class="dash-btn dash-btn--primary">
                                    <i class="fa fa-coins"></i> Credit PayKoin
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Recent Financial Transactions --}}
                <div class="dash-section dash-card">
                    <div class="dash-card__head">
                        <div>
                            <h2 class="dash-card__title"><i class="fa fa-receipt" style="color:var(--dash-accent);margin-right:0.4rem;"></i> Wallet transactions</h2>
                            <p class="dash-muted" style="margin:0.25rem 0 0;">Recent wallet movements, bonuses, and withdrawals</p>
                        </div>
                        <a href="{{ route('admin.users.transactions', $user) }}" class="dash-link">View all transactions &rarr;</a>
                    </div>
                    <div class="dash-card__body dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentTransactions as $tx)
                                        <tr>
                                            <td><code>{{ $tx->ref ?: substr($tx->id, 0, 10) }}</code></td>
                                            <td>
                                                <span class="dash-badge dash-badge--gray">{{ ucfirst(str_replace('_', ' ', $tx->type)) }}</span>
                                            </td>
                                            <td>{{ $tx->description ?: '—' }}</td>
                                            <td>
                                                <strong style="color:{{ $tx->action === 'Credit' ? '#059669' : '#e11d48' }};">
                                                    {{ $tx->action === 'Credit' ? '+' : '−' }}{{ $currencySymbol }}{{ number_format($tx->amount, 2) }}
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="dash-badge {{ $tx->status === 'successful' ? 'dash-badge--emerald' : 'dash-badge--amber' }}">
                                                    {{ ucfirst($tx->status) }}
                                                </span>
                                            </td>
                                            <td class="dash-muted">{{ $tx->created_at?->format('M j, Y g:i A') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <div class="dash-empty">No wallet transactions recorded for this user.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- PayKoin Transactions Ledger --}}
                <div class="dash-section dash-card">
                    <div class="dash-card__head">
                        <div>
                            <h2 class="dash-card__title"><i class="fa fa-list-check" style="color:var(--dash-accent);margin-right:0.4rem;"></i> PayKoin activity log</h2>
                            <p class="dash-muted" style="margin:0.25rem 0 0;">{{ $paykoin['transactions']->total() }} recorded event(s)</p>
                        </div>
                    </div>
                    <div class="dash-card__body dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>PK change</th>
                                        <th>Reference</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($paykoin['transactions'] as $tx)
                                        <tr>
                                            <td>
                                                <span class="dash-badge dash-badge--gray">
                                                    {{ $paykoin['type_labels'][$tx->type] ?? ucfirst(str_replace('_', ' ', $tx->type)) }}
                                                </span>
                                            </td>
                                            <td>{{ $tx->description ?: '—' }}</td>
                                            <td>
                                                <strong class="{{ $tx->pk_amount >= 0 ? 'dash-pk dash-pk--credit' : 'dash-pk dash-pk--debit' }}">
                                                    {{ $tx->pk_amount >= 0 ? '+' : '' }}{{ number_format($tx->pk_amount) }} PK
                                                </strong>
                                            </td>
                                            <td><code style="font-size:0.75rem;">{{ $tx->ref ?: '—' }}</code></td>
                                            <td class="dash-muted">{{ $tx->created_at?->format('M j, Y g:i A') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <div class="dash-empty">No PayKoin activity recorded yet.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($paykoin['transactions']->hasPages())
                            <div class="dash-pagination">{{ $paykoin['transactions']->links('pagination::bootstrap-5') }}</div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- =============================================================== --}}
            {{-- TAB 3: CONTENT & POSTS                                          --}}
            {{-- =============================================================== --}}
            <div id="tab-content" class="dash-tab-pane">

                <div class="dash-section dash-card">
                    <div class="dash-card__head">
                        <div>
                            <h2 class="dash-card__title"><i class="fa fa-newspaper" style="color:var(--dash-accent);margin-right:0.4rem;"></i> Published posts</h2>
                            <p class="dash-muted" style="margin:0.25rem 0 0;">{{ number_format($postsCount) }} total post(s) authored</p>
                        </div>
                        <a href="{{ route('admin.users.posts', $user) }}" class="dash-btn dash-btn--ghost">
                            <i class="fa fa-list"></i> Full posts manager
                        </a>
                    </div>
                    <div class="dash-card__body dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Post preview</th>
                                        <th>Stats</th>
                                        <th>Monetization</th>
                                        <th>Status</th>
                                        <th>Published</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentPosts as $post)
                                        <tr>
                                            <td style="max-width:320px;">
                                                <div style="font-weight:600;color:var(--dash-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                    {{ Str::limit($post->content, 60) }}
                                                </div>
                                                <div class="dash-muted" style="font-size:0.75rem;">
                                                    <code>{{ $post->unicode }}</code>
                                                    @if ($post->has_video) <span class="dash-badge dash-badge--indigo" style="font-size:0.625rem;padding:0.1rem 0.4rem;">Video</span> @endif
                                                    @if ($post->has_images) <span class="dash-badge dash-badge--indigo" style="font-size:0.625rem;padding:0.1rem 0.4rem;">Images</span> @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size:0.8125rem;display:flex;gap:0.75rem;align-items:center;">
                                                    <span title="Views"><i class="fa fa-eye text-muted"></i> {{ number_format($post->views) }}</span>
                                                    <span title="Likes"><i class="fa fa-heart text-danger"></i> {{ number_format($post->likes) }}</span>
                                                    <span title="Comments"><i class="fa fa-comment text-primary"></i> {{ number_format($post->comments) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($post->is_monetized)
                                                    <span class="dash-badge dash-badge--emerald"><i class="fa fa-check"></i> Monetized</span>
                                                @else
                                                    <span class="dash-badge dash-badge--gray">Unmonetized</span>
                                                @endif
                                                @if ($post->monetization_note)
                                                    <div class="dash-muted" style="font-size:0.72rem;margin-top:0.2rem;" title="{{ $post->monetization_note }}">
                                                        {{ Str::limit($post->monetization_note, 35) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="dash-badge {{ $post->status === 'LIVE' ? 'dash-badge--emerald' : 'dash-badge--amber' }}">
                                                    {{ $post->status }}
                                                </span>
                                            </td>
                                            <td class="dash-muted" style="font-size:0.8125rem;">
                                                {{ $post->created_at?->format('M j, Y') }}
                                            </td>
                                            <td>
                                                <a href="{{ url('posts/' . $post->unicode) }}" target="_blank" rel="noopener noreferrer" class="dash-link">
                                                    <i class="fa fa-external-link-alt"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <div class="dash-empty">This user has not published any posts yet.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Monthly Engagement Aggregates --}}
                @if ($monthlyStats->isNotEmpty())
                    <div class="dash-section dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title"><i class="fa fa-calendar-check" style="color:var(--dash-accent);margin-right:0.4rem;"></i> Historical monthly engagement</h2>
                        </div>
                        <div class="dash-card__body dash-card__body--flush">
                            <div class="dash-table-wrap">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Level</th>
                                            <th>Views</th>
                                            <th>Likes</th>
                                            <th>Comments</th>
                                            <th>Points</th>
                                            <th>Payout amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($monthlyStats as $ms)
                                            <tr>
                                                <td><strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $ms->month)->format('F Y') }}</strong></td>
                                                <td><span class="dash-badge dash-badge--indigo">{{ $ms->level }}</span></td>
                                                <td class="dash-num">{{ number_format($ms->views) }}</td>
                                                <td class="dash-num">{{ number_format($ms->likes) }}</td>
                                                <td class="dash-num">{{ number_format($ms->comments) }}</td>
                                                <td class="dash-num"><strong>{{ number_format($ms->points) }}</strong></td>
                                                <td class="dash-num">₦{{ number_format(convertToBaseCurrency($ms->amount ?? 0, 'NGN'), 2) }}</td>
                                                <td>
                                                    <span class="dash-badge {{ $ms->status === 'Paid' ? 'dash-badge--gray' : ($ms->status === 'Queued' ? 'dash-badge--amber' : 'dash-badge--emerald') }}">
                                                        {{ $ms->status ?? 'Pending' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- =============================================================== --}}
            {{-- TAB 4: ACCOUNT ACTIONS                                          --}}
            {{-- =============================================================== --}}
            <div id="tab-actions" class="dash-tab-pane">

                <div class="dash-section-head">
                    <h2 class="dash-section-head__title"><i class="fa fa-sliders"></i> Account control panel</h2>
                    <span class="dash-muted">Administrative actions for {{ '@' . $user->username }}</span>
                </div>

                <div class="dash-grid dash-grid--3">

                    {{-- Action 1: Upgrade / Plan Tier --}}
                    @if (isAdmin())
                    <div class="dash-card dash-action-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title"><i class="fa fa-arrow-up-right-dots" style="color:var(--dash-accent);margin-right:0.4rem;"></i> Plan & tier upgrade</h2>
                        </div>
                        <div class="dash-card__body">
                            <p class="dash-muted" style="margin:0 0 1rem;">
                                Upgrade user membership level. Adds next payment renewal date 30 days ahead.
                            </p>
                            <form method="POST" action="{{ route('admin.users.upgrade') }}" class="dash-form">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <div class="dash-field">
                                    <label for="level">Target tier</label>
                                    <select id="level" name="level" class="dash-select" required>
                                        <option value="">Select level</option>
                                        @foreach ($levels as $planOption)
                                            <option value="{{ $planOption->id }}" @selected($planOption->name === $planName)>
                                                {{ $planOption->name }} (₦{{ number_format(convertToBaseCurrency($planOption->amount, 'NGN'), 0) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="dash-field">
                                    <label for="upgrade-code">Validation code</label>
                                    <input type="text" id="upgrade-code" name="validationCode" class="dash-input" required autocomplete="off" placeholder="Enter validation code">
                                </div>
                                <button type="submit" class="dash-btn dash-btn--primary dash-btn--full">
                                    <i class="fa fa-crown"></i> Upgrade plan
                                </button>
                            </form>

                            @if (in_array($planName, ['Creator', 'Influencer'], true) && $userLevel)
                                <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--dash-border);">
                                    <form method="POST" action="{{ route('admin.users.bonus', [$user, $planName]) }}"
                                        onsubmit="return confirm('Credit registration bonus of {{ $currencySymbol }}{{ number_format(convertToBaseCurrency($userLevel->reg_bonus, $currency), 2) }} to {{ $user->username }}?');">
                                        @csrf
                                        <button type="submit" class="dash-btn dash-btn--ghost dash-btn--full">
                                            <i class="fa fa-gift"></i> Credit upgrade bonus ({{ $currencySymbol }}{{ number_format(convertToBaseCurrency($userLevel->reg_bonus, $currency), 2) }})
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Action 2: Change Account Status --}}
                    <div class="dash-card dash-action-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title"><i class="fa fa-shield-halved" style="color:#e11d48;margin-right:0.4rem;"></i> Account status & safety</h2>
                        </div>
                        <div class="dash-card__body">
                            <p class="dash-muted" style="margin:0 0 1rem;">
                                Control account visibility and platform permissions.
                            </p>
                            <form method="POST" action="{{ route('admin.users.status.update') }}" class="dash-form"
                                onsubmit="return confirm('Change status of {{ $user->username }} to ' + document.getElementById('status').value + '?');">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <div class="dash-field">
                                    <label for="status">Account status</label>
                                    <select id="status" name="status" class="dash-select" required>
                                        <option value="ACTIVE" @selected($user->status === 'ACTIVE')>ACTIVE (Standard access)</option>
                                        <option value="SHADOW_BANNED" @selected($user->status === 'SHADOW_BANNED')>SHADOW BANNED (Feed hidden)</option>
                                        <option value="BLOCKED" @selected($user->status === 'BLOCKED')>BLOCKED (Account suspended)</option>
                                    </select>
                                </div>
                                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:0.75rem;font-size:0.75rem;color:var(--dash-muted);margin-bottom:1rem;">
                                    <strong style="color:#334155;">Note:</strong> Shadow ban suppresses user posts from the public discovery feed without notifying them. Block completely prevents login.
                                </div>
                                <button type="submit" class="dash-btn dash-btn--danger dash-btn--full">
                                    <i class="fa fa-gavel"></i> Apply status change
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Action 3: Currency Switcher --}}
                    <div class="dash-card dash-action-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title"><i class="fa fa-money-bill-wave" style="color:var(--dash-accent);margin-right:0.4rem;"></i> Change currency</h2>
                        </div>
                        <div class="dash-card__body">
                            <p class="dash-muted" style="margin:0 0 1rem;">
                                Modifies the user wallet currency. <strong>Warning:</strong> resets saved withdrawal method.
                            </p>
                            <form method="POST" action="{{ route('admin.users.currency.update') }}" class="dash-form"
                                onsubmit="return confirm('Change currency for {{ $user->username }}? Any saved withdrawal method will be cleared.');">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <div class="dash-field">
                                    <label for="currency">Select base currency</label>
                                    <select id="currency" name="currency" class="dash-select" required>
                                        <option value="">Select currency</option>
                                        @foreach (countryList() as $country)
                                            <option value="{{ $country['code'] }}" @selected($currency === $country['code'])>
                                                {{ $country['code'] }} — {{ $country['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="font-size:0.75rem;color:#b45309;background:#fffbeb;border:1px solid #fef3c7;border-radius:8px;padding:0.75rem;margin-bottom:1rem;">
                                    <i class="fa fa-triangle-exclamation"></i> The user will need to re-add their bank details in their new currency before requesting withdrawals.
                                </div>
                                <button type="submit" class="dash-btn dash-btn--primary dash-btn--full">
                                    <i class="fa fa-rotate"></i> Update currency
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection

@section('script')
<script>
    // Tab switching logic
    function activateTab(tabName) {
        // Update tabs
        document.querySelectorAll('.dash-nav-tab').forEach(function(el) {
            if (el.dataset.tab === tabName) {
                el.classList.add('is-active');
            } else {
                el.classList.remove('is-active');
            }
        });

        // Update tab panes
        document.querySelectorAll('.dash-tab-pane').forEach(function(pane) {
            if (pane.id === 'tab-' + tabName) {
                pane.classList.add('is-active');
            } else {
                pane.classList.remove('is-active');
            }
        });

        // Update URL hash without scrolling
        if (history.replaceState) {
            history.replaceState(null, null, '#' + tabName);
        }
    }

    // Quick text copier helper
    function copyText(text, buttonElement) {
        if (!navigator.clipboard) {
            var tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            showCopyFeedback(buttonElement);
            return;
        }

        navigator.clipboard.writeText(text).then(function() {
            showCopyFeedback(buttonElement);
        });
    }

    function showCopyFeedback(el) {
        if (!el) return;
        var originalHtml = el.innerHTML;
        el.innerHTML = '<i class="fa fa-check" style="color:#10b981;"></i>';
        setTimeout(function() {
            el.innerHTML = originalHtml;
        }, 1500);
    }

    // Listen for hash change on load
    document.addEventListener('DOMContentLoaded', function() {
        var hash = window.location.hash.replace('#', '');
        var validTabs = ['overview', 'financials', 'content', 'actions'];
        if (hash && validTabs.indexOf(hash) !== -1) {
            activateTab(hash);
        }
    });
</script>
@endsection
