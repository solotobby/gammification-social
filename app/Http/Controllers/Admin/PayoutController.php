<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EngagementMonthlyStat;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index($level)
    {

        if (! $level) {
            return view('admin.payouts.index', [
                'status' => 'error',
                'message' => 'Invalid payout level selected.',
            ]);
        }

        $lastMonth = now()->subMonth()->format('Y-m');

        // Freemium users
        if (! in_array($level, ['Influencer', 'Creator'])) {
            return view('admin.payouts.index', [
                'status' => 'skipped',
                'message' => 'Freemium users do not receive payouts.',
                'level' => $level,
                'lastmonth' => $lastMonth,
            ]);
        }

        // Premium processing
        $payouts = $this->processPremium($level, $lastMonth);

        // ❌ Error from service
        if (isset($payouts['error'])) {
            return view('admin.payouts.index', [
                'status' => 'error',
                'message' => $payouts['error'],
                'level' => $level,
                'lastmonth' => $lastMonth,
            ]);
        }

        // ✅ SUCCESS
        return view('admin.payouts.index', [
            'status'           => 'success',
            'payouts'          => $payouts['userEngagement'],
            'totalEngagement'  => $payouts['totalEngagement'],
            'revenue'          => $payouts['revenue'],
            'levelPool'        => $payouts['levelPool'],
            'memberCount'      => $payouts['memberCount'],
            'level'            => $level,
            'lastmonth'        => $lastMonth,
        ]);
    }

    public function queuePayout($id)
    {
        $engagementStat = EngagementMonthlyStat::find($id);

        $payout = Payout::create([
            'engagement_monthly_stats_id' => $id,
            'user_id' => $engagementStat->user_id,
            'level' => $engagementStat->level,
            'amount' => convertToBaseCurrency($engagementStat->amount, 'NGN'),
            'total_engagement' => $engagementStat->points,
            'month' => $engagementStat->month,
            'currency' => $engagementStat->currency ?? 'NGN',
            'status' => 'Queued',
            'type' => 'Premium'
        ]);

        $engagementStat->update(['status' => 'Queued']);

        return back();
    }

    public function viewPayoutInformation($engagementStatId){

       $payoutInformation = Payout::where('engagement_monthly_stats_id', $engagementStatId)->first();
       return view('admin.payouts.show', ['payout' => $payoutInformation]);
    }

    private function processPremium(string $level, string $lastMonth): array
    {
        $planPrices = [
            'Creator' => 1,
            'Influencer' => 5,
        ];

        // Fetch last month engagement
        $members = EngagementMonthlyStat::with(['user.wallet'])
            ->where('level', $level)
            ->where('month', $lastMonth)
            // ->WHERE('status', 'Pending')
            ->get();

        $memberCount = $members->count();


        if ($memberCount === 0) {
            return [
                'error' => 'No users found.',
            ];
        }

        // Revenue + pool
        $revenue   = $memberCount * $planPrices[$level];
        $levelPool = round($revenue * 0.50, 2);

        $totalEngagement = $members->sum('points');

        $userEngagements = [];

        foreach ($members as $member) {

            // ---- Pro-rata calculations ----
            $percentage = $totalEngagement > 0
                ? round(($member->points / $totalEngagement) * 100, 2)
                : 0;

            $payout = $totalEngagement > 0
                ? round(($member->points / $totalEngagement) * $levelPool, 2)
                : 0;

            // ✅ UPDATE payout in DB
            $member->update([
                'amount' => $payout,
            ]);

            $user = $member->user;

            $userEngagements[] = [
                'id'               => $member->id,
                'user_id'          => $member->user_id,
                'name'             => $user->name ?? 'N/A',
                'email'            => $user->email ?? 'N/A',
                'engagement'       => $member->points ?? 0,
                'userPercentage'   => $percentage,
                'userPayout'       => $payout,
                'userWallet'       => $user->wallet->currency ?? 'USD',
                'status'            =>  $member->status
                // 'userPayoutMethod' => $user->withdrawalMethod(),
            ];
        }

        return [
            'userEngagement'  => $userEngagements,
            'totalEngagement' => $totalEngagement,
            'revenue'         => $revenue,
            'memberCount'     => $memberCount,
            'levelPool'       => $levelPool,
        ];
    }
}
