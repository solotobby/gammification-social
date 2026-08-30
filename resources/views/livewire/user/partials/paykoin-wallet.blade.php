@include('livewire.user.partials.paykoin-ui')

<div class="pkoin-wrap">
    @if (session()->has('paykoin_status'))
        <div class="pk-alert pk-alert--success">{{ session('paykoin_status') }}</div>
    @endif
    @if (session()->has('paykoin_info'))
        <div class="pk-alert pk-alert--info">{{ session('paykoin_info') }}</div>
    @endif
    @if (session()->has('paykoin_error'))
        <div class="pk-alert pk-alert--error">{{ session('paykoin_error') }}</div>
    @endif

    <section class="pkoin-hero">
        <div class="pkoin-hero-top">
            <div class="pkoin-brand">
                <span class="pkoin-coin" aria-hidden="true">
                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="15" fill="url(#pkoinGrad)" stroke="#D4A017" stroke-width="1.5"/>
                        <text x="16" y="21" text-anchor="middle" font-size="14" font-weight="800" fill="#7C5E0A" font-family="Plus Jakarta Sans, sans-serif">PK</text>
                        <defs>
                            <linearGradient id="pkoinGrad" x1="4" y1="4" x2="28" y2="28">
                                <stop stop-color="#FDE68A"/>
                                <stop offset="1" stop-color="#F59E0B"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <div>
                    <p class="pkoin-kicker">PayKoin · Payhankey Currency</p>
                    <h2 class="pkoin-balance">{{ number_format($paykoinSpendable + $paykoinEarned) }} PK</h2>
                </div>
            </div>
            <div class="pkoin-actions">
                <button type="button" class="pk-btn pk-btn--primary" wire:click="openTopUpModal">
                    <i class="fa fa-plus"></i> Top up
                </button>
                <button type="button" class="pk-btn pk-btn--ghost pkoin-btn-light" wire:click="openConvertModal" @disabled($paykoinEarned < 1)>
                    <i class="fa fa-exchange"></i> Convert gift earnings
                </button>
            </div>
        </div>

        <div class="pkoin-stats pkoin-stats--3">
            <div class="pkoin-stat">
                <span class="pkoin-stat-label">For sending gifts</span>
                <strong>{{ number_format($paykoinSpendable) }} PK</strong>
            </div>
            <div class="pkoin-stat">
                <span class="pkoin-stat-label">Earned from gifts</span>
                <strong>{{ number_format($paykoinEarned) }} PK</strong>
            </div>
            <div class="pkoin-stat">
                <span class="pkoin-stat-label">Top-up rate</span>
                <strong>{{ $pkSymbol }}{{ $pkCurrency === 'NGN' ? number_format($listRate) : number_format($listRate, 2) }} / PK</strong>
            </div>
        </div>

        <p class="pkoin-note">
            Top-up PayKoin is for <strong>sending gifts</strong>. Only PK <strong>earned from gifts</strong> can be converted to cash in your wallet.
        </p>
    </section>

    <div class="pk-panel">
        <div class="pk-panel-head pk-panel-head--split">
            <h2>PayKoin activity</h2>
            <div class="pk-panel-head-actions">
                @if ($this->hasMorePaykoinTransactions)
                    <button type="button" class="pk-btn pk-btn--ghost pkoin-more-btn" wire:click="openPaykoinSidebar">
                        More <i class="fa fa-angle-right"></i>
                    </button>
                @endif
                <div class="pkoin-tabs" role="tablist">
                    @foreach (['all' => 'All', 'topup' => 'Top-ups', 'gift_sent' => 'Sent', 'gift_received' => 'Received', 'convert' => 'Converted'] as $tabId => $tabLabel)
                        <button
                            type="button"
                            class="pkoin-tab @if($paykoinTab === $tabId) is-active @endif"
                            wire:click="$set('paykoinTab', '{{ $tabId }}')"
                        >{{ $tabLabel }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="pk-panel-body pkoin-tx-body">
            @if (count($this->filteredPaykoinTransactions) === 0)
                <div class="pk-empty">
                    <h3>No activity yet</h3>
                    <p>Top up PayKoin to send gifts on posts.</p>
                </div>
            @else
                <ul class="pkoin-tx-list">
                    @include('livewire.user.partials.paykoin-tx-items', [
                        'transactions' => $this->visiblePaykoinTransactions,
                        'pkSymbol' => $pkSymbol,
                        'pkCurrency' => $pkCurrency,
                    ])
                </ul>
                @if ($this->hasMorePaykoinTransactions)
                    <div class="pkoin-tx-more">
                        <button type="button" class="pk-btn pk-btn--ghost pkoin-more-btn" wire:click="openPaykoinSidebar">
                            View all {{ count($this->filteredPaykoinTransactions) }} transactions
                            <i class="fa fa-angle-right"></i>
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @if ($paykoinSidebarOpen)
        <div class="pkoin-sidebar-overlay" wire:click.self="closePaykoinSidebar">
            <aside class="pkoin-sidebar" role="dialog" aria-modal="true" aria-label="PayKoin transactions" wire:click.stop>
                <header class="pkoin-sidebar-head">
                    <div>
                        <p class="pkoin-modal-kicker">PayKoin</p>
                        <h3>All transactions</h3>
                    </div>
                    <button type="button" class="pkoin-close" wire:click="closePaykoinSidebar" aria-label="Close">&times;</button>
                </header>

                <div class="pkoin-sidebar-tabs">
                    @foreach (['all' => 'All', 'topup' => 'Top-ups', 'gift_sent' => 'Sent', 'gift_received' => 'Received', 'convert' => 'Converted'] as $tabId => $tabLabel)
                        <button
                            type="button"
                            class="pkoin-tab @if($paykoinTab === $tabId) is-active @endif"
                            wire:click="$set('paykoinTab', '{{ $tabId }}')"
                        >{{ $tabLabel }}</button>
                    @endforeach
                </div>

                <div class="pkoin-sidebar-body">
                    @if (count($this->filteredPaykoinTransactions) === 0)
                        <div class="pk-empty">
                            <h3>No activity yet</h3>
                            <p>Top up PayKoin to send gifts on posts.</p>
                        </div>
                    @else
                        <ul class="pkoin-tx-list">
                            @include('livewire.user.partials.paykoin-tx-items', [
                                'transactions' => $this->filteredPaykoinTransactions,
                                'pkSymbol' => $pkSymbol,
                                'pkCurrency' => $pkCurrency,
                            ])
                        </ul>
                    @endif
                </div>
            </aside>
        </div>
    @endif

    @if ($topUpOpen)
        <div class="pkoin-overlay" wire:click.self="closeTopUpModal">
            <div class="pkoin-modal" role="dialog" aria-modal="true" wire:click.stop>
                <header class="pkoin-modal-head">
                    <div>
                        <p class="pkoin-modal-kicker">Top up PayKoin</p>
                        <h3>
                            @if ($topUpStep === 'amount') Choose amount
                            @elseif ($topUpStep === 'review') Review top-up
                            @else Complete payment
                            @endif
                        </h3>
                    </div>
                    <button type="button" class="pkoin-close" wire:click="closeTopUpModal" aria-label="Close">&times;</button>
                </header>

                <div class="pkoin-steps-bar">
                    @foreach (['amount', 'review', 'pay'] as $i => $step)
                        <span @class([
                            'pkoin-step-dot',
                            'is-current' => $topUpStep === $step,
                            'is-done' => array_search($topUpStep, ['amount', 'review', 'pay'], true) > $i,
                        ])></span>
                    @endforeach
                </div>

                <div class="pkoin-modal-body">
                    @if ($topUpStep === 'amount')
                        <p class="pkoin-modal-lead">Choose how much to add. You receive the <strong>full PayKoin value</strong> at {{ $pkSymbol }}{{ $pkCurrency === 'NGN' ? number_format($listRate) : number_format($listRate, 2) }} per PK.</p>

                        <div class="pkoin-preset-grid">
                            @foreach ($this->topUpPresets as $preset)
                                <button
                                    type="button"
                                    class="pkoin-preset @if($selectedTopUpPreset == $preset && $topUpAmount === '') is-selected @endif"
                                    wire:click="selectTopUpPreset({{ $preset }})"
                                >{{ $pkSymbol }}{{ number_format($preset, $pkCurrency === 'NGN' ? 0 : 2) }}</button>
                            @endforeach
                        </div>

                        <div class="pk-field" style="margin-top:16px">
                            <label class="pk-label" for="pkoin-custom">Enter Custom amount ({{ $pkCurrency }})</label>
                            <div class="pkoin-input-prefix">
                                <span>{{ $pkSymbol }}</span>
                                <input id="pkoin-custom" type="number" class="pk-input" min="{{ $minTopUp }}" step="100" placeholder="e.g. 15000" wire:model.live="topUpAmount">
                            </div>
                            <span class="pk-hint">Minimum {{ $pkSymbol }}{{ number_format($minTopUp, $pkCurrency === 'NGN' ? 0 : 2) }}</span>
                            @error('topUpAmount') <span class="pk-error">{{ $message }}</span> @enderror
                        </div>

                        @if ($this->previewTopUpPk > 0)
                            <div class="pkoin-preview-card">
                                <div class="pkoin-preview-row">
                                    <span>You pay</span>
                                    <b>{{ $pkSymbol }}{{ number_format($this->resolvedTopUpAmount, $pkCurrency === 'NGN' ? 0 : 2) }}</b>
                                </div>
                                <div class="pkoin-preview-row pkoin-preview-row--highlight">
                                    <span>You receive</span>
                                    <b>{{ number_format($this->previewTopUpPk) }} PK</b>
                                </div>
                            </div>
                        @endif
                    @elseif ($topUpStep === 'review')
                        <div class="pkoin-review-hero">
                            <span class="pkoin-review-pk">{{ number_format($this->previewTopUpPk) }} PK</span>
                            <p>added to your PayKoin balance</p>
                        </div>
                        <ul class="pk-detail-list pkoin-review-list">
                            <li><span>You pay</span><b>{{ $pkSymbol }}{{ number_format($this->resolvedTopUpAmount, $pkCurrency === 'NGN' ? 0 : 2) }}</b></li>
                            <li><span>PayKoin credited</span><b>{{ number_format($this->previewTopUpPk) }} PK</b></li>
                            <li><span>New balance (preview)</span><b>{{ number_format($paykoinSpendable + $paykoinEarned + $this->previewTopUpPk) }} PK</b></li>
                        </ul>
                    @else
                        <p class="pkoin-modal-lead">You will be redirected to Korapay to complete payment securely.</p>
                        <div class="pkoin-pay-total">
                            <span>Total due</span>
                            <strong>{{ $pkSymbol }}{{ number_format($this->resolvedTopUpAmount, $pkCurrency === 'NGN' ? 0 : 2) }}</strong>
                        </div>
                        @error('topUpAmount') <span class="pk-error">{{ $message }}</span> @enderror
                    @endif
                </div>

                <footer class="pkoin-modal-foot">
                    @if ($topUpStep !== 'amount')
                        <button type="button" class="pk-btn pk-btn--ghost" wire:click="topUpBack">Back</button>
                    @endif
                    <button type="button" class="pk-btn pk-btn--ghost" wire:click="closeTopUpModal">Cancel</button>
                    <button
                        type="button"
                        class="pk-btn pk-btn--primary"
                        wire:click="topUpContinue"
                        wire:loading.attr="disabled"
                        wire:target="topUpContinue"
                    >
                        @if ($topUpStep === 'pay')
                            Pay {{ $pkSymbol }}{{ number_format($this->resolvedTopUpAmount, $pkCurrency === 'NGN' ? 0 : 2) }}
                        @else
                            Continue
                        @endif
                    </button>
                </footer>
            </div>
        </div>
    @endif

    @if ($convertOpen)
        <div class="pkoin-overlay" wire:click.self="closeConvertModal">
            <div class="pkoin-modal pkoin-modal--sm" role="dialog" aria-modal="true" wire:click.stop>
                <header class="pkoin-modal-head">
                    <div>
                        <p class="pkoin-modal-kicker">Convert PayKoin</p>
                        <h3>Convert gift earnings</h3>
                    </div>
                    <button type="button" class="pkoin-close" wire:click="closeConvertModal" aria-label="Close">&times;</button>
                </header>

                <form class="pkoin-modal-body" wire:submit.prevent="convertGiftEarnings">
                    <p class="pkoin-modal-lead">
                        Only PayKoin <strong>earned from gifts</strong> can be converted to cash in your wallet.
                    </p>

                    <div class="pk-field">
                        <label class="pk-label" for="pkoin-convert-amt">Gift earnings to convert (PK)</label>
                        <input id="pkoin-convert-amt" type="number" class="pk-input" min="1" max="{{ $paykoinEarned }}" wire:model.live="convertPk" placeholder="e.g. 200">
                        <span class="pk-hint">Convertible: {{ number_format($paykoinEarned) }} PK from gifts received</span>
                        @error('convertPk') <span class="pk-error">{{ $message }}</span> @enderror
                    </div>

                    @if ((int) $convertPk > 0)
                        <div class="pkoin-preview-card">
                            <div class="pkoin-preview-row pkoin-preview-row--highlight">
                                <span>Wallet credit</span>
                                <b>{{ $pkSymbol }}{{ number_format($this->previewConvertFiat, $pkCurrency === 'NGN' ? 0 : 2) }}</b>
                            </div>
                            <div class="pkoin-preview-row pkoin-preview-row--muted">
                                <span>Gift earnings deducted</span>
                                <b>− {{ number_format((int) $convertPk) }} PK</b>
                            </div>
                        </div>
                    @endif

                    <div class="pk-alert pk-alert--warn" style="margin:0">
                        Top-up PayKoin cannot be converted to cash — use it to send gifts on posts.
                    </div>

                    <footer class="pkoin-modal-foot" style="margin:16px -20px -20px;padding:14px 20px;border-top:1px solid var(--pk-line);background:var(--pk-bg);">
                        <button type="button" class="pk-btn pk-btn--ghost" wire:click="closeConvertModal">Cancel</button>
                        <button type="submit" class="pk-btn pk-btn--primary" wire:loading.attr="disabled" wire:target="convertGiftEarnings">Convert</button>
                    </footer>
                </form>
            </div>
        </div>
    @endif
</div>
