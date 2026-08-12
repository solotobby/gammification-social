@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-badge--action { background: rgba(99,102,241,.12); color:#4338ca; font-family:ui-monospace,monospace; font-size:.75rem; }
        .dash-meta-code {
            display:block;
            max-width:320px;
            max-height:4.5rem;
            overflow:auto;
            font-size:.72rem;
            line-height:1.35;
            white-space:pre-wrap;
            word-break:break-word;
            color:var(--dash-muted);
            background:#f8fafc;
            border:1px solid var(--dash-border);
            border-radius:8px;
            padding:.4rem .55rem;
        }
        .dash-btn--sm { padding:.35rem .65rem; font-size:.78rem; }
    </style>
@endsection

@section('content')
<div class="content p-0"><div class="dash">
    <header class="dash-header">
        <div>
            <h1>Audit log</h1>
            <p>Admin actions across moderation, finance, and configuration</p>
        </div>
        <div class="dash-pill">{{ number_format($logs->total()) }} entries</div>
    </header>

    <section class="dash-section">
        <form method="get" class="dash-toolbar">
            <input type="search" name="q" value="{{ $search }}" class="dash-input" placeholder="Search admin, action, IP, subject id">
            <select name="action" class="dash-input" style="flex:0 0 220px">
                <option value="">All actions</option>
                @foreach ($actions as $option)
                    <option value="{{ $option }}" @selected($action === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <button class="dash-btn dash-btn--primary" type="submit">Filter</button>
            @if ($search || $action)
                <a href="{{ route('admin.audit-logs.index') }}" class="dash-btn dash-btn--ghost">Clear</a>
            @endif
        </form>

        <div class="dash-table-wrap dash-card">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Subject</th>
                        <th>Details</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>
                                <div>{{ $log->created_at?->format('M j, Y') }}</div>
                                <div class="dash-muted" style="font-size:.75rem">{{ $log->created_at?->format('g:i A') }} · {{ $log->created_at?->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if ($log->admin)
                                    <a href="{{ route('admin.users.show', $log->admin) }}" class="dash-link">{{ $log->admin->name }}</a>
                                    <div class="dash-muted" style="font-size:.75rem">{{ $log->admin->email }}</div>
                                @else
                                    <span class="dash-muted">Unknown</span>
                                @endif
                            </td>
                            <td><span class="dash-badge dash-badge--action">{{ $log->action }}</span></td>
                            <td>
                                @if ($log->subject_type)
                                    <div>{{ class_basename($log->subject_type) }}</div>
                                    <div class="dash-muted" style="font-size:.75rem;font-family:ui-monospace,monospace">#{{ $log->subject_id }}</div>
                                @else
                                    <span class="dash-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($log->metadata)
                                    <code class="dash-meta-code">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code>
                                @else
                                    <span class="dash-muted">—</span>
                                @endif
                            </td>
                            <td class="dash-muted" style="font-family:ui-monospace,monospace;font-size:.8rem">{{ $log->ip ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><div class="dash-empty">No audit entries match.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="dash-pagination">{{ $logs->links('pagination::bootstrap-5') }}</div>
        @endif
    </section>
</div></div>
@endsection
