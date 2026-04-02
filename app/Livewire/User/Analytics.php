<?php

namespace App\Livewire\User;

use App\Models\EngagementDailyStat;
use App\Models\Post;
use Livewire\Component;

class Analytics extends Component
{
    public $post;
    public $dailyEngagement;
    public $month;
    public function mount(){
        $month = now()->format('Y-m');
        $this->post = Post::where('user_id', auth()->user()->id)->where('created_at', 'like', $month . '%')->get();
        //this month
       
        // $this->dailyEngagement = EngagementDailyStat::where('user_id', auth()->user()->id)
        //     ->where('date', 'like', $month . '%')
        //     //  ->selectRaw('
        //     //     SUM(views) as total_views,
        //     //     SUM(likes) as total_likes
        //     // ')
        //     ->orderBy('date', 'desc')
        //     // ->take(7)
        //     ->get();
            //  $totalViews = $totals->total_views ?? 0;
            //     $totalLikes = $totals->total_likes ?? 0;

            // dd($totalViews, $totalLikes);


        $this->month = now()->translatedFormat('F Y');
    }
    public function render()
    {
        return view('livewire.user.analytics');
    }
}
