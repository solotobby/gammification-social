<?php

namespace App\Livewire\User;

use App\Models\Wallet;
use App\Models\WithdrawalMethod;
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


    public $paymentMethod;
    public $subPaymentMethod;
    public $accountNumber;
    public $accountName;
    public $paypalEmail;
    public $usdtWallet;

    

    protected $listeners = ['paymentMethodChanged' => 'resetSubPaymentMethod'];


    public function mount(){
       
        $this->wallets = auth()->user()->wallet;
        $this->withdrawals = WithdrawalMethod::where(['user_id'=> auth()->user()->id])->first(); //auth()->user()->usdt_wallet_address;
        
    }

    public function createWithdrawalMethod(){

        $validated = $this->validate([ 
            'account_number' => 'numeric|unique:withdrawal_methods',
            'country' => 'required|string',
            'bank_name' => 'string|sometimes',
            'payment_method' => 'string|sometimes',
            'paypal_email' => 'email|unique:withdrawal_methods',
            'usdt_wallet' => 'string|unique:withdrawal_methods',
        ]);

         if($validated['country'] == 'Nigeria'){
                $paymentMethod = 'bank_transfer';
                $currency = 'NGN';
        }else{
                $paymentMethod = $this->payment_method;
                $currency = 'USD';
        }

            //validation
            if($paymentMethod == 'bank_transfer'){

                if($validated['bank_name'] == ''){
                    session()->flash('fail', 'Bank Name is required');
                    // Redirect back to the form
                    return redirect()->back();
                }

                if($validated['account_number'] == ''){
                    session()->flash('fail', 'Account Number is required');
                    // Redirect back to the form
                    return redirect()->back();
                }

            }

            if($paymentMethod == 'paypal'){
                if($validated['paypal_email'] == ''){
                    session()->flash('fail', 'Paypal email is required');
                    // Redirect back to the form
                    return redirect()->back();
                }

                

            }

            if($paymentMethod == 'usdt'){
                if($validated['usdt_wallet'] == ''){
                    session()->flash('fail', 'USDT Wallet address is required');
                    // Redirect back to the form
                    return redirect()->back();
                }
            }

        // dd($validated['payment_method']);

        WithdrawalMethod::create([
            'user_id' => auth()->user()->id, 
            'account_number' => $validated['account_number'], 
            'currency' => $currency, 
            'bank_name' => $validated['bank_name'], 
            'payment_method' => $paymentMethod, //$validated['payment_method'], 
            'paypal_email' => $validated['paypal_email'],
            'usdt_wallet' => $validated['usdt_wallet'],
            'country' => $validated['country']
        ]);
        $this->reset('country');
        return redirect()->to('/wallets');

            // dd($this->account_bank);

        // if($this->bank_name == ''){ //if the persoon is not in Nigeria, no need paypal and usdt
        //     $paymentMethod = $this->payment_method;
        //     $paypalEmail = $this->paypal_email;
        //     $usdtWallet = $this->usdt_wallet;
        // }else{
        //     $paymentMethod = null;//$this->payment_method;
        //     $paypalEmail = null;//$this->paypal_email;
        //     $usdtWallet = null;//$this->usdt_wallet;
        // }

        // dd($this->account_bank);

        // if($this->country == 'Nigeria'){
        //     $paymentMethod = 'Bank_Transfer';

        // }else{
        //     $paymentMethod = $this->payment_method;

            
        // }

        


        // WithdrawalMethod::create([
        //     'user_id' => auth()->user()->id, 
        //     'account_number' => $this->account_number, 
        //     'currency' => 'USD', 
        //     'bank_name' => $this->account_bank, 
        //     'payment_method' => $paymentMethod,
        //     'paypal_email' => $this->paypal_email,
        //     'usdt_wallet' => $this->usdt_wallet,
        //     'country' => $this->country
        // ]);

        // return redirect()->to('/wallets');

    }

    
    public function updateUSDTWallet(){
       
        Wallet::updateOrCreate(
            ['user_id'=> auth()->user()->id], 
            ['usdt_wallet_address' => $this->usdt_wallet_address]
        );

        session()->flash('success', 'Wallet Address Updated Successfully');

    }
    public function render()
    {
        return view('livewire.user.wallets');
    }
}
