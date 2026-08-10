<?php

namespace App\Livewire\User;

use App\Models\Referral;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ReferralList extends Component
{
    use WithPagination;

    public int $monthlyReferralsCount = 0;
    public int $totalReferrals = 0;
    public string $referralLink = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->referralLink = url('/reg?referral_code=' . $user->referral_code);
        $this->totalReferrals = Referral::where('referral_id', $user->id)->count();
        $this->monthlyReferralsCount = Referral::where('referral_id', $user->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
    }

    public function render()
    {
        $referralList = DB::table('users')
            ->join('referrals', 'users.id', '=', 'referrals.user_id')
            ->where('referrals.referral_id', Auth::id())
            ->select([
                'users.id',
                'users.name',
                'users.username',
                'users.avatar',
                'users.followers',
                'users.following',
                'users.created_at',
            ])
            ->orderByDesc('referrals.created_at')
            ->paginate(20);

        return view('livewire.user.referral-list', [
            'referralList' => $referralList,
            'qualifiedForMonetization' => $this->monthlyReferralsCount >= 500,
        ]);
    }
}
