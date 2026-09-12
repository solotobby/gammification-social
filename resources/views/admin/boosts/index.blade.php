@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-switch-hero {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }
        .dash-switch-hero--active {
            border-color: #c7d2fe;
            background: linear-gradient(135deg, #ffffff 0%, #f5f3ff 100%);
        }
        .dash-switch-hero--inactive {
            border-color: #fde68a;
            background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
        }
        .dash-switch-info h3 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0 0 0.35rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dash-switch-info p {
            margin: 0;
            font-size: 0.88rem;
            color: #64748b;
        }
        .dash-toggle-btn {
            font-size: 0.92rem;
            font-weight: 700;
            padding: 0.65rem 1.4rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .dash-toggle-btn--enable {
            background: #059669;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        }
        .dash-toggle-btn--enable:hover {
            background: #047857;
        }
        .dash-toggle-btn--disable {
            background: #dc2626;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }
        .dash-toggle-btn--disable:hover {
            background: #b91c1c;
        }
        .dash-tab-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }
        .dash-tab-row .dash-tab {
            text-decoration: none;
            padding: 0.45rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.15s ease;
        }
        .dash-tab-row .dash-tab:hover {
            border-color: #cbd5e1;
            color: #334155;
        }
        .dash-tab-row .dash-tab.is-active {
            background: #6366f1;
            border-color: #6366f1;
            color: #ffffff;
        }
        .dash-progress-track {
            background: #e2e8f0;
            border-radius: 999px;
            height: 7px;
            width: 100%;
            overflow: hidden;
            margin-top: 4px;
        }
        .dash-progress-bar {
            height: 100%;
            border-radius: 999px;
            background: #6366f1;
        }
        .dash-pill-platform {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 6px;
            background: #f1f5f9;
            color: #475569;
            margin-right: 4px;
        }
        @media (max-width: 768px) {
            .dash-switch-hero {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endsection

@section('content')
<div class="content p-0">
    <div class="dash">
        <header class="dash-header">
            <div>
                <h1>Post Boost & Advertising Management</h1>
                <p>Monitor campaign traffic, review boosted posts, and manage platform-wide boost visibility</p>
            </div>
        </header>

        @if (session('success'))
            <div class="dash-alert dash-alert--success" style="margin-bottom:1.5rem">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="dash-alert dash-alert--error" style="margin-bottom:1.5rem">
                {{ session('error') }}
            </div>
        @endif

        <!-- ══════════════════════════════════════════════════
             MASTER TOGGLE BUTTON: TURN POST BOOST ON OR OFF
        ══════════════════════════════════════════════════ -->
        <div class="dash-switch-hero {{ $isBoostEnabled ? 'dash-switch-hero--active' : 'dash-switch-hero--inactive' }}">
            <div class="dash-switch-info">
                @if ($isBoostEnabled)
                    <h3 style="color:#4338ca">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#10b981"></span>
                        Post Boost is Currently ONLINE (Visible to Users)
                    </h3>
                    <p>
                        Timeline post owners can see the <strong>🚀 Boost Post</strong> strip and the <strong>Boost post</strong> / <strong>View analytics</strong> options in post menus. New campaigns can be launched.
                    </p>
                @else
                    <h3 style="color:#b45309">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#ef4444"></span>
                        Post Boost is Currently TURNED OFF (Hidden on Timeline)
                    </h3>
                    <p>
                        The boost strip and analytics buttons are hidden from regular social timelines. When you're ready after talking to the media, click the button below to turn it on with one click.
                    </p>
                @endif
            </div>

            <form action="{{ route('admin.boosts.toggle') }}" method="POST">
                @csrf
                <input type="hidden" name="state" value="{{ $isBoostEnabled ? '0' : '1' }}">
                @if ($isBoostEnabled)
                    <button type="submit" class="dash-toggle-btn dash-toggle-btn--disable" onclick="return confirm('Turn OFF Post Boost platform-wide? This will hide the boost strip and analytics buttons on user timelines.')">
                        <span>⏸️</span> Turn OFF Post Boost
                    </button>
                @else
                    <button type="submit" class="dash-toggle-btn dash-toggle-btn--enable">
                        <span>🚀</span> Turn ON Post Boost
                    </button>
                @endif
            </form>
        </div>

        <!-- ══════════════════════════════════════════════════
             KEY METRICS OVERVIEW
        ══════════════════════════════════════════════════ -->
        <section class="dash-section">
            <div class="dash-grid dash-grid--4">
                <div class="dash-kpi">
                    <span class="dash-kpi__label">Total Boost Campaigns</span>
                    <div class="dash-kpi__value">{{ number_format($metrics['totalCampaigns']) }}</div>
                    <div class="dash-muted">
                        <span style="color:#059669;font-weight:600">{{ $metrics['activeCampaigns'] }} active</span> · 
                        <span>{{ $metrics['pausedCampaigns'] }} paused</span> · 
                        <span>{{ $metrics['completedCampaigns'] }} completed</span>
                    </div>
                </div>

                <div class="dash-kpi">
                    <span class="dash-kpi__label">Clicks Delivered / Booked</span>
                    <div class="dash-kpi__value">{{ number_format($metrics['totalClicksDelivered']) }} <span style="font-size:0.9rem;color:#64748b">/ {{ number_format($metrics['totalClicksBooked']) }}</span></div>
                    <div class="dash-muted">
                        {{ $metrics['deliveryRate'] }}% overall delivery rate
                    </div>
                </div>

                <div class="dash-kpi">
                    <span class="dash-kpi__label">PayKoin Revenue Generated</span>
                    <div class="dash-kpi__value" style="color:#6366f1">{{ number_format($metrics['totalPkRevenue']) }} PK</div>
                    <div class="dash-muted">
                        Equivalent to ₦{{ number_format($metrics['totalNairaRevenue'], 2) }}
                    </div>
                </div>

                <div class="dash-kpi">
                    <span class="dash-kpi__label">Verified Logged Clicks</span>
                    <div class="dash-kpi__value" style="color:#059669">{{ number_format($metrics['totalLoggedClicks']) }}</div>
                    <div class="dash-muted">
                        Payhankey: {{ number_format($metrics['payhankeyClicks']) }} · Partner: {{ number_format($metrics['partnerClicks']) }}
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════
             SEARCH & FILTER CONTROLS
        ══════════════════════════════════════════════════ -->
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:1rem">
            <div class="dash-tab-row" style="margin-bottom:0">
                <a href="{{ route('admin.boosts.index', ['tab' => 'all', 'q' => $search]) }}"
                   class="dash-tab {{ $tab === 'all' ? 'is-active' : '' }}">
                   All ({{ $metrics['totalCampaigns'] }})
                </a>
                <a href="{{ route('admin.boosts.index', ['tab' => 'active', 'q' => $search]) }}"
                   class="dash-tab {{ $tab === 'active' ? 'is-active' : '' }}">
                   Active ({{ $metrics['activeCampaigns'] }})
                </a>
                <a href="{{ route('admin.boosts.index', ['tab' => 'paused', 'q' => $search]) }}"
                   class="dash-tab {{ $tab === 'paused' ? 'is-active' : '' }}">
                   Paused ({{ $metrics['pausedCampaigns'] }})
                </a>
                <a href="{{ route('admin.boosts.index', ['tab' => 'completed', 'q' => $search]) }}"
                   class="dash-tab {{ $tab === 'completed' ? 'is-active' : '' }}">
                   Completed ({{ $metrics['completedCampaigns'] }})
                </a>
                <a href="{{ route('admin.boosts.index', ['tab' => 'cancelled', 'q' => $search]) }}"
                   class="dash-tab {{ $tab === 'cancelled' ? 'is-active' : '' }}">
                   Cancelled ({{ $metrics['cancelledCampaigns'] }})
                </a>
            </div>

            <form action="{{ route('admin.boosts.index') }}" method="GET" style="display:flex;gap:0.4rem">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search creator, link, or post..."
                    class="dash-input"
                    style="min-width:240px;padding:0.45rem 0.85rem;border-radius:8px;border:1px solid #cbd5e1;font-size:0.85rem"
                >
                <button type="submit" class="dash-btn dash-btn--primary" style="padding:0.45rem 0.9rem">Search</button>
                @if ($search !== '')
                    <a href="{{ route('admin.boosts.index', ['tab' => $tab]) }}" class="dash-btn dash-btn--ghost" style="padding:0.45rem 0.8rem">Clear</a>
                @endif
            </form>
        </div>

        <!-- ══════════════════════════════════════════════════
             CAMPAIGNS TABLE
        ══════════════════════════════════════════════════ -->
        <section class="dash-section">
            <div class="dash-table-wrap">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Creator</th>
                            <th>Post Preview</th>
                            <th>Target URL & CTA</th>
                            <th>Networks</th>
                            <th style="min-width:140px">Progress</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($boosts as $boost)
                            @php
                                $progressPercent = $boost->total_clicks > 0
                                    ? min(100, round(($boost->delivered_clicks / $boost->total_clicks) * 100))
                                    : 0;
                            @endphp
                            <tr>
                                <td>
                                    @if ($boost->user)
                                        <div class="dash-author">
                                            @if ($boost->user->avatar)
                                                <img src="{{ asset($boost->user->avatar) }}" alt="{{ $boost->user->name }}">
                                            @else
                                                <div style="width:36px;height:36px;border-radius:50%;background:#e0e7ff;color:#4338ca;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem">
                                                    {{ strtoupper(substr($boost->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('admin.users.show', $boost->user) }}" class="dash-author__name">
                                                    {{ $boost->user->name }}
                                                </a>
                                                <div class="dash-muted" style="font-size:0.78rem">&#64;{{ $boost->user->username }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="dash-muted">User Deleted</span>
                                    @endif
                                </td>

                                <td style="max-width:240px">
                                    @if ($boost->post)
                                        <div style="font-size:0.84rem;color:#334155;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;line-height:1.35">
                                            {{ plainPostText($boost->post->content ?? '') ?: 'Post media/attachment' }}
                                        </div>
                                        <div style="font-size:0.75rem;color:#94a3b8;margin-top:2px">
                                            ID: {{ substr($boost->post_id, 0, 8) }}…
                                        </div>
                                    @else
                                        <span class="dash-muted">Post removed</span>
                                    @endif
                                </td>

                                <td>
                                    <div style="font-size:0.85rem;font-weight:600;color:#1e293b;margin-bottom:2px">
                                        <a href="{{ $boost->target_url }}" target="_blank" rel="noopener noreferrer" style="color:inherit;text-decoration:none">
                                            {{ Str::limit(parse_url($boost->target_url, PHP_URL_HOST) ?: $boost->target_url, 24) }} ↗
                                        </a>
                                    </div>
                                    <span style="display:inline-block;font-size:0.72rem;font-weight:700;background:#ede9fe;color:#6d28d9;padding:2px 8px;border-radius:999px">
                                        {{ $boost->cta }}
                                    </span>
                                </td>

                                <td>
                                    @if ($boost->platform_payhankey)
                                        <span class="dash-pill-platform">Payhankey</span>
                                    @endif
                                    @if ($boost->platform_partner)
                                        <span class="dash-pill-platform">Partners</span>
                                    @endif
                                </td>

                                <td>
                                    <div style="display:flex;justify-content:space-between;font-size:0.8rem;font-weight:600;margin-bottom:2px">
                                        <span>{{ number_format($boost->delivered_clicks) }}</span>
                                        <span class="dash-muted">/ {{ number_format($boost->total_clicks) }}</span>
                                    </div>
                                    <div class="dash-progress-track">
                                        <div class="dash-progress-bar" style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                    <div style="font-size:0.72rem;color:#64748b;margin-top:2px">
                                        {{ $progressPercent }}% delivered
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight:700;color:#6366f1;font-size:0.86rem">
                                        {{ number_format($boost->pk_cost) }} PK
                                    </div>
                                    <div class="dash-muted" style="font-size:0.75rem">
                                        ₦{{ number_format($boost->pk_cost * 10) }}
                                    </div>
                                </td>

                                <td>
                                    @if ($boost->status === 'active')
                                        <span class="dash-badge dash-badge--success" style="font-weight:700">Active</span>
                                    @elseif ($boost->status === 'paused')
                                        <span class="dash-badge dash-badge--warn" style="font-weight:700">Paused</span>
                                    @elseif ($boost->status === 'completed')
                                        <span class="dash-badge" style="background:#e0f2fe;color:#0369a1;font-weight:700">Completed</span>
                                    @else
                                        <span class="dash-badge dash-badge--danger" style="font-weight:700">{{ ucfirst($boost->status) }}</span>
                                    @endif
                                </td>

                                <td style="font-size:0.82rem;color:#64748b;white-space:nowrap">
                                    {{ $boost->created_at?->format('M d, Y') }}
                                </td>

                                <td style="text-align:right">
                                    <div style="display:inline-flex;gap:0.35rem;align-items:center">
                                        @if ($boost->status === 'active')
                                            <form action="{{ route('admin.boosts.status', $boost) }}" method="POST" style="display:inline">
                                                @csrf
                                                <input type="hidden" name="status" value="paused">
                                                <button type="submit" class="dash-btn dash-btn--sm dash-btn--ghost" title="Pause ad delivery">
                                                    Pause
                                                </button>
                                            </form>
                                        @elseif ($boost->status === 'paused')
                                            <form action="{{ route('admin.boosts.status', $boost) }}" method="POST" style="display:inline">
                                                @csrf
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="dash-btn dash-btn--sm dash-btn--primary" title="Resume ad delivery">
                                                    Resume
                                                </button>
                                            </form>
                                        @endif

                                        @if ($boost->status !== 'completed' && $boost->status !== 'cancelled')
                                            <form action="{{ route('admin.boosts.status', $boost) }}" method="POST" style="display:inline">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dash-btn dash-btn--sm dash-btn--danger" onclick="return confirm('Cancel this boost campaign?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif

                                        @if ($boost->post)
                                            <a href="{{ url('timeline/' . $boost->post_id) }}" target="_blank" class="dash-btn dash-btn--sm dash-btn--ghost" title="View post on feed">
                                                View
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align:center;padding:3rem 1rem;color:#64748b">
                                    <div style="font-size:2rem;margin-bottom:0.5rem">🚀</div>
                                    <div style="font-weight:600">No boost campaigns found</div>
                                    <div style="font-size:0.85rem">
                                        @if ($search !== '')
                                            No campaigns match your search query "{{ $search }}".
                                        @elseif ($tab !== 'all')
                                            No campaigns with status "{{ $tab }}".
                                        @else
                                            No posts have been boosted yet.
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($boosts->hasPages())
                <div style="margin-top:1.25rem">
                    {{ $boosts->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
