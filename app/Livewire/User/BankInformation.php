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
            'account_number' => 'numeric|unique:withdrawal_methods',
            'country' => 'required|string',
            'bank_code' => 'string|sometimes',
            'payment_method' => 'string|sometimes',
            'paypal_email' => 'email|unique:withdrawal_methods',
            'usdt_wallet' => 'string|unique:withdrawal_methods',
        ]);

       

        if ($validated['country'] == 'Nigeria') {
            $paymentMethod = 'bank_transfer';
            // $currency = 'NGN';
             $bank = ($validated['bank_code']);
            [$bankCode, $bankName] = array_map('trim', explode(',', $bank));
        } else {
            $paymentMethod = $this->payment_method;
            // $currency = 'USD';
        }



        //validation
        if ($paymentMethod == 'bank_transfer') {

            if ($validated['bank_code'] == '') {
                session()->flash('fail', 'Bank Name is required');
                // Redirect back to the form
                return redirect()->back();
            }

            if ($validated['account_number'] == '') {
                session()->flash('fail', 'Account Number is required');
                // Redirect back to the form
                return redirect()->back();
            }
        }

        if ($paymentMethod == 'paypal') {


            if ($validated['paypal_email'] == '') {
                session()->flash('fail', 'Paypal email is required');
                // Redirect back to the form
                return redirect()->back();
            }
        }

        if ($paymentMethod == 'usdt') {
            if ($validated['usdt_wallet'] == '') {
                session()->flash('fail', 'USDT Wallet address is required');
                // Redirect back to the form
                return redirect()->back();
            }
        }

        if ($validated['country'] == 'Nigeria') {
            ///resolve bank information
           $trf =  $this->transferRecipient(auth()->user()->name, $this->account_number, $bankCode);

           
           $trf['details']['account_number']; 
           $trf['details']['account_name']; 
           $trf['details']['bank_name'];


            WithdrawalMethod::create([
                'user_id' => auth()->user()->id,
                'account_number' => $trf['details']['account_number'],
                'account_name' => $trf['details']['account_name'],
                'currency' => $this->baseCurrency,
                'recipient_code' => $trf['recipient_code'],
                'bank_name' => $trf['details']['bank_name'],
                'payment_method' => $paymentMethod, //$validated['payment_method'], 
                'paypal_email' => $validated['paypal_email'],
                'usdt_wallet' => $validated['usdt_wallet'],
                'country' => $validated['country']
            ]);


        }else{

            WithdrawalMethod::create([
                'user_id' => auth()->user()->id,
                'account_number' => null,
                'account_name' => null,
                'currency' => 'USD', //$this->baseCurrency,
                'recipient_code' => null,
                'bank_name' => null,
                'payment_method' => $paymentMethod, //$validated['payment_method'], 
                'paypal_email' => $validated['paypal_email'],
                'usdt_wallet' => $validated['usdt_wallet'],
                'country' => $validated['country']
            ]);
        }

       
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

    private function transferRecipient($account_name, $account_number, $bank_code){

        $data = [
            "type"=> "nuban",
            "name"=> $account_name,
            "account_number"=> $account_number,
            "bank_code"=> $bank_code,
            "currency"=> "NGN"
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
