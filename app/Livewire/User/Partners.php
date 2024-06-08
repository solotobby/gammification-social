<?php

namespace App\Livewire\User;

use App\Mail\AccessCodeMail;
use App\Models\AccessCode;
use App\Models\Level;
use App\Models\Partner;
use App\Models\PartnerSlot;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Partners extends Component
{

    public $phone = '';
    public $country = '';
    public $identification = '';


    public $slot_number = '';
    public $currency = '';
    public $package = '';
    public $name = '';
    public $email = '';
    public $partner_id = '';
    public $full_name = '';

    public $partners, $slot;
    protected $listeners = ['refreshPartner' => 'refreshPartner'];
    public function partner(){

        $validated = $this->validate([ 
            'phone' => 'required|numeric|unique:partners',
            'country' => 'required|string',
            'name' => 'required|string|unique:partners',
            'identification' => 'required|string',
        ]);

        Partner::create(['name' => $validated['name'], 'user_id' => auth()->user()->id, 'email' => auth()->user()->email, 'phone' => $validated['phone'], 'identification' => $validated['identification'], 'country' => $validated['country']]);
        
        return redirect()->to('/partner');

        
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
            $payment =  processPayment($finalAmount, 'USD', $validated['package'], $level, $quantity);
            return redirect($payment);
        }else{

            $payment =  processPayment($finalAmount*1500,  'NGN', $validated['package'], $level, $quantity);
            return redirect($payment);
        }

    }

    public function sendSlot(){
        $validated = $this->validate([ 
            'email' => 'required|email',
            'full_name' => 'required|string',
            'package' => 'required|string',
            // 'partner_id' => 'required|string',
        ]);

        $prtner = Partner::where('user_id', auth()->user()->id)->first();

        // dd($prtner);
       
        $slot= $prtner->partnerSlot->id;

        $partnerSlot = PartnerSlot::where('id', $slot)->first();
        if($validated['package'] == 'Influencer'){

            if($partnerSlot->influencer > 0){

                if($this->sendCode($validated['package'], $validated['email'], $prtner, $validated['full_name'])){
                    $partnerSlot->influencer -= 1;
                    $partnerSlot->save();
                   
                };
                session()->flash('status', 'Slot sent Successfully');
                // $this->dispatch('refreshPartner');
               
               
            }else{
                session()->flash('fail', 'You do not have enough Influencer slot');
            }
            
        }elseif($validated['package'] == 'Creator'){

            if($partnerSlot->creator > 0){

                if($this->sendCode($validated['package'], $validated['email'], $prtner, $validated['full_name'])){
                    $partnerSlot->creator -= 1;
                    $partnerSlot->save();
                };
                
                // $this->dispatch('refreshPartner');
                session()->flash('status', 'Slot sent Successfully');

            }else{
                session()->flash('fail', 'You do not have enough Creator slot');
            }

        }else{

            if($partnerSlot->beginner > 0){

                if($this->sendCode($validated['package'], $validated['email'], $prtner, $validated['full_name'])){
                    $partnerSlot->beginner -= 1;
                    $partnerSlot->save();
                };

                // $this->dispatch('refreshPartner');
                session()->flash('status', 'Slot sent Successfully');

            }else{
                session()->flash('fail', 'You do not have Beginner enough slot');
            }

        }
    }

    private function sendCode($levelName, $email, $prtner, $name){

            $code = $this->generateCode(7);
           
            $ref = time();
            $level = Level::where('name', $levelName)->first();

            $access = AccessCode::create(['tx_id' => $ref, 'partner_id' => $prtner->id, 'name' =>$level->name, 'email' => auth()->user()->email, 
            'amount' => $level->amount, 'code' => $code, 'level_id' => $level->id,
            'recepient_name' => $name, 'recepient_email' => $email
            ]);
            
            Mail::to($email)->send(new AccessCodeMail($code));

            return $access;
    }

    public function generateCode($number){
        $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $code='';

        for($i=0;$i<$number;$i++){
           $code .= $alph[rand(0, 35)];
        }

        return $code;
    }



    public function refreshPartner(){
        $this->partners =  Partner::where(['email' => auth()->user()->email])->first();

        if(@$this->partners->status == true){
            $this->slot = PartnerSlot::where(['partner_id' => $this->partners->id])->first();
        }
    }

    public function mount(){
        $this->partners =  Partner::where(['email' => auth()->user()->email])->first();

        if(@$this->partners->status == true){
            $this->slot = PartnerSlot::where(['partner_id' => $this->partners->id])->first();
        }

    }
    public function render()
    {
        return view('livewire.user.partners');
    }
}
