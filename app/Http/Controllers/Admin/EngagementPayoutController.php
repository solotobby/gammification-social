<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserComment;
use App\Models\UserLevel;
use App\Models\UserLike;
use App\Models\UserView;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EngagementPayoutController extends Controller
{
    public function index()
    {
        
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

         $planPrices = [
            'Creator' => 1,
            'Influencer' => 5,
        ];


      $members = UserLevel::where('status', 'active')
            // ->where('plan_name', $level)
            ->where('created_at', '<=', $endOfMonth)
            ->where(function ($q) use ($startOfMonth) {
                $q->whereNull('next_payment_date')
                    ->orWhere('next_payment_date', '>=', $startOfMonth);
            })
            ->with('user:id,name')
            ->get();

            foreach ($members as $member) {

                $engagementPoints = $this->getEngagementPointsGiven(
                    $member->user_id,
                    $startOfMonth,
                    $endOfMonth
                );

                $member->engagement_points = $engagementPoints;
            }

        return view('admin.pay.basic_engagement_payout', ['users' => $members]);
    }




     protected function getEngagementPointsGiven($userId, $startDate, $endDate)
    {
        
        $views = UserView::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $likes = UserLike::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $comments = UserComment::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return $views + $likes + $comments;
    }

}
