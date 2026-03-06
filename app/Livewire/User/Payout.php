<?php

namespace App\Livewire\User;

use App\Models\Payout as ModelsPayout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Payout extends Component
{

    public $payouts;
    public $totalPaid;
    public $totalQueued;

    public function mount(){
        $user = Auth::user();
        $this->payouts =  ModelsPayout::where('user_id', $user->id)->get();

        $this->totalPaid = $this->payouts
                        ->where('status', 'Paid')
                        ->sum('amount');
        $this->totalQueued = $this->payouts
                        ->where('status', 'Queued')
                        ->sum('amount');


    }

    public function render()
    {
        return view('livewire.user.payout');
    }
}
