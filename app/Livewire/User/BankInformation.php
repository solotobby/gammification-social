<?php

namespace App\Livewire\User;

use App\Models\WithdrawalMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class BankInformation extends Component
{


    public $baseCurrency;
    public $wallets, $withdrawals;
    #[Validate('string')]
    public $usdt_wallet_address = '';


    public $bank_code = '';
    public $country = '';

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

    public function mount()
    {


        $user = Auth::user();
        $this->baseCurrency = $user->wallet->currency;
        $this->withdrawals = WithdrawalMethod::where(['user_id' => auth()->user()->id])->first();
    }


    

    public function createWithdrawalMethod()
    {
        
        $validated = $this->validate([
            'account_number' => 'nullable|numeric|unique:withdrawal_methods',
            'country' => 'required|string',
            'bank_code' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'paypal_email' => 'nullable|email|unique:withdrawal_methods',
            'usdt_wallet' => 'nullable|string|unique:withdrawal_methods',
        ]);

        $paymentMethod = $validated['country'] === 'Nigeria' ? 'bank_transfer' : $this->payment_method;

  
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

        $data = [
            'user_id' => auth()->user()->id,
            'account_number' => null,
            'account_name' => null,
            'currency' => $validated['country'] === 'Nigeria' ? $this->baseCurrency : 'USD',
            'recipient_code' => null,
            'bank_name' => null,
            'payment_method' => $paymentMethod,
            'paypal_email' => $validated['paypal_email'] ?? null,
            'usdt_wallet' => $validated['usdt_wallet'] ?? null,
            'country' => $validated['country']
        ];


        if ($validated['country'] === 'Nigeria') {
            [$bankCode, $bankName] = array_map('trim', explode(',', $validated['bank_code']));
            $trf = $this->transferRecipient(auth()->user()->name, $validated['account_number'], $bankCode);

            $data = array_merge($data, [
                'account_number' => $trf['details']['account_number'],
                'account_name' => $trf['details']['account_name'],
                'bank_name' => $trf['details']['bank_name'],
                'recipient_code' => $trf['recipient_code']
            ]);
        }

    
        WithdrawalMethod::create($data);

       
        $this->reset('country');
        return redirect()->to('/bank/information');
    }


    // private function resolveBankAccount($accountNumber, $bankCode)
    // {
    //     $url  = "https://api.paystack.co/bank/resolve"
    //         . "?account_number={$accountNumber}"
    //         . "&bank_code={$bankCode}";

    //     $res = Http::withHeaders([
    //         'Accept' => 'application/json',
    //         'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
    //     ])
    //         ->get($url)
    //         ->throw();



    //     return json_decode($res->getBody()->getContents(), true)['data'];
    // }

    private function transferRecipient($account_name, $account_number, $bank_code)
    {

        $data = [
            "type" => "nuban",
            "name" => $account_name,
            "account_number" => $account_number,
            "bank_code" => $bank_code,
            "currency" => "NGN"
        ];
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->post('https://api.paystack.co/transferrecipient', $data);

        return json_decode($res->getBody()->getContents(), true)['data'];
    }



    public function UpdateBankInformation()
    {

        $validated = $this->validate([
            'account_number' => 'numeric|unique:withdrawal_methods',
            'country' => 'required|string',
            'bank_name' => 'string|sometimes',
            'payment_method' => 'string|sometimes',
            'paypal_email' => 'email|unique:withdrawal_methods',
            'usdt_wallet' => 'string|unique:withdrawal_methods',
        ]);
    }


    public function render()
    {
        return view('livewire.user.bank-information');
    }
}
