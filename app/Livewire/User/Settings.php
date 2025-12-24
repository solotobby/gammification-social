<?php

namespace App\Livewire\User;

use App\Models\Social;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Settings extends Component
{
    public $socials;
    #[Validate('string')]
    public $facebook = '';
    public $twitter = '';

    public $instagram = '';
    public $tiktok = '';
    public $pinterest = '';
    public $linkedin = '';

    public $upgrade = '';
    public $currency = '';

    public function mount(){
        
        $this->facebook = @auth()->user()->social->facebook;
        $this->twitter = @auth()->user()->social->twitter;
        $this->instagram = @auth()->user()->social->instagram;
        $this->linkedin = @auth()->user()->social->linkedin;
        $this->pinterest = @auth()->user()->social->pinterest;
        $this->tiktok = @auth()->user()->social->tiktok;
    }
  
    public function updateSocial(){
        
        Social::updateOrCreate(
                ['user_id'=> auth()->user()->id], 
                ['facebook' => $this->facebook, 'instagram' => $this->instagram, 'twitter' => $this->twitter, 'tiktok' => $this->tiktok, 'linkedin' => $this->linkedin, 'pinterest' => $this->pinterest]);
                // $this->reset(['facebook', 'twitter', 'tiktok', 'instagram', 'linkedin', 'pinterest']);
                //  session()->flash('success', 'Socials Updated Successfully');
                // $this->timelines->push($timelines);
                // $this->dispatch()

    }

    public function upgradeAccount(){
        $upgrade = $this->upgrade;
        $currency = $this->currency;
        $currentLevel = auth()->user()->activeLevel->plan_name;

        $price = $this->pricingList($currentLevel, $upgrade);

        switch ($upgrade) {

            case "Creator":
                
                if($currency == 'USD'){
                    $link = upgradePayment($price, $currency, $upgrade);
                    return redirect($link);
                }else{
                    $link = upgradePayment($price*1500, 'NGN', $upgrade);
                    return redirect($link);
                }

                break;

            case "Influencer":

                if($currency == 'USD'){
                    $link = upgradePayment($price, $currency, $upgrade);
                    return redirect($link);
                }else{
                    $link = upgradePayment($price*1500, 'NGN', $upgrade);
                    return redirect($link);
                }

                break;

            default:
                dd("invalid");

        }



        // if($validated['currency'] == 'USDT'){
        //     // return 
        // }elseif($validated['currency'] == 'USD'){
        //     $payment =  processPayment($finalAmount, 'USD', $validated['package'], $level, $quantity);
        //     return redirect($payment);
        // }else{
        //     $payment =  processPayment($finalAmount*1500,  'NGN', $validated['package'], $level, $quantity);
        //     return redirect($payment);
        // }

    }


    private function pricingList($level, $upgrade){

        $price = '';
        $newLevel = $upgrade;

        if($level == 'Beginner'){
            if($newLevel == 'Creator'){
                $price = 5;
            }elseif($newLevel == 'Influencer'){
                $price = 15;
            }
        }else{

            if($newLevel == 'Influencer'){
                $price = 10;
            }
        }

        return $price;
    }

    public function render()
    {
        return view('livewire.user.settings');
    }
}
