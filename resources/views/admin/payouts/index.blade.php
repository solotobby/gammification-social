@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .dash-grid--5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }

        .dash-kpi {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1.25rem;
            background: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            box-shadow: var(--dash-shadow);
            height: 100%;
        }

        .dash-kpi__label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dash-muted);
        }

        .dash-kpi__value {
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .dash-num { font-weight: 600; font-variant-numeric: tabular-nums; }
        .dash-badge--amber { background: #fffbeb; color: #b45309; }
        .dash-badge--green { background: #ecfdf5; color: #047857; }
        .dash-badge--violet { background: #f5f3ff; color: #6d28d9; }

        .payout-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            align-items: center;
        }

        .payout-actions .dash-btn {
            padding: .4rem .55rem;
            font-size: .72rem;
        }

        .payout-total {
            font-weight: 700;
            color: #047857;
        }

        .payout-component-list {
            margin: .35rem 0 0;
            padding: 0;
            list-style: none;
            font-size: .75rem;
            color: var(--dash-muted);
        }

        .payout-component-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-top: .2rem;
        }

        /* Payout sheets — custom overlay (avoids Bootstrap modal/footer conflicts) */
        .payout-sheet {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 10050;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(15, 23, 42, .55);
            backdrop-filter: blur(4px);
        }

        .payout-sheet.is-open {
            display: flex;
        }

        .payout-sheet__panel {
            width: 100%;
            max-width: 440px;
            max-height: min(92vh, 640px);
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 24px 64px rgba(15, 23, 42, .28);
            overflow: hidden;
        }

        .payout-sheet__head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .payout-sheet__title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -.02em;
            color: #0f172a;
        }

        .payout-sheet__subtitle {
            margin: .25rem 0 0;
            font-size: .8125rem;
            color: #64748b;
        }

        .payout-sheet__close {
            flex-shrink: 0;
            width: 2rem;
            height: 2rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            color: #64748b;
            font-size: 1.25rem;
            line-height: 1;
            cursor: pointer;
        }

        .payout-sheet__close:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .payout-sheet__form {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
        }

        .payout-sheet__body {
            padding: 1.25rem;
            overflow-y: auto;
            flex: 1;
        }

        .payout-sheet__foot {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: .5rem;
            padding: 1rem 1.25rem;
            border-top: 1px solid #e2e8f0;
            background: #fff;
            flex-shrink: 0;
        }

        .payout-sheet__foot--split {
            justify-content: space-between;
        }

        .payout-sheet__foot-group {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .payout-sheet .dash-field label {
            display: block;
            font-size: .8125rem;
            font-weight: 600;
            margin-bottom: .35rem;
            color: #334155;
        }

        .payout-sheet .dash-field {
            margin-bottom: 1rem;
        }

        .payout-sheet .dash-field:last-child {
            margin-bottom: 0;
        }

        .payout-sheet .dash-input {
            flex: none;
            width: 100%;
            min-width: 0;
            display: block;
            box-sizing: border-box;
        }

        .payout-sheet__hint {
            display: block;
            margin-top: .35rem;
            font-size: .75rem;
            color: #64748b;
        }

        .payout-sheet__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            padding: .65rem 1.1rem;
            border-radius: 10px;
            font-size: .875rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            line-height: 1.2;
        }

        .payout-sheet__btn--primary {
            background: #6366f1;
            color: #fff !important;
            border-color: #6366f1;
        }

        .payout-sheet__btn--primary:hover {
            background: #4f46e5;
            border-color: #4f46e5;
        }

        .payout-sheet__btn--ghost {
            background: #fff;
            color: #334155;
            border-color: #e2e8f0;
        }

        .payout-sheet__btn--ghost:hover {
            background: #f8fafc;
        }

        .payout-sheet__btn--danger {
            background: #fff;
            color: #b42318;
            border-color: #fecaca;
        }

        .payout-sheet__btn--danger:hover {
            background: #fef2f2;
        }

        @media (max-width: 1200px) {
            .dash-grid--4, .dash-grid--5 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .dash-grid--4, .dash-grid--5 { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    @php
        $tabs = $levelTabs ?? ['Influencer', 'Creator', 'Basic'];
        $currentLevel = $level ?? 'Influencer';
        $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $lastmonth ?? now()->subMonth()->format('Y-m'))->format('F Y');
        $analytics = $componentAnalytics ?? [];
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Level payouts</h1>
                    <p>Pro-rata distribution · {{ $monthLabel }}</p>
                </div>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap">
                    <a href="{{ route('admin.audit-logs.index', ['action' => 'payout.']) }}" class="dash-btn dash-btn--ghost">Payout audit log</a>
                    <a href="{{ route('admin.payouts.pro-rata') }}" class="dash-btn dash-btn--ghost">
                        <i class="fa fa-arrow-left"></i> Pro-rata overview
                    </a>
                </div>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <div class="dash-tabs">
                @foreach ($tabs as $tab)
                    <a href="{{ route('admin.payouts.levels.show', $tab) }}"
                        class="dash-tab {{ $currentLevel === $tab ? 'is-active' : '' }}">
                        {{ $tab }}
                    </a>
                @endforeach
            </div>

            @if (($status ?? '') === 'error')
                <div class="dash-alert dash-alert--error">{{ $message ?? 'Unable to load payout data.' }}</div>
                @if ($currentLevel !== 'Basic')
                    <p class="dash-muted">
                        Run engagement processing from the
                        <a href="{{ route('admin.payouts.monthly.users', $currentLevel) }}" class="dash-link">monthly breakdown</a>
                        page first.
                    </p>
                @endif
            @else
                <section class="dash-section">
                    <div class="dash-grid dash-grid--5">
                        <div class="dash-kpi">
                            <span class="dash-kpi__label">Level</span>
                            <div class="dash-kpi__value">{{ $currentLevel }}</div>
                        </div>
                        <div class="dash-kpi">
                            <span class="dash-kpi__label">Members</span>
                            <div class="dash-kpi__value">{{ number_format($memberCount ?? 0) }}</div>
                        </div>
                        <div class="dash-kpi">
                            <span class="dash-kpi__label">Total engagement</span>
                            <div class="dash-kpi__value">{{ number_format($totalEngagement ?? 0) }}</div>
                        </div>
                        <div class="dash-kpi">
                            <span class="dash-kpi__label">{{ $poolLabel ?? 'Level pool' }}</span>
                            <div class="dash-kpi__value">₦{{ number_format(convertToBaseCurrency($levelPool ?? 0, 'NGN'), 2) }}</div>
                        </div>
                        <div class="dash-kpi">
                            <span class="dash-kpi__label">Manual adjustments</span>
                            <div class="dash-kpi__value" style="font-size:1rem;line-height:1.35">
                                ₦{{ number_format(($analytics['revenue_total'] ?? 0) + ($analytics['bonus_total'] ?? 0), 0) }}
                            </div>
                            <div class="dash-muted">
                                {{ number_format($analytics['members_with_adjustments'] ?? 0) }} member(s) ·
                                {{ number_format(($analytics['revenue_count'] ?? 0) + ($analytics['bonus_count'] ?? 0)) }} line(s)
                            </div>
                        </div>
                    </div>
                </section>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <div>
                            <h2 class="dash-card__title">{{ $currentLevel }} member payouts</h2>
                            <p class="dash-muted" style="margin:0.25rem 0 0;">
                                Add revenue or bonus payouts before queueing · recorded for audit & analytics
                            </p>
                        </div>
                        @if ($currentLevel !== 'Basic')
                            <a href="{{ route('admin.payouts.monthly.users', $currentLevel) }}" class="dash-link">Monthly breakdown</a>
                        @endif
                    </div>

                    <div class="dash-card__body--flush">
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Engagement</th>
                                        <th>Share</th>
                                        <th>Eng. payout</th>
                                        <th>Revenue</th>
                                        <th>Bonus</th>
                                        <th>Total (NGN)</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payouts ?? [] as $user)
                                        @php
                                            $payoutStatus = $user['status'] ?? 'Pending';
                                            $statusClass = $payoutStatus === 'Paid' ? 'dash-badge--gray' : ($payoutStatus === 'Queued' ? 'dash-badge--amber' : 'dash-badge--indigo');
                                            $rowId = $user['id'];
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $user['name'] ?? 'N/A' }}</strong>
                                                <div class="dash-muted" style="font-size:.75rem">{{ $user['email'] ?? '' }}</div>
                                            </td>
                                            <td class="dash-num">{{ number_format($user['engagement'] ?? 0) }}</td>
                                            <td><span class="dash-badge dash-badge--indigo">{{ $user['userPercentage'] ?? 0 }}%</span></td>
                                            <td class="dash-num">
                                                ₦{{ number_format(convertToBaseCurrency($user['userPayout'] ?? 0, 'NGN'), 2) }}
                                            </td>
                                            <td class="dash-num">
                                                ₦{{ number_format($user['revenuePayout'] ?? 0, 2) }}
                                                @if (! empty($user['components']))
                                                    <ul class="payout-component-list">
                                                        @foreach (collect($user['components'])->where('type', 'revenue') as $comp)
                                                            <li>
                                                                <span>₦{{ number_format($comp['amount'], 2) }}</span>
                                                                @if ($payoutStatus === 'Pending')
                                                                    <button type="button" class="dash-link js-payout-edit-component"
                                                                        data-component-id="{{ $comp['id'] }}"
                                                                        data-amount="{{ $comp['amount'] }}"
                                                                        data-note="{{ e($comp['note'] ?? '') }}"
                                                                        data-member="{{ e($user['name']) }}">
                                                                        Edit
                                                                    </button>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                            <td class="dash-num">
                                                ₦{{ number_format($user['bonusPayout'] ?? 0, 2) }}
                                                @if (! empty($user['components']))
                                                    <ul class="payout-component-list">
                                                        @foreach (collect($user['components'])->where('type', 'bonus') as $comp)
                                                            <li>
                                                                <span>₦{{ number_format($comp['amount'], 2) }}</span>
                                                                @if ($payoutStatus === 'Pending')
                                                                    <button type="button" class="dash-link js-payout-edit-component"
                                                                        data-component-id="{{ $comp['id'] }}"
                                                                        data-amount="{{ $comp['amount'] }}"
                                                                        data-note="{{ e($comp['note'] ?? '') }}"
                                                                        data-member="{{ e($user['name']) }}">
                                                                        Edit
                                                                    </button>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                            <td class="dash-num payout-total">₦{{ number_format($user['totalPayoutNgn'] ?? 0, 2) }}</td>
                                            <td><span class="dash-badge {{ $statusClass }}">{{ $payoutStatus }}</span></td>
                                            <td>
                                                @if ($payoutStatus === 'Pending')
                                                    <div class="payout-actions">
                                                        <button type="button" class="dash-btn dash-btn--ghost js-payout-add"
                                                            data-stat-id="{{ $rowId }}"
                                                            data-type="revenue"
                                                            data-member="{{ e($user['name']) }}">
                                                            + Revenue
                                                        </button>
                                                        <button type="button" class="dash-btn dash-btn--ghost js-payout-add"
                                                            data-stat-id="{{ $rowId }}"
                                                            data-type="bonus"
                                                            data-member="{{ e($user['name']) }}">
                                                            + Bonus
                                                        </button>
                                                        <button type="button" class="dash-btn dash-btn--ghost js-payout-edit-engagement"
                                                            data-stat-id="{{ $rowId }}"
                                                            data-amount="{{ (float) ($user['userPayout'] ?? 0) }}"
                                                            data-member="{{ e($user['name']) }}">
                                                            Edit
                                                        </button>
                                                        <form method="POST" action="{{ route('admin.payouts.queue', $rowId) }}"
                                                            onsubmit="return confirm('Queue total payout of ₦{{ number_format($user['totalPayoutNgn'] ?? 0, 2) }} for {{ $user['name'] }}?');">
                                                            @csrf
                                                            <button type="submit" class="dash-btn dash-btn--primary">Queue</button>
                                                        </form>
                                                    </div>
                                                @elseif ($payoutStatus === 'Paid')
                                                    <span class="dash-muted">Processed</span>
                                                @else
                                                    <a href="{{ route('admin.payouts.show', $rowId) }}" class="dash-btn dash-btn--ghost" style="padding:0.5rem 0.75rem;">
                                                        View
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9">
                                                <div class="dash-empty">No eligible users for payout this month.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Payout sheets (appended at page level, outside .dash overflow) --}}
    <div class="payout-sheet" id="payoutAddSheet" aria-hidden="true">
        <div class="payout-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="payoutAddTitle">
            <div class="payout-sheet__head">
                <div>
                    <h2 class="payout-sheet__title" id="payoutAddTitle">Add payout</h2>
                    <p class="payout-sheet__subtitle" id="addSheetMember"></p>
                </div>
                <button type="button" class="payout-sheet__close js-payout-sheet-close" aria-label="Close">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.payouts.components.store') }}" class="payout-sheet__form">
                @csrf
                <input type="hidden" name="engagement_stat_id" id="addStatId">
                <input type="hidden" name="type" id="addType">
                <div class="payout-sheet__body">
                    <div class="dash-field">
                        <label for="addAmount">Amount (NGN)</label>
                        <input type="number" id="addAmount" name="amount" class="dash-input" min="0.01" step="0.01" required placeholder="e.g. 5000">
                    </div>
                    <div class="dash-field">
                        <label for="addNote">Note (optional)</label>
                        <input type="text" id="addNote" name="note" class="dash-input" maxlength="500" placeholder="Reason for this payout">
                    </div>
                    <div class="dash-field">
                        <label for="addValidation">Validation code</label>
                        <input type="text" id="addValidation" name="validationCode" class="dash-input" required autocomplete="off" placeholder="Enter validation code">
                    </div>
                </div>
                <div class="payout-sheet__foot">
                    <button type="button" class="payout-sheet__btn payout-sheet__btn--ghost js-payout-sheet-close">Cancel</button>
                    <button type="submit" class="payout-sheet__btn payout-sheet__btn--primary">Save payout</button>
                </div>
            </form>
        </div>
    </div>

    <div class="payout-sheet" id="payoutEditSheet" aria-hidden="true">
        <div class="payout-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="payoutEditTitle">
            <div class="payout-sheet__head">
                <div>
                    <h2 class="payout-sheet__title" id="payoutEditTitle">Edit payout line</h2>
                    <p class="payout-sheet__subtitle" id="editSheetMember"></p>
                </div>
                <button type="button" class="payout-sheet__close js-payout-sheet-close" aria-label="Close">&times;</button>
            </div>
            <form method="POST" id="editComponentForm" class="payout-sheet__form">
                @csrf
                @method('PUT')
                <div class="payout-sheet__body">
                    <div class="dash-field">
                        <label for="editAmount">Amount (NGN)</label>
                        <input type="number" id="editAmount" name="amount" class="dash-input" min="0.01" step="0.01" required>
                    </div>
                    <div class="dash-field">
                        <label for="editNote">Note (optional)</label>
                        <input type="text" id="editNote" name="note" class="dash-input" maxlength="500">
                    </div>
                    <div class="dash-field">
                        <label for="editValidation">Validation code</label>
                        <input type="text" id="editValidation" name="validationCode" class="dash-input" required autocomplete="off" placeholder="Enter validation code">
                    </div>
                </div>
                <div class="payout-sheet__foot payout-sheet__foot--split">
                    <button type="button" class="payout-sheet__btn payout-sheet__btn--danger" id="deleteComponentBtn">Remove line</button>
                    <div class="payout-sheet__foot-group">
                        <button type="button" class="payout-sheet__btn payout-sheet__btn--ghost js-payout-sheet-close">Cancel</button>
                        <button type="submit" class="payout-sheet__btn payout-sheet__btn--primary">Update</button>
                    </div>
                </div>
            </form>
            <form method="POST" id="deleteComponentForm" class="d-none">
                @csrf
                @method('DELETE')
                <input type="hidden" name="validationCode" id="deleteValidation">
            </form>
        </div>
    </div>

    <div class="payout-sheet" id="payoutEngagementSheet" aria-hidden="true">
        <div class="payout-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="payoutEngagementTitle">
            <div class="payout-sheet__head">
                <div>
                    <h2 class="payout-sheet__title" id="payoutEngagementTitle">Edit engagement payout</h2>
                    <p class="payout-sheet__subtitle" id="engagementSheetMember"></p>
                </div>
                <button type="button" class="payout-sheet__close js-payout-sheet-close" aria-label="Close">&times;</button>
            </div>
            <form method="POST" id="engagementForm" class="payout-sheet__form">
                @csrf
                @method('PATCH')
                <div class="payout-sheet__body">
                    <div class="dash-field">
                        <label for="engagementAmount">Engagement payout (USD)</label>
                        <input type="number" id="engagementAmount" name="amount" class="dash-input" min="0" step="0.01" required>
                        <span class="payout-sheet__hint">Auto-calculated pro-rata share; override if needed.</span>
                    </div>
                    <div class="dash-field">
                        <label for="engagementValidation">Validation code</label>
                        <input type="text" id="engagementValidation" name="validationCode" class="dash-input" required autocomplete="off" placeholder="Enter validation code">
                    </div>
                </div>
                <div class="payout-sheet__foot">
                    <button type="button" class="payout-sheet__btn payout-sheet__btn--ghost js-payout-sheet-close">Cancel</button>
                    <button type="submit" class="payout-sheet__btn payout-sheet__btn--primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var componentsBase = @json(url('admin/payouts/components'));
        var engagementBase = @json(url('admin/payouts/engagement'));

        var sheets = {
            add: document.getElementById('payoutAddSheet'),
            edit: document.getElementById('payoutEditSheet'),
            engagement: document.getElementById('payoutEngagementSheet'),
        };

        Object.values(sheets).forEach(function (sheet) {
            if (sheet && sheet.parentElement !== document.body) {
                document.body.appendChild(sheet);
            }
        });

        function openSheet(sheet) {
            Object.values(sheets).forEach(function (el) {
                el.classList.remove('is-open');
                el.setAttribute('aria-hidden', 'true');
            });
            sheet.classList.add('is-open');
            sheet.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            var focusable = sheet.querySelector('input:not([type="hidden"])');
            if (focusable) {
                setTimeout(function () { focusable.focus(); }, 50);
            }
        }

        function closeSheets() {
            Object.values(sheets).forEach(function (el) {
                el.classList.remove('is-open');
                el.setAttribute('aria-hidden', 'true');
            });
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.js-payout-sheet-close').forEach(function (btn) {
            btn.addEventListener('click', closeSheets);
        });

        Object.values(sheets).forEach(function (sheet) {
            sheet.addEventListener('click', function (e) {
                if (e.target === sheet) {
                    closeSheets();
                }
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeSheets();
            }
        });

        document.querySelectorAll('.js-payout-add').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var type = this.dataset.type;
                document.getElementById('addStatId').value = this.dataset.statId;
                document.getElementById('addType').value = type;
                document.getElementById('payoutAddTitle').textContent = type === 'revenue' ? 'Add revenue payout' : 'Add bonus payout';
                document.getElementById('addSheetMember').textContent = this.dataset.member || '';
                document.getElementById('addAmount').value = '';
                document.getElementById('addNote').value = '';
                document.getElementById('addValidation').value = '';
                openSheet(sheets.add);
            });
        });

        document.querySelectorAll('.js-payout-edit-component').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var componentId = this.dataset.componentId;
                document.getElementById('editComponentForm').action = componentsBase + '/' + componentId;
                document.getElementById('deleteComponentForm').action = componentsBase + '/' + componentId;
                document.getElementById('editSheetMember').textContent = this.dataset.member || '';
                document.getElementById('editAmount').value = this.dataset.amount || '';
                document.getElementById('editNote').value = this.dataset.note || '';
                document.getElementById('editValidation').value = '';
                openSheet(sheets.edit);
            });
        });

        document.getElementById('deleteComponentBtn').addEventListener('click', function () {
            var code = document.getElementById('editValidation').value;
            if (!code) {
                alert('Enter your validation code to remove this line.');
                document.getElementById('editValidation').focus();
                return;
            }
            if (!confirm('Remove this payout line?')) {
                return;
            }
            document.getElementById('deleteValidation').value = code;
            document.getElementById('deleteComponentForm').submit();
        });

        document.querySelectorAll('.js-payout-edit-engagement').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var statId = this.dataset.statId;
                document.getElementById('engagementForm').action = engagementBase + '/' + statId + '/amount';
                document.getElementById('engagementSheetMember').textContent = this.dataset.member || '';
                document.getElementById('engagementAmount').value = this.dataset.amount || '';
                document.getElementById('engagementValidation').value = '';
                openSheet(sheets.engagement);
            });
        });
    });
</script>
@endsection
