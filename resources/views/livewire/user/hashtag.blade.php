<div>
    <style>
        .ht-page {
            --ht-violet: #5A4FDC;
            --ht-text: #0f1419;
            --ht-muted: #536471;
            --ht-line: #eff3f4;
            --ht-surface: #f7f8f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .ht-hero {
            background: #fff;
            border: 1px solid var(--ht-line);
            border-radius: 16px;
            padding: 20px 20px 18px;
            margin-bottom: 12px;
            position: relative;
            overflow: hidden;
        }

        .ht-hero::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #5A4FDC, #8B7CFF 55%, #c4b5fd);
        }

        .ht-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .82rem;
            font-weight: 600;
            color: var(--ht-muted);
            text-decoration: none;
            margin-bottom: 10px;
        }

        .ht-back:hover { color: var(--ht-violet); }

        .ht-kicker {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ht-violet);
            margin: 0 0 6px;
        }

        .ht-title {
            margin: 0;
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--ht-text);
            line-height: 1.15;
        }

        .ht-meta {
            margin: 8px 0 0;
            color: var(--ht-muted);
            font-size: .9rem;
        }

        .ht-feed-head {
            font-size: 1.02rem;
            font-weight: 700;
            color: var(--ht-text);
            padding: 10px 0 12px;
            border-bottom: 1px solid var(--ht-line);
            margin-bottom: 1px;
        }

        .ht-empty {
            text-align: center;
            padding: 48px 24px;
            background: #fff;
            border: 1px solid var(--ht-line);
        }

        .ht-empty-ic {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(90, 79, 220, .1);
            color: var(--ht-violet);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 20px;
        }

        .ht-empty h6 {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 6px;
            color: var(--ht-text);
        }

        .ht-empty p {
            font-size: 14px;
            color: var(--ht-muted);
            margin: 0;
        }

        .ht-loadmore {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #cfd9de;
            background: #fff;
            color: var(--ht-violet);
            font-weight: 700;
            font-size: .9rem;
            padding: 11px 24px;
            border-radius: 999px;
            cursor: pointer;
            transition: .2s;
        }

        .ht-loadmore:hover {
            background: var(--ht-surface);
            transform: translateY(-2px);
        }

        .ht-loadmore:disabled {
            opacity: .6;
            cursor: wait;
        }

        .ht-side-card {
            background: #fff;
            border: 1px solid var(--ht-line);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .ht-side-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px 10px;
            border-bottom: 1px solid var(--ht-line);
        }

        .ht-side-head h5 {
            margin: 0;
            font-size: .95rem;
            font-weight: 800;
            color: var(--ht-text);
        }

        .ht-side-head small {
            color: var(--ht-muted);
            font-size: .72rem;
            font-weight: 600;
        }

        .ht-side-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: inherit;
            transition: background .15s;
        }

        .ht-side-row:hover { background: var(--ht-surface); }

        .ht-side-row + .ht-side-row { border-top: 1px solid var(--ht-line); }

        .ht-rank {
            width: 28px;
            text-align: center;
            font-size: .78rem;
            font-weight: 800;
            color: var(--ht-muted);
            flex-shrink: 0;
        }

        .ht-rank.is-hot { color: #e11d48; }
        .ht-rank.is-bolt { color: #d97706; }
        .ht-rank.is-star { color: #0284c7; }

        .ht-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .ht-side-name {
            font-size: .9rem;
            font-weight: 700;
            color: var(--ht-text);
            line-height: 1.2;
        }

        .ht-side-sub {
            font-size: .75rem;
            color: var(--ht-muted);
            margin-top: 2px;
        }

        .ht-topic {
            font-size: .95rem;
            font-weight: 700;
            color: var(--ht-violet);
        }

        .ht-side-empty {
            padding: 18px 16px;
            color: var(--ht-muted);
            font-size: .85rem;
        }

        @media (max-width: 767.98px) {
            .ht-title { font-size: 1.35rem; }
        }
    </style>

    <div class="row ht-page">
        <div class="col-md-8 ph-feed-wrap">
            <div class="ht-hero">
                <a href="{{ url('timeline') }}" class="ht-back" wire:navigate>
                    <i class="fa fa-arrow-left"></i> Back to feed
                </a>
                <p class="ht-kicker">Hashtag</p>
                <h1 class="ht-title">#{{ $hashtag->name }}</h1>
                <p class="ht-meta">
                    {{ formatNumber($posts->count()) }}{{ $hasMore ? '+' : '' }}
                    {{ \Illuminate\Support\Str::plural('post', max(1, $posts->count())) }} showing
                    · explore related conversations
                </p>
            </div>

            @if (session()->has('success'))
                <div class="ph-flash ph-flash--success" role="alert" style="border-radius:12px;padding:12px 16px;background:#e6f4ea;color:#1b5e20;margin-bottom:12px">
                    {{ session('success') }}
                </div>
            @endif

            <div class="ht-feed-head">Latest posts</div>

            @forelse ($posts as $post)
                <livewire:user.post-content
                    :post="$post"
                    :estimated-earnings="$earnings[$post->id] ?? 0"
                    :gift-summary="$postGiftSummaries[$post->id] ?? ['total' => 0, 'recent' => []]"
                    :format-text="false"
                    :show-post-menu="true"
                    wire:key="hashtag-post-{{ $post->id }}" />
            @empty
                <div class="ht-empty">
                    <div class="ht-empty-ic"><i class="fa fa-hashtag"></i></div>
                    <h6>No posts for #{{ $hashtag->name }} yet</h6>
                    <p>Be the first to post about this topic on your timeline.</p>
                </div>
            @endforelse

            @if ($hasMore)
                <div class="text-center my-3">
                    <button type="button" class="ht-loadmore" wire:click="loadMore" wire:loading.attr="disabled" wire:target="loadMore">
                        <span wire:loading.remove wire:target="loadMore">Load more posts <i class="fa fa-arrow-down"></i></span>
                        <span wire:loading wire:target="loadMore">Loading…</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="col-md-4 mt-3 mt-md-0">
            <div class="ht-side-card">
                <div class="ht-side-head">
                    <h5><i class="fa fa-fire text-danger me-1"></i> Trending topics</h5>
                    <small>Top {{ $trendingTopics->count() }}</small>
                </div>
                @forelse ($trendingTopics as $index => $trend)
                    <a href="{{ url('hashtag/' . $trend->name) }}" class="ht-side-row" wire:navigate>
                        <div @class(['ht-rank', 'is-hot' => $index === 0, 'is-bolt' => $index === 1, 'is-star' => $index === 2])>
                            @if ($index === 0)
                                <i class="fa fa-fire"></i>
                            @elseif ($index === 1)
                                <i class="fa fa-bolt"></i>
                            @elseif ($index === 2)
                                <i class="fa fa-star"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <div style="min-width:0">
                            <div class="ht-topic">#{{ $trend->name }}</div>
                            <div class="ht-side-sub">
                                {{ formatNumber($trend->posts_count) }}
                                {{ \Illuminate\Support\Str::plural('post', $trend->posts_count) }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="ht-side-empty">No trending topics yet.</div>
                @endforelse
            </div>

            <div class="ht-side-card">
                <div class="ht-side-head">
                    <h5><i class="fa fa-users text-primary me-1"></i> Trending members</h5>
                    <small>Last 6 hours</small>
                </div>
                @forelse ($trendingMembers as $member)
                    @php
                        $username = is_array($member) ? ($member['username'] ?? null) : ($member->user->username ?? null);
                        $name = is_array($member) ? ($member['name'] ?? 'Member') : ($member->user->name ?? 'Member');
                        $avatar = is_array($member)
                            ? ($member['avatar'] ?? null)
                            : ($member->user->avatar ?? null);
                        $memberId = is_array($member)
                            ? ($member['id'] ?? $member['user_id'] ?? null)
                            : ($member->user_id ?? $member->user->id ?? null);
                        $score = is_array($member)
                            ? ($member['total_engagement'] ?? 0)
                            : ($member->total ?? 0);
                    @endphp
                    @continue(! $username)
                    <a href="{{ url('profile/' . $username) }}" class="ht-side-row" wire:navigate>
                        <x-user-avatar
                            :user-id="$memberId"
                            :src="$avatar"
                            :alt="displayName($name)"
                            :href="false"
                            size="sm"
                        />
                        <div style="min-width:0;flex:1">
                            <div class="ht-side-name">{{ displayName($name) }}</div>
                            <div class="ht-side-sub"><span>@</span>{{ $username }} · {{ formatNumber($score) }} engagements</div>
                        </div>
                    </a>
                @empty
                    <div class="ht-side-empty">No trending members right now.</div>
                @endforelse
            </div>
        </div>
    </div>

    @include('layouts.onboarding')

    <livewire:user.post-photo-viewer />
</div>
