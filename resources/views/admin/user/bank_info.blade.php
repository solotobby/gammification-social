@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-user__name {
            font-weight: 600;
            color: var(--dash-accent);
            text-decoration: none;
        }

        .dash-user__name:hover { text-decoration: underline; }
        .dash-user__meta { font-size: 0.75rem; color: var(--dash-muted); }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Bank accounts</h1>
                    <p>User withdrawal methods and payout details</p>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> Dashboard
                </a>
            </header>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Saved withdrawal methods</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">
                            {{ number_format($withdrawals->total()) }} total · showing {{ $withdrawals->firstItem() ?? 0 }}–{{ $withdrawals->lastItem() ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Method</th>
                                    <th>Details</th>
                                    <th>Currency</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($withdrawals as $method)
                                    <tr>
                                        <td>
                                            <div class="dash-user">
                                                <a href="{{ route('admin.users.show', $method->user_id) }}" class="dash-user__name">
                                                    {{ $method->user?->name ?? 'Unknown' }}
                                                </a>
                                                <span class="dash-user__meta">{{ $method->user?->email }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="dash-badge dash-badge--indigo">{{ strtoupper($method->payment_method ?: 'bank') }}</span>
                                        </td>
                                        <td class="dash-muted">
                                            @if ($method->payment_method === 'usdt')
                                                {{ maskCode($method->usdt_wallet) }}
                                            @elseif ($method->payment_method === 'paypal')
                                                {{ maskCode($method->paypal_email) }}
                                            @else
                                                {{ $method->account_name }} · {{ $method->bank_name }} · {{ $method->account_number }}
                                            @endif
                                        </td>
                                        <td class="dash-muted">{{ $method->currency ?: '—' }}</td>
                                        <td class="dash-muted">
                                            {{ $method->created_at?->format('M j, Y') }}
                                            <span style="display:block; font-size:0.75rem;">
                                                {{ $method->created_at?->diffForHumans() }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="dash-empty">No bank accounts or withdrawal methods saved yet.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($withdrawals->hasPages())
                        <div class="dash-pagination">
                            {{ $withdrawals->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
