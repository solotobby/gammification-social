<div class="pk-analytics-tab">
    <div class="row g-3" style="margin-bottom:16px">
        <div class="col-sm-6 col-xl-3">
            <div class="pk-card pk-stat-card">
                <div class="pk-stat-lbl">Total members</div>
                <div class="pk-stat-val">{{ number_format($stats['members_total']) }}</div>
                <div class="pk-stat-sub">+{{ $stats['members_7d'] }} this week · +{{ $stats['members_30d'] }} this month</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="pk-card pk-stat-card">
                <div class="pk-stat-lbl">Total posts</div>
                <div class="pk-stat-val">{{ number_format($stats['posts_total']) }}</div>
                <div class="pk-stat-sub">{{ $stats['posts_7d'] }} this week · {{ $stats['posts_30d'] }} this month</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="pk-card pk-stat-card">
                <div class="pk-stat-lbl">Engagement</div>
                <div class="pk-stat-val">{{ number_format($stats['likes_total'] + $stats['comments_total']) }}</div>
                <div class="pk-stat-sub">{{ number_format($stats['likes_total']) }} likes · {{ number_format($stats['comments_total']) }} comments</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="pk-card pk-stat-card">
                <div class="pk-stat-lbl">Post views</div>
                <div class="pk-stat-val">{{ number_format($stats['views_total']) }}</div>
                <div class="pk-stat-sub">
                    @if ($community->type === 'paid')
                        {{ number_format($stats['active_subscribers']) }} paying members
                    @elseif ($community->type === 'approval')
                        {{ number_format($stats['pending_requests']) }} pending requests
                    @elseif ($community->type === 'private')
                        {{ number_format($stats['invite_link_uses']) }} link invite uses
                    @else
                        All-time impressions
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="pk-card pk-settings-section h-100">
                <h3>Top posts</h3>
                <div class="pk-sub" style="margin-bottom:6px">Ranked by likes and comments.</div>

                @forelse ($topPosts as $post)
                    <div class="pk-member-row d-flex flex-wrap align-items-start gap-2" wire:key="top-post-{{ $post->id }}">
                        <div class="pk-ph-av" style="background:{{ $community->color }};width:32px;height:32px;font-size:.72rem">
                            {{ mb_strtoupper(mb_substr($post->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1" style="min-width:160px">
                            <div class="pk-n" style="font-size:.84rem">{{ $post->user->name ?? 'Unknown' }}</div>
                            <div class="pk-h">{{ Str::limit(strip_tags($post->content), 90) ?: 'Media post' }}</div>
                            <div class="pk-h">{{ $post->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="pk-pay-col text-sm-end">
                            <div class="pk-pay-amt">{{ number_format($post->likes_count) }} ♥</div>
                            <div class="pk-pay-meta">{{ number_format($post->comments_count) }} comments · {{ number_format($post->views_count) }} views</div>
                        </div>
                    </div>
                @empty
                    <div class="pk-sub" style="margin-bottom:0;margin-top:8px">No posts yet — engagement stats will appear here.</div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-5">
            <div class="pk-card pk-settings-section h-100">
                <h3>Recent members</h3>
                <div class="pk-sub" style="margin-bottom:6px">Latest people who joined.</div>

                @forelse ($recentMembers as $member)
                    <div class="pk-member-row d-flex flex-wrap align-items-center gap-2" wire:key="recent-member-{{ $member->id }}">
                        <div class="pk-ph-av" style="background:{{ $community->color }}">
                            {{ mb_strtoupper(mb_substr($member->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1" style="min-width:140px">
                            <div class="pk-n">{{ $member->name }}</div>
                            <div class="pk-h">Joined {{ $member->pivot->created_at?->diffForHumans() ?? 'recently' }}</div>
                        </div>
                        <span class="pk-role-badge @if ($member->pivot->role === 'member') pk-member-role @endif">
                            {{ ucfirst($member->pivot->role) }}
                        </span>
                    </div>
                @empty
                    <div class="pk-sub" style="margin-bottom:0;margin-top:8px">No members yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
