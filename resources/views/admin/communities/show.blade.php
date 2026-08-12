@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 0.875rem;
        }

        .dash-meta-item {
            padding: 0.875rem 1rem;
            border: 1px solid var(--dash-border);
            border-radius: 12px;
            background: #f8fafc;
        }

        .dash-meta-item span {
            display: block;
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dash-muted);
            margin-bottom: 0.35rem;
        }

        .dash-meta-item strong {
            font-size: 0.9375rem;
        }

        .dash-post-snippet {
            max-width: 420px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dash-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .dash-grid--2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        @media (max-width: 900px) {
            .dash-grid--2 { grid-template-columns: 1fr; }
        }

        .dash-btn--danger {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .dash-btn--sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.75rem;
        }

        .dash-section h2 {
            margin: 0 0 1rem;
            font-size: 1rem;
            font-weight: 700;
        }

        .dash-muted {
            color: var(--dash-muted);
            font-size: 0.8125rem;
        }

        .dash-post-title {
            font-weight: 600;
            color: var(--dash-text);
            text-decoration: none;
        }

        .dash-post-title:hover {
            color: var(--dash-accent);
        }
    </style>
@endsection

@section('content')
    @php
        $tabs = [
            'overview' => 'Overview',
            'members' => 'Members (' . number_format($community->members_count) . ')',
            'subscriptions' => 'Subscriptions (' . number_format($community->subscriptions_count) . ')',
            'payouts' => 'Payouts',
            'posts' => 'Posts (' . number_format($community->posts_count) . ')',
            'invites' => 'Invites & requests',
        ];
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <p><a href="{{ route('admin.communities.index') }}">&larr; Communities</a></p>
                    <h1>{{ $community->name }}</h1>
                    <p>
                        {{ $community->slug }} · {{ ucfirst($community->type) }} · {{ $community->currency ?? 'NGN' }}
                        @if ($community->archived_at)
                            · <strong>Archived {{ $community->archived_at->diffForHumans() }}</strong>
                        @endif
                    </p>
                </div>
                <div class="dash-actions">
                    @unless ($community->isArchived())
                        <a href="{{ $community->public_url }}" class="dash-btn dash-btn--ghost" target="_blank">Public page</a>
                    @endunless
                    <a href="{{ route('community.show', $community) }}" class="dash-btn dash-btn--ghost" target="_blank">View live</a>
                    @if ($community->user)
                        <a href="{{ route('admin.users.show', $community->user) }}" class="dash-btn dash-btn--ghost">Owner profile</a>
                    @endif
                </div>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <nav class="dash-tabs">
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('admin.communities.show', ['community' => $community, 'tab' => $key]) }}"
                        class="dash-tab @if ($tab === $key) is-active @endif">{{ $label }}</a>
                @endforeach
            </nav>

            @if ($tab === 'overview')
                <section class="dash-section dash-grid dash-grid--2">
                    <div class="dash-card">
                        <div class="dash-card__head"><h2 class="dash-card__title">Community details</h2></div>
                        <div class="dash-card__body">
                            <div class="dash-meta-grid">
                                <div class="dash-meta-item"><span>Owner</span><strong>{{ $community->user->name ?? '—' }}</strong></div>
                                <div class="dash-meta-item"><span>Owner email</span><strong>{{ $community->user->email ?? '—' }}</strong></div>
                                <div class="dash-meta-item"><span>Category</span><strong>{{ $community->category->name ?? '—' }}</strong></div>
                                <div class="dash-meta-item"><span>Currency</span><strong>{{ $community->currency ?? 'NGN' }}</strong></div>
                                <div class="dash-meta-item"><span>Members</span><strong>{{ number_format($community->members_count) }}</strong></div>
                                <div class="dash-meta-item"><span>Posts</span><strong>{{ number_format($community->posts_count) }}</strong></div>
                                <div class="dash-meta-item"><span>Banned members</span><strong>{{ number_format($community->banned_members_count) }}</strong></div>
                                <div class="dash-meta-item"><span>Created</span><strong>{{ $community->created_at?->format('M j, Y g:i A') }}</strong></div>
                                <div class="dash-meta-item"><span>Last updated</span><strong>{{ $community->updated_at?->format('M j, Y g:i A') }}</strong></div>
                                <div class="dash-meta-item"><span>Community ID</span><strong style="font-size:.75rem;word-break:break-all">{{ $community->id }}</strong></div>
                            </div>

                            @if ($community->description)
                                <p class="dash-muted" style="margin-top:1rem">{{ $community->description }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card__head"><h2 class="dash-card__title">Moderation</h2></div>
                        <div class="dash-card__body">
                            <div class="dash-actions">
                                @if ($community->archived_at)
                                    <form method="post" action="{{ route('admin.communities.unarchive', $community) }}">
                                        @csrf
                                        <button type="submit" class="dash-btn dash-btn--primary">Restore community</button>
                                    </form>
                                @else
                                    <form method="post" action="{{ route('admin.communities.archive', $community) }}"
                                        onsubmit="return confirm('Archive this community?')">
                                        @csrf
                                        <button type="submit" class="dash-btn dash-btn--ghost">Archive</button>
                                    </form>
                                @endif

                                <form method="post" action="{{ route('admin.communities.destroy', $community) }}"
                                    onsubmit="return confirm('Permanently delete this community and all posts?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dash-btn dash-btn--danger">Delete permanently</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                @if ($community->type === 'paid')
                    <section class="dash-section dash-grid dash-grid--2">
                        <div class="dash-card">
                            <div class="dash-card__head"><h2 class="dash-card__title">Billing configuration</h2></div>
                            <div class="dash-card__body">
                                <div class="dash-meta-grid">
                                    <div class="dash-meta-item"><span>List price</span><strong>{{ $community->currency }} {{ number_format((float) $community->monthly_fee, 2) }}{{ $community->price_suffix }}</strong></div>
                                    <div class="dash-meta-item"><span>Members pay</span><strong>{{ $community->currency }} {{ number_format((float) ($community->member_charge ?? 0), 2) }}{{ $community->price_suffix }}</strong></div>
                                    <div class="dash-meta-item"><span>Creator receives</span><strong>{{ $community->currency }} {{ number_format((float) ($community->creator_payout ?? 0), 2) }}{{ $community->price_suffix }}</strong></div>
                                    <div class="dash-meta-item"><span>Platform fee</span><strong>{{ $community->platform_fee_percent ?? 0 }}% ({{ $community->currency }} {{ number_format((float) ($community->platform_fee_amount ?? 0), 2) }})</strong></div>
                                    <div class="dash-meta-item"><span>Fee payer</span><strong>{{ ucfirst($community->fee_payer ?? 'creator') }}</strong></div>
                                    <div class="dash-meta-item"><span>Billing</span><strong>{{ $community->billing_label }}</strong></div>
                                    <div class="dash-meta-item"><span>Active subs</span><strong>{{ number_format($community->active_subscriptions_count) }}</strong></div>
                                    <div class="dash-meta-item"><span>Pending subs</span><strong>{{ number_format($community->pending_subscriptions_count) }}</strong></div>
                                </div>
                            </div>
                        </div>

                        <div class="dash-card">
                            <div class="dash-card__head"><h2 class="dash-card__title">Revenue summary</h2></div>
                            <div class="dash-card__body dash-card__body--flush">
                                @if ($revenueSummary->isEmpty())
                                    <p class="dash-muted" style="padding:1.25rem">No payouts recorded yet.</p>
                                @else
                                    <div class="dash-table-wrap">
                                        <table class="dash-table">
                                            <thead>
                                                <tr>
                                                    <th>Currency</th>
                                                    <th>Payments</th>
                                                    <th>Gross</th>
                                                    <th>Platform</th>
                                                    <th>Creator</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($revenueSummary as $row)
                                                    <tr>
                                                        <td>{{ $row->currency }}</td>
                                                        <td>{{ number_format($row->payments) }}</td>
                                                        <td>{{ number_format((float) $row->gross, 2) }}</td>
                                                        <td>{{ number_format((float) $row->platform, 2) }}</td>
                                                        <td>{{ number_format((float) $row->creator, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>

                    @if ($paymentPlans->isNotEmpty())
                        <section class="dash-section">
                            <div class="dash-card">
                                <div class="dash-card__head"><h2 class="dash-card__title">Flutterwave payment plans</h2></div>
                                <div class="dash-card__body dash-card__body--flush">
                                    <div class="dash-table-wrap">
                                        <table class="dash-table">
                                            <thead>
                                                <tr>
                                                    <th>Currency</th>
                                                    <th>Interval</th>
                                                    <th>Amount</th>
                                                    <th>Plan ID</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($paymentPlans as $plan)
                                                    <tr>
                                                        <td>{{ $plan->currency }}</td>
                                                        <td>{{ ucfirst($plan->billing_interval) }}</td>
                                                        <td>{{ number_format((float) $plan->amount, 2) }}</td>
                                                        <td><code style="font-size:.75rem">{{ $plan->flutterwave_plan_id }}</code></td>
                                                        <td>{{ ucfirst($plan->status) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif
                @endif

            @elseif ($tab === 'members')
                <section class="dash-section">
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">All members</h2>
                            <span class="dash-muted">Includes banned and active</span>
                        </div>
                        <div class="dash-card__body dash-card__body--flush">
                            <div class="dash-table-wrap">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Joined</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($members as $member)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $member) }}" class="dash-link">{{ $member->name }}</a>
                                                </td>
                                                <td>{{ $member->username }}</td>
                                                <td>{{ $member->email }}</td>
                                                <td><span class="dash-badge dash-badge--gray">{{ ucfirst($member->pivot->role ?? 'member') }}</span></td>
                                                <td>
                                                    @if (($member->pivot->status ?? 'active') === 'banned')
                                                        <span class="dash-badge dash-badge--amber">Banned</span>
                                                    @else
                                                        <span class="dash-badge dash-badge--emerald">Active</span>
                                                    @endif
                                                </td>
                                                <td>{{ $member->pivot->created_at?->format('M j, Y') }}</td>
                                                <td>
                                                    @if ($member->id !== $community->user_id)
                                                        <div class="dash-actions">
                                                            @if (($member->pivot->status ?? 'active') === 'banned')
                                                                <form method="post" action="{{ route('admin.communities.unban-member', $community) }}">
                                                                    @csrf
                                                                    <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                                    <button type="submit" class="dash-btn dash-btn--ghost dash-btn--sm">Unban</button>
                                                                </form>
                                                            @else
                                                                <form method="post" action="{{ route('admin.communities.ban-member', $community) }}"
                                                                    onsubmit="return confirm('Ban this member?')">
                                                                    @csrf
                                                                    <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                                    <button type="submit" class="dash-btn dash-btn--ghost dash-btn--sm">Ban</button>
                                                                </form>
                                                            @endif
                                                            <form method="post" action="{{ route('admin.communities.remove-member', $community) }}"
                                                                onsubmit="return confirm('Remove this member from the community?')">
                                                                @csrf
                                                                <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                                <button type="submit" class="dash-btn dash-btn--danger dash-btn--sm">Remove</button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="dash-muted">Owner</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7">No members.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $members->links() }}
                </section>

            @elseif ($tab === 'subscriptions')
                <section class="dash-section">
                    <div class="dash-card">
                        <div class="dash-card__head"><h2 class="dash-card__title">Subscriptions</h2></div>
                        <div class="dash-card__body dash-card__body--flush">
                            <div class="dash-table-wrap">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>Member</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Creator share</th>
                                            <th>Platform fee</th>
                                            <th>Billing</th>
                                            <th>Gateway</th>
                                            <th>Started</th>
                                            <th>Expires</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($subscriptions as $subscription)
                                            <tr>
                                                <td>
                                                    @if ($subscription->user)
                                                        <a href="{{ route('admin.users.show', $subscription->user) }}" class="dash-link">
                                                            {{ $subscription->user->name }}
                                                        </a>
                                                        <div class="dash-muted">{{ $subscription->user->email }}</div>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="dash-badge @if ($subscription->status === 'active') dash-badge--emerald @elseif ($subscription->status === 'pending') dash-badge--amber @else dash-badge--gray @endif">
                                                        {{ ucfirst($subscription->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format((float) $subscription->amount, 2) }}</td>
                                                <td>{{ number_format((float) $subscription->creator_amount, 2) }}</td>
                                                <td>{{ number_format((float) $subscription->platform_fee, 2) }}</td>
                                                <td>{{ ucfirst($subscription->billing_type ?? '—') }} {{ $subscription->billing_interval ? '· ' . $subscription->billing_interval : '' }}</td>
                                                <td>{{ $subscription->gateway ?? '—' }}</td>
                                                <td>{{ $subscription->starts_at?->format('M j, Y') ?? '—' }}</td>
                                                <td>{{ $subscription->expires_at?->format('M j, Y') ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="9">No subscriptions.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $subscriptions->links() }}
                </section>

            @elseif ($tab === 'payouts')
                <section class="dash-section">
                    <div class="dash-card">
                        <div class="dash-card__head"><h2 class="dash-card__title">Payout ledger</h2></div>
                        <div class="dash-card__body dash-card__body--flush">
                            <div class="dash-table-wrap">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Payer</th>
                                            <th>Gross</th>
                                            <th>Platform fee</th>
                                            <th>Creator amount</th>
                                            <th>Currency</th>
                                            <th>Billing</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($payouts as $payout)
                                            <tr>
                                                <td>{{ $payout->paid_at?->format('M j, Y g:i A') ?? $payout->created_at?->format('M j, Y g:i A') }}</td>
                                                <td>{{ $payout->payer->name ?? '—' }}</td>
                                                <td>{{ number_format((float) $payout->gross_amount, 2) }}</td>
                                                <td>{{ number_format((float) $payout->platform_fee, 2) }}</td>
                                                <td>{{ number_format((float) $payout->creator_amount, 2) }}</td>
                                                <td>{{ $payout->currency }}</td>
                                                <td>{{ ucfirst($payout->billing_type ?? '—') }}</td>
                                                <td>{{ ucfirst($payout->status ?? '—') }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8">No payouts recorded.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $payouts->links() }}
                </section>

            @elseif ($tab === 'posts')
                <section class="dash-section">
                    <div class="dash-card">
                        <div class="dash-card__head"><h2 class="dash-card__title">Community posts</h2></div>
                        <div class="dash-card__body dash-card__body--flush">
                            <div class="dash-table-wrap">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>Author</th>
                                            <th>Content</th>
                                            <th>Engagement</th>
                                            <th>Posted</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($posts as $post)
                                            <tr>
                                                <td>
                                                    @if ($post->user)
                                                        <a href="{{ route('admin.users.show', $post->user) }}" class="dash-link">{{ $post->user->name }}</a>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td><div class="dash-post-snippet">{{ Str::limit(strip_tags($post->content), 120) }}</div></td>
                                                <td>
                                                    <span class="dash-muted">{{ number_format($post->likes_count) }} likes · {{ number_format($post->comments_count) }} comments · {{ number_format($post->views_count) }} views</span>
                                                </td>
                                                <td>{{ $post->created_at?->format('M j, Y g:i A') }}</td>
                                                <td>
                                                    <form method="post" action="{{ route('admin.communities.posts.destroy', [$community, $post]) }}"
                                                        onsubmit="return confirm('Delete this post permanently?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dash-btn dash-btn--danger dash-btn--sm">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5">No posts.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $posts->links() }}
                </section>

            @elseif ($tab === 'invites')
                <section class="dash-section dash-grid dash-grid--2">
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">Invites</h2>
                            <span class="dash-muted">{{ number_format($community->invites_count) }} total</span>
                        </div>
                        <div class="dash-card__body dash-card__body--flush">
                            <div class="dash-table-wrap">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Target</th>
                                            <th>Status</th>
                                            <th>Uses</th>
                                            <th>Expires</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($invites as $invite)
                                            <tr>
                                                <td>{{ ucfirst($invite->type) }}</td>
                                                <td>
                                                    @if ($invite->user)
                                                        {{ $invite->user->name }}<br><span class="dash-muted">{{ $invite->user->email }}</span>
                                                    @else
                                                        Link invite
                                                    @endif
                                                </td>
                                                <td>{{ ucfirst($invite->status) }}</td>
                                                <td>{{ number_format($invite->uses_count ?? 0) }}</td>
                                                <td>{{ $invite->expires_at?->format('M j, Y') ?? 'Never' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5">No invites.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{ $invites->links() }}
                    </div>

                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">Join requests</h2>
                            <span class="dash-muted">{{ number_format($community->pending_join_requests_count) }} pending</span>
                        </div>
                        <div class="dash-card__body dash-card__body--flush">
                            <div class="dash-table-wrap">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Status</th>
                                            <th>Reason</th>
                                            <th>Requested</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($joinRequests as $request)
                                            <tr>
                                                <td>
                                                    @if ($request->user)
                                                        <a href="{{ route('admin.users.show', $request->user) }}" class="dash-link">{{ $request->user->name }}</a>
                                                        <div class="dash-muted">{{ $request->user->email }}</div>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ ucfirst($request->status) }}</td>
                                                <td>{{ Str::limit($request->reason, 80) ?: '—' }}</td>
                                                <td>{{ $request->created_at?->format('M j, Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4">No join requests.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{ $joinRequests->links() }}
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
