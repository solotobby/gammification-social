@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }

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
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .dash-badge--rose { background: #fff1f2; color: #be123c; }
        .dash-code { font-family: ui-monospace, monospace; font-weight: 600; }

        .dash-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }

        .dash-btn--danger { background: #e11d48; color: #fff; }
        .dash-btn--success { background: #059669; color: #fff; }

        .dash-dialog {
            border: none;
            border-radius: var(--dash-radius);
            padding: 0;
            width: min(480px, calc(100vw - 2rem));
            box-shadow: 0 24px 48px rgba(15, 23, 42, .18);
        }

        .dash-dialog::backdrop { background: rgba(15, 23, 42, .45); }

        .dash-dialog__head {
            padding: 1.125rem 1.25rem;
            border-bottom: 1px solid var(--dash-border);
        }

        .dash-dialog__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .dash-dialog__body { padding: 1.25rem; }

        .dash-dialog__foot {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--dash-border);
        }

        .dash-field label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--dash-muted);
        }

        .dash-field + .dash-field { margin-top: 1rem; }

        @media (max-width: 960px) {
            .dash-grid--3 { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    @php
        $activeCount = $currencies->where('is_active', true)->count();
        $inactiveCount = $currencies->count() - $activeCount;
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Currencies</h1>
                    <p>Manage exchange rates and availability by country</p>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> Dashboard
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <section class="dash-section">
                <div class="dash-grid dash-grid--3">
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Total currencies</span>
                        <div class="dash-kpi__value">{{ number_format($currencies->count()) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Active</span>
                        <div class="dash-kpi__value">{{ number_format($activeCount) }}</div>
                    </div>
                    <div class="dash-kpi">
                        <span class="dash-kpi__label">Inactive</span>
                        <div class="dash-kpi__value">{{ number_format($inactiveCount) }}</div>
                    </div>
                </div>
            </section>

            <div class="dash-card">
                <div class="dash-card__head">
                    <div>
                        <h2 class="dash-card__title">Currency list</h2>
                        <p class="dash-muted" style="margin:0.25rem 0 0;">Base rates relative to USD</p>
                    </div>
                </div>

                <div class="dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Currency</th>
                                    <th>Code</th>
                                    <th>Symbol</th>
                                    <th>Base rate</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($currencies as $currency)
                                    <tr>
                                        <td>{{ $currency->country }}</td>
                                        <td>{{ $currency->name }}</td>
                                        <td><span class="dash-badge dash-badge--indigo dash-code">{{ $currency->code }}</span></td>
                                        <td class="dash-muted">{{ $currency->symbol ?? '—' }}</td>
                                        <td class="dash-code">{{ number_format((float) $currency->base_rate, 4) }}</td>
                                        <td>
                                            @if ($currency->is_active)
                                                <span class="dash-badge dash-badge--emerald">Active</span>
                                            @else
                                                <span class="dash-badge dash-badge--rose">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dash-actions">
                                                <form method="POST" action="{{ route('admin.currencies.status', $currency) }}"
                                                    onsubmit="return confirm('{{ $currency->is_active ? 'Deactivate' : 'Activate' }} {{ $currency->code }}?');">
                                                    @csrf
                                                    <button type="submit" class="dash-btn dash-btn--ghost" style="padding:0.5rem 0.75rem;">
                                                        {{ $currency->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                                <button type="button" class="dash-btn dash-btn--primary" style="padding:0.5rem 0.75rem;"
                                                    onclick="document.getElementById('currency-dialog-{{ $currency->id }}').showModal()">
                                                    Edit rate
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="dash-empty">No currencies configured yet.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @foreach ($currencies as $currency)
                <dialog id="currency-dialog-{{ $currency->id }}" class="dash-dialog">
                    <div class="dash-dialog__head">
                        <h3 class="dash-dialog__title">Edit base rate — {{ $currency->code }}</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.currencies.update', $currency) }}">
                        @csrf
                        @method('PUT')
                        <div class="dash-dialog__body">
                            <div class="dash-field">
                                <label>Country</label>
                                <input type="text" class="dash-input" value="{{ $currency->country }}" readonly>
                            </div>
                            <div class="dash-field">
                                <label>Currency</label>
                                <input type="text" class="dash-input"
                                    value="{{ $currency->name }} ({{ $currency->code }})" readonly>
                            </div>
                            <div class="dash-field">
                                <label for="base_rate_{{ $currency->id }}">Base rate</label>
                                <input type="number" step="0.0001" min="0" name="base_rate"
                                    id="base_rate_{{ $currency->id }}" class="dash-input"
                                    value="{{ $currency->base_rate }}" required>
                            </div>
                        </div>
                        <div class="dash-dialog__foot">
                            <button type="button" class="dash-btn dash-btn--ghost"
                                onclick="this.closest('dialog').close()">Cancel</button>
                            <button type="submit" class="dash-btn dash-btn--primary">Save rate</button>
                        </div>
                    </form>
                </dialog>
            @endforeach
        </div>
    </div>
@endsection
