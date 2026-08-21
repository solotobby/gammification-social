@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .dash-kpi {
            display: flex; flex-direction: column; gap: .75rem; padding: 1.25rem;
            background: var(--dash-surface); border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius); box-shadow: var(--dash-shadow); height: 100%;
        }
        .dash-kpi__label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--dash-muted); }
        .dash-kpi__value { font-size: 1.5rem; font-weight: 700; letter-spacing: -.03em; }
        .dash-post-title { font-weight: 600; color: var(--dash-accent); text-decoration: none; }
        .dash-post-title:hover { text-decoration: underline; }
        .dash-badge--danger { background: rgba(220, 53, 69, .12); color: #b42318; }
        .dash-badge--warn { background: rgba(245, 158, 11, .14); color: #b54708; }
        .dash-badge--info { background: rgba(59, 130, 246, .12); color: #1d4ed8; }
        .dash-badge--success { background: rgba(16, 185, 129, .12); color: #067647; }
        @media (max-width: 1200px) { .dash-grid--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 640px) { .dash-grid--4 { grid-template-columns: 1fr; } }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>User Feedback</h1>
                    <p>Complaints, suggestions, improvements, and bug reports from users</p>
                </div>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">All</span>
                        <div class="dash-kpi__value">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">New</span>
                        <div class="dash-kpi__value">{{ number_format($stats['new']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">In review</span>
                        <div class="dash-kpi__value">{{ number_format($stats['reviewed']) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Awaiting reply</span>
                        <div class="dash-kpi__value">{{ number_format($stats['awaiting']) }}</div>
                    </div>
                </div>
            </section>

            <section class="dash-section">
                <form method="get" class="dash-toolbar" style="flex-wrap:wrap;gap:.5rem;">
                    <input type="search" name="q" value="{{ $search }}" class="dash-input"
                        placeholder="Search subject, message, or user">
                    <select name="type" class="dash-input" style="max-width:180px">
                        <option value="">All types</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="dash-input" style="max-width:180px">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="dash-btn dash-btn--primary">Filter</button>
                    @if ($search || $type || $status)
                        <a href="{{ route('admin.feedback.index') }}" class="dash-btn dash-btn--ghost">Clear</a>
                    @endif
                </form>

                <div class="dash-card">
                    <div class="dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Messages</th>
                                        <th>Updated</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $item)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.feedback.show', $item) }}" class="dash-post-title">
                                                    {{ $item->subject }}
                                                </a>
                                                <div class="dash-muted" style="font-size:.8rem;margin-top:.2rem;">
                                                    {{ \Illuminate\Support\Str::limit($item->message, 80) }}
                                                </div>
                                            </td>
                                            <td class="dash-muted">
                                                @if ($item->user)
                                                    <a href="{{ route('admin.users.show', $item->user) }}" class="dash-link">
                                                        {{ '@'.$item->user->username }}
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $typeClass = match ($item->type) {
                                                        'complaint', 'bug' => 'dash-badge--danger',
                                                        'suggestion', 'improvement' => 'dash-badge--info',
                                                        default => 'dash-badge--gray',
                                                    };
                                                @endphp
                                                <span class="dash-badge {{ $typeClass }}">{{ $item->typeLabel() }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match ($item->status) {
                                                        'new' => 'dash-badge--warn',
                                                        'reviewed' => 'dash-badge--info',
                                                        'resolved' => 'dash-badge--success',
                                                        default => 'dash-badge--gray',
                                                    };
                                                @endphp
                                                <span class="dash-badge {{ $statusClass }}">{{ $item->statusLabel() }}</span>
                                                @if ($item->last_message_by === 'user' && $item->status !== 'closed')
                                                    <div style="margin-top:.35rem;">
                                                        <span class="dash-badge dash-badge--warn">Needs reply</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="dash-muted">{{ number_format($item->messages_count) }}</td>
                                            <td class="dash-muted">{{ ($item->last_message_at ?? $item->created_at)?->format('M j, Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.feedback.show', $item) }}" class="dash-btn dash-btn--ghost" style="padding:.5rem .75rem;">
                                                    Open
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="dash-muted" style="text-align:center;padding:2rem;">
                                                No feedback yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($items->hasPages())
                    <div style="margin-top:1rem;">{{ $items->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
