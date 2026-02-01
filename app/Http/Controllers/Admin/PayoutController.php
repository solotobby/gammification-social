<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EngagementMonthlyStat;
use App\Models\User;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index($level)
    {

        if (!$level) {
            return 'Type Invalid';
        }

        $lastMonth = now()->subMonth()->format('Y-m');

        if ($level === 'Influencer' || $level === 'Creator') {
            $payouts = $this->ProcessPremium($level, $lastMonth);
        } else {
            return 'Freemium';
        }

        return view('admin.payouts.index', [
            'payouts' => $payouts['userEngagement'], 
            'totalEngagement' => $payouts['totalEngagement'], 
            'revenue' => $payouts['revenue'],
            'levelPool' => $payouts['levelPool'],
            'level' => $level, 
            'lastmonth' => $lastMonth,
            'memberCount' => $payouts['memberCount']
        ]);
    }

    private function ProcessPremium($level, $lastMonth)
    {

        $planPrices = [
            'Creator' => 1,
            'Influencer' => 5,
        ];

        //fetch last month engagement

        $members = EngagementMonthlyStat::where('level', $level)->where('month', $lastMonth)->get();

        $memberCount = $members->count();
        if ($memberCount === 0) {
            return redirect()->back()->with('error', 'No users found.');
        }

        // Tier pool
        $revenue  = $memberCount * $planPrices[$level]; //in dolars
        $levelPool = round($revenue * 0.50, 2); //amount to share

        $totalEngagement = $members->sum('points');
        $userEngagements = [];

        foreach ($members as $member) {
            ///pro-rata
            $member['percentage'] = $totalEngagement > 0
                ? round(($member->points / $totalEngagement) * 100, 2)
                : 0;

            $member['payout'] = $totalEngagement > 0
                ? round(($member->points  / $totalEngagement) * $levelPool, 2)
                : 0;
  
            $user = User::find($member->user_id);

            ///engagement data
            $userEngagements[] = [
                'user_id'    => $member->user_id,
                'name'       => $member->user->name ?? 'N/A',
                'email'       => $member->user->email ?? 'N/A',
                'engagement' => $member->points ?? '00',
                'userPercentage' =>  $member['percentage'] ?? '00',
                'userPayout' =>  $member['payout'] ?? '00',
                'userWallet' => $user->wallet->currency,
                'userPayoutMethod' =>  $user->withdrawalMethod()

            ];
        }

        $data['userEngagement'] = $userEngagements;
        $data['totalEngagement'] = $totalEngagement;
        $data['revenue'] = $revenue;
        $data['memberCount'] = $memberCount;
        $data['levelPool'] = $levelPool;


        return $data;
    }
}
