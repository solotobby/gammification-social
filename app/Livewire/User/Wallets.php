<?php

namespace App\Livewire\User;

use Livewire\Component;

class Wallets extends Component
{

    public $wallets;
    public function mount(){
        $this->wallets = auth()->user()->wallet;

    }
    public function render()
    {
        return view('livewire.user.wallets');
    }
}
