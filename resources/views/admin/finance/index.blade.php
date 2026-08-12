@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-badge--danger { background: rgba(220,53,69,.12); color:#b42318; }
        .dash-badge--success { background: rgba(16,185,129,.12); color:#067647; }
        .dash-badge--warn { background: rgba(245,158,11,.14); color:#b54708; }
        .dash-btn--sm { padding:.35rem .65rem; font-size:.78rem; }
        .dash-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; align-items:start; }
        @media (max-width:960px){ .dash-grid-2{grid-template-columns:1fr;} }
    </style>
@endsection

@section('content')
<div class="content p-0"><div class="dash">
    <header class="dash-header">
        <div>
            <h1>Wallets & earnings</h1>
            <p>Ledger search, anomalous balances, and payout vs withdrawal reconciliation</p>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
            <a href="{{ route('admin.withdrawals.index') }}" class="dash-btn dash-btn--ghost">Withdrawals</a>
            <a href="{{ route('admin.payouts.current') }}" class="dash-btn dash-btn--ghost">Current payouts</a>
        </div>
    </header>

    <section class="dash-section">
        <div class="dash-grid dash-grid--4">
            <div class="dash-kpi">
                <span class="dash-kpi__label">Queued withdrawals</span>
                <div class="dash-kpi__value">{{ number_format($stats['queued_withdrawals']) }}</div>
                <div class="dash-muted">${{ number_format($stats['queued_withdrawal_amount'], 2) }}</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Queued engagement payouts</span>
                <div class="dash-kpi__value">{{ number_format($stats['queued_payouts']) }}</div>
                <div class="dash-muted">${{ number_format($stats['queued_payout_amount'], 2) }}</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Paid withdrawals (month)</span>
                <div class="dash-kpi__value">${{ number_format($stats['paid_withdrawals_month'], 2) }}</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Anomalous wallets</span>
                <div class="dash-kpi__value">{{ number_format($stats['negative_wallets']) }}</div>
                <div class="dash-muted">{{ number_format($stats['pending_engagement_stats']) }} pending engagement stats</div>
            </div>
        </div>
    </section>

    @if ($stats['walletTotals']->isNotEmpty())
        <section class="dash-section">
            <div class="dash-card">
                <div class="dash-card__head"><h2 class="dash-card__title">Wallet balances by currency</h2></div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>Currency</th><th>Wallets</th><th>Main</th><th>Referral</th><th>Promoter</th><th>Total</th></tr></thead>
                            <tbody>
                                @foreach ($stats['walletTotals'] as $row)
                                    <tr>
                                        <td>{{ $row->currency ?: '—' }}</td>
                                        <td>{{ number_format($row->wallets) }}</td>
                                        <td>{{ number_format((float) $row->main, 2) }}</td>
                                        <td>{{ number_format((float) $row->referral, 2) }}</td>
                                        <td>{{ number_format((float) $row->promoter, 2) }}</td>
                                        <td><strong>{{ number_format((float) $row->main + (float) $row->referral + (float) $row->promoter, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="dash-section dash-grid-2">
        <div class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Withdrawal breakdown</h2></div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>Wallet</th><th>Status</th><th>Count</th><th>Amount</th></tr></thead>
                        <tbody>
                            @forelse ($reconciliation['withdrawalByWallet'] as $row)
                                <tr>
                                    <td>{{ $row->wallet_type ?: '—' }}</td>
                                    <td>{{ $row->status }}</td>
                                    <td>{{ number_format($row->total) }}</td>
                                    <td>${{ number_format((float) $row->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="dash-empty">No withdrawals.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Engagement payout status</h2></div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>Status</th><th>Count</th><th>Amount</th></tr></thead>
                        <tbody>
                            @forelse ($reconciliation['payoutByStatus'] as $row)
                                <tr>
                                    <td>{{ $row->status }}</td>
                                    <td>{{ number_format($row->total) }}</td>
                                    <td>${{ number_format((float) $row->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3"><div class="dash-empty">No payouts.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="dash-section dash-grid-2">
        <div class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Queued withdrawals</h2></div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>User</th><th>Wallet</th><th>Amount</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($reconciliation['queuedWithdrawals'] as $item)
                                <tr>
                                    <td>{{ $item->user ? '@'.$item->user->username : '—' }}</td>
                                    <td>{{ $item->wallet_type }}</td>
                                    <td>${{ number_format((float) $item->amount, 2) }}</td>
                                    <td>@if($item->user)<a href="{{ route('admin.users.show', $item->user) }}" class="dash-btn dash-btn--ghost dash-btn--sm">User</a>@endif</td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="dash-empty">No queued withdrawals.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Queued / pending engagement</h2></div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>User</th><th>Type</th><th>Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($reconciliation['queuedPayouts'] as $item)
                                <tr>
                                    <td>{{ $item->user ? '@'.$item->user->username : '—' }}</td>
                                    <td>Payout · {{ $item->level }}</td>
                                    <td>${{ number_format((float) $item->amount, 2) }}</td>
                                    <td><span class="dash-badge dash-badge--warn">{{ $item->status }}</span></td>
                                </tr>
                            @endforeach
                            @foreach ($reconciliation['pendingEngagement'] as $item)
                                <tr>
                                    <td>{{ $item->user ? '@'.$item->user->username : '—' }}</td>
                                    <td>Stat · {{ $item->level }} · {{ $item->month }}</td>
                                    <td>${{ number_format((float) $item->amount, 2) }}</td>
                                    <td><span class="dash-badge dash-badge--warn">{{ $item->status }}</span></td>
                                </tr>
                            @endforeach
                            @if ($reconciliation['queuedPayouts']->isEmpty() && $reconciliation['pendingEngagement']->isEmpty())
                                <tr><td colspan="4"><div class="dash-empty">No pending engagement payouts.</div></td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="dash-section">
        <div class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Anomalous wallets</h2></div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>User</th><th>Currency</th><th>Main</th><th>Referral</th><th>Promoter</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($anomalies as $wallet)
                                <tr>
                                    <td>
                                        {{ $wallet->user ? displayName($wallet->user->name) : '—' }}
                                        <div class="dash-muted" style="font-size:.75rem">{{ $wallet->user ? '@'.$wallet->user->username : '' }}</div>
                                    </td>
                                    <td>{{ $wallet->currency }}</td>
                                    <td class="{{ $wallet->balance < 0 ? 'text-danger' : '' }}">{{ number_format((float) $wallet->balance, 2) }}</td>
                                    <td class="{{ $wallet->referral_balance < 0 ? 'text-danger' : '' }}">{{ number_format((float) $wallet->referral_balance, 2) }}</td>
                                    <td class="{{ $wallet->promoter_balance < 0 ? 'text-danger' : '' }}">{{ number_format((float) $wallet->promoter_balance, 2) }}</td>
                                    <td>@if($wallet->user)<a href="{{ route('admin.users.show', $wallet->user) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Open</a>@endif</td>
                                </tr>
                            @empty
                                <tr><td colspan="6"><div class="dash-empty">No anomalous wallets detected.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="dash-section">
        <form method="get" class="dash-toolbar">
            <input type="search" name="q" value="{{ $search }}" class="dash-input" placeholder="Search ref, user, description">
            <select name="type" class="dash-input" style="flex:0 0 180px">
                <option value="">All types</option>
                @foreach ($transactionTypes as $option)
                    <option value="{{ $option }}" @selected($type === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <select name="status" class="dash-input" style="flex:0 0 140px">
                <option value="">All statuses</option>
                @foreach (['successful','failed','processing','cancelled','flagged','Queued','Paid','allocated'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <select name="action" class="dash-input" style="flex:0 0 120px">
                <option value="">Credit/Debit</option>
                <option value="Credit" @selected($action === 'Credit')>Credit</option>
                <option value="Debit" @selected($action === 'Debit')>Debit</option>
            </select>
            <button class="dash-btn dash-btn--primary" type="submit">Search ledger</button>
            @if ($search || $type || $status || $action)
                <a href="{{ route('admin.finance.index') }}" class="dash-btn dash-btn--ghost">Clear</a>
            @endif
        </form>

        <div class="dash-table-wrap dash-card">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Action</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $tx)
                        <tr>
                            <td style="font-family:ui-monospace,monospace;font-size:.8rem">{{ $tx->ref }}</td>
                            <td>
                                @if ($tx->user)
                                    <a href="{{ route('admin.users.show', $tx->user) }}">{{ '@'.$tx->user->username }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $tx->type }}</td>
                            <td>{{ $tx->action }}</td>
                            <td>{{ $tx->currency }} {{ number_format((float) $tx->amount, 2) }}</td>
                            <td><span class="dash-badge dash-badge--gray">{{ $tx->status }}</span></td>
                            <td class="dash-muted">{{ $tx->created_at?->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="dash-empty">No ledger rows match.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="dash-pagination">{{ $transactions->links('pagination::bootstrap-5') }}</div>
        @endif
    </section>
</div></div>
@endsection
