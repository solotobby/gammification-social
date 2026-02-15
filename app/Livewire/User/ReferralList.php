<?php

namespace App\Livewire\User;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\WithPagination;
use Livewire\Component;

class ReferralList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap'; // optional

   
    public $monthlyReferralsCount = 0;
    public $totalReferrals = 0;

    public function mount()
    {
        $this->totalReferrals = $this->getTotalReferralsCount();
        $this->monthlyReferralsCount = $this->monthlyReferralsCount();
    }

    public function monthlyReferralsCount()
    {
        $user = Auth::user();
        return DB::table('referrals')
            ->where('referral_id', $user->id)
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->count();
    }

    public function getTotalReferralsCount()
    {
        return DB::table('referrals')
            ->where('referral_id', Auth::id())
            ->count();
    }


    // public function referees()
    // {
    //     $user = Auth::user();

    //     return DB::table('users')
    //         ->join('referrals', 'users.id', '=', 'referrals.user_id')
    //         ->where('referrals.referral_id', $user->id)
    //         ->select('users.*')
    //         ->get(); //paginate(20);
    // }

    public function render()
    {
        $user = Auth::user();

        $referralList = DB::table('users')
            ->join('referrals', 'users.id', '=', 'referrals.user_id')
            ->where('referrals.referral_id', $user->id)
            ->select('users.*')
            ->paginate(20);

        return view('livewire.user.referral-list', ['referralList' => $referralList]);
    }
}
