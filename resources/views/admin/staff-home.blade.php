@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .dash-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .dash-kpi {
            display: flex; flex-direction: column; gap: .75rem; padding: 1.25rem;
            background: var(--dash-surface); border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius); box-shadow: var(--dash-shadow); height: 100%;
            color: inherit; text-decoration: none; transition: border-color .2s, transform .2s;
        }
        a.dash-kpi:hover { border-color: #c7d2fe; transform: translateY(-1px); }
        .dash-kpi__top { display: flex; align-items: center; justify-content: space-between; }
        .dash-kpi__icon {
            width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center;
        }
        .dash-kpi__icon--indigo { background: #eef2ff; color: #4f46e5; }
        .dash-kpi__icon--emerald { background: #ecfdf5; color: #059669; }
        .dash-kpi__icon--amber { background: #fffbeb; color: #d97706; }
        .dash-kpi__icon--sky { background: #f0f9ff; color: #0284c7; }
        .dash-kpi__icon--rose { background: #fff1f2; color: #e11d48; }
        .dash-kpi__label {
            font-size: .75rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .06em; color: var(--dash-muted);
        }
        .dash-kpi__value { font-size: 1.75rem; font-weight: 700; letter-spacing: -.03em; }
        .dash-kpi__hint { font-size: .8125rem; color: var(--dash-muted); }
        .dash-pill__dot {
            width: 8px; height: 8px; border-radius: 50%; background: var(--dash-success);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, .15);
        }
        .dash-actions { display: flex; flex-wrap: wrap; gap: .625rem; }
        @media (max-width: 1100px) {
            .dash-grid--4, .dash-grid--3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .dash-grid--4, .dash-grid--3 { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Staff dashboard</h1>
                    <p>Moderation & content · {{ $dateRange->label() }}</p>
                </div>
                <div class="dash-pill">
                    <span class="dash-pill__dot"></span>
                    {{ number_format($onlineUsers) }} users online
                </div>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif

            @include('admin.partials.date-range-filter', ['routeName' => 'admin.home'])

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <a href="{{ route('admin.users.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Users</span>
                            <span class="dash-kpi__icon dash-kpi__icon--emerald"><i class="fa fa-users"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($userCount) }}</div>
                        <div class="dash-kpi__hint">{{ number_format($newUsers) }} new · {{ $dateRange->label() }}</div>
                    </a>
                    <a href="{{ route('admin.posts.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Timeline posts</span>
                            <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-newspaper"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($postsInRange) }}</div>
                        <div class="dash-kpi__hint">{{ number_format($livePosts) }} live total</div>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Pending reports</span>
                            <span class="dash-kpi__icon dash-kpi__icon--rose"><i class="fa fa-flag"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($pendingReports) }}</div>
                        <div class="dash-kpi__hint">Needs moderation</div>
                    </a>
                    <a href="{{ route('admin.feedback.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Feedback queue</span>
                            <span class="dash-kpi__icon dash-kpi__icon--amber"><i class="fa fa-comment-dots"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($awaitingFeedback) }}</div>
                        <div class="dash-kpi__hint">{{ number_format($newFeedback) }} new threads</div>
                    </a>
                </div>
            </section>

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <a href="{{ route('admin.communities.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Communities</span>
                            <span class="dash-kpi__icon dash-kpi__icon--sky"><i class="fa fa-object-group"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($communities) }}</div>
                        <div class="dash-kpi__hint">All communities</div>
                    </a>
                    <a href="{{ route('admin.videos.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Rolls</span>
                            <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-film"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($rollsReady) }}</div>
                        <div class="dash-kpi__hint">{{ number_format($rollsInRange) }} uploaded · {{ $dateRange->label() }}</div>
                    </a>
                    <a href="{{ route('admin.bookmarks.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Bookmarks</span>
                            <span class="dash-kpi__icon dash-kpi__icon--amber"><i class="fa fa-bookmark"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($bookmarksInRange) }}</div>
                        <div class="dash-kpi__hint">Saved in range</div>
                    </a>
                    <a href="{{ route('admin.blog.index') }}" class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Blog posts</span>
                            <span class="dash-kpi__icon dash-kpi__icon--emerald"><i class="fa fa-blog"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ number_format($blogPosts) }}</div>
                        <div class="dash-kpi__hint">Published + drafts</div>
                    </a>
                </div>
            </section>

            <section class="dash-section">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Quick actions</h2>
                    </div>
                    <div class="dash-card__body">
                        <div class="dash-actions">
                            <a href="{{ route('admin.reports.index') }}" class="dash-btn dash-btn--primary">Review reports</a>
                            <a href="{{ route('admin.feedback.index') }}" class="dash-btn dash-btn--ghost">Reply to feedback</a>
                            <a href="{{ route('admin.posts.index') }}" class="dash-btn dash-btn--ghost">Moderate posts</a>
                            <a href="{{ route('admin.videos.index') }}" class="dash-btn dash-btn--ghost">Manage rolls</a>
                            <a href="{{ route('admin.blog.create') }}" class="dash-btn dash-btn--ghost">Write blog post</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
