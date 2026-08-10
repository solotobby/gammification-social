@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-badge--rose { background: #fff1f2; color: #be123c; }
        .dash-ref { font-family: ui-monospace, monospace; font-size: 0.8125rem; color: var(--dash-text); }
        .dash-amount { font-weight: 600; white-space: nowrap; }
        .dash-desc { max-width: 280px; color: var(--dash-muted); font-size: 0.8125rem; }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Transactions</h1>
                    <p>{{ $user->name }} · {{ $user->email }}</p>
                </div>
                <a href="{{ route('admin.users.show', $user) }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> User profile
                </a>
            </header>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Payment history</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            {{ number_format($transactions->total()) }} total · showing {{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    @php
                                        $status = strtolower((string) $transaction->status);
                                        $statusClass = match (true) {
                                            in_array($status, ['successful', 'allocated', 'success', 'paid'], true) => 'dash-badge--emerald',
                                            in_array($status, ['failed', 'cancelled', 'canceled', 'declined'], true) => 'dash-badge--rose',
                                            in_array($status, ['pending', 'queued', 'processing'], true) => 'dash-badge--amber',
                                            default => 'dash-badge--gray',
                                        };
                                    @endphp
                                    <tr>
                                        <td><span class="dash-ref">{{ $transaction->ref ?: '—' }}</span></td>
                                        <td class="dash-amount">
                                            {{ getCurrencyCode($transaction->currency) }}{{ number_format((float) $transaction->amount, 2) }}
                                            <span class="dash-muted" style="display:block; font-size:0.75rem;">{{ $transaction->currency }}</span>
                                        </td>
                                        <td>
                                            <span class="dash-badge {{ $statusClass }}">{{ $transaction->status }}</span>
                                        </td>
                                        <td class="dash-muted">{{ $transaction->type ?: '—' }}</td>
                                        <td class="dash-muted">{{ $transaction->action ?: '—' }}</td>
                                        <td class="dash-desc">{{ $transaction->description ?: '—' }}</td>
                                        <td class="dash-muted">
                                            {{ $transaction->created_at?->format('M j, Y') }}
                                            <span style="display:block; font-size:0.75rem;">
                                                {{ $transaction->created_at?->format('g:i A') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="dash-empty">No transactions recorded for this user.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($transactions->hasPages())
                        <div class="dash-pagination">
                            {{ $transactions->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
