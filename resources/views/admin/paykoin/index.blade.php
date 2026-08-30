@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-badge--gold { background: rgba(245,158,11,.14); color: #b45309; }
        .dash-badge--pk { background: #fef3c7; color: #92400e; }
        .dash-tab-row { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; }
        .dash-tab-row .dash-tab.is-active { background: var(--dash-accent-soft); border-color: #c7d2fe; color: var(--dash-accent); }
        .dash-tab-row .dash-tab { text-decoration:none; }
        .dash-pk { font-weight:700; color:#b45309; }
        .dash-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; align-items:start; }
        @media (max-width:960px){ .dash-grid-2{grid-template-columns:1fr;} }
    </style>
@endsection

@section('content')
@php
    $queryBase = array_merge($dateRange->queryParams(), ['tab' => $tab]);
    $typeLabels = $transactionTypes;
@endphp
<div class="content p-0"><div class="dash">
    <header class="dash-header">
        <div>
            <h1>PayKoin</h1>
            <p>Platform currency — top-ups, gifts, conversions, and wallet balances · {{ $dateRange->label() }}</p>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
            <a href="{{ route('admin.finance.index') }}" class="dash-btn dash-btn--ghost">Wallets & earnings</a>
        </div>
    </header>

    @include('admin.partials.date-range-filter', ['routeName' => 'admin.paykoin.index', 'extraQuery' => ['tab' => $tab, 'q' => $search, 'type' => $type]])

    <div class="dash-tab-row">
        @foreach (['overview' => 'Overview', 'transactions' => 'Transactions', 'gifts' => 'Gifts', 'wallets' => 'Wallets'] as $tabId => $tabLabel)
            <a href="{{ route('admin.paykoin.index', array_merge($dateRange->queryParams(), ['tab' => $tabId])) }}"
               class="dash-tab @if($tab === $tabId) is-active @endif">{{ $tabLabel }}</a>
        @endforeach
    </div>

    <section class="dash-section">
        <div class="dash-grid dash-grid--4">
            <div class="dash-kpi">
                <span class="dash-kpi__label">PK in circulation</span>
                <div class="dash-kpi__value dash-pk">{{ number_format($stats['totalSpendable'] + $stats['totalEarned']) }}</div>
                <div class="dash-muted">{{ number_format($stats['walletsWithPk']) }} wallets holding PK</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">For sending gifts</span>
                <div class="dash-kpi__value">{{ number_format($stats['totalSpendable']) }} PK</div>
                <div class="dash-muted">Spendable balance</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Earned from gifts</span>
                <div class="dash-kpi__value">{{ number_format($stats['totalEarned']) }} PK</div>
                <div class="dash-muted">Convertible earnings</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Gifts (all time)</span>
                <div class="dash-kpi__value">{{ number_format($stats['totalGiftsAllTime']) }}</div>
                <div class="dash-muted">{{ number_format($stats['giftsCountInRange']) }} in {{ $dateRange->label() }}</div>
            </div>
        </div>
    </section>

    <section class="dash-section">
        <div class="dash-grid dash-grid--4">
            <div class="dash-kpi">
                <span class="dash-kpi__label">Top-ups (range)</span>
                <div class="dash-kpi__value">+{{ number_format($stats['topupsInRange']) }} PK</div>
                <div class="dash-muted">
                    @if ($stats['topupFiatNgn'] > 0) ₦{{ number_format($stats['topupFiatNgn'], 0) }} @endif
                    @if ($stats['topupFiatUsd'] > 0) ${{ number_format($stats['topupFiatUsd'], 2) }} @endif
                </div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Gifts sent (range)</span>
                <div class="dash-kpi__value">{{ number_format($stats['giftsSentInRange']) }} PK</div>
                <div class="dash-muted">{{ number_format($stats['giftsReceivedInRange']) }} PK received</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Converted (range)</span>
                <div class="dash-kpi__value">{{ number_format($stats['convertedInRange']) }} PK</div>
                <div class="dash-muted">
                    @if ($stats['convertFiatNgn'] > 0) ₦{{ number_format($stats['convertFiatNgn'], 0) }} @endif
                    @if ($stats['convertFiatUsd'] > 0) ${{ number_format($stats['convertFiatUsd'], 2) }} @endif
                </div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Rates</span>
                <div class="dash-kpi__value" style="font-size:1rem;line-height:1.4">
                    NGN ₦{{ number_format($rates['NGN']['list'] ?? 10) }}/PK<br>
                    USD ${{ number_format($rates['USD']['list'] ?? 0.10, 2) }}/PK
                </div>
                <div class="dash-muted">Convert at 75% list rate</div>
            </div>
        </div>
    </section>

    @if ($tab === 'overview')
        <section class="dash-section dash-grid-2">
            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">PayKoin volume</h2>
                    <span class="dash-muted">{{ $dateRange->label() }}</span>
                </div>
                <div class="dash-card__body">
                    <div class="dash-chart" style="height:260px">
                        <canvas id="paykoin-volume-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="dash-card">
                <div class="dash-card__head"><h2 class="dash-card__title">Activity by type</h2></div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>Type</th><th>Count</th><th>PK volume</th></tr></thead>
                            <tbody>
                                @forelse ($stats['byType'] as $row)
                                    <tr>
                                        <td>{{ $typeLabels[$row->type] ?? $row->type }}</td>
                                        <td>{{ number_format($row->total) }}</td>
                                        <td class="dash-pk">{{ number_format((int) $row->pk_volume) }} PK</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3"><div class="dash-empty">No PayKoin activity in this range.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="dash-section dash-grid-2">
            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Recent transactions</h2>
                    <a href="{{ route('admin.paykoin.index', array_merge($dateRange->queryParams(), ['tab' => 'transactions'])) }}" class="dash-link">View all</a>
                </div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>User</th><th>Type</th><th>PK</th><th>When</th></tr></thead>
                            <tbody>
                                @forelse ($stats['recentTransactions'] as $tx)
                                    <tr>
                                        <td>
                                            @if ($tx->user)
                                                <a href="{{ route('admin.users.show', $tx->user) }}">@{{ $tx->user->username }}</a>
                                            @else — @endif
                                        </td>
                                        <td><span class="dash-badge dash-badge--pk">{{ $typeLabels[$tx->type] ?? $tx->type }}</span></td>
                                        <td class="{{ $tx->pk_amount >= 0 ? 'dash-pk' : '' }}">{{ $tx->pk_amount >= 0 ? '+' : '' }}{{ number_format($tx->pk_amount) }}</td>
                                        <td>{{ $tx->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4"><div class="dash-empty">No transactions yet.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Recent gifts</h2>
                    <a href="{{ route('admin.paykoin.index', array_merge($dateRange->queryParams(), ['tab' => 'gifts'])) }}" class="dash-link">View all</a>
                </div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>From → To</th><th>Gift</th><th>PK</th><th>When</th></tr></thead>
                            <tbody>
                                @forelse ($stats['recentGifts'] as $gift)
                                    <tr>
                                        <td>
                                            @if ($gift->sender && $gift->recipient)
                                                @{{ $gift->sender->username }} → @{{ $gift->recipient->username }}
                                            @else — @endif
                                        </td>
                                        <td>{{ $artifactLabel($gift->artifact_id) }}</td>
                                        <td class="dash-pk">{{ number_format($gift->pk_amount) }}</td>
                                        <td>{{ $gift->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4"><div class="dash-empty">No gifts yet.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        @if ($stats['giftsByArtifact']->isNotEmpty())
            <section class="dash-section dash-card">
                <div class="dash-card__head"><h2 class="dash-card__title">Popular gifts · {{ $dateRange->label() }}</h2></div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>Artifact</th><th>Sent</th><th>PK volume</th></tr></thead>
                            <tbody>
                                @foreach ($stats['giftsByArtifact'] as $row)
                                    <tr>
                                        <td>{{ $artifactLabel($row->artifact_id) }}</td>
                                        <td>{{ number_format($row->total) }}</td>
                                        <td class="dash-pk">{{ number_format((int) $row->pk_volume) }} PK</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @endif
    @endif

    @if ($tab === 'transactions')
        <section class="dash-section dash-card">
            <div class="dash-card__head">
                <h2 class="dash-card__title">Transaction ledger</h2>
                <form method="get" action="{{ route('admin.paykoin.index') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
                    @foreach ($dateRange->queryParams() as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="tab" value="transactions">
                    <input type="search" name="q" value="{{ $search }}" placeholder="Ref, user, email…" class="adm-field" style="min-width:180px">
                    <select name="type" class="adm-field">
                        <option value="">All types</option>
                        @foreach ($transactionTypes as $typeId => $typeLabel)
                            <option value="{{ $typeId }}" @selected($type === $typeId)>{{ $typeLabel }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="dash-btn dash-btn--ghost">Filter</button>
                </form>
            </div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>PK</th>
                                <th>Fiat</th>
                                <th>Ref</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $tx)
                                <tr>
                                    <td>{{ $tx->created_at->format('M j, Y H:i') }}</td>
                                    <td>
                                        @if ($tx->user)
                                            <a href="{{ route('admin.users.show', $tx->user) }}">@{{ $tx->user->username }}</a>
                                        @else — @endif
                                    </td>
                                    <td><span class="dash-badge dash-badge--pk">{{ $typeLabels[$tx->type] ?? $tx->type }}</span></td>
                                    <td class="{{ $tx->pk_amount >= 0 ? 'dash-pk' : '' }}">{{ $tx->pk_amount >= 0 ? '+' : '' }}{{ number_format($tx->pk_amount) }}</td>
                                    <td>
                                        @if ($tx->fiat_amount)
                                            {{ $tx->currency === 'NGN' ? '₦' : '$' }}{{ number_format((float) $tx->fiat_amount, $tx->currency === 'NGN' ? 0 : 2) }}
                                        @else — @endif
                                    </td>
                                    <td><code style="font-size:.75rem">{{ $tx->ref ?: '—' }}</code></td>
                                    <td>{{ $tx->description ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7"><div class="dash-empty">No transactions match your filters.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())
                    <div class="dash-card__body">{{ $transactions->links() }}</div>
                @endif
            </div>
        </section>
    @endif

    @if ($tab === 'gifts')
        <section class="dash-section dash-card">
            <div class="dash-card__head">
                <h2 class="dash-card__title">Post gifts</h2>
                <form method="get" action="{{ route('admin.paykoin.index') }}" style="display:flex;gap:.5rem;flex-wrap:wrap">
                    @foreach ($dateRange->queryParams() as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="tab" value="gifts">
                    <input type="search" name="q" value="{{ $search }}" placeholder="Ref, username, artifact…" class="adm-field" style="min-width:200px">
                    <button type="submit" class="dash-btn dash-btn--ghost">Search</button>
                </form>
            </div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sender</th>
                                <th>Recipient</th>
                                <th>Gift</th>
                                <th>PK</th>
                                <th>Target</th>
                                <th>Ref</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gifts as $gift)
                                <tr>
                                    <td>{{ $gift->created_at->format('M j, Y H:i') }}</td>
                                    <td>
                                        @if ($gift->sender)
                                            <a href="{{ route('admin.users.show', $gift->sender) }}">@{{ $gift->sender->username }}</a>
                                        @else — @endif
                                    </td>
                                    <td>
                                        @if ($gift->recipient)
                                            <a href="{{ route('admin.users.show', $gift->recipient) }}">@{{ $gift->recipient->username }}</a>
                                        @else — @endif
                                    </td>
                                    <td>{{ $artifactLabel($gift->artifact_id) }}</td>
                                    <td class="dash-pk">{{ number_format($gift->pk_amount) }}</td>
                                    <td>
                                        @php
                                            $target = class_basename($gift->giftable_type ?? '');
                                        @endphp
                                        {{ $target ?: '—' }}
                                        @if ($gift->giftable_id)
                                            <br><small class="dash-muted">{{ Str::limit($gift->giftable_id, 12) }}</small>
                                        @endif
                                    </td>
                                    <td><code style="font-size:.75rem">{{ $gift->ref }}</code></td>
                                </tr>
                            @empty
                                <tr><td colspan="7"><div class="dash-empty">No gifts in this range.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($gifts->hasPages())
                    <div class="dash-card__body">{{ $gifts->links() }}</div>
                @endif
            </div>
        </section>
    @endif

    @if ($tab === 'wallets')
        <section class="dash-section dash-card">
            <div class="dash-card__head">
                <h2 class="dash-card__title">Top PayKoin wallets</h2>
                <span class="dash-muted">By total PK (spendable + earned)</span>
            </div>
            <div class="dash-card__body dash-card__body--flush">
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Currency</th>
                                <th>Spendable</th>
                                <th>Earned</th>
                                <th>Total PK</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topWallets as $wallet)
                                <tr>
                                    <td>
                                        @if ($wallet->user)
                                            <strong>{{ $wallet->user->name }}</strong><br>
                                            <span class="dash-muted">@{{ $wallet->user->username }}</span>
                                        @else — @endif
                                    </td>
                                    <td>{{ $wallet->currency ?: '—' }}</td>
                                    <td>{{ number_format((int) $wallet->paykoin_spendable) }} PK</td>
                                    <td>{{ number_format((int) $wallet->paykoin_earned) }} PK</td>
                                    <td class="dash-pk"><strong>{{ number_format((int) $wallet->paykoin_spendable + (int) $wallet->paykoin_earned) }} PK</strong></td>
                                    <td>
                                        @if ($wallet->user)
                                            <a href="{{ route('admin.users.show', $wallet->user) }}" class="dash-btn dash-btn--ghost" style="font-size:.78rem;padding:.35rem .65rem">Profile</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6"><div class="dash-empty">No wallets with PayKoin yet.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif
</div></div>
@endsection

@if ($tab === 'overview')
@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
    const canvas = document.getElementById('paykoin-volume-chart');
    if (!canvas) return;

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: @json($stats['volumeChart']['labels']),
            datasets: [
                {
                    label: 'Top-ups',
                    data: @json($stats['volumeChart']['topups']),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,.08)',
                    tension: .35,
                    fill: true,
                },
                {
                    label: 'Gifts sent',
                    data: @json($stats['volumeChart']['gifts']),
                    borderColor: '#d97706',
                    backgroundColor: 'rgba(217,119,6,.08)',
                    tension: .35,
                    fill: true,
                },
                {
                    label: 'Converted',
                    data: @json($stats['volumeChart']['converts']),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5,150,105,.08)',
                    tension: .35,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
            },
        },
    });
})();
</script>
@endsection
@endif
