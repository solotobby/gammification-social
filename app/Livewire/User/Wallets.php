<?php

namespace App\Livewire\User;

use App\Models\Wallet;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Wallets extends Component
{

    public $wallets;
    #[Validate('string')]
    public $usdt_wallet_address = '';
    public $gender;
    public $options = [];

    public $paymentMethod;
    public $subPaymentMethod;
    public $accountNumber;
    public $accountName;
    public $paypalEmail;
    public $usdtWallet;

    

    protected $listeners = ['paymentMethodChanged' => 'resetSubPaymentMethod'];

  



    public function mount(){
        $this->wallets = auth()->user()->wallet;
        $this->usdt_wallet_address = auth()->user()->usdt_wallet_address;
    }

    
    public function updateUSDTWallet(){
       
        Wallet::updateOrCreate(
            ['user_id'=> auth()->user()->id], 
            ['usdt_wallet_address' => $this->usdt_wallet_address]);
            session()->flash('success', 'Wallet Address Updated Successfully');

    }
    public function render()
    {
        return view('livewire.user.wallets');
    }
}
