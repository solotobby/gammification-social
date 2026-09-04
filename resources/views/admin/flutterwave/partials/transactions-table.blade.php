@php
    $rows = collect($transactions instanceof \Illuminate\Contracts\Pagination\Paginator
        ? $transactions->items()
        : ($transactions ?? []));

    if (isset($limit)) {
        $rows = $rows->take($limit);
    }
@endphp
<div class="dash-table-wrap">
    <table class="dash-table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Reference</th>
                <th>Type</th>
                <th>Flow</th>
                <th>Amount</th>
                <th>Status</th>
                <th>When</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $tx)
                @php
                    $txStatus = $tx->status ?? '';
                    $badge = $txStatus === 'successful' ? 'dash-badge--success'
                        : (in_array($txStatus, ['initiated', 'processing'], true) ? 'dash-badge--warn' : 'dash-badge--danger');
                    $isOut = strcasecmp((string) $tx->action, 'Debit') === 0;
                @endphp
                <tr>
                    <td>
                        <strong>{{ $tx->user?->name ?? '—' }}</strong>
                        <div class="dash-muted" style="font-size:.75rem">{{ $tx->user?->email }}</div>
                    </td>
                    <td>
                        <span style="font-size:.8rem">{{ $tx->ref }}</span>
                        @if ($tx->description)
                            <div class="dash-muted" style="font-size:.75rem">{{ \Illuminate\Support\Str::limit($tx->description, 48) }}</div>
                        @endif
                    </td>
                    <td><span class="dash-badge dash-badge--flw">{{ $tx->type }}</span></td>
                    <td>
                        <span class="dash-badge {{ $isOut ? 'dash-badge--warn' : 'dash-badge--success' }}">
                            {{ $isOut ? 'Out' : 'In' }}
                        </span>
                    </td>
                    <td class="dash-num">{{ $tx->currency }} {{ number_format((float) $tx->amount, 2) }}</td>
                    <td><span class="dash-badge {{ $badge }}">{{ $txStatus }}</span></td>
                    <td class="dash-muted">{{ $tx->created_at?->format('M j, Y g:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="dash-empty">No Flutterwave transactions in this range.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
