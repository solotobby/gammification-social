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

        .payout-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1050;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(15, 23, 42, .45);
        }

        .payout-modal.is-open { display: flex; }

        .payout-modal__dialog {
            width: 100%;
            max-width: 440px;
            background: var(--dash-surface);
            border-radius: var(--dash-radius);
            border: 1px solid var(--dash-border);
            box-shadow: 0 24px 48px rgba(15, 23, 42, .18);
            overflow: hidden;
        }

        .payout-modal__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--dash-border);
        }

        .payout-modal__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .payout-modal__close {
            border: none;
            background: transparent;
            color: var(--dash-muted);
            font-size: 1.25rem;
            cursor: pointer;
            line-height: 1;
        }

        .payout-modal__body {
            padding: 1.25rem;
            display: grid;
            gap: 1rem;
        }

        .payout-modal__foot {
            display: flex;
            justify-content: flex-end;
            gap: .5rem;
            padding: 0 1.25rem 1.25rem;
        }

        .dash-field label {
            display: block;
            font-size: .8125rem;
            font-weight: 600;
            margin-bottom: .35rem;
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
                                                                    <button type="button" class="dash-link"
                                                                        onclick="openEditModal('{{ $comp['id'] }}', {{ json_encode($comp['amount']) }}, {{ json_encode($comp['note'] ?? '') }}, {{ json_encode($user['name']) }})">
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
                                                                    <button type="button" class="dash-link"
                                                                        onclick="openEditModal('{{ $comp['id'] }}', {{ json_encode($comp['amount']) }}, {{ json_encode($comp['note'] ?? '') }}, {{ json_encode($user['name']) }})">
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
                                                        <button type="button" class="dash-btn dash-btn--ghost"
                                                            onclick="openAddModal('{{ $rowId }}', 'revenue', {{ json_encode($user['name']) }})">
                                                            + Revenue
                                                        </button>
                                                        <button type="button" class="dash-btn dash-btn--ghost"
                                                            onclick="openAddModal('{{ $rowId }}', 'bonus', {{ json_encode($user['name']) }})">
                                                            + Bonus
                                                        </button>
                                                        <button type="button" class="dash-btn dash-btn--ghost"
                                                            onclick="openEngagementModal('{{ $rowId }}', {{ json_encode((float) ($user['userPayout'] ?? 0)) }}, {{ json_encode($user['name']) }})">
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

    {{-- Add revenue / bonus modal --}}
    <div class="payout-modal" id="addComponentModal" aria-hidden="true">
        <div class="payout-modal__dialog" role="dialog">
            <div class="payout-modal__head">
                <h3 class="payout-modal__title" id="addModalTitle">Add payout</h3>
                <button type="button" class="payout-modal__close" onclick="closeModal('addComponentModal')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.payouts.components.store') }}">
                @csrf
                <input type="hidden" name="engagement_stat_id" id="addStatId">
                <input type="hidden" name="type" id="addType">
                <div class="payout-modal__body">
                    <p class="dash-muted" id="addModalMember" style="margin:0"></p>
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
                        <input type="text" id="addValidation" name="validationCode" class="dash-input" required>
                    </div>
                </div>
                <div class="payout-modal__foot">
                    <button type="button" class="dash-btn dash-btn--ghost" onclick="closeModal('addComponentModal')">Cancel</button>
                    <button type="submit" class="dash-btn dash-btn--primary">Save payout</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit component modal --}}
    <div class="payout-modal" id="editComponentModal" aria-hidden="true">
        <div class="payout-modal__dialog" role="dialog">
            <div class="payout-modal__head">
                <h3 class="payout-modal__title">Edit payout line</h3>
                <button type="button" class="payout-modal__close" onclick="closeModal('editComponentModal')">&times;</button>
            </div>
            <form method="POST" id="editComponentForm">
                @csrf
                @method('PUT')
                <div class="payout-modal__body">
                    <p class="dash-muted" id="editModalMember" style="margin:0"></p>
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
                        <input type="text" id="editValidation" name="validationCode" class="dash-input" required>
                    </div>
                </div>
                <div class="payout-modal__foot">
                    <button type="button" class="dash-btn dash-btn--ghost" onclick="closeModal('editComponentModal')">Cancel</button>
                    <button type="submit" class="dash-btn dash-btn--primary">Update</button>
                </div>
            </form>
            <form method="POST" id="deleteComponentForm" style="padding:0 1.25rem 1.25rem">
                @csrf
                @method('DELETE')
                <input type="hidden" name="validationCode" id="deleteValidation">
                <button type="submit" class="dash-btn dash-btn--ghost" style="color:#b42318;border-color:#fecaca"
                    onclick="document.getElementById('deleteValidation').value = document.getElementById('editValidation').value; return confirm('Remove this payout line?');">
                    Remove line
                </button>
            </form>
        </div>
    </div>

    {{-- Edit engagement payout modal --}}
    <div class="payout-modal" id="engagementModal" aria-hidden="true">
        <div class="payout-modal__dialog" role="dialog">
            <div class="payout-modal__head">
                <h3 class="payout-modal__title">Edit engagement payout</h3>
                <button type="button" class="payout-modal__close" onclick="closeModal('engagementModal')">&times;</button>
            </div>
            <form method="POST" id="engagementForm">
                @csrf
                @method('PATCH')
                <div class="payout-modal__body">
                    <p class="dash-muted" id="engagementModalMember" style="margin:0"></p>
                    <div class="dash-field">
                        <label for="engagementAmount">Engagement payout (USD)</label>
                        <input type="number" id="engagementAmount" name="amount" class="dash-input" min="0" step="0.01" required>
                        <span class="dash-muted" style="font-size:.75rem">Auto-calculated pro-rata share; override if needed.</span>
                    </div>
                    <div class="dash-field">
                        <label for="engagementValidation">Validation code</label>
                        <input type="text" id="engagementValidation" name="validationCode" class="dash-input" required>
                    </div>
                </div>
                <div class="payout-modal__foot">
                    <button type="button" class="dash-btn dash-btn--ghost" onclick="closeModal('engagementModal')">Cancel</button>
                    <button type="submit" class="dash-btn dash-btn--primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('is-open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('is-open');
    }

    function openAddModal(statId, type, memberName) {
        document.getElementById('addStatId').value = statId;
        document.getElementById('addType').value = type;
        document.getElementById('addModalTitle').textContent = type === 'revenue' ? 'Add revenue payout' : 'Add bonus payout';
        document.getElementById('addModalMember').textContent = memberName;
        document.getElementById('addAmount').value = '';
        document.getElementById('addNote').value = '';
        document.getElementById('addValidation').value = '';
        openModal('addComponentModal');
    }

    function openEditModal(componentId, amount, note, memberName) {
        document.getElementById('editComponentForm').action = '{{ url('admin/payouts/components') }}/' + componentId;
        document.getElementById('deleteComponentForm').action = '{{ url('admin/payouts/components') }}/' + componentId;
        document.getElementById('editModalMember').textContent = memberName;
        document.getElementById('editAmount').value = amount;
        document.getElementById('editNote').value = note || '';
        document.getElementById('editValidation').value = '';
        openModal('editComponentModal');
    }

    function openEngagementModal(statId, amount, memberName) {
        document.getElementById('engagementForm').action = '{{ url('admin/payouts/engagement') }}/' + statId + '/amount';
        document.getElementById('engagementModalMember').textContent = memberName;
        document.getElementById('engagementAmount').value = amount;
        document.getElementById('engagementValidation').value = '';
        openModal('engagementModal');
    }

    document.querySelectorAll('.payout-modal').forEach(function (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('is-open');
            }
        });
    });
</script>
@endsection
