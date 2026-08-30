<?php

namespace App\Livewire\User;

use App\Models\Payout;
use App\Models\Transaction;
use App\Models\UserLevel;
use App\Models\Wallet;
use App\Models\WithdrawalMethod;
use App\Models\Withdrawals;
use App\Services\PayKoinService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Wallets extends Component
{
    public $wallets, $withdrawals;

    #[Validate('string')]
    public $usdt_wallet_address = '';

    public $bank_name = '';
    public $country = '';
    public $account_number = '';
    public $payment_method = '';
    public $paypal_email = '';
    public $usdt_wallet = '';
    public $wallet_type = '';
    public $amount = '';
    public $paymentMethod;
    public $subPaymentMethod;
    public $accountNumber;
    public $accountName;
    public $paypalEmail;
    public $usdtWallet;
    public $currency;
    public $paidWithdrawals;
    public $subscription;
    public $payouts;

    // PayKoin
    public int $paykoinSpendable = 0;
    public int $paykoinEarned = 0;
    public float $listRate = 10;
    public float $convertRate = 7.5;
    public float $minTopUp = 100;
    public string $pkCurrency = 'NGN';
    public string $pkSymbol = '₦';
    public array $paykoinTransactions = [];
    public string $paykoinTab = 'all';

    public bool $topUpOpen = false;
    public string $topUpStep = 'amount';
    public $topUpAmount = '';
    public ?float $selectedTopUpPreset = null;

    public bool $convertOpen = false;
    public $convertPk = '';

    public bool $paykoinSidebarOpen = false;

    public const PAYKOIN_ACTIVITY_PREVIEW = 5;

    protected $listeners = ['paymentMethodChanged' => 'resetSubPaymentMethod'];

    public function mount(PayKoinService $payKoin)
    {
        $user = Auth::user();

        $this->wallets = $user->wallet;
        $this->withdrawals = WithdrawalMethod::where(['user_id' => $user->id])->first();
        $this->paidWithdrawals = Transaction::where('type', 'withdrawals')->where('user_id', $user->id)->sum('amount');
        $this->subscription = UserLevel::where('user_id', $user->id)->where('status', 'active')->first();

        $lastMonth = now()->subMonth()->format('Y-m');
        $this->payouts = Payout::where('user_id', $user->id)->where('month', $lastMonth)->first();

        $this->loadPayKoin($payKoin);
    }

    protected function loadPayKoin(PayKoinService $payKoin): void
    {
        $wallet = $this->wallets;
        $this->pkCurrency = strtoupper((string) ($wallet->currency ?? 'NGN'));
        $this->pkSymbol = getCurrencyCode($this->pkCurrency) ?? '₦';

        $rates = $payKoin->rates($this->pkCurrency);
        $this->listRate = (float) $rates['list'];
        $this->convertRate = (float) $rates['convert'];
        $this->minTopUp = $payKoin->minTopUp($this->pkCurrency);

        $this->paykoinSpendable = (int) ($wallet->paykoin_spendable ?? 0);
        $this->paykoinEarned = (int) ($wallet->paykoin_earned ?? 0);
        $this->paykoinTransactions = $payKoin->recentTransactions(auth()->user())
            ->map(fn ($tx) => [
                'id' => $tx->id,
                'type' => $tx->type,
                'pk_amount' => (int) $tx->pk_amount,
                'fiat_amount' => $tx->fiat_amount,
                'currency' => $tx->currency,
                'description' => $tx->description,
                'created_at' => $tx->created_at->format('M j, Y'),
            ])
            ->all();
    }

    public function getTopUpPresetsProperty(): array
    {
        return $this->pkCurrency === 'NGN'
            ? [5000, 10000, 25000, 50000, 100000]
            : [10, 25, 50, 100, 250];
    }

    public function getResolvedTopUpAmountProperty(): float
    {
        if ($this->topUpAmount !== '' && $this->topUpAmount !== null) {
            return max(0, (float) $this->topUpAmount);
        }

        return (float) ($this->selectedTopUpPreset ?? 0);
    }

    public function getPreviewTopUpPkProperty(): int
    {
        if ($this->resolvedTopUpAmount < $this->minTopUp) {
            return 0;
        }

        return (int) floor($this->resolvedTopUpAmount / $this->listRate);
    }

    public function getPreviewConvertFiatProperty(): float
    {
        $pk = (int) $this->convertPk;

        return round($pk * $this->convertRate, 2);
    }

    public function getFilteredPaykoinTransactionsProperty(): array
    {
        if ($this->paykoinTab === 'all') {
            return $this->paykoinTransactions;
        }

        $map = [
            'topup' => ['topup'],
            'gift_sent' => ['gift_sent'],
            'gift_received' => ['gift_received'],
            'convert' => ['convert'],
        ];

        $types = $map[$this->paykoinTab] ?? [];

        return array_values(array_filter(
            $this->paykoinTransactions,
            fn ($tx) => in_array($tx['type'], $types, true)
        ));
    }

    public function getVisiblePaykoinTransactionsProperty(): array
    {
        return array_slice($this->filteredPaykoinTransactions, 0, self::PAYKOIN_ACTIVITY_PREVIEW);
    }

    public function getHasMorePaykoinTransactionsProperty(): bool
    {
        return count($this->filteredPaykoinTransactions) > self::PAYKOIN_ACTIVITY_PREVIEW;
    }

    public function openPaykoinSidebar(): void
    {
        $this->paykoinSidebarOpen = true;
    }

    public function closePaykoinSidebar(): void
    {
        $this->paykoinSidebarOpen = false;
    }

    public function openTopUpModal(): void
    {
        $this->resetValidation();
        $this->topUpStep = 'amount';
        $this->topUpAmount = '';
        $this->selectedTopUpPreset = $this->topUpPresets[2] ?? $this->topUpPresets[0] ?? null;
        $this->topUpOpen = true;
    }

    public function closeTopUpModal(): void
    {
        $this->topUpOpen = false;
    }

    public function selectTopUpPreset(float $amount): void
    {
        $this->selectedTopUpPreset = $amount;
        $this->topUpAmount = '';
    }

    public function topUpBack(): void
    {
        $steps = ['amount', 'review', 'pay'];
        $index = array_search($this->topUpStep, $steps, true);

        if ($index !== false && $index > 0) {
            $this->topUpStep = $steps[$index - 1];
        }
    }

    public function topUpContinue(PayKoinService $payKoin): void
    {
        if ($this->topUpStep === 'amount') {
            if ($this->resolvedTopUpAmount < $this->minTopUp) {
                $this->addError('topUpAmount', 'Minimum top-up is '.$this->pkSymbol.number_format($this->minTopUp).'.');

                return;
            }

            $this->topUpStep = 'review';

            return;
        }

        if ($this->topUpStep === 'review') {
            $this->topUpStep = 'pay';

            return;
        }

        if ($this->topUpStep === 'pay') {
            try {
                $url = $payKoin->initiateTopUp(auth()->user(), $this->resolvedTopUpAmount);
                $this->redirect($url);
            } catch (\Throwable $e) {
                $this->addError('topUpAmount', $e->getMessage());
            }
        }
    }

    public function openConvertModal(): void
    {
        $this->resetValidation();
        $this->convertPk = '';
        $this->convertOpen = true;
    }

    public function closeConvertModal(): void
    {
        $this->convertOpen = false;
    }

    public function convertGiftEarnings(PayKoinService $payKoin): void
    {
        $this->validate([
            'convertPk' => 'required|integer|min:1|max:'.$this->paykoinEarned,
        ], [
            'convertPk.max' => 'You can only convert PayKoin earned from gifts.',
        ]);

        try {
            $payKoin->convertEarned(auth()->user(), (int) $this->convertPk);
            $this->wallets = auth()->user()->wallet?->fresh();
            $this->loadPayKoin($payKoin);
            $this->convertOpen = false;
            $this->convertPk = '';
            session()->flash('paykoin_status', 'Gift earnings converted to your wallet.');
        } catch (\Throwable $e) {
            $this->addError('convertPk', $e->getMessage());
        }
    }

    public function refresh()
    {
        updateWalletEarnings();
        redirect('wallets');
        session()->flash('status_refresh', 'Wallet Successfully refreshed');
    }

    public function submit()
    {
        $validated = $this->validate([
            'amount' => 'numeric|min:10',
            'wallet_type' => 'required|string',
        ]);

        $withdrwalaMethod = WithdrawalMethod::where('user_id', auth()->user()->id)->first();
        if ($withdrwalaMethod) {

            if ($validated['wallet_type'] == 'main') {

                $wallet = Wallet::where('user_id', auth()->user()->id)->first();
                if ($wallet->balance  >= $validated['amount']) {
                    $wallet->balance -= $validated['amount'];
                    $wallet->save();
                    $naira = $validated['amount'] * 1500;

                    Withdrawals::create([
                        'user_id' => auth()->user()->id,
                        'withdrawal_method_id' => $withdrwalaMethod->id,
                        'amount' => $validated['amount'],
                        'naira' => $naira,
                        'currency' => 'USD',
                        'wallet_type' => $validated['wallet_type'],
                        'method' => 'bank_transfer',
                        'status' => 'Queued'
                    ]);
                    $this->reset(['amount', 'wallet_type']);

                    session()->flash('status', 'Withdrawal Queued, it will be processed in 3 hours');
                } else {
                    session()->flash('status_error', 'Insurficient Balance');
                }
            } elseif ($validated['wallet_type'] == 'referral') {

                $wallet = Wallet::where('user_id', auth()->user()->id)->first();
                if ($wallet->referral_balance  >= $validated['amount']) {
                    $wallet->referral_balance -= $validated['amount'];
                    $wallet->save();
                    $naira = $validated['amount'] * 1500;

                    Withdrawals::create([
                        'user_id' => auth()->user()->id,
                        'withdrawal_method_id' => $withdrwalaMethod->id,
                        'amount' => $validated['amount'],
                        'naira' => $naira,
                        'currency' => 'USD',
                        'wallet_type' => $validated['wallet_type'],
                        'method' => 'bank_transfer',
                        'status' => 'Queued'
                    ]);
                    $this->reset(['amount', 'wallet_type']);

                    session()->flash('status', 'Withdrawal Queued, it will be processed in 3 hours');
                } else {
                    session()->flash('status_error', 'Insurficient Balance');
                }
            } else {

                $wallet = Wallet::where('user_id', auth()->user()->id)->first();
                if ($wallet->promoter_balance  >= $validated['amount']) {
                    $wallet->promoter_balance -= $validated['amount'];
                    $wallet->save();
                    $naira = $validated['amount'] * 1500;

                    Withdrawals::create([
                        'user_id' => auth()->user()->id,
                        'withdrawal_method_id' => $withdrwalaMethod->id,
                        'amount' => $validated['amount'],
                        'naira' => $naira,
                        'currency' => 'USD',
                        'wallet_type' => $validated['wallet_type'],
                        'method' => 'bank_transfer',
                        'status' => 'Queued'
                    ]);
                    $this->reset(['amount', 'wallet_type']);

                    session()->flash('status', 'Withdrawal Queued, it will be processed in 3 hours');
                } else {
                    session()->flash('status_error', 'Insurficient Balance');
                }
            }
        }
    }

    public function createWithdrawalMethod()
    {
        $validated = $this->validate([
            'account_number' => 'numeric|unique:withdrawal_methods',
            'country' => 'required|string',
            'bank_name' => 'string|sometimes',
            'payment_method' => 'string|sometimes',
            'paypal_email' => 'email|unique:withdrawal_methods',
            'usdt_wallet' => 'string|unique:withdrawal_methods',
        ]);

        if ($validated['country'] == 'Nigeria') {
            $paymentMethod = 'bank_transfer';
        } else {
            $paymentMethod = $this->payment_method;
        }

        if ($paymentMethod == 'bank_transfer') {

            if ($validated['bank_name'] == '') {
                session()->flash('fail', 'Bank Name is required');

                return redirect()->back();
            }

            if ($validated['account_number'] == '') {
                session()->flash('fail', 'Account Number is required');

                return redirect()->back();
            }
        }

        if ($paymentMethod == 'paypal') {

            if ($validated['paypal_email'] == '') {
                session()->flash('fail', 'Paypal email is required');

                return redirect()->back();
            }
        }

        if ($paymentMethod == 'usdt') {
            if ($validated['usdt_wallet'] == '') {
                session()->flash('fail', 'USDT Wallet address is required');

                return redirect()->back();
            }
        }

        WithdrawalMethod::create([
            'user_id' => auth()->user()->id,
            'account_number' => $validated['account_number'],
            'currency' => 'USD',
            'bank_name' => $validated['bank_name'],
            'payment_method' => $paymentMethod,
            'paypal_email' => $validated['paypal_email'],
            'usdt_wallet' => $validated['usdt_wallet'],
            'country' => $validated['country']
        ]);
        $this->reset('country');

        return redirect()->to('/wallets');
    }

    public function updateUSDTWallet()
    {
        Wallet::updateOrCreate(
            ['user_id' => auth()->user()->id],
            ['usdt_wallet_address' => $this->usdt_wallet_address]
        );
        session()->flash('success', 'Wallet Address Updated Successfully');
    }

    public function render()
    {
        return view('livewire.user.wallets');
    }
}
