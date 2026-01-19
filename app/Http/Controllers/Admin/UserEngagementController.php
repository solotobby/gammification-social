<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EngagementDailyStat;
use App\Models\User;
use Illuminate\Http\Request;

class UserEngagementController extends Controller
{
    public function engagementAnalytics($id)
    {
        $user = User::findOrFail($id);
        
        $dailyEngagements = EngagementDailyStat::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.engagement.analytics', ['user' => $user, 'dailyEngagements' => $dailyEngagements]);
    }
}
