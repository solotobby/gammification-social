<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawalMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Carbon\Carbon;

class BankInformation extends Component
{


    public $showEditModal = false;

    public $baseCurrency;
    public $wallets, $withdrawals;
    #[Validate('string')]
    public $usdt_wallet_address = '';


    public $bank_code = '';
    public $country = '';

    public $bank_name = '';
    public $account_number = '';
    public $payment_method = '';
    public $paypal_email = '';
    public $usdt_wallet = '';


    public $wallet_type = '';
    // #[Validate('numeric')]
    public $amount = '';


    public $paymentMethod;
    public $subPaymentMethod;

    public $accountName;
    public $paypalEmail;
    public $usdtWallet;
    public $currency;
    public $paidWithdrawals;


    public $canUpdateCurrency = true;

    public function mount()
    {
        $user = Auth::user();
        $this->baseCurrency = $user->wallet->currency;
        $this->withdrawals = WithdrawalMethod::where(['user_id' => $user->id])->first();

        if ($user->wallet->currency_updated_at) {
        $this->canUpdateCurrency =
            Carbon::parse($user->wallet->currency_updated_at)
                ->addMonths(6)
                ->isPast();
    }
    }

    public function updateCurrency()
    {
        $this->validate([
            'currency' => 'required|string|size:3',
        ]);

        $user = auth()->user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        $wallet->update([
            'currency' => $this->currency,
            'currency_updated_at' => now(),
        ]);

        WithdrawalMethod::where(['user_id' => $user->id])->delete();

        $this->canUpdateCurrency = false;

        $this->dispatch('refreshPage');

        session()->flash('success', 'Currency updated successfully.');
    }

    public function createWithdrawalMethod()
    {

        $validated = $this->validate([
            'account_number' => 'nullable|numeric|unique:withdrawal_methods',
            // 'country' => 'required|string',
            'bank_code' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'paypal_email' => 'nullable|email|unique:withdrawal_methods',
            'usdt_wallet' => 'nullable|string|unique:withdrawal_methods',
        ]);

        $paymentMethod = $this->baseCurrency === 'NGN' ? 'bank_transfer' : $this->payment_method;



        $errors = [];

        if ($paymentMethod === 'bank_transfer') {
            if (empty($validated['bank_code'])) $errors[] = 'Bank Name is required';
            if (empty($validated['account_number'])) $errors[] = 'Account Number is required';
        }

        if ($paymentMethod === 'paypal' && empty($validated['paypal_email'])) {
            $errors[] = 'Paypal email is required';
        }

        if ($paymentMethod === 'usdt' && empty($validated['usdt_wallet'])) {
            $errors[] = 'USDT Wallet address is required';
        }


        if (!empty($errors)) {
            foreach ($errors as $error) {
                session()->flash('fail', $error);
            }
            return redirect()->back();
        }

        $user = Auth::user();

        $data = [
            'user_id' => $user->id,
            'account_number' => null,
            'account_name' => null,
            'currency' => $this->baseCurrency,
            'recipient_code' => null,
            'bank_name' => null,
            'payment_method' => $paymentMethod,
            'paypal_email' => $validated['paypal_email'] ?? null,
            'usdt_wallet' => $validated['usdt_wallet'] ?? null,
            'country' => 'Nigeria'
        ];


        if ($this->baseCurrency === 'NGN') {

            [$bankCode, $bankName] = array_map('trim', explode(',', $validated['bank_code']));


            $trf = $this->resolveBankAccount($validated['account_number'], $bankCode);


            $data = array_merge($data, [
                'account_number' => $trf['account_number'],
                'account_name' => $trf['account_name'],
                'bank_name' => $trf['bank_name'],
                'recipient_code' => $trf['recipient_code'] ?? 'random_number'
            ]);

            $updateUserName = User::find($user->id);
            $updateUserName->name = $trf['account_name'];
            $updateUserName->save();
        }


        WithdrawalMethod::create($data);


        $this->reset('country');
        return redirect()->to('/bank/information');
    }


    private function resolveBankAccount($accountNumber, $bankCode)
    {
        $data = [
            'bank' => $bankCode,
            'account' => $accountNumber
        ];
        $url  = "https://api.korapay.com/merchant/api/v1/misc/banks/resolve";

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.env.kora_pub')
        ])
            ->post($url, $data)
            ->throw();



        return json_decode($res->getBody()->getContents(), true)['data'];
    }

    // private function transferRecipient($account_name, $account_number, $bank_code)
    // {

    //     $data = [
    //         "type" => "nuban",
    //         "name" => $account_name,
    //         "account_number" => $account_number,
    //         "bank_code" => $bank_code,
    //         "currency" => "NGN"
    //     ];
    //     $res = Http::withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
    //     ])->post('https://api.paystack.co/transferrecipient', $data);

    //     return json_decode($res->getBody()->getContents(), true)['data'];
    // }



    public function openEditModal()
    {
        $this->resetValidation();

        $this->showEditModal = true;

        if (!$this->withdrawals) {
            return;
        }

        // NGN
        $this->bank_code = $this->withdrawals->bank_code
            ? $this->withdrawals->bank_code . ',' . $this->withdrawals->bank_name
            : null;

        $this->account_number = $this->withdrawals->account_number;

        // Non-NGN
        $this->payment_method = $this->withdrawals->payment_method;
        $this->paypal_email = $this->withdrawals->paypal_email;
        $this->usdt_wallet = $this->withdrawals->usdt_wallet;
    }

    public function updateWithdrawalMethod()
    {
        $user = auth()->user();

        if (!$this->withdrawals) {
            session()->flash('fail', 'No payout information found.');
            return;
        }

        // 🔐 Base validation (ignore current record for unique checks)
        $rules = [
            'account_number' => 'nullable|numeric', //|unique:withdrawal_methods,account_number,' . $this->withdrawals->id,
            'bank_code'      => 'nullable|string',
            // 'payment_method' => 'nullable|string|in:paypal,usdt',
            'paypal_email'   => 'nullable|email', //|unique:withdrawal_methods,paypal_email,' . $this->withdrawals->id,
            'usdt_wallet'    => 'nullable|string', //|unique:withdrawal_methods,usdt_wallet,' . $this->withdrawals->id,
        ];


        // 🔁 Currency-based validation
        if ($this->baseCurrency === 'NGN') {
            $rules['bank_code'] = 'required';
            $rules['account_number'] = 'required|numeric';
        } else {
            $rules['payment_method'] = 'required|in:paypal,usdt';
            $rules['paypal_email'] = 'required_if:payment_method,paypal|email';
            $rules['usdt_wallet'] = 'required_if:payment_method,usdt';
        }

        $validated = $this->validate($rules);

        $validated['bank_code'];

        // 🔁 Determine payment method
        $paymentMethod = $this->baseCurrency === 'NGN'
            ? 'bank_transfer'
            : $validated['payment_method'];


        $data = [
            'currency'        => $this->baseCurrency,
            'payment_method' => $paymentMethod,
            'paypal_email'   => $validated['paypal_email'] ?? null,
            'usdt_wallet'    => $validated['usdt_wallet'] ?? null,
        ];


        // 🇳🇬 NGN BANK UPDATE PATH
        if ($this->baseCurrency === 'NGN') {

            [$bankCode, $bankName] = array_map(
                'trim',
                explode(',', $validated['bank_code'])
            );

            // 🔗 Re-validate bank account
            $trf = $this->resolveBankAccount(
                $validated['account_number'],
                $bankCode
            );

            if (!$trf) {
                $this->addError('account_number', 'Unable to resolve bank account');
                return;
            }

            $data = array_merge($data, [
                'account_number' => $trf['account_number'],
                'account_name'   => $trf['account_name'],
                'bank_name'      => $trf['bank_name'],
                'recipient_code' => $trf['recipient_code']
                    ?? $this->withdrawals->recipient_code
                    ?? Str::uuid(),
            ]);
        }



        // 💾 Update
        $this->withdrawals->update($data);

        // 🔄 Refresh component state
        $this->withdrawals->refresh();

        // 🔐 Close edit modal
        $this->showEditModal = false;

        session()->flash('success', 'Payout information updated successfully.');
    }



    public function render()
    {
        return view('livewire.user.bank-information');
    }
}
