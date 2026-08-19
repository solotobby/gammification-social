@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-pill__dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--dash-success);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, .15);
        }

        .dash-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .dash-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        .dash-kpi {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1.25rem;
            background: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            box-shadow: var(--dash-shadow);
            transition: border-color .2s, transform .2s;
            height: 100%;
            color: inherit;
            text-decoration: none;
        }

        a.dash-kpi:hover {
            border-color: #c7d2fe;
            transform: translateY(-1px);
        }

        .dash-kpi__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dash-kpi__icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1rem;
        }

        .dash-kpi__icon--indigo { background: #eef2ff; color: #4f46e5; }
        .dash-kpi__icon--emerald { background: #ecfdf5; color: #059669; }
        .dash-kpi__icon--amber { background: #fffbeb; color: #d97706; }
        .dash-kpi__icon--sky { background: #f0f9ff; color: #0284c7; }

        .dash-kpi__label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dash-muted);
        }

        .dash-kpi__value {
            font-size: 1.875rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .dash-kpi__hint {
            font-size: 0.8125rem;
            color: var(--dash-muted);
        }

        .dash-levels {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.75rem;
        }

        .dash-level {
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid var(--dash-border);
            background: #fafafa;
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: background .2s, border-color .2s;
        }

        .dash-level:hover {
            background: var(--dash-accent-soft);
            border-color: #c7d2fe;
        }

        .dash-level__name {
            font-size: 0.8125rem;
            color: var(--dash-muted);
            font-weight: 500;
        }

        .dash-level__count {
            margin-top: 0.35rem;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .dash-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.625rem;
        }

        .dash-btn--ghost:hover { background: #f8fafc; }

        .dash-section { margin-bottom: 1.5rem; }

        .dash-chart {
            position: relative;
            height: 280px;
            width: 100%;
        }

        .dash-chart--engagement {
            height: 320px;
        }

        @media (max-width: 640px) {
            .dash-grid--3, .dash-grid--2 { grid-template-columns: 1fr; }
            .dash-chart { height: 240px; }
        }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}</h1>
                    <p>Platform overview · {{ $dateRange->label() }}</p>
                </div>
                <div class="dash-pill">
                    <span class="dash-pill__dot"></span>
                    {{ number_format($onlineUsers) }} users online
                </div>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            @include('admin.partials.date-range-filter', ['routeName' => 'admin.home'])

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Active users</span>
                            <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-bolt"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($activeUsers) }}</div>
                        <div class="dash-kpi__hint">Unique sessions · {{ $dateRange->label() }}</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">New users</span>
                            <span class="dash-kpi__icon dash-kpi__icon--sky"><i class="fa fa-user-plus"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($newUsers) }}</div>
                        <div class="dash-kpi__hint">Joined in selected range</div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Total users</span>
                            <span class="dash-kpi__icon dash-kpi__icon--emerald"><i class="fa fa-users"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($userCount) }}</div>
                        <div class="dash-kpi__hint">All-time platform users</div>
                    </a>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Revenue (est.)</span>
                            <span class="dash-kpi__icon dash-kpi__icon--emerald"><i class="fa fa-dollar-sign"></i></span>
                        </div>
                        <div class="dash-kpi__value">${{ number_format($totalRevenueUsd, 2) }}</div>
                        <div class="dash-kpi__hint">Successful + allocated · {{ $dateRange->label() }}</div>
                    </div>
                </div>
            </section>

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Posts</span>
                            <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-image"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($postsInRange) }}</div>
                        <div class="dash-kpi__hint">{{ number_format($viewsInRange) }} views · {{ $dateRange->label() }}</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Engagement</span>
                            <span class="dash-kpi__icon dash-kpi__icon--amber"><i class="fa fa-heart"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($engagementInRange) }}</div>
                        <div class="dash-kpi__hint">Likes + comments · {{ $dateRange->label() }}</div>
                    </div>
                    <a href="{{ route('admin.audit-logs.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Audit trail</span>
                            <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-shield-halved"></i></span>
                        </div>
                        <div class="dash-kpi__value" style="font-size:1.125rem;">View logs</div>
                        <div class="dash-kpi__hint">Admin action history</div>
                    </a>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Range</span>
                            <span class="dash-kpi__icon dash-kpi__icon--sky"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="dash-kpi__value" style="font-size:1.125rem;">{{ $dateRange->label() }}</div>
                        <div class="dash-kpi__hint">{{ $dateRange->days() }} day{{ $dateRange->days() === 1 ? '' : 's' }} selected</div>
                    </div>
                </div>
            </section>

            @php($ca = $communityAnalytics)

            <section class="dash-section">
                <div class="dash-card__head" style="margin-bottom:1rem;padding:0 0.25rem">
                    <div>
                        <h2 class="dash-card__title" style="font-size:1.125rem">Community analytics</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0">Paid communities, memberships, and platform revenue · {{ $dateRange->label() }}</p>
                    </div>
                    <a href="{{ route('admin.communities.index') }}" class="dash-link">Manage all</a>
                </div>

                <div class="dash-grid dash-grid--4" style="margin-bottom:1rem">
                    <a href="{{ route('admin.communities.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Communities</span>
                            <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-object-group"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($ca['total']) }}</div>
                        <div class="dash-kpi__hint">{{ number_format($ca['newCommunities']) }} created in range</div>
                    </a>
                    <a href="{{ route('admin.communities.index', ['type' => 'paid']) }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Paid communities</span>
                            <span class="dash-kpi__icon dash-kpi__icon--emerald"><i class="fa fa-tags"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($ca['paid']) }}</div>
                        <div class="dash-kpi__hint">{{ number_format($ca['public']) }} public · active</div>
                    </a>
                    <a href="{{ route('admin.communities.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Active subscriptions</span>
                            <span class="dash-kpi__icon dash-kpi__icon--sky"><i class="fa fa-repeat"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($ca['activeSubscriptions']) }}</div>
                        <div class="dash-kpi__hint">{{ number_format($ca['newSubscriptions']) }} new in range · {{ number_format($ca['pendingSubscriptions']) }} pending</div>
                    </a>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Members / posts</span>
                            <span class="dash-kpi__icon dash-kpi__icon--amber"><i class="fa fa-user-group"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($ca['totalMembers']) }}</div>
                        <div class="dash-kpi__hint">{{ number_format($ca['totalPosts']) }} posts in range</div>
                    </div>
                </div>

                <div class="dash-grid dash-grid--2">
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <div>
                                <h2 class="dash-card__title">Community growth</h2>
                                <p class="dash-muted" style="margin:0.25rem 0 0">
                                    {{ $dateRange->label() }} · {{ number_format($ca['growthChart']['communitiesTotal']) }} new communities · {{ number_format($ca['growthChart']['subscriptionsTotal']) }} active subscriptions
                                </p>
                            </div>
                        </div>
                        <div class="dash-card__body">
                            <div class="dash-chart">
                                <canvas id="community-growth-chart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card__head">
                            <div>
                                <h2 class="dash-card__title">Community payments</h2>
                                <p class="dash-muted" style="margin:0.25rem 0 0">
                                    {{ $dateRange->label() }} · {{ number_format($ca['revenueChart']['paymentsTotal']) }} payments
                                </p>
                            </div>
                        </div>
                        <div class="dash-card__body">
                            <div class="dash-chart">
                                <canvas id="community-revenue-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if ($ca['revenueByCurrency']->isNotEmpty())
                <section class="dash-section dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Community revenue by currency</h2>
                    </div>
                    <div class="dash-card__body dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Currency</th>
                                        <th>Payments</th>
                                        <th>Gross volume</th>
                                        <th>Platform fee</th>
                                        <th>Creator payouts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ca['revenueByCurrency'] as $row)
                                        <tr>
                                            <td>{{ $row->currency }}</td>
                                            <td>{{ number_format($row->payments) }}</td>
                                            <td>{{ $row->currency }} {{ number_format((float) $row->gross, 2) }}</td>
                                            <td>{{ $row->currency }} {{ number_format((float) $row->platform, 2) }}</td>
                                            <td>{{ $row->currency }} {{ number_format((float) $row->creator, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endif

            <section class="dash-section dash-grid dash-grid--2">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Top communities</h2>
                        <span class="dash-muted">By member count</span>
                    </div>
                    <div class="dash-card__body dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Community</th>
                                        <th>Type</th>
                                        <th>Members</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ca['topByMembers'] as $community)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.communities.show', $community) }}" class="dash-link">{{ $community->name }}</a>
                                                <div class="dash-muted">{{ $community->user->name ?? '—' }}</div>
                                            </td>
                                            <td>{{ ucfirst($community->type) }}</td>
                                            <td>{{ number_format($community->members_count) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3">No communities yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Recently created</h2>
                        <a href="{{ route('admin.communities.index') }}" class="dash-link">View all</a>
                    </div>
                    <div class="dash-card__body dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Community</th>
                                        <th>Currency</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ca['recentCommunities'] as $community)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.communities.show', $community) }}" class="dash-link">{{ $community->name }}</a>
                                                <div class="dash-muted">{{ ucfirst($community->type) }} · {{ number_format($community->members_count) }} members</div>
                                            </td>
                                            <td>{{ $community->currency ?? 'NGN' }}</td>
                                            <td>{{ $community->created_at?->format('M j, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3">No communities yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dash-section dash-grid dash-grid--2">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <div>
                            <h2 class="dash-card__title">Verified sign ups per day</h2>
                            <p class="dash-muted" style="margin:0.25rem 0 0;">
                                {{ $dateRange->label() }} · {{ number_format($signupChart['total'] ?? 0) }} email-verified users
                            </p>
                        </div>
                    </div>
                    <div class="dash-card__body">
                        <div class="dash-chart">
                            <canvas id="signup-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <div>
                            <h2 class="dash-card__title">Engagement per day</h2>
                            <p class="dash-muted" style="margin:0.25rem 0 0;">
                                {{ $dateRange->label() }} · {{ number_format($engagementChart['total'] ?? 0) }} interactions · {{ number_format($engagementChart['postsTotal'] ?? 0) }} posts
                            </p>
                        </div>
                    </div>
                    <div class="dash-card__body">
                        <div class="dash-chart dash-chart--engagement">
                            <canvas id="engagement-chart" role="img" aria-label="Engagement per day chart"></canvas>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dash-section dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Users by level</h2>
                    <a href="{{ route('admin.users.index') }}" class="dash-link">View all</a>
                </div>
                <div class="dash-card__body">
                    <div class="dash-levels">
                        @forelse ($levelCounts as $level)
                            <a href="{{ route('admin.users.index', ['level' => $level->level->name ?? 'all']) }}" class="dash-level">
                                <div class="dash-level__name">{{ $level->level->name ?? 'Unknown' }}</div>
                                <div class="dash-level__count">{{ number_format($level->total) }}</div>
                            </a>
                        @empty
                            <p style="margin:0; color:var(--dash-muted);">No active subscription data yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="dash-section dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Quick actions</h2>
                </div>
                <div class="dash-card__body">
                    <div class="dash-actions">
                        <a href="{{ route('admin.users.index') }}" class="dash-btn dash-btn--ghost"><i class="fa fa-users"></i> Users</a>
                        <a href="{{ route('admin.currencies.index') }}" class="dash-btn dash-btn--ghost"><i class="fa fa-coins"></i> Currencies</a>
                        <a href="{{ route('admin.levels.index') }}" class="dash-btn dash-btn--ghost"><i class="fa fa-layer-group"></i> Levels</a>
                        <a href="{{ route('admin.blog.index') }}" class="dash-btn dash-btn--ghost"><i class="fa fa-blog"></i> Blog</a>
                        <a href="{{ route('admin.academy.index') }}" class="dash-btn dash-btn--ghost"><i class="fa fa-graduation-cap"></i> Academy</a>
                        <a href="{{ route('admin.help.index') }}" class="dash-btn dash-btn--ghost"><i class="fa fa-life-ring"></i> Help</a>
                        <a href="{{ route('admin.communities.index') }}" class="dash-btn dash-btn--ghost"><i class="fa fa-object-group"></i> Communities</a>
                        @if ($showTestPayment ?? false)
                            <a href="{{ route('admin.test.subscribe', $levelId) }}" class="dash-btn dash-btn--primary"><i class="fa fa-flask"></i> Test payment</a>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const signupLabels = @json($signupChart['labels'] ?? []);
            const signupValues = @json($signupChart['values'] ?? []);
            const engagementLabels = @json($engagementChart['labels'] ?? []);
            const engagementViews = @json($engagementChart['views'] ?? []);
            const engagementLikes = @json($engagementChart['likes'] ?? []);
            const engagementComments = @json($engagementChart['comments'] ?? []);
            const engagementPosts = @json($engagementChart['posts'] ?? []);
            const communityGrowthLabels = @json($communityAnalytics['growthChart']['labels'] ?? []);
            const communityGrowthValues = @json($communityAnalytics['growthChart']['communities'] ?? []);
            const communitySubscriptionValues = @json($communityAnalytics['growthChart']['subscriptions'] ?? []);
            const communityRevenueLabels = @json($communityAnalytics['revenueChart']['labels'] ?? []);
            const communityPaymentCounts = @json($communityAnalytics['revenueChart']['payments'] ?? []);
            const communityNgnPlatform = @json($communityAnalytics['revenueChart']['ngnPlatform'] ?? []);
            const communityUsdPlatform = @json($communityAnalytics['revenueChart']['usdPlatform'] ?? []);

            function asNumbers(values) {
                return (values || []).map(function (value) {
                    return Number(value) || 0;
                });
            }

            function createChart(canvasId, config) {
                if (typeof Chart === 'undefined') {
                    return null;
                }

                var canvas = document.getElementById(canvasId);
                if (!canvas) {
                    return null;
                }

                return new Chart(canvas, config);
            }

            function initDashboardCharts() {
                var scaleDefaults = {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            maxTicksLimit: 8,
                            font: { size: 11 },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            precision: 0,
                            font: { size: 11 },
                        },
                    },
                };

                createChart('signup-chart', {
                    type: 'line',
                    data: {
                        labels: signupLabels,
                        datasets: [{
                            label: 'Verified sign ups',
                            data: asNumbers(signupValues),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 10,
                                cornerRadius: 8,
                            },
                        },
                        scales: scaleDefaults,
                    },
                });

                var views = asNumbers(engagementViews);
                var likes = asNumbers(engagementLikes);
                var comments = asNumbers(engagementComments);
                var posts = asNumbers(engagementPosts);
                var peak = Math.max.apply(null, views.concat(likes, comments, posts).concat([0]));

                createChart('engagement-chart', {
                    type: 'bar',
                    data: {
                        labels: engagementLabels,
                        datasets: [
                            {
                                label: 'Views',
                                data: views,
                                backgroundColor: 'rgba(14, 165, 233, 0.9)',
                                borderColor: 'rgba(14, 165, 233, 1)',
                                borderWidth: 1,
                                borderRadius: 3,
                                maxBarThickness: 16,
                            },
                            {
                                label: 'Likes',
                                data: likes,
                                backgroundColor: 'rgba(245, 158, 11, 0.9)',
                                borderColor: 'rgba(245, 158, 11, 1)',
                                borderWidth: 1,
                                borderRadius: 3,
                                maxBarThickness: 16,
                            },
                            {
                                label: 'Comments',
                                data: comments,
                                backgroundColor: 'rgba(16, 185, 129, 0.9)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 1,
                                borderRadius: 3,
                                maxBarThickness: 16,
                            },
                            {
                                label: 'Posts created',
                                data: posts,
                                backgroundColor: 'rgba(99, 102, 241, 0.9)',
                                borderColor: 'rgba(99, 102, 241, 1)',
                                borderWidth: 1,
                                borderRadius: 3,
                                maxBarThickness: 16,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        datasets: {
                            bar: {
                                categoryPercentage: 0.72,
                                barPercentage: 0.92,
                            },
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    color: '#64748b',
                                    font: { size: 11 },
                                    padding: 14,
                                },
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 10,
                                cornerRadius: 8,
                            },
                        },
                        scales: {
                            x: scaleDefaults.x,
                            y: Object.assign({}, scaleDefaults.y, {
                                suggestedMax: peak > 0 ? Math.ceil(peak * 1.15) : 5,
                            }),
                        },
                    },
                });

                createChart('community-growth-chart', {
                    type: 'line',
                    data: {
                        labels: communityGrowthLabels,
                        datasets: [
                            {
                                label: 'New communities',
                                data: asNumbers(communityGrowthValues),
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.12)',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                borderWidth: 2,
                            },
                            {
                                label: 'Active subscriptions',
                                data: asNumbers(communitySubscriptionValues),
                                borderColor: '#059669',
                                backgroundColor: 'rgba(5, 150, 105, 0.08)',
                                fill: false,
                                tension: 0.35,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                borderWidth: 2,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: { boxWidth: 10, boxHeight: 10, color: '#64748b', font: { size: 11 }, padding: 14 },
                            },
                            tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8 },
                        },
                        scales: scaleDefaults,
                    },
                });

                createChart('community-revenue-chart', {
                    type: 'bar',
                    data: {
                        labels: communityRevenueLabels,
                        datasets: [
                            {
                                label: 'Payments',
                                data: asNumbers(communityPaymentCounts),
                                backgroundColor: 'rgba(99, 102, 241, 0.9)',
                                borderColor: 'rgba(99, 102, 241, 1)',
                                borderWidth: 1,
                                borderRadius: 3,
                                maxBarThickness: 18,
                                yAxisID: 'y',
                            },
                            {
                                label: 'Platform fee (NGN)',
                                data: asNumbers(communityNgnPlatform),
                                backgroundColor: 'rgba(245, 158, 11, 0.85)',
                                borderColor: 'rgba(245, 158, 11, 1)',
                                borderWidth: 1,
                                borderRadius: 3,
                                maxBarThickness: 18,
                                yAxisID: 'y1',
                            },
                            {
                                label: 'Platform fee (USD)',
                                data: asNumbers(communityUsdPlatform),
                                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 1,
                                borderRadius: 3,
                                maxBarThickness: 18,
                                yAxisID: 'y1',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: { boxWidth: 10, boxHeight: 10, color: '#64748b', font: { size: 11 }, padding: 14 },
                            },
                            tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8 },
                        },
                        scales: {
                            x: scaleDefaults.x,
                            y: Object.assign({}, scaleDefaults.y, { position: 'left' }),
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                ticks: { color: '#64748b', font: { size: 11 } },
                            },
                        },
                    },
                });
            }

            function bootCharts() {
                window.requestAnimationFrame(initDashboardCharts);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', bootCharts);
            } else {
                bootCharts();
            }
        })();
    </script>
@endsection
