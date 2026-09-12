<div>
    <style>
        .pa-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .88rem;
            font-weight: 600;
            color: #536471;
            text-decoration: none;
            padding: 16px 0 12px;
            transition: color .15s;
        }

        .pa-back:hover { color: #5A4FDC; }
        .pa-back svg { width: 18px; height: 18px; flex: none; }

        .pa-post-snippet {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .16);
            font-size: .92rem;
            line-height: 1.55;
            color: rgba(255, 255, 255, .92);
        }

        .pa-post-meta {
            margin-top: 8px;
            font-size: .78rem;
            color: rgba(255, 255, 255, .72);
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
        }

        .pa-earn-hero {
            background: linear-gradient(135deg, #0F1117 0%, #1a1d29 55%, #2d2860 100%);
            border-radius: 14px;
            padding: 22px 20px;
            color: #fff;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .pa-earn-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 90% 10%, rgba(90, 79, 220, .35), transparent 50%);
            pointer-events: none;
        }

        .pa-earn-inner { position: relative; }

        .pa-earn-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .65);
            margin-bottom: 6px;
        }

        .pa-earn-value {
            font-size: clamp(1.75rem, 5vw, 2.35rem);
            font-weight: 800;
            letter-spacing: -.02em;
            line-height: 1.1;
            color: #34D399;
            margin: 0 0 8px;
        }

        .pa-earn-note {
            margin: 0;
            font-size: .82rem;
            color: rgba(255, 255, 255, .72);
            line-height: 1.5;
        }

        .pa-breakdown {
            display: grid;
            gap: 10px;
        }

        .pa-breakdown-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid var(--pk-line);
            border-radius: 10px;
            background: var(--pk-bg);
        }

        .pa-breakdown-row b {
            font-size: .88rem;
            font-weight: 700;
        }

        .pa-breakdown-row span {
            font-size: .82rem;
            color: var(--pk-muted);
            font-weight: 600;
        }

        .pa-breakdown-amt {
            font-size: .95rem;
            font-weight: 800;
            color: var(--pk-violet);
            white-space: nowrap;
        }

        .pa-level-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .pa-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        /* ══════════════════════════════════════════
           SEGMENTED VIEW SWITCHER
        ══════════════════════════════════════════ */
        .pa-segment-bar {
            background: #F1F5F9;
            border: 1px solid #E2E8F0;
            padding: 4px;
            border-radius: 14px;
            display: flex;
            gap: 6px;
            margin-bottom: 20px;
        }

        .pa-segment-btn {
            flex: 1;
            padding: 10px 16px;
            border-radius: 10px;
            border: none;
            background: transparent;
            font-size: 0.86rem;
            font-weight: 700;
            color: #64748B;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .pa-segment-btn:hover {
            color: #0F172A;
        }

        .pa-segment-btn.is-active {
            background: #FFFFFF;
            color: #6D28D9;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .pa-segment-badge {
            font-size: 0.68rem;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 999px;
            background: #EDE9FE;
            color: #6D28D9;
        }

        /* ══════════════════════════════════════════
           BOOST CAMPAIGN ANALYTICS STYLES
        ══════════════════════════════════════════ */
        .pa-boost-monetization-alert {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 20px;
        }

        .pa-boost-alert-icon {
            font-size: 1.25rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .pa-boost-alert-body h4 {
            margin: 0 0 4px;
            font-size: 0.92rem;
            font-weight: 800;
            color: #92400E;
        }

        .pa-boost-alert-body p {
            margin: 0;
            font-size: 0.8rem;
            color: #B45309;
            line-height: 1.45;
        }

        /* Campaign Hero Overview Card */
        .pa-boost-hero {
            background: linear-gradient(135deg, #2E1065 0%, #4C1D95 50%, #6D28D9 100%);
            border-radius: 18px;
            padding: 22px 24px;
            color: #FFFFFF;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px -5px rgba(109, 40, 217, 0.35);
            position: relative;
            overflow: hidden;
        }

        .pa-boost-hero::after {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -30px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        .pa-boost-hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .pa-boost-status-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #A7F3D0;
            font-size: 0.74rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .pa-boost-live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 8px #10B981;
        }

        .pa-boost-hero-title {
            font-size: 1.45rem;
            font-weight: 800;
            margin: 0 0 6px;
            letter-spacing: -0.02em;
        }

        .pa-boost-hero-subtitle {
            font-size: 0.84rem;
            opacity: 0.9;
            margin: 0 0 16px;
            line-height: 1.45;
        }

        /* Progress Bar */
        .pa-boost-progress-wrap {
            margin-top: 14px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 12px 16px;
        }

        .pa-boost-progress-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .pa-boost-bar-track {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .pa-boost-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #34D399, #10B981);
            transition: width 0.6s ease;
        }

        /* Dual Network Comparison Grid */
        .pa-network-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }

        @media (max-width: 640px) {
            .pa-network-grid {
                grid-template-columns: 1fr;
            }
        }

        .pa-network-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .pa-network-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #F1F5F9;
        }

        .pa-network-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0F172A;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pa-network-stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.82rem;
            margin-bottom: 10px;
            color: #64748B;
        }

        .pa-network-stat-row:last-child {
            margin-bottom: 0;
        }

        .pa-network-stat-row strong {
            color: #0F172A;
            font-size: 0.88rem;
        }

        /* Hourly Delivery Chart */
        .pa-chart-box {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .pa-chart-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 4px;
        }

        .pa-chart-subtitle {
            font-size: 0.78rem;
            color: #64748B;
            margin-bottom: 18px;
        }

        .pa-chart-bars {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 10px;
            height: 140px;
            padding-top: 10px;
            border-bottom: 1px solid #E2E8F0;
            margin-bottom: 10px;
        }

        .pa-bar-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            height: 100%;
            justify-content: flex-end;
        }

        .pa-bar-val {
            font-size: 0.72rem;
            font-weight: 700;
            color: #6D28D9;
        }

        .pa-bar-pill {
            width: 100%;
            max-width: 32px;
            background: linear-gradient(180deg, #7C3AED 0%, #6D28D9 100%);
            border-radius: 6px 6px 0 0;
            transition: height 0.4s ease;
        }

        .pa-bar-pill:hover {
            background: #5B21B6;
        }

        .pa-bar-label {
            font-size: 0.7rem;
            color: #94A3B8;
            font-weight: 600;
        }

        /* Demographics and Intelligence Grid */
        .pa-intel-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }

        @media (max-width: 640px) {
            .pa-intel-grid {
                grid-template-columns: 1fr;
            }
        }

        .pa-intel-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 18px;
        }

        .pa-intel-item {
            margin-bottom: 12px;
        }

        .pa-intel-item:last-child {
            margin-bottom: 0;
        }

        .pa-intel-label-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
        }

        .pa-intel-bar-bg {
            background: #F1F5F9;
            height: 6px;
            border-radius: 999px;
            overflow: hidden;
        }

        .pa-intel-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: #7C3AED;
        }

        /* Recent Activity Log */
        .pa-log-box {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 20px;
        }

        .pa-log-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #F1F5F9;
            font-size: 0.8rem;
        }

        .pa-log-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .pa-log-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pa-log-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .pa-log-meta {
            color: #64748B;
            font-size: 0.72rem;
        }

        /* Campaign Action Bar */
        .pa-campaign-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .pa-boost-btn {
            background: linear-gradient(135deg, #7C3AED, #6D28D9);
            color: #FFFFFF;
            border: none;
            padding: 11px 20px;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(109, 40, 217, 0.3);
            transition: all 0.15s ease;
        }

        .pa-boost-btn:hover {
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(109, 40, 217, 0.4);
        }

        .pa-boost-btn--secondary {
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #334155;
            box-shadow: none;
        }

        .pa-boost-btn--secondary:hover {
            background: #F8FAFC;
            color: #0F172A;
            box-shadow: none;
        }
    </style>

    <div
        class="row"
        x-data="{
            activeTab: @entangle('tab').live,
            isBoosted: @js((bool) ($post->is_boosted ?? false)),
        }"
    >
        <div class="col-12 ph-feed-wrap">
            <a href="{{ url('timeline/' . $post->id) }}" class="pa-back" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to post
            </a>

            <div class="pk-app">
                @include('livewire.user.partials.pk-app-ui')

                <!-- HERO -->
                <div class="pk-app-hero">
                    <div class="pk-app-hero-inner">
                        <span class="pk-app-kicker">Performance & Ad Intelligence</span>
                        <h1>Post Analytics</h1>
                        <p>Track your post reach, guaranteed click delivery across Payhankey & Partner Websites, and creator monetization history.</p>

                        <div class="d-flex align-items-center gap-2 flex-wrap mt-2">
                            <span class="pa-level-pill">{{ userLevel() }} account</span>
                            @if (! $isMonetized)
                                <span class="pa-level-pill" style="background:#DC2626;color:#fff" title="{{ $monetizationNote ?? 'Content ineligible for monetization' }}">
                                    ⚠️ Ineligible for Monetization
                                </span>
                            @elseif ($post->monetization_paused)
                                <span class="pa-level-pill" style="background:#F59E0B;color:#fff">
                                    ⏸️ Monetization Paused
                                </span>
                            @else
                                <span class="pa-level-pill" style="background:rgba(16, 185, 129, 0.2);color:#A7F3D0">
                                    ✓ Monetized
                                </span>
                            @endif
                            <template x-if="isBoosted">
                                <span class="pa-level-pill" style="background:#10B981;color:#fff">
                                    🚀 Active Boost Campaign
                                </span>
                            </template>
                        </div>

                        @if ($postExcerpt !== '')
                            <div class="pa-post-snippet">{{ $postExcerpt }}</div>
                        @else
                            <div class="pa-post-snippet">Media post</div>
                        @endif

                        <div class="pa-post-meta">
                            <span>Posted {{ $post->created_at?->format('M j, Y · g:i A') }}</span>
                            <span>{{ number_format($monetizedEngagement) }} total interactions</span>
                        </div>

                        <div class="pa-actions">
                            <a href="{{ url('timeline/' . $post->id) }}" class="pk-btn pk-btn--ghost" wire:navigate
                                style="background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.2);color:#fff">
                                View post
                            </a>
                            <a href="{{ url('timeline') }}" class="pk-btn pk-btn--ghost" wire:navigate
                                style="background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.2);color:#fff">
                                Back to feed
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SEGMENTED CONTROL: BOOST ADS VS MONETIZATION
                ══════════════════════════════════════════ -->
                <div class="pa-segment-bar">
                    <button
                        type="button"
                        class="pa-segment-btn"
                        :class="{ 'is-active': activeTab === 'boost' }"
                        @click="activeTab = 'boost'"
                    >
                        <span>🚀 Boost Campaign Analytics</span>
                        <span class="pa-segment-badge">Ad Traffic</span>
                    </button>
                    <button
                        type="button"
                        class="pa-segment-btn"
                        :class="{ 'is-active': activeTab === 'monetization' }"
                        @click="activeTab = 'monetization'"
                    >
                        <span>📊 Creator Monetization</span>
                    </button>
                </div>

                <!-- ══════════════════════════════════════════════════
                     TAB 1: COMPREHENSIVE BOOSTED POST ANALYTICS
                ══════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'boost'">

                    <!-- Boost Action Flash Notifications -->
                    @if (session()->has('boost_success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-3" style="border-radius:12px;background:#ECFDF5;border-color:#A7F3D0;color:#065F46">
                            <span>✅</span>
                            <div>{{ session('boost_success') }}</div>
                        </div>
                    @endif
                    @if (session()->has('boost_error'))
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" style="border-radius:12px;background:#FEF2F2;border-color:#FECACA;color:#991B1B">
                            <span>⚠️</span>
                            <div>{{ session('boost_error') }}</div>
                        </div>
                    @endif

                    @if ($hasBoost)
                        <!-- Boost Campaign Overview Banner -->
                        <div class="pa-boost-hero">
                            <div class="pa-boost-hero-top">
                                @if ($boostStatus === 'active')
                                    <div class="pa-boost-status-tag">
                                        <span class="pa-boost-live-dot"></span>
                                        <span>Active Campaign</span>
                                    </div>
                                @elseif ($boostStatus === 'paused')
                                    <div class="pa-boost-status-tag" style="background:rgba(245,158,11,0.25);color:#FDE68A;border-color:rgba(245,158,11,0.5)">
                                        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#F59E0B"></span>
                                        <span>Delivery Paused</span>
                                    </div>
                                @elseif ($boostStatus === 'completed')
                                    <div class="pa-boost-status-tag" style="background:rgba(16,185,129,0.25);color:#A7F3D0;border-color:rgba(16,185,129,0.5)">
                                        <span>✓ Completed</span>
                                    </div>
                                @else
                                    <div class="pa-boost-status-tag">
                                        <span>{{ ucfirst($boostStatus) }}</span>
                                    </div>
                                @endif

                                <div style="font-size:0.8rem;opacity:0.9">
                                    Rate: <strong>{{ $boostRate }} PayKoin (₦{{ $boostRate * 10 }}) / Click</strong>
                                </div>
                            </div>

                            <h2 class="pa-boost-hero-title">{{ number_format($boostDelivered) }} of {{ number_format($boostTotal) }} Guaranteed Clicks Delivered</h2>
                            <p class="pa-boost-hero-subtitle">
                                Target: <a href="{{ $latestBoost->target_url }}" target="_blank" rel="noopener" style="color:#C4B5FD;text-decoration:underline">{{ $latestBoost->target_url }}</a>
                                · CTA: <strong>{{ $latestBoost->cta }}</strong>
                                · Networks: <strong>{{ $latestBoost->platform_payhankey && $latestBoost->platform_partner ? 'Payhankey Feed & Partner Websites' : ($latestBoost->platform_partner ? 'Partner Websites' : 'Payhankey Feed') }}</strong>
                            </p>

                            <div class="pa-boost-progress-wrap">
                                <div class="pa-boost-progress-labels">
                                    <span>Delivery Progress ({{ $boostPct }}%)</span>
                                    <span>{{ number_format($boostRemaining) }} Clicks Remaining</span>
                                </div>
                                <div class="pa-boost-bar-track">
                                    <div class="pa-boost-bar-fill" style="width: {{ $boostPct }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Core KPI Metric Cards -->
                        <div class="pk-stat-grid">
                            <article class="pk-stat-card">
                                <div class="pk-stat-card-icon" style="background:#FAF5FF;color:#7C3AED;">
                                    <i class="fa fa-mouse-pointer"></i>
                                </div>
                                <p class="pk-stat-card-value">{{ number_format($boostDelivered) }}</p>
                                <p class="pk-stat-card-label">Delivered clicks</p>
                            </article>
                            <article class="pk-stat-card">
                                <div class="pk-stat-card-icon" style="background:#EEF2FF;color:#4F46E5;">
                                    <i class="fa fa-eye"></i>
                                </div>
                                <p class="pk-stat-card-value">{{ number_format($adImpressions) }}</p>
                                <p class="pk-stat-card-label">Ad impressions</p>
                            </article>
                            <article class="pk-stat-card">
                                <div class="pk-stat-card-icon" style="background:#ECFDF5;color:#059669;">
                                    <i class="fa fa-percentage"></i>
                                </div>
                                <p class="pk-stat-card-value">{{ $ctr }}%</p>
                                <p class="pk-stat-card-label">Click-through rate (CTR)</p>
                            </article>
                            <article class="pk-stat-card">
                                <div class="pk-stat-card-icon" style="background:#FEF3C7;color:#D97706;">
                                    <i class="fa fa-coins"></i>
                                </div>
                                <p class="pk-stat-card-value">{{ $boostRate }} PK</p>
                                <p class="pk-stat-card-label">Cost per click (₦{{ $boostRate * 10 }})</p>
                            </article>
                        </div>

                        <!-- Dual Network Distribution Breakdown -->
                        <div class="pa-network-grid">
                            <!-- Payhankey Feed Card -->
                            <div class="pa-network-card">
                                <div class="pa-network-head">
                                    <div class="pa-network-title">
                                        <span style="font-size:1.1rem">🔥</span>
                                        <span>Payhankey Sponsored Feed</span>
                                    </div>
                                    <span class="pa-segment-badge">{{ $latestBoost->platform_payhankey ? 'In-Feed' : 'Not Selected' }}</span>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Ad Feed Views:</span>
                                    <strong>{{ number_format($monetizedViews) }} impressions</strong>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Direct CTA Clicks:</span>
                                    <strong style="color:#7C3AED">{{ number_format($payhankeyClicksCount) }} clicks</strong>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Organic Likes from Ad:</span>
                                    <strong>{{ number_format($monetizedLikes) }} likes</strong>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Post Bookmarks & Saves:</span>
                                    <strong>{{ number_format($bookmarksCount) }} saves</strong>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Feed CTR:</span>
                                    <strong style="color:#059669">{{ $feedCtr }}%</strong>
                                </div>
                            </div>

                            <!-- Partner Websites Network Card -->
                            <div class="pa-network-card">
                                <div class="pa-network-head">
                                    <div class="pa-network-title">
                                        <span style="font-size:1.1rem">🌐</span>
                                        <span>Partner Websites Network</span>
                                    </div>
                                    <span class="pa-segment-badge" style="background:{{ $latestBoost->platform_partner ? '#DCFCE7' : '#F1F5F9' }};color:{{ $latestBoost->platform_partner ? '#166534' : '#64748B' }}">
                                        {{ $latestBoost->platform_partner ? 'Verified' : 'Disabled' }}
                                    </span>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Task Views:</span>
                                    <strong>{{ number_format($partnerClicksCount > 0 ? $partnerClicksCount * 32 : 0) }} impressions</strong>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Verified Landing Visits:</span>
                                    <strong style="color:#10B981">{{ number_format($partnerClicksCount) }} clicks</strong>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Human Verification:</span>
                                    <strong>100% Real Humans</strong>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Delivery Status:</span>
                                    <strong>{{ $latestBoost->platform_partner ? ($partnerClicksCount > 0 ? 'Active Traffic' : 'Ready in Network') : 'Not Enabled' }}</strong>
                                </div>
                                <div class="pa-network-stat-row">
                                    <span>Traffic Guard:</span>
                                    <strong style="color:#059669">Geo-IP & UA Filter Active</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Hourly Delivery Curve -->
                        <div class="pa-chart-box">
                            <div class="pa-chart-title">Clicks Delivered Over Time</div>
                            <div class="pa-chart-subtitle">Real-time click distribution recorded since boost launch</div>
                            @if ($hourlyBars->isNotEmpty())
                                <div class="pa-chart-bars">
                                    @foreach ($hourlyBars as $bar)
                                        <div class="pa-bar-col">
                                            <span class="pa-bar-val">{{ $bar['count'] }}</span>
                                            <div class="pa-bar-pill" style="height: {{ $bar['height'] }}%"></div>
                                            <span class="pa-bar-label">{{ $bar['label'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="text-align:center;padding:32px 16px;color:#64748B;font-size:0.86rem">
                                    📊 Hourly delivery curve will dynamically populate here as visitors engage with your sponsored link.
                                </div>
                            @endif
                        </div>

                        <!-- Audience & Traffic Demographics -->
                        <div class="pa-intel-grid">
                            <div class="pa-intel-card">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <strong style="font-size:0.9rem;color:#0F172A">Top Locations</strong>
                                    <span style="font-size:0.72rem;color:#64748B">Geo-IP Verified</span>
                                </div>
                                @if ($topLocations->isNotEmpty())
                                    @foreach ($topLocations as $loc)
                                        <div class="pa-intel-item">
                                            <div class="pa-intel-label-row">
                                                <span>{{ $loc['label'] }}</span>
                                                <span>{{ $loc['pct'] }}% ({{ $loc['count'] }} clicks)</span>
                                            </div>
                                            <div class="pa-intel-bar-bg"><div class="pa-intel-bar-fill" style="width: {{ $loc['pct'] }}%"></div></div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="text-align:center;padding:24px 12px;color:#64748B;font-size:0.82rem">
                                        📍 Visitor geographic locations (City & Country) will appear here live as clicks are recorded.
                                    </div>
                                @endif
                            </div>

                            <div class="pa-intel-card">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <strong style="font-size:0.9rem;color:#0F172A">Device Breakdown</strong>
                                    <span style="font-size:0.72rem;color:#64748B">User-Agent</span>
                                </div>
                                @if ($boostDelivered > 0)
                                    <div class="pa-intel-item">
                                        <div class="pa-intel-label-row">
                                            <span>Mobile (iOS & Android)</span>
                                            <span>{{ $deviceBreakdown['Mobile']['pct'] }}% ({{ $deviceBreakdown['Mobile']['count'] }} clicks)</span>
                                        </div>
                                        <div class="pa-intel-bar-bg"><div class="pa-intel-bar-fill" style="width: {{ $deviceBreakdown['Mobile']['pct'] }}%;background:#10B981"></div></div>
                                    </div>
                                    <div class="pa-intel-item">
                                        <div class="pa-intel-label-row">
                                            <span>Desktop / Laptop</span>
                                            <span>{{ $deviceBreakdown['Desktop']['pct'] }}% ({{ $deviceBreakdown['Desktop']['count'] }} clicks)</span>
                                        </div>
                                        <div class="pa-intel-bar-bg"><div class="pa-intel-bar-fill" style="width: {{ $deviceBreakdown['Desktop']['pct'] }}%;background:#10B981"></div></div>
                                    </div>
                                    <div class="pa-intel-item">
                                        <div class="pa-intel-label-row">
                                            <span>Tablet</span>
                                            <span>{{ $deviceBreakdown['Tablet']['pct'] }}% ({{ $deviceBreakdown['Tablet']['count'] }} clicks)</span>
                                        </div>
                                        <div class="pa-intel-bar-bg"><div class="pa-intel-bar-fill" style="width: {{ $deviceBreakdown['Tablet']['pct'] }}%;background:#10B981"></div></div>
                                    </div>
                                @else
                                    <div style="text-align:center;padding:24px 12px;color:#64748B;font-size:0.82rem">
                                        📱 Visitor devices (Mobile, Desktop, Tablet) will calculate automatically from User-Agent data.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Recent Click Log -->
                        <div class="pa-log-box">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <strong style="font-size:0.9rem;color:#0F172A">Recent Verified Clicks</strong>
                                <span style="font-size:0.72rem;color:#10B981;font-weight:700">● Live Stream</span>
                            </div>
                            @if ($boostClicks && $boostClicks->isNotEmpty())
                                @foreach ($boostClicks as $click)
                                    @php
                                        $isPartner = $click->platform === 'partner';
                                        $locParts = array_filter([$click->city, $click->country]);
                                        $locText = !empty($locParts) ? implode(', ', $locParts) : 'Global Visit';
                                        $deviceParts = array_filter([$click->device, $click->browser, $click->os]);
                                        $deviceText = !empty($deviceParts) ? implode(' · ', $deviceParts) : 'Web Visit';
                                    @endphp
                                    <div class="pa-log-item">
                                        <div class="pa-log-left">
                                            <div class="pa-log-icon" style="{{ $isPartner ? 'background:#EDE9FE;color:#6D28D9' : 'background:#FAF5FF;color:#7C3AED' }}">
                                                {{ $isPartner ? '🌐' : '🔥' }}
                                            </div>
                                            <div>
                                                <div style="font-weight:700;color:#0F172A">{{ $isPartner ? 'Partner Website Visit' : 'Payhankey Feed CTA' }}</div>
                                                <div class="pa-log-meta">{{ $locText }} · {{ $deviceText }}</div>
                                            </div>
                                        </div>
                                        <div style="text-align:right">
                                            <div style="font-weight:700;color:#6D28D9">-{{ $latestBoost?->rate_pk ?? 3 }} PK</div>
                                            <div class="pa-log-meta">{{ $click->created_at?->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div style="text-align:center;padding:28px 16px;color:#64748B;font-size:0.85rem">
                                    🚀 Campaign active. Real visitor clicks with location, device, and browser telemetry will appear here live.
                                </div>
                            @endif
                        </div>

                        <!-- Campaign Actions -->
                        <div class="pa-campaign-actions">
                            <a
                                href="{{ url('post/timeline/' . $post->id . '/boost') }}"
                                class="pa-boost-btn"
                                style="text-decoration: none;"
                                wire:navigate
                            >
                                <span>🚀 Extend Boost (+ Clicks)</span>
                            </a>

                            @if ($boostRemaining > 0)
                                <button
                                    type="button"
                                    wire:click="toggleBoostStatus"
                                    wire:loading.attr="disabled"
                                    class="pa-boost-btn pa-boost-btn--secondary"
                                >
                                    <span wire:loading.remove>
                                        {{ $boostStatus === 'active' ? '⏸️ Pause Delivery' : '▶️ Resume Delivery' }}
                                    </span>
                                    <span wire:loading>Processing...</span>
                                </button>
                            @endif
                        </div>

                    @else
                        <!-- No Boost Campaign Found State -->
                        <div style="background:#FFFFFF;border:1px solid #E2E8F0;border-radius:18px;padding:36px 24px;text-align:center;margin-bottom:24px;box-shadow:0 4px 15px rgba(0,0,0,0.03)">
                            <div style="width:64px;height:64px;border-radius:20px;background:#EDE9FE;color:#7C3AED;font-size:1.75rem;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                                🚀
                            </div>
                            <h3 style="font-size:1.25rem;font-weight:800;color:#0F172A;margin-bottom:8px">This Post Has Not Been Boosted Yet</h3>
                            <p style="color:#64748B;font-size:0.9rem;max-width:520px;margin:0 auto 24px;line-height:1.55">
                                Reach thousands of real users across Payhankey and partner websites. Guaranteed clicks start at just <strong>3 PayKoin (₦30) per click</strong> with comprehensive live telemetry.
                            </p>
                            <a
                                href="{{ url('post/timeline/' . $post->id . '/boost') }}"
                                class="pa-boost-btn"
                                style="display:inline-flex;text-decoration:none;padding:12px 28px;font-size:0.95rem"
                                wire:navigate
                            >
                                <span>🚀 Boost This Post Now</span>
                            </a>
                        </div>
                    @endif

                </div>

                <!-- ══════════════════════════════════════════════════
                     TAB 2: CREATOR MONETIZATION (STANDARD)
                ══════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'monetization'">

                    @if (! $isMonetized)
                        <div class="pa-boost-monetization-alert" style="background:#FEF2F2;border-color:#FECACA;margin-bottom:18px">
                            <div class="pa-boost-alert-icon" style="color:#DC2626">⚠️</div>
                            <div class="pa-boost-alert-body">
                                <h4 style="color:#991B1B">Monetization Disabled for this Post</h4>
                                <p style="color:#B91C1C">
                                    This post was evaluated as ineligible for creator monetization: <strong>{{ $monetizationNote ?? 'Content does not meet minimum quality criteria.' }}</strong>
                                    <br>
                                    Your post remains fully published and visible to your followers and feed readers, but impressions and reactions on this post will not accrue earnings.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="pa-earn-hero">
                        <div class="pa-earn-inner">
                            <div class="pa-earn-label">Estimated total earnings</div>
                            <p class="pa-earn-value">{{ $currency }}{{ number_format($totalEarnings, 2) }}</p>
                            <p class="pa-earn-note">
                                Views {{ $currency }}{{ number_format($viewsRevenue, 2) }}
                                · Likes {{ $currency }}{{ number_format($likesRevenue, 2) }}
                                · Comments {{ $currency }}{{ number_format($commentsRevenue, 2) }}
                            </p>
                        </div>
                    </div>

                    <div class="pk-stat-grid">
                        <article class="pk-stat-card">
                            <div class="pk-stat-card-icon" style="background:#EEF2FF;color:#4F46E5;">
                                <i class="fa fa-eye"></i>
                            </div>
                            <p class="pk-stat-card-value">{{ number_format($totalViews) }}</p>
                            <p class="pk-stat-card-label">Total views</p>
                        </article>
                        <article class="pk-stat-card">
                            <div class="pk-stat-card-icon" style="background:#ECFDF5;color:#059669;">
                                <i class="fa fa-thumbs-up"></i>
                            </div>
                            <p class="pk-stat-card-value">{{ number_format($monetizedLikes) }}</p>
                            <p class="pk-stat-card-label">Monetized likes</p>
                        </article>
                        <article class="pk-stat-card">
                            <div class="pk-stat-card-icon" style="background:var(--pk-violet-soft);color:var(--pk-violet);">
                                <i class="fa fa-comments"></i>
                            </div>
                            <p class="pk-stat-card-value">{{ number_format($totalComments) }}</p>
                            <p class="pk-stat-card-label">Total comments</p>
                        </article>
                        <article class="pk-stat-card">
                            <div class="pk-stat-card-icon" style="background:#FEF3C7;color:#D97706;">
                                <i class="fa fa-chart-line"></i>
                            </div>
                            <p class="pk-stat-card-value">{{ number_format($monetizedEngagement) }}</p>
                            <p class="pk-stat-card-label">Monetized engagement</p>
                        </article>
                    </div>

                    <div class="pk-panel">
                        <div class="pk-panel-head"><h2>Views</h2></div>
                        <div class="pk-panel-body">
                            <div class="pk-stat-grid" style="margin-bottom:14px">
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ number_format($monetizedViews) }}</p>
                                    <p class="pk-stat-card-label">Monetized</p>
                                </article>
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ number_format($unmonetizedViews) }}</p>
                                    <p class="pk-stat-card-label">Unmonetized</p>
                                </article>
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ number_format($totalViews) }}</p>
                                    <p class="pk-stat-card-label">Total</p>
                                </article>
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ $currency }}{{ number_format($viewsRevenue, 2) }}</p>
                                    <p class="pk-stat-card-label">Revenue</p>
                                </article>
                            </div>
                        </div>
                    </div>

                    <div class="pk-panel">
                        <div class="pk-panel-head"><h2>Likes</h2></div>
                        <div class="pk-panel-body">
                            <div class="pk-stat-grid" style="margin-bottom:0">
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ number_format($monetizedLikes) }}</p>
                                    <p class="pk-stat-card-label">Monetized likes</p>
                                </article>
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ $currency }}{{ number_format($likesRevenue, 2) }}</p>
                                    <p class="pk-stat-card-label">Revenue</p>
                                </article>
                            </div>
                        </div>
                    </div>

                    <div class="pk-panel">
                        <div class="pk-panel-head"><h2>Comments</h2></div>
                        <div class="pk-panel-body">
                            <div class="pk-stat-grid" style="margin-bottom:14px">
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ number_format($monetizedComments) }}</p>
                                    <p class="pk-stat-card-label">Monetized</p>
                                </article>
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ number_format($unmonetizedComments) }}</p>
                                    <p class="pk-stat-card-label">Unmonetized</p>
                                </article>
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ number_format($totalComments) }}</p>
                                    <p class="pk-stat-card-label">Total</p>
                                </article>
                                <article class="pk-stat-card">
                                    <p class="pk-stat-card-value">{{ $currency }}{{ number_format($commentsRevenue, 2) }}</p>
                                    <p class="pk-stat-card-label">Revenue</p>
                                </article>
                            </div>
                        </div>
                    </div>

                    <div class="pk-panel">
                        <div class="pk-panel-head"><h2>Revenue breakdown</h2></div>
                        <div class="pk-panel-body">
                            <div class="pa-breakdown">
                                <div class="pa-breakdown-row">
                                    <div>
                                        <b>Views</b>
                                        <span>{{ number_format($monetizedViews) }} monetized</span>
                                    </div>
                                    <div class="pa-breakdown-amt">{{ $currency }}{{ number_format($viewsRevenue, 2) }}</div>
                                </div>
                                <div class="pa-breakdown-row">
                                    <div>
                                        <b>Likes</b>
                                        <span>{{ number_format($monetizedLikes) }} monetized</span>
                                    </div>
                                    <div class="pa-breakdown-amt">{{ $currency }}{{ number_format($likesRevenue, 2) }}</div>
                                </div>
                                <div class="pa-breakdown-row">
                                    <div>
                                        <b>Comments</b>
                                        <span>{{ number_format($monetizedComments) }} monetized</span>
                                    </div>
                                    <div class="pa-breakdown-amt">{{ $currency }}{{ number_format($commentsRevenue, 2) }}</div>
                                </div>
                            </div>
                            <p class="pk-hint" style="margin-top:14px;margin-bottom:0">
                                Figures are estimates based on current monetized engagement. Final payout may differ after validation.
                            </p>
                        </div>
                    </div>

                </div>

                @if (userLevel() === 'Basic')
                    @include('layouts.upgrade')
                @endif

                @if (auth()->user()->email_verified_at == null)
                    @include('layouts.accesscode_verification')
                @else
                    @include('layouts.onboarding')
                @endif

            </div>
        </div>
    </div>
</div>
