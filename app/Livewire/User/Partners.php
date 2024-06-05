<?php

namespace App\Livewire\User;

use App\Models\Level;
use App\Models\Partner;
use App\Models\PartnerSlot;
use Livewire\Component;

class Partners extends Component
{

    public $phone = '';
    public $country = '';
    public $identification = '';


    public $slot_number = '';
    public $currency = '';
    public $package = '';

    public $partners, $slot;
    public function partner(){

        $validated = $this->validate([ 
            'phone' => 'required|numeric|unique:partners',
            'country' => 'required|string',
            'identification' => 'required|string',
        ]);

        Partner::create(['name' => auth()->user()->name, 'email' => auth()->user()->email, 'phone' => $validated['phone'], 'identification' => $validated['identification'], 'country' => $validated['country']]);
        
        return redirect()->to('/partner');

        // $this->reset(['phone', 'identification', 'country']);
        //  session()->flash('success', 'Socials Updated Successfully');
        // session()->flash('success', 'Successfully');
    }

    public function purchaseSlot(){

        $validated = $this->validate([ 
            'slot_number' => 'required|numeric',
            'currency' => 'required|string',
            'package' => 'required|string',
        ]);
        $level = Level::where('name', $validated['package'])->first();
        $unitPrice = $level->amount;
        $quantity = $validated['slot_number'];
        $amount = $unitPrice*$quantity;

        $percentage =  0.1*$amount;

        $finalAmount = $amount - $percentage;

        if($validated['currency'] == 'USDT'){
            // return 
        }elseif($validated['currency'] == 'USD'){
            $payment =  processPayment($finalAmount, $validated['currency'], 'USD');
            return redirect($payment);
        }else{
            $payment =  processPayment($finalAmount, $validated['currency'], 'NGN');
            return redirect($payment);
        }
       
        

       
       

    }

    public function mount(){
        $this->partners =  Partner::where(['email' => auth()->user()->email])->first();

        if($this->partners->status == true){
            $this->slot = PartnerSlot::where(['partner_id' => $this->partners->id])->first();
        }

    }
    public function render()
    {
        return view('livewire.user.partners');
    }
}
