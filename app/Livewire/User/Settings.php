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

    public function mount(){
        $this->facebook = auth()->user()->social->facebook;
        $this->twitter = auth()->user()->social->twitter;
        $this->instagram = auth()->user()->social->instagram;
        $this->linkedin = auth()->user()->social->linkedin;
        $this->pinterest = auth()->user()->social->pinterest;
        $this->tiktok = auth()->user()->social->tiktok;
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
    public function render()
    {
        return view('livewire.user.settings');
    }
}
