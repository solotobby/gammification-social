@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        .dash-dl {
            display: grid;
            grid-template-columns: minmax(140px, 38%) 1fr;
            gap: 0.75rem 1rem;
            margin: 0;
            font-size: 0.875rem;
        }

        .dash-dl dt { margin: 0; font-weight: 600; color: var(--dash-muted); }
        .dash-dl dd { margin: 0; }

        .dash-field label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--dash-muted);
        }

        .dash-select {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: 10px;
            border: 1px solid var(--dash-border);
            font: inherit;
            font-size: 0.875rem;
            background: var(--dash-surface);
        }

        .dash-form { display: grid; gap: 1rem; max-width: 520px; }
        .dash-transfer-response { font-size: 0.875rem; }

        @media (max-width: 960px) {
            .dash-grid--2 { grid-template-columns: 1fr; }
            .dash-dl { grid-template-columns: 1fr; gap: 0.25rem; }
        }
    </style>
@endsection

@section('content')
    @php
        $totalPayout = (float) $payout->amount + (float) ($wallet->balance ?? 0);
        $nextPayout = \Carbon\Carbon::now()->addMonth()->day(1)->format('F j, Y');
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Payout details</h1>
                    <p>{{ $payout->user->name }} · {{ $payout->level }} · {{ $payout->month }}</p>
                </div>
                <a href="{{ route('admin.payouts.levels.show', $payout->level) }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> Back to payouts
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif

            <section class="dash-section dash-grid dash-grid--2">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Payout summary</h2>
                        <span class="dash-badge {{ $payout->status === 'Paid' ? 'dash-badge--emerald' : 'dash-badge--amber' }}">
                            {{ $payout->status }}
                        </span>
                    </div>
                    <div class="dash-card__body">
                        <dl class="dash-dl">
                            <dt>Member</dt>
                            <dd>{{ $payout->user->name }}</dd>
                            <dt>Plan</dt>
                            <dd>{{ $payout->level }}</dd>
                            <dt>Engagement</dt>
                            <dd>{{ number_format($payout->total_engagement) }}</dd>
                            <dt>Payout currency</dt>
                            <dd>{{ $payout->currency }}</dd>
                            <dt>Wallet currency</dt>
                            <dd>{{ $wallet->currency ?? '—' }}</dd>
                            <dt>Payout amount</dt>
                            <dd>{{ getCurrencyCode($payout->currency) }}{{ number_format((float) $payout->amount, 2) }}</dd>
                            <dt>Wallet balance</dt>
                            <dd>{{ getCurrencyCode($wallet->currency ?? $payout->currency) }}{{ number_format((float) ($wallet->balance ?? 0), 2) }}</dd>
                            <dt>Total transfer</dt>
                            <dd><strong>{{ getCurrencyCode($payout->currency) }}{{ number_format($totalPayout, 2) }}</strong></dd>
                            <dt>Next payout window</dt>
                            <dd>{{ $nextPayout }}</dd>
                        </dl>

                        @if ($payout->status !== 'Paid')
                            <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout) }}" class="dash-form" style="margin-top:1.25rem;"
                                onsubmit="return confirm('Mark this payout as paid without transfer?');">
                                @csrf
                                <button type="submit" class="dash-btn dash-btn--ghost">Mark as paid manually</button>
                            </form>
                        @endif

                        <a href="{{ route('admin.users.show', $payout->user) }}" class="dash-link" style="display:inline-block; margin-top:1rem;">
                            View user profile
                        </a>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Withdrawal method</h2>
                    </div>
                    <div class="dash-card__body">
                        @if ($withdrawals)
                            @if ($withdrawals->payment_method === 'usdt')
                                <dl class="dash-dl">
                                    <dt>Method</dt><dd>USDT</dd>
                                    <dt>Wallet</dt><dd>{{ maskCode($withdrawals->usdt_wallet) }}</dd>
                                </dl>
                            @elseif ($withdrawals->payment_method === 'paypal')
                                <dl class="dash-dl">
                                    <dt>Method</dt><dd>PayPal</dd>
                                    <dt>Email</dt><dd>{{ maskCode($withdrawals->paypal_email) }}</dd>
                                </dl>
                            @else
                                <dl class="dash-dl">
                                    <dt>Account name</dt><dd>{{ $withdrawals->account_name }}</dd>
                                    <dt>Bank</dt><dd>{{ $withdrawals->bank_name }}</dd>
                                    <dt>Account number</dt><dd>{{ $withdrawals->account_number }}</dd>
                                    <dt>Recipient code</dt><dd>{{ $withdrawals->recipient_code ?: '—' }}</dd>
                                </dl>
                            @endif
                        @else
                            <div class="dash-empty">No withdrawal method on file.</div>
                        @endif
                    </div>
                </div>
            </section>

            @if ($payout->status !== 'Paid' && $withdrawals)
                <div class="dash-card dash-section">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Process bank transfer</h2>
                    </div>
                    <div class="dash-card__body">
                        <p class="dash-muted" style="margin:0 0 1rem;">
                            Transfer payout plus wallet balance to the user's bank account.
                        </p>
                        <div id="transfer-response" class="dash-transfer-response"></div>
                        <form id="fund-transfer-form" method="POST" action="{{ route('admin.payouts.fund-transfer') }}" class="dash-form">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $payout->user_id }}">
                            <input type="hidden" name="payout_id" value="{{ $payout->id }}">
                            <div class="dash-field">
                                <label for="bank_code">Bank</label>
                                <select id="bank_code" name="bank_code" class="dash-select" required>
                                    <option value="">Select bank</option>
                                    @foreach (bankList() as $bank)
                                        <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="dash-field">
                                <label for="validationCode">Validation code</label>
                                <input type="text" id="validationCode" name="validationCode" class="dash-input" required>
                            </div>
                            <button type="submit" class="dash-btn dash-btn--primary">Process transfer</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('fund-transfer-form');
            if (!form) return;

            const responseBox = document.getElementById('transfer-response');
            const submitBtn = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (submitBtn.disabled) return;

                responseBox.innerHTML = '';
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing…';

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                })
                    .then(async (res) => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then((data) => {
                        const ok = data.status === 'success';
                        responseBox.innerHTML = `<div class="dash-alert dash-alert--${ok ? 'success' : 'error'}">${data.message}</div>`;
                        if (ok) form.reset();
                    })
                    .catch((err) => {
                        const message = err?.message || (typeof err === 'object' && err.message) || 'Transfer failed';
                        responseBox.innerHTML = `<div class="dash-alert dash-alert--error">${message}</div>`;
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Process transfer';
                    });
            });
        });
    </script>
@endsection
