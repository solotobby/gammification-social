{{-- <div> --}}
{{-- In work, do what you enjoy. --}}
{{-- <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Transaction List
                </h3>
            </div>
            <div class="block-content block-content-full">


                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($txList as $list)
                            <tr>
                                <td >{{ $list->ref }}</td>
                                <td >{{ $list->amount }}</td>
                                <td >{{ $list->currency }}</td>
                                <td >{{ $list->description }}</td>
                                <td >{{ $list->created_at }}</td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div> --}}




{{-- </div> --}}


<div>
    {{-- Water benefits all things without contending. --}}

    <div class="pk-tx-page">

        @verbatim
            <style>
                .pk-tx-page {
                    --pk-violet: #5A4FDC;
                    --pk-violet-dark: #4B41C4;
                    --pk-violet-tint: #EEECFC;
                    --pk-mint: #1FAE64;
                    --pk-mint-tint: #E6F7EE;
                    --pk-mint-line: #CBEBDA;
                    --pk-gold: #E3A421;
                    --pk-gold-tint: #FCF1DA;
                    --pk-red: #EF4444;
                    --pk-red-tint: #FDECEC;
                    --pk-ink: #171B24;
                    --pk-gray-700: #4B5163;
                    --pk-gray-500: #8A8FA3;
                    --pk-gray-400: #AEB2C2;
                    --pk-line: #E7E8F0;
                    --pk-line-strong: #DADCE9;
                    --pk-r-sm: 8px;
                    --pk-r-md: 12px;
                    --pk-r-lg: 14px;
                    --pk-r-pill: 999px;
                    --pk-shadow: 0 1px 2px rgba(23, 27, 36, .04);
                    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
                    color: var(--pk-ink);
                }

                .pk-tx-page * {
                    box-sizing: border-box
                }

                .pk-tx-page .pk-card {
                    background: #fff;
                    border: 1px solid var(--pk-line);
                    border-radius: var(--pk-r-lg);
                    box-shadow: var(--pk-shadow)
                }

                /* ---- header ---- */
                .pk-tx-page .pk-tx-head {
                    display: flex;
                    align-items: flex-end;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-bottom: 16px
                }

                .pk-tx-page .pk-tx-head h2 {
                    font-size: 1.2rem;
                    font-weight: 800;
                    margin: 0
                }

                .pk-tx-page .pk-tx-head p {
                    font-size: .84rem;
                    color: var(--pk-gray-500);
                    margin: 2px 0 0
                }

                /* ---- stat chips ---- */
                .pk-tx-page .pk-stats-row {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 12px;
                    margin-bottom: 16px
                }

                .pk-tx-page .pk-stat {
                    padding: 14px 16px
                }

                .pk-tx-page .pk-stat .pk-stat-n {
                    font-size: 1.3rem;
                    font-weight: 800;
                    font-family: 'Space Mono', ui-monospace, monospace
                }

                .pk-tx-page .pk-stat .pk-stat-l {
                    font-size: .76rem;
                    color: var(--pk-gray-500);
                    font-weight: 600;
                    margin-top: 2px;
                    display: flex;
                    align-items: center;
                    gap: 6px
                }

                .pk-tx-page .pk-stat .pk-dot {
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    flex: none
                }

                @media (max-width: 767.98px) {
                    .pk-tx-page .pk-stats-row {
                        grid-template-columns: repeat(2, 1fr)
                    }
                }

                /* ---- filters ---- */
                .pk-tx-page .pk-filters {
                    padding: 14px 16px;
                    margin-bottom: 16px;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    align-items: center
                }

                .pk-tx-page .pk-search-row {
                    display: flex;
                    align-items: center;
                    gap: 9px;
                    background: #F4F5F9;
                    border-radius: var(--pk-r-pill);
                    padding: 9px 16px;
                    color: var(--pk-gray-500);
                    flex: 1 1 220px;
                    min-width: 180px
                }

                .pk-tx-page .pk-search-row svg {
                    width: 16px;
                    height: 16px;
                    flex: none
                }

                .pk-tx-page .pk-search-row input {
                    border: none;
                    outline: none;
                    font-family: inherit;
                    font-size: .86rem;
                    width: 100%;
                    background: none;
                    color: var(--pk-ink)
                }

                .pk-tx-page .pk-filters select,
                .pk-tx-page .pk-filters input[type=date] {
                    border: 1.3px solid var(--pk-line-strong);
                    border-radius: var(--pk-r-sm);
                    padding: 9px 12px;
                    font-family: inherit;
                    font-size: .84rem;
                    color: var(--pk-ink);
                    outline: none;
                    background: #F7F7FB;
                    transition: .15s
                }

                .pk-tx-page .pk-filters select:focus,
                .pk-tx-page .pk-filters input[type=date]:focus {
                    border-color: var(--pk-violet);
                    background: #fff
                }

                .pk-tx-page .pk-filters select {
                    appearance: none;
                    padding-right: 30px;
                    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%235A4FDC" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>');
                    background-repeat: no-repeat;
                    background-position: right 10px center;
                    background-size: 13px
                }

                .pk-tx-page .pk-date-sep {
                    color: var(--pk-gray-400);
                    font-size: .8rem;
                    flex: none
                }

                .pk-tx-page .pk-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 7px;
                    padding: 9px 14px;
                    border-radius: var(--pk-r-md);
                    font-weight: 700;
                    font-size: .82rem;
                    border: none;
                    cursor: pointer;
                    font-family: inherit;
                    white-space: nowrap;
                    transition: .15s
                }

                .pk-tx-page .pk-btn svg {
                    width: 14px;
                    height: 14px
                }

                .pk-tx-page .pk-btn-outline {
                    background: #fff;
                    border: 1.3px solid var(--pk-line-strong);
                    color: var(--pk-gray-700)
                }

                .pk-tx-page .pk-btn-outline:hover {
                    border-color: var(--pk-violet);
                    color: var(--pk-violet)
                }

                /* ---- table ---- */
                .pk-tx-page .pk-table-card {
                    padding: 0;
                    overflow: hidden;
                    position: relative
                }

                .pk-tx-page .pk-table-scroll {
                    overflow-x: auto
                }

                .pk-tx-page table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: .86rem;
                    min-width: 640px
                }

                .pk-tx-page thead th {
                    text-align: left;
                    font-size: .74rem;
                    text-transform: uppercase;
                    letter-spacing: .03em;
                    color: var(--pk-gray-500);
                    font-weight: 700;
                    padding: 13px 18px;
                    border-bottom: 1px solid var(--pk-line);
                    background: #FAFAFC;
                    white-space: nowrap
                }

                .pk-tx-page thead th.pk-sortable {
                    cursor: pointer;
                    user-select: none
                }

                .pk-tx-page thead th.pk-sortable:hover {
                    color: var(--pk-violet)
                }

                .pk-tx-page thead th .pk-sort-ic {
                    display: inline-flex;
                    vertical-align: -2px;
                    margin-left: 4px;
                    width: 11px;
                    height: 11px;
                    opacity: .5
                }

                .pk-tx-page thead th.pk-sort-active .pk-sort-ic {
                    opacity: 1;
                    color: var(--pk-violet)
                }

                .pk-tx-page tbody td {
                    padding: 13px 18px;
                    border-bottom: 1px solid var(--pk-line);
                    color: var(--pk-gray-700);
                    vertical-align: middle
                }

                .pk-tx-page tbody tr:last-child td {
                    border-bottom: none
                }

                .pk-tx-page tbody tr:hover {
                    background: #FAFAFC
                }

                .pk-tx-page .pk-ref {
                    font-family: 'Space Mono', ui-monospace, monospace;
                    font-size: .78rem;
                    color: var(--pk-ink)
                }

                .pk-tx-page .pk-amt {
                    font-family: 'Space Mono', ui-monospace, monospace;
                    font-weight: 700;
                    color: var(--pk-ink);
                    white-space: nowrap
                }

                .pk-tx-page .pk-desc {
                    max-width: 240px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap
                }

                .pk-tx-page .pk-date {
                    font-size: .8rem;
                    color: var(--pk-gray-500);
                    white-space: nowrap
                }

                .pk-tx-page .pk-status-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    font-size: .72rem;
                    font-weight: 700;
                    padding: 5px 10px;
                    border-radius: var(--pk-r-pill);
                    white-space: nowrap
                }

                .pk-tx-page .pk-status-pill svg {
                    width: 10px;
                    height: 10px
                }

                .pk-tx-page .pk-status-paid {
                    background: var(--pk-mint-tint);
                    color: #0D7A45
                }

                .pk-tx-page .pk-status-approval {
                    background: var(--pk-gold-tint);
                    color: #946409
                }

                .pk-tx-page .pk-status-failed {
                    background: var(--pk-red-tint);
                    color: #B91C1C
                }

                .pk-tx-page .pk-status-private {
                    background: #EEF0F4;
                    color: var(--pk-gray-700)
                }

                /* ---- responsive stacked rows on mobile ---- */
                @media (max-width: 767.98px) {
                    .pk-tx-page .pk-table-scroll {
                        overflow-x: visible
                    }

                    .pk-tx-page table,
                    .pk-tx-page tbody,
                    .pk-tx-page tr,
                    .pk-tx-page td {
                        display: block;
                        width: 100%
                    }

                    .pk-tx-page thead {
                        display: none
                    }

                    .pk-tx-page tbody tr {
                        border-bottom: 1px solid var(--pk-line);
                        padding: 12px 16px
                    }

                    .pk-tx-page tbody td {
                        border-bottom: none;
                        padding: 5px 0;
                        display: flex;
                        justify-content: space-between;
                        gap: 10px;
                        text-align: right
                    }

                    .pk-tx-page tbody td::before {
                        content: attr(data-label);
                        font-size: .72rem;
                        font-weight: 700;
                        color: var(--pk-gray-500);
                        text-align: left
                    }

                    .pk-tx-page .pk-desc {
                        max-width: 60%;
                        text-align: right
                    }
                }

                /* ---- empty state ---- */
                .pk-tx-page .pk-empty {
                    text-align: center;
                    padding: 50px 20px;
                    color: var(--pk-gray-500)
                }

                .pk-tx-page .pk-empty svg {
                    width: 36px;
                    height: 36px;
                    color: var(--pk-line-strong);
                    margin: 0 auto 12px
                }

                .pk-tx-page .pk-empty b {
                    display: block;
                    color: var(--pk-ink);
                    font-size: .94rem;
                    margin-bottom: 3px
                }

                /* ---- loading overlay ---- */
                .pk-tx-page .pk-loading-overlay {
                    position: absolute;
                    inset: 0;
                    background: rgba(255, 255, 255, .6);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 5
                }

                .pk-tx-page .pk-spinner {
                    width: 26px;
                    height: 26px;
                    border-radius: 50%;
                    border: 3px solid var(--pk-violet-tint);
                    border-top-color: var(--pk-violet);
                    animation: pk-spin .7s linear infinite
                }

                @keyframes pk-spin {
                    to {
                        transform: rotate(360deg)
                    }
                }

                /* ---- pagination ---- */
                .pk-tx-page .pk-pagination {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 10px;
                    padding: 14px 18px;
                    border-top: 1px solid var(--pk-line)
                }

                .pk-tx-page .pk-pg-info {
                    font-size: .8rem;
                    color: var(--pk-gray-500)
                }

                .pk-tx-page .pk-pg-btns {
                    display: flex;
                    align-items: center;
                    gap: 6px
                }

                .pk-tx-page .pk-pg-btn {
                    width: 32px;
                    height: 32px;
                    border-radius: var(--pk-r-sm);
                    border: 1.3px solid var(--pk-line-strong);
                    background: #fff;
                    color: var(--pk-gray-700);
                    display: grid;
                    place-items: center;
                    cursor: pointer;
                    font-size: .8rem;
                    font-weight: 700
                }

                .pk-tx-page .pk-pg-btn svg {
                    width: 14px;
                    height: 14px
                }

                .pk-tx-page .pk-pg-btn:hover:not(:disabled) {
                    border-color: var(--pk-violet);
                    color: var(--pk-violet)
                }

                .pk-tx-page .pk-pg-btn:disabled {
                    opacity: .4;
                    cursor: not-allowed
                }

                .pk-tx-page .pk-pg-btn.pk-pg-active {
                    background: var(--pk-violet);
                    border-color: var(--pk-violet);
                    color: #fff
                }
            </style>
        @endverbatim

        {{-- ============ HEADER ============ --}}
        <div class="pk-tx-head">
            <div>
                <h2>Transaction history</h2>
                <p>{{ number_format($transactions->total()) }} transaction{{ $transactions->total() === 1 ? '' : 's' }}
                    in total</p>
            </div>
        </div>

        {{-- ============ STAT CHIPS ============ --}}
        <div class="pk-stats-row">
            <div class="pk-card pk-stat">
                <div class="pk-stat-n">{{ number_format($stats->sum()) }}</div>
                <div class="pk-stat-l"><span class="pk-dot" style="background:var(--pk-gray-400)"></span>All</div>
            </div>
            <div class="pk-card pk-stat">
                <div class="pk-stat-n">{{ number_format($stats->get('successful', 0)) }}</div>
                <div class="pk-stat-l"><span class="pk-dot" style="background:var(--pk-mint)"></span>Successful</div>
            </div>
            <div class="pk-card pk-stat">
                <div class="pk-stat-n">{{ number_format($stats->get('pending', 0)) }}</div>
                <div class="pk-stat-l"><span class="pk-dot" style="background:var(--pk-gold)"></span>Pending</div>
            </div>
            <div class="pk-card pk-stat">
                <div class="pk-stat-n">{{ number_format($stats->get('failed', 0)) }}</div>
                <div class="pk-stat-l"><span class="pk-dot" style="background:var(--pk-red)"></span>Failed</div>
            </div>
        </div>

        {{-- ============ FILTERS ============ --}}
        <div class="pk-card pk-filters">
            <div class="pk-search-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
                <input type="text" wire:model.live.debounce.350ms="search"
                    placeholder="Search reference, description, currency…">
            </div>

            <select wire:model.live="statusFilter">
                <option value="">All statuses</option>
                <option value="successful">Successful</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
            </select>

            <input type="date" wire:model.live="dateFrom">
            <span class="pk-date-sep">to</span>
            <input type="date" wire:model.live="dateTo">

            <select wire:model.live="perPage" style="width:auto">
                <option value="10">10 / page</option>
                <option value="15">15 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>

            @if ($search !== '' || $statusFilter !== '' || $dateFrom !== '' || $dateTo !== '')
                <button type="button" class="pk-btn pk-btn-outline" wire:click="clearFilters">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" />
                    </svg>
                    Clear
                </button>
            @endif
        </div>

        {{-- ============ TABLE ============ --}}
        <div class="pk-card pk-table-card">

            <div wire:loading.flex
                wire:target="search,statusFilter,dateFrom,dateTo,perPage,sortBy,previousPage,nextPage,gotoPage"
                class="pk-loading-overlay" style="display:none">
                <div class="pk-spinner"></div>
            </div>

            <div class="pk-table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th class="pk-sortable @if ($sortField === 'amount') pk-sort-active @endif"
                                wire:click="sortBy('amount')">
                                Amount
                                <svg class="pk-sort-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3">
                                    @if ($sortField === 'amount' && $sortDirection === 'asc')
                                        <path d="m6 15 6-6 6 6" />
                                    @else
                                        <path d="m6 9 6 6 6-6" />
                                    @endif
                                </svg>
                            </th>
                            <th>Currency</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="pk-sortable @if ($sortField === 'created_at') pk-sort-active @endif"
                                wire:click="sortBy('created_at')">
                                Date
                                <svg class="pk-sort-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3">
                                    @if ($sortField === 'created_at' && $sortDirection === 'asc')
                                        <path d="m6 15 6-6 6 6" />
                                    @else
                                        <path d="m6 9 6 6 6-6" />
                                    @endif
                                </svg>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $tx)
                            @php
                                $meta = $this->statusMeta($tx->status);
                            @endphp
                            <tr wire:key="tx-{{ $tx->id }}">
                                <td data-label="Reference"><span class="pk-ref">{{ $tx->ref }}</span></td>
                                <td data-label="Amount">
                                    <span
                                        class="pk-amt">{{ $this->currencySymbol($tx->currency) }}{{ number_format((float) $tx->amount, 2) }}</span>
                                </td>
                                <td data-label="Currency">{{ strtoupper($tx->currency) }}</td>
                                <td data-label="Description"><span class="pk-desc"
                                        title="{{ $tx->description }}">{{ $tx->description ?: '—' }}</span></td>
                                <td data-label="Status">
                                    <span class="pk-status-pill {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                                </td>
                                <td data-label="Date">
                                    <span class="pk-date" title="{{ $tx->created_at->format('M d, Y · h:ia') }}">
                                        {{ $tx->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="border:none">
                                    <div class="pk-empty">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.6">
                                            <rect x="3" y="5" width="18" height="14" rx="2" />
                                            <path d="M3 10h18M7 15h4" />
                                        </svg>
                                        <b>No transactions found</b>
                                        {{ $search !== '' || $statusFilter !== '' || $dateFrom !== '' || $dateTo !== ''
                                            ? 'Try adjusting your filters or search term.'
                                            : 'Your transactions will show up here once you have some.' }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                @php
                    $current = $transactions->currentPage();
                    $last = $transactions->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp
                <div class="pk-pagination">
                    <div class="pk-pg-info">
                        @if ($transactions->total() > 0)
                            Showing {{ number_format($transactions->firstItem()) }}–{{ number_format($transactions->lastItem()) }}
                            of {{ number_format($transactions->total()) }}
                        @else
                            No results
                        @endif
                    </div>
                    <div class="pk-pg-btns">
                        <button type="button" class="pk-pg-btn" wire:click="previousPage"
                            wire:loading.attr="disabled" @disabled($transactions->onFirstPage())
                            aria-label="Previous page">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        @if ($start > 1)
                            <button type="button" class="pk-pg-btn" wire:click="gotoPage(1)">1</button>
                            @if ($start > 2)
                                <span class="pk-pg-info" style="padding:0 2px">…</span>
                            @endif
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            <button type="button"
                                class="pk-pg-btn {{ $page === $current ? 'pk-pg-active' : '' }}"
                                wire:click="gotoPage({{ $page }})"
                                wire:loading.attr="disabled"
                                aria-label="Page {{ $page }}"
                                @if ($page === $current) aria-current="page" @endif>
                                {{ $page }}
                            </button>
                        @endfor

                        @if ($end < $last)
                            @if ($end < $last - 1)
                                <span class="pk-pg-info" style="padding:0 2px">…</span>
                            @endif
                            <button type="button" class="pk-pg-btn" wire:click="gotoPage({{ $last }})">{{ $last }}</button>
                        @endif

                        <button type="button" class="pk-pg-btn" wire:click="nextPage"
                            wire:loading.attr="disabled" @disabled(! $transactions->hasMorePages())
                            aria-label="Next page">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @elseif ($transactions->total() > 0)
                <div class="pk-pagination">
                    <div class="pk-pg-info">
                        Showing {{ number_format($transactions->total()) }}
                        transaction{{ $transactions->total() === 1 ? '' : 's' }}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
