@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .dash-kpi {
            display: flex; flex-direction: column; gap: .5rem; padding: 1.1rem 1.25rem;
            background: var(--dash-surface); border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius); box-shadow: var(--dash-shadow); height: 100%;
        }
        .dash-kpi__label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--dash-muted); }
        .dash-kpi__value { font-size: 1.4rem; font-weight: 700; letter-spacing: -.03em; }
        .dash-author { display: flex; align-items: center; gap: .65rem; }
        .dash-author img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; background: #e2e8f0; }
        .dash-author__fallback {
            width: 36px; height: 36px; border-radius: 50%; background: #eef2ff; color: #4f46e5;
            display: grid; place-items: center; font-weight: 700; font-size: .85rem;
        }
        .dash-author__name { font-weight: 600; color: inherit; text-decoration: none; }
        .dash-author__name:hover { text-decoration: underline; }
        .dash-num { font-variant-numeric: tabular-nums; font-weight: 600; white-space: nowrap; }
        .dash-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; align-items: end; margin-bottom: 1rem; }
        .dash-toolbar .dash-field { margin: 0; min-width: 120px; }
        .dash-toolbar label { display: block; margin-bottom: .3rem; font-size: .75rem; font-weight: 600; color: var(--dash-muted); }
        .dash-rank {
            width: 28px; height: 28px; border-radius: 8px; display: grid; place-items: center;
            font-size: .78rem; font-weight: 700; background: #f1f5f9; color: #475569;
        }
        .dash-rank--1 { background: #fef3c7; color: #b45309; }
        .dash-rank--2 { background: #e2e8f0; color: #475569; }
        .dash-rank--3 { background: #ffedd5; color: #c2410c; }
        @media (max-width: 900px) { .dash-grid--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 560px) { .dash-grid--4 { grid-template-columns: 1fr; } }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Outreach leaders</h1>
                    <p>Highest post &amp; engagement creators from {{ $yearLabel }} — for ads and reach-outs (posts table only)</p>
                </div>
            </header>

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Posts in range</span>
                        <div class="dash-kpi__value">{{ number_format((int) ($totals->posts ?? 0)) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Creators</span>
                        <div class="dash-kpi__value">{{ number_format((int) ($totals->creators ?? 0)) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Views</span>
                        <div class="dash-kpi__value">{{ number_format((int) ($totals->views ?? 0)) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Likes + comments</span>
                        <div class="dash-kpi__value">{{ number_format((int) ($totals->likes ?? 0) + (int) ($totals->comments ?? 0)) }}</div>
                    </div>
                </div>
            </section>

            <section class="dash-section">
                <form method="get" class="dash-toolbar">
                    <div class="dash-field">
                        <label for="from">From</label>
                        <input id="from" type="date" name="from" value="{{ $from }}" class="dash-input">
                    </div>
                    <div class="dash-field">
                        <label for="to">To</label>
                        <input id="to" type="date" name="to" value="{{ $to }}" class="dash-input">
                    </div>
                    <div class="dash-field">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="dash-input">
                            <option value="LIVE" @selected($status === 'LIVE')>LIVE</option>
                            <option value="SHADOW_BANNED" @selected($status === 'SHADOW_BANNED')>Shadow banned</option>
                            <option value="all" @selected($status === 'all')>All statuses</option>
                        </select>
                    </div>
                    <div class="dash-field">
                        <label for="media">Media</label>
                        <select id="media" name="media" class="dash-input">
                            <option value="all" @selected($media === 'all')>All posts</option>
                            <option value="video" @selected($media === 'video')>With video</option>
                            <option value="image" @selected($media === 'image')>With images</option>
                            <option value="text" @selected($media === 'text')>Text only</option>
                        </select>
                    </div>
                    <div class="dash-field">
                        <label for="sort">Sort by</label>
                        <select id="sort" name="sort" class="dash-input">
                            <option value="engagement" @selected($sort === 'engagement')>Engagement score</option>
                            <option value="posts" @selected($sort === 'posts')>Post count</option>
                            <option value="views" @selected($sort === 'views')>Views</option>
                            <option value="likes" @selected($sort === 'likes')>Likes</option>
                            <option value="comments" @selected($sort === 'comments')>Comments</option>
                            <option value="clicks" @selected($sort === 'clicks')>Clicks</option>
                        </select>
                    </div>
                    <div class="dash-field" style="min-width:90px">
                        <label for="min_posts">Min posts</label>
                        <input id="min_posts" type="number" min="1" name="min_posts" value="{{ $minPosts }}" class="dash-input">
                    </div>
                    <div class="dash-field" style="min-width:160px">
                        <label for="q">Find user</label>
                        <input id="q" type="search" name="q" value="{{ $q }}" class="dash-input" placeholder="Name, @user, email">
                    </div>
                    <div class="dash-field" style="min-width:90px">
                        <label for="per_page">Per page</label>
                        <select id="per_page" name="per_page" class="dash-input">
                            @foreach ([25, 50, 100] as $n)
                                <option value="{{ $n }}" @selected((int) $perPage === $n)>{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="dash-btn dash-btn--primary">Apply</button>
                    <a href="{{ route('admin.outreach.index') }}" class="dash-btn dash-btn--ghost">Reset YTD</a>
                </form>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <div>
                            <h2 class="dash-card__title">Top creators</h2>
                            <p class="dash-muted" style="margin:.25rem 0 0;">
                                Score = views + (likes×2) + (comments×3) + (clicks×2) · {{ $from }} → {{ $to }}
                            </p>
                        </div>
                    </div>
                    <div class="dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Creator</th>
                                        <th>Posts</th>
                                        <th>Views</th>
                                        <th>Likes</th>
                                        <th>Comments</th>
                                        <th>Clicks</th>
                                        <th>Score</th>
                                        <th>Last post</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rows as $index => $row)
                                        @php
                                            $user = $users->get($row->user_id);
                                            $rank = $rows->firstItem() ? ($rows->firstItem() + $index) : ($index + 1);
                                            $rankClass = match ($rank) {
                                                1 => 'dash-rank--1',
                                                2 => 'dash-rank--2',
                                                3 => 'dash-rank--3',
                                                default => '',
                                            };
                                        @endphp
                                        <tr>
                                            <td><span class="dash-rank {{ $rankClass }}">{{ $rank }}</span></td>
                                            <td>
                                                @if ($user)
                                                    <div class="dash-author">
                                                        @if ($user->avatar)
                                                            <img src="{{ $user->avatar }}" alt="">
                                                        @else
                                                            <div class="dash-author__fallback">{{ strtoupper(substr($user->username ?? $user->name ?? 'U', 0, 1)) }}</div>
                                                        @endif
                                                        <div>
                                                            <a href="{{ route('admin.users.show', $user) }}" class="dash-author__name">{{ $user->name }}</a>
                                                            <div class="dash-muted" style="font-size:.8rem;">
                                                                {{ '@'.$user->username }}
                                                                @if ($user->email)
                                                                    · {{ $user->email }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="dash-muted">Unknown user</span>
                                                @endif
                                            </td>
                                            <td class="dash-num">
                                                {{ number_format((int) $row->post_count) }}
                                                <div class="dash-muted" style="font-size:.72rem;font-weight:500;">
                                                    {{ (int) $row->video_posts }} video · {{ (int) $row->image_posts }} image
                                                </div>
                                            </td>
                                            <td class="dash-num">{{ number_format((int) $row->total_views) }}</td>
                                            <td class="dash-num">{{ number_format((int) $row->total_likes) }}</td>
                                            <td class="dash-num">{{ number_format((int) $row->total_comments) }}</td>
                                            <td class="dash-num">{{ number_format((int) $row->total_clicks) }}</td>
                                            <td class="dash-num">{{ number_format((int) $row->engagement_score) }}</td>
                                            <td class="dash-muted">
                                                {{ $row->last_posted_at ? \Illuminate\Support\Carbon::parse($row->last_posted_at)->format('M j, Y') : '—' }}
                                            </td>
                                            <td>
                                                @if ($user)
                                                    <a href="{{ route('admin.users.show', $user) }}" class="dash-btn dash-btn--ghost" style="padding:.4rem .7rem;">Profile</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="dash-muted" style="text-align:center;padding:2rem;">
                                                No creators matched these filters.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($rows->hasPages())
                    <div style="margin-top:1rem;">{{ $rows->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
