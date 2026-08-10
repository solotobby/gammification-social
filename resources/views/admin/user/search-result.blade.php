@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Search results</h1>
                    <p>Showing matches for “{{ $query }}”</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> All users
                </a>
            </header>

            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Results</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            {{ number_format($users->total()) }} match{{ $users->total() === 1 ? '' : 'es' }}
                        </p>
                    </div>
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="dash-empty">No users matched your search.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($users->hasPages())
                        <div class="dash-pagination">
                            {{ $users->appends(['query' => $query])->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
