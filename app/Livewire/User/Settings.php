<?php

namespace App\Livewire\User;

use App\Models\Profile;
use App\Models\Social;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
    public $user;

    public $username;
    public $about;
    public $date_of_birth;
    public $gender;
    public $location;
    public $username_updated_at;

    public $canEditUsername = false;
    public $usernameNextEditDate;

     protected $messages = [
                'date_of_birth.before_or_equal' => 'You must be at least 13 years old to use Payhankey.',
            ];


    public function mount(){

        $user = Auth::user();
        
        $this->facebook = @$user->social->facebook;
        $this->twitter = @$user->social->twitter;
        $this->instagram = @$user->social->instagram;
        $this->linkedin = @$user->social->linkedin;
        $this->pinterest = @$user->social->pinterest;
        $this->tiktok = @$user->social->tiktok;

        //profile

        $this->username = $user->username;
        $this->about = @$user->profile->about;
        $this->date_of_birth = @$user->profile->date_of_birth;
        $this->gender = @$user->profile->gender;
        $this->location = @$user->profile->location;

        $this->canEditUsername = !@$user->profile->username_updated_at
            || @$user->profile->username_updated_at->addMonths(6)->isPast();

        $this->usernameNextEditDate = @$user->profile->username_updated_at
            ? @$user->profile->username_updated_at->addMonths(6)->toFormattedDateString()
            : null;

       

    }

    protected function rules()
    {
        
        return [
            'username' => 'required|string|min:3|max:20|alpha_dash|unique:users,username,' . auth()->id(),
            'about' => 'nullable|string|max:40',
        //     'date_of_birth' => [
        //     'nullable',
        //     'date',
        //     // 'before_or_equal:' . Carbon::now()->subYears(13)->toDateString(),
        // ],
            'gender' => 'nullable|in:male,female',
            'location' => 'nullable|string|max:50',
        ];

    }
  
    public function updateSocial(){
        
        Social::updateOrCreate(
                ['user_id'=> auth()->user()->id], 
                ['facebook' => $this->facebook, 'instagram' => $this->instagram, 'twitter' => $this->twitter, 'tiktok' => $this->tiktok, 'linkedin' => $this->linkedin, 'pinterest' => $this->pinterest]);
                // $this->reset(['facebook', 'twitter', 'tiktok', 'instagram', 'linkedin', 'pinterest']);
                 session()->flash('success', 'Socials Updated Successfully');
                // $this->timelines->push($timelines);
                // $this->dispatch()

    }

    public function updateProfile()
    {
        $user = auth()->user();

        if (!$this->canEditUsername && $this->username !== $user->username) {
            $this->addError('username', 'Username can only be changed once every 6 months.');
            return;
        }

      

         $this->validate();

        if ($this->username !== $user->username) {
            $user->username_updated_at = now();
        }
        $userInfor = User::find($user->id);
        $userInfor->username = $this->username;
        $userInfor->save();
  dd($this->date_of_birth);
         

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
            //'username' => $this->username,
            'about' => $this->about,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'location' => $this->location,
            'username_updated_at' => now() //$this->username_updated_at
        ]);

        session()->flash('success', 'Profile updated successfully.');



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
