<?php

namespace App\Livewire\User;

use App\Models\Payout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class EarningList extends Component
{

    public $earnings = [];

    public function mount()
    {
        $this->earnings = $this->loadEarners();
    }

    public function loadEarners()
    {



        //$payout = Payout::where('status', 'Paid')->get();
        $sub = DB::table('payouts')
            ->join('users', 'users.id', '=', 'payouts.user_id')
            ->selectRaw("
        payouts.month as month_key,
        users.id,
        users.username,
        SUM(payouts.amount) as total_paid,
        ROW_NUMBER() OVER (
            PARTITION BY payouts.month
            ORDER BY SUM(payouts.amount) DESC
        ) as rank_position
    ")
            ->where('payouts.status', 'Queued')
            ->groupBy('payouts.month', 'users.id', 'users.username');

        return $topEarners = DB::query()
            ->fromSub($sub, 'ranked')
            ->where('rank_position', '<=', 10)
            ->orderBy('month_key', 'desc')
            ->orderBy('total_paid', 'desc')
            ->get()
            ->groupBy('month_key');
    }

    public function render()
    {
        return view('livewire.user.earning-list');
    }
}
