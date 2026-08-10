@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Communities</h1>
                    <p>Moderate communities, review revenue, members, and subscriptions</p>
                </div>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Total communities</span>
                        <div class="dash-kpi__value">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Paid</span>
                        <div class="dash-kpi__value">{{ number_format($stats['paid']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Active subscriptions</span>
                        <div class="dash-kpi__value">{{ number_format($stats['activeSubscriptions']) }}</div>
                        <div class="dash-muted">{{ number_format($stats['pendingSubscriptions']) }} pending</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Archived</span>
                        <div class="dash-kpi__value">{{ number_format($stats['archived']) }}</div>
                    </div>
                </div>
            </section>

            @if ($stats['revenueByCurrency']->isNotEmpty())
                <section class="dash-section">
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">Platform revenue (all communities)</h2>
                        </div>
                        <div class="dash-card__body dash-card__body--flush">
                            <div class="dash-table-wrap">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>Currency</th>
                                            <th>Payments</th>
                                            <th>Gross</th>
                                            <th>Platform fee</th>
                                            <th>Creator share</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stats['revenueByCurrency'] as $row)
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
                    </div>
                </section>
            @endif

            <section class="dash-section">
                <form method="get" class="dash-toolbar">
                    <input type="search" name="q" value="{{ $search }}" placeholder="Search name, slug, or owner" class="dash-input">
                    <select name="type" class="dash-input" style="flex:0 0 160px">
                        <option value="">All types</option>
                        @foreach (['public', 'private', 'paid', 'approval', 'archived'] as $option)
                            <option value="{{ $option }}" @selected($type === $option)>{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                    <select name="currency" class="dash-input" style="flex:0 0 120px">
                        <option value="">All currencies</option>
                        @foreach ($stats['byCurrency']->keys() as $code)
                            <option value="{{ $code }}" @selected($currency === $code)>{{ $code }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="dash-btn dash-btn--primary">Filter</button>
                    @if ($search || $type || $currency)
                        <a href="{{ route('admin.communities.index') }}" class="dash-btn dash-btn--ghost">Clear</a>
                    @endif
                </form>

                <div class="dash-table-wrap dash-card">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Community</th>
                                <th>Type</th>
                                <th>Currency</th>
                                <th>Owner</th>
                                <th>Members</th>
                                <th>Posts</th>
                                <th>Revenue</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($communities as $community)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.communities.show', $community) }}" class="dash-post-title">
                                            {{ $community->name }}
                                        </a>
                                        <div class="dash-muted">{{ $community->slug }}</div>
                                        <div class="dash-muted">{{ $community->category->name ?? 'Uncategorised' }}</div>
                                    </td>
                                    <td>
                                        <span class="dash-badge dash-badge--indigo">{{ ucfirst($community->type) }}</span>
                                    </td>
                                    <td>{{ $community->currency ?? 'NGN' }}</td>
                                    <td>
                                        @if ($community->user)
                                            <a href="{{ route('admin.users.show', $community->user) }}" class="dash-link">
                                                {{ $community->user->name }}
                                            </a>
                                            <div class="dash-muted">{{ $community->user->username }}</div>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ number_format($community->members_count) }}</td>
                                    <td>{{ number_format($community->posts_count) }}</td>
                                    <td>
                                        @if ($community->type === 'paid' && $community->gross_revenue)
                                            {{ $community->currency }} {{ number_format((float) $community->gross_revenue, 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $community->created_at?->format('M j, Y') }}</td>
                                    <td>
                                        @if ($community->archived_at)
                                            <span class="dash-badge dash-badge--amber">Archived</span>
                                        @else
                                            <span class="dash-badge dash-badge--emerald">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.communities.show', $community) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Manage</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">No communities found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="dash-pagination">
                    {{ $communities->links('pagination::bootstrap-5') }}
                </div>
            </section>
        </div>
    </div>
@endsection
