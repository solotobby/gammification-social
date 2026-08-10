@extends('layouts.admin')

@section('content')
    <div class="content">
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Audit log</h3>
            </div>
            <div class="block-content">
                <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2 mb-4">
                    <div class="col-md-4">
                        <select name="action" class="form-control">
                            <option value="">All actions</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        @if (request('action'))
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-alt-secondary">Clear</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Subject</th>
                                <th>Metadata</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at?->format('M j, Y g:i A') }}</td>
                                    <td>{{ $log->admin->name ?? 'Unknown' }}</td>
                                    <td><span class="badge bg-primary">{{ $log->action }}</span></td>
                                    <td>
                                        @if ($log->subject_type)
                                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td style="max-width:240px; word-break:break-all;">
                                        @if ($log->metadata)
                                            <small><code>{{ json_encode($log->metadata) }}</code></small>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $log->ip ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No audit entries yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
