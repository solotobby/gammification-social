<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReferralList extends Component
{

    public $referralList = '';

    public function mount()
    {
        return $this->referralList = $this->referees();
    }

    public function referees()
    {
        $user = Auth::user();
        return  DB::table('referrals')
            ->join('users', 'users.id', '=', 'referrals.user_id') // join NEW USERS
            ->where('referrals.referral_id', $user->id)
            ->select('users.id', 'users.name', 'users.email, users.created_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.user.referral-list');
    }
}
