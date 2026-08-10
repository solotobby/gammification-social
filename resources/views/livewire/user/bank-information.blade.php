<div class="pk-app">
    @include('livewire.user.partials.pk-app-ui')

    <div class="pk-app-hero">
        <div class="pk-app-hero-inner">
            <span class="pk-app-kicker">Payout setup</span>
            <h1>Bank & payout information</h1>
            <p>Configure where we send your earnings. Currency: <strong>{{ $baseCurrency }}</strong></p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="pk-alert pk-alert--success">{{ session('success') }}</div>
    @endif
    @if (session()->has('fail'))
        <div class="pk-alert pk-alert--error">{{ session('fail') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="pk-alert pk-alert--error">{{ session('error') }}</div>
    @endif

    <div class="pk-panel">
        <div class="pk-panel-head"><h2>Payout details</h2></div>
        <div class="pk-panel-body">
            @if ($withdrawals)
                @if ($baseCurrency === 'NGN')
                    <ul class="pk-detail-list">
                        <li><span>Bank name</span><b>{{ $withdrawals->bank_name }}</b></li>
                        <li><span>Account number</span><b>{{ Str::mask($withdrawals->account_number, '*', 0, 6) }}</b></li>
                        <li><span>Account name</span><b>{{ $withdrawals->account_name ?? '—' }}</b></li>
                    </ul>
                @else
                    <ul class="pk-detail-list">
                        <li><span>Method</span><b>{{ ucfirst($withdrawals->payment_method) }}</b></li>
                        @if ($withdrawals->payment_method === 'paypal')
                            <li><span>PayPal email</span><b>{{ $withdrawals->paypal_email }}</b></li>
                        @endif
                        @if ($withdrawals->payment_method === 'usdt')
                            <li><span>USDT wallet</span><b style="word-break:break-all">{{ $withdrawals->usdt_wallet }}</b></li>
                        @endif
                    </ul>
                @endif
                <button type="button" class="pk-btn pk-btn--primary" wire:click="openEditModal" style="margin-top:16px">
                    <i class="fa fa-pencil"></i> Update payout details
                </button>
            @else
                <form wire:submit.prevent="createWithdrawalMethod">
                    @if ($baseCurrency === 'NGN')
                        <div class="pk-field">
                            <label class="pk-label" for="bank">Select bank</label>
                            <select id="bank" class="pk-select" wire:model="bank_code">
                                <option value="">Choose bank…</option>
                                @foreach (bankList() as $list)
                                    <option value="{{ $list['code'] }}, {{ $list['name'] }}">{{ $list['name'] }}</option>
                                @endforeach
                            </select>
                            @error('bank_code') <span class="pk-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="pk-field">
                            <label class="pk-label" for="accountNumber">Account number</label>
                            <input id="accountNumber" type="text" class="pk-input" wire:model="account_number" placeholder="10-digit account number">
                            @error('account_number') <span class="pk-error">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <div class="pk-field">
                            <label class="pk-label" for="paymentMethod">Payment method</label>
                            <select id="paymentMethod" class="pk-select" wire:model.live="payment_method">
                                <option value="">Choose method…</option>
                                <option value="paypal">PayPal</option>
                                <option value="usdt">USDT Wallet</option>
                            </select>
                            @error('payment_method') <span class="pk-error">{{ $message }}</span> @enderror
                        </div>
                        @if ($payment_method === 'paypal')
                            <div class="pk-field">
                                <label class="pk-label" for="paypalEmail">PayPal email</label>
                                <input id="paypalEmail" type="email" class="pk-input" wire:model="paypal_email" placeholder="you@email.com">
                                @error('paypal_email') <span class="pk-error">{{ $message }}</span> @enderror
                            </div>
                        @endif
                        @if ($payment_method === 'usdt')
                            <div class="pk-field">
                                <label class="pk-label" for="usdtWallet">USDT wallet address</label>
                                <input id="usdtWallet" type="text" class="pk-input" wire:model="usdt_wallet" placeholder="Wallet address">
                                @error('usdt_wallet') <span class="pk-error">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    @endif
                    <button type="submit" class="pk-btn pk-btn--primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save payout information</span>
                        <span wire:loading>Saving…</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="pk-panel">
        <div class="pk-panel-head"><h2>Base currency</h2></div>
        <div class="pk-panel-body">
            <p class="pk-hint" style="margin-top:0">Current: <strong>{{ $baseCurrency }}</strong></p>
            <form wire:submit.prevent="updateCurrency" style="margin-top:12px;">
                <div class="pk-field">
                    <label class="pk-label" for="currency">Change currency</label>
                    <select id="currency" class="pk-select" wire:model="currency" @disabled(! $canUpdateCurrency)>
                        <option value="">Select currency</option>
                        @foreach (countryList() as $country)
                            <option value="{{ $country['code'] }}">{{ $country['code'] }} – {{ $country['name'] }}</option>
                        @endforeach
                    </select>
                    @unless ($canUpdateCurrency)
                        <span class="pk-hint">Currency can be updated once every 6 months.</span>
                    @endunless
                </div>
                <button type="submit" class="pk-btn pk-btn--ghost" @disabled(! $canUpdateCurrency)>Update currency</button>
            </form>
        </div>
    </div>

    @if ($showEditModal)
        <div style="position:fixed;inset:0;z-index:1050;background:rgba(15,17,23,.55);display:flex;align-items:center;justify-content:center;padding:16px;">
            <div class="pk-panel" style="width:100%;max-width:480px;margin:0;">
                <div class="pk-panel-head">
                    <h2>Update payout information</h2>
                    <button type="button" class="pk-btn pk-btn--ghost" wire:click="$set('showEditModal', false)">&times;</button>
                </div>
                <form wire:submit.prevent="updateWithdrawalMethod">
                    <div class="pk-panel-body">
                        @if ($errors->any())
                            <div class="pk-alert pk-alert--error">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        @if ($baseCurrency === 'NGN')
                            <div class="pk-field">
                                <label class="pk-label">Bank</label>
                                <select class="pk-select" wire:model="bank_code">
                                    <option value="">Select bank</option>
                                    @foreach (bankList() as $list)
                                        <option value="{{ $list['code'] }}, {{ $list['name'] }}">{{ $list['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="pk-field">
                                <label class="pk-label">Account number</label>
                                <input class="pk-input" wire:model="account_number">
                            </div>
                        @else
                            <div class="pk-field">
                                <label class="pk-label">Payment method</label>
                                <select class="pk-select" wire:model.live="payment_method">
                                    <option value="paypal">PayPal</option>
                                    <option value="usdt">USDT</option>
                                </select>
                            </div>
                            @if ($payment_method === 'paypal')
                                <div class="pk-field">
                                    <label class="pk-label">PayPal email</label>
                                    <input type="email" class="pk-input" wire:model="paypal_email">
                                </div>
                            @endif
                            @if ($payment_method === 'usdt')
                                <div class="pk-field">
                                    <label class="pk-label">USDT wallet</label>
                                    <input class="pk-input" wire:model="usdt_wallet">
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="pk-panel-body" style="border-top:1px solid var(--pk-line);display:flex;gap:10px;justify-content:flex-end;padding-top:14px;">
                        <button type="button" class="pk-btn pk-btn--ghost" wire:click="$set('showEditModal', false)">Cancel</button>
                        <button type="submit" class="pk-btn pk-btn--primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('refreshPage', () => window.location.reload());
        });
    </script>
</div>
