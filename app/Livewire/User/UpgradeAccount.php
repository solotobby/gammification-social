<?php

namespace App\Livewire\User;

use App\Models\Level;
use App\Models\LevelPlanId;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class UpgradeAccount extends Component
{

    public $levels;
    public $activeLevel;


    public function mount(){

         //PLN_jpan26fg9bz60p7
        // createPlan();

        // dd(createSubscription());
        
        $user = Auth::user();
        
        $this->levels = Level::orderBy('name', 'asc')->get();

        $this->activeLevel = $user->activeLevel;
        
    }

    

    public function upgradeLevel($levelId){
        $user = Auth::user();
        $level = Level::find($levelId);
       

        if(!$level){
            session()->flash('error', 'Invalid Level Selected');
            return;
        }

       $userCurrency = userBaseCurrency($user->id);

        //get plan code based on currency and plan
        $levelPlan = LevelPlanId::where('level_name', $level->name)->where('currency', $userCurrency)->first();

        
        $convertedAmount = convertToBaseCurrency($levelPlan->amount, $userCurrency);

        if($levelPlan){

            if($userCurrency == 'NGN'){
                $this->createSubscriptionNGN($levelPlan->plan_id, $levelPlan->amount, $level->name);
            }
            
        }

    }

    private function createSubscriptionNGN($planCode, $amount, $level){
        $user = Auth::user();

        
        $url = 'https://api.paystack.co/transaction/initialize';
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->post($url, [
            "plan" => $planCode,
            'email' => $user->email,
            'amount' => $amount*100, // first charge
            'callback_url' => route('upgrade.api'),
            'channel' => ["card", "bank", "apple_pay", "ussd", "qr", "mobile_money", "bank_transfer", "payattitude"],
            'metadata' => [
                'user_id' => $user->id,
                'level' => $level,
                'name' => $user->name
            ],
        ])->throw();

        if (!$res->successful()) {
            session()->flash('error', 'Unable to initialize payment.');
            return;
        }

        return redirect($res['data']['authorization_url']);

    }

    public function render()
    {
        return view('livewire.user.upgrade-account');
    }
}
