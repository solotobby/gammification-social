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
    </style>

    <div class="row">
        <div class="col-12 ph-feed-wrap">
            <a href="{{ url('timeline/' . $post->id) }}" class="pa-back" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to post
            </a>

            <div class="pk-app">
                @include('livewire.user.partials.pk-app-ui')

                <div class="pk-app-hero">
                    <div class="pk-app-hero-inner">
                        <span class="pk-app-kicker">Post performance</span>
                        <h1>Analytics</h1>
                        <p>Engagement breakdown and estimated earnings for this post. Payouts are validated at month end.</p>

                        <span class="pa-level-pill">{{ userLevel() }} account</span>

                        @if ($postExcerpt !== '')
                            <div class="pa-post-snippet">{{ $postExcerpt }}</div>
                        @else
                            <div class="pa-post-snippet">Media post</div>
                        @endif

                        <div class="pa-post-meta">
                            <span>Posted {{ $post->created_at?->format('M j, Y · g:i A') }}</span>
                            <span>{{ number_format($monetizedEngagement) }} monetized engagements</span>
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
