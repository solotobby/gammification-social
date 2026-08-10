@props(['users', 'level' => 'all', 'levelTabs' => null])

@php
    $currentLevel = $level ?? 'all';
    $tabs = collect(['all' => 'All']);
    if ($levelTabs) {
        $tabs = $tabs->merge($levelTabs);
    }
@endphp

<div class="dash-tabs">
    @foreach ($tabs as $key => $label)
        <a href="{{ route('admin.users.index', $key === 'all' ? [] : ['level' => $key]) }}"
            class="dash-tab {{ $currentLevel === $key ? 'is-active' : '' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="dash-card dash-section">
    <div class="dash-card__head">
        <h2 class="dash-card__title">Search</h2>
    </div>
    <div class="dash-card__body">
        <form action="{{ route('admin.users.search') }}" method="GET" class="dash-search">
            <input type="text" name="query" value="{{ request('query') }}" class="dash-input"
                placeholder="Search by name, username, or email">
            <button type="submit" class="dash-btn dash-btn--primary">
                <i class="fa fa-search"></i> Search
            </button>
        </form>
    </div>
</div>

<div class="dash-card">
    <div class="dash-card__head">
        <div>
            <h2 class="dash-card__title">All users</h2>
            <p class="dash-muted" style="margin:0.25rem 0 0;">
                {{ number_format($users->total()) }} total · showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
            </p>
        </div>
        <span class="dash-pill">Level: {{ ucfirst($currentLevel) }}</span>
    </div>

    <div class="dash-card__body--flush">
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Level</th>
                        <th>Verified</th>
                        <th>Channel</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="dash-user">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="dash-user__name">
                                        {{ $user->name }}
                                    </a>
                                    <span class="dash-user__meta">{{ $user->email }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="dash-badge dash-badge--indigo">
                                    {{ $user->userLevel?->plan_name ?? 'Basic' }}
                                </span>
                            </td>
                            <td>
                                @if ($user->email_verified_at)
                                    <span class="dash-badge dash-badge--emerald">Verified</span>
                                @else
                                    <span class="dash-badge dash-badge--gray">Pending</span>
                                @endif
                            </td>
                            <td class="dash-muted">{{ $user->heard ?: '—' }}</td>
                            <td class="dash-muted">
                                {{ $user->created_at?->format('M j, Y') }}
                                <span style="display:block; font-size:0.75rem;">
                                    {{ $user->created_at?->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="dash-empty">No users found for this filter.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="dash-pagination">
                {{ $users->appends($currentLevel !== 'all' ? ['level' => $currentLevel] : [])->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
