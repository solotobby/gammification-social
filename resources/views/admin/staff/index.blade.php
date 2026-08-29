@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-field label { display: block; margin-bottom: .375rem; font-size: .8125rem; font-weight: 600; color: var(--dash-muted); }
        .dash-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        @media (max-width: 900px) { .dash-grid--2 { grid-template-columns: 1fr; } }
        .dash-badge--warn { background: rgba(245, 158, 11, .14); color: #b54708; }
        .dash-badge--success { background: rgba(16, 185, 129, .12); color: #067647; }
        .dash-badge--danger { background: rgba(220, 53, 69, .12); color: #b42318; }
        .dash-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Staff</h1>
                    <p>Invite teammates who can moderate users, content, communities, and feedback</p>
                </div>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <div class="dash-grid dash-grid--2" style="gap:1rem;margin-bottom:1.5rem;">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Invite staff</h2>
                    </div>
                    <div class="dash-card__body">
                        <form method="post" action="{{ route('admin.staff.invite') }}">
                            @csrf
                            <div class="dash-field" style="margin-bottom:1rem;">
                                <label for="name">Full name</label>
                                <input id="name" type="text" name="name" class="dash-input" value="{{ old('name') }}" required>
                            </div>
                            <div class="dash-field" style="margin-bottom:1rem;">
                                <label for="email">Work email</label>
                                <input id="email" type="email" name="email" class="dash-input" value="{{ old('email') }}" required>
                            </div>
                            <p class="dash-muted" style="font-size:.8rem;margin-bottom:1rem;">
                                They’ll get an email link to set a password. Staff can manage users, communities, posts, rolls, bookmarks, reports, feedback, and blog.
                            </p>
                            <button type="submit" class="dash-btn dash-btn--primary">Send invite</button>
                        </form>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Active staff</h2>
                    </div>
                    <div class="dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($staff as $member)
                                        <tr>
                                            <td>
                                                <strong>{{ $member->name }}</strong>
                                                <div class="dash-muted" style="font-size:.8rem;">{{ '@'.$member->username }}</div>
                                            </td>
                                            <td class="dash-muted">{{ $member->email }}</td>
                                            <td>
                                                <form method="post" action="{{ route('admin.staff.remove', $member) }}"
                                                    onsubmit="return confirm('Delete staff account for {{ $member->email }}? This cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dash-btn dash-btn--ghost" style="padding:.4rem .7rem;">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="dash-muted" style="text-align:center;padding:1.5rem;">No staff yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Invites</h2>
                </div>
                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Invitee</th>
                                    <th>Invited by</th>
                                    <th>Status</th>
                                    <th>Expires</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invites as $invite)
                                    <tr>
                                        <td>
                                            <strong>{{ $invite->name }}</strong>
                                            <div class="dash-muted" style="font-size:.8rem;">{{ $invite->email }}</div>
                                        </td>
                                        <td class="dash-muted">{{ $invite->inviter?->name ?? '—' }}</td>
                                        <td>
                                            @if ($invite->accepted_at)
                                                <span class="dash-badge dash-badge--success">Accepted</span>
                                            @elseif ($invite->isExpired())
                                                <span class="dash-badge dash-badge--danger">Expired</span>
                                            @else
                                                <span class="dash-badge dash-badge--warn">Pending</span>
                                            @endif
                                        </td>
                                        <td class="dash-muted">{{ $invite->expires_at?->format('M j, Y') ?? '—' }}</td>
                                        <td>
                                            @if ($invite->isPending())
                                                <div class="dash-actions">
                                                    <button type="button" class="dash-btn dash-btn--ghost" style="padding:.4rem .7rem;"
                                                        onclick="navigator.clipboard.writeText(@js($invite->acceptUrl()))">Copy link</button>
                                                    <form method="post" action="{{ route('admin.staff.invites.revoke', $invite) }}"
                                                        onsubmit="return confirm('Revoke this invite?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dash-btn dash-btn--ghost" style="padding:.4rem .7rem;">Revoke</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="dash-muted" style="text-align:center;padding:1.5rem;">No invites yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
