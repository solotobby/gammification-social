<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EngagementDailyStat;
use App\Models\EngagementMonthlyStat;
use App\Models\SubscriptionStat;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserLevel;
use App\Models\UserLike;
use App\Models\UserView;
use Carbon\Carbon;
use Illuminate\Http\Request;


class MonthlyPayoutController extends Controller
{
    public function index()
    {

        $currentMonth =  now()->format('Y-m');
        // $substat = SubscriptionStat::whereMonth('created_at', $currentMonth)->get();
        $substat = SubscriptionStat::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->paginate(50);

        return view('admin.pay.payout', ['stats' => $substat, 'currentMonth' => $currentMonth]);
    }

    public function processLevelPrayout($level)
    {
        //fetch the people to get paid from last month activities
        // return $level;


         $month = now()->subMonth()->format('Y-m');
        // $month = now()->format('Y-m');


        // $this->info('Fetching Daily Engagement stat');
        $stats = EngagementDailyStat::whereBetween(
            'date',
            [
                Carbon::createFromFormat('Y-m', $month)->startOfMonth(),
                Carbon::createFromFormat('Y-m', $month)->endOfMonth(),
            ]
        )->groupBy('user_id', 'level')
            ->selectRaw('
                user_id,
                level,
                SUM(views) as views,
                SUM(likes) as likes,
                SUM(comments) as comments,
                SUM(points) as points
            ')->get();

        $monthlyEngament = [];

        foreach ($stats as $stat) {

            $monthlyEngament[] =   EngagementMonthlyStat::updateOrCreate(
                [
                    'user_id' => $stat->user_id,
                    'level'    => $stat->level,
                    'month'   => $month,
                ],
                [
                    'views'    => $stat->views,
                    'likes'    => $stat->likes,
                    'comments' => $stat->comments,
                    'points'   => $stat->points,
                ]
            );
        }



        return $monthlyEngament;
    }


    public function payouts(Request $request)
    {

        // Get month from query (?month=2026-01)
        $month =  now()->format('Y-m'); //$request->query('month');

        // if ($month) {
        //     $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        //     $endOfMonth   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        // } else {
        //     $startOfMonth = Carbon::now()->startOfMonth();
        //     $endOfMonth   = Carbon::now()->endOfMonth();
        // }


        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();


        // Plan prices in dollars
        $planPrices = [
            'Creator' => 1,
            'Influencer' => 5,
        ];

        $levels = ['Creator', 'Influencer'];
        $result = [];

        foreach ($levels as $levelName) {

            // Active members in this level
            $members = UserLevel::where('status', 'active')
                ->where('plan_name', $levelName)
                ->where('next_payment_date', '>=', $startOfMonth)
                ->get();

            $memberCount = $members->count();

            if ($memberCount === 0) {
                continue;
            }

            // Revenue calculation
            $totalRevenue = $memberCount * $planPrices[$levelName];
            $platformRev = round($totalRevenue * 0.30, 2); ///platform cut
            $levelPool     = round($totalRevenue * 0.50, 2); ///sharing percentage
            $savingsPool     = round($totalRevenue * 0.10, 2); //savings
            $fremiumPool     = round($totalRevenue * 0.10, 2); //fremium

            // Engagement calculation
            $totalEngagement = 0;

            foreach ($members as $member) {
                $totalEngagement += $this->getEngagementPoints(
                    $member->user_id,
                    $startOfMonth,
                    $endOfMonth
                );
            }

            $result[] = [
                'level'                 => $levelName,
                'totalRev'              => $totalRevenue,
                'platformRev'           => $platformRev,
                'levelPool'             => $levelPool,
                'totalEngagement'       => $totalEngagement,
                'savingsPool'           => $savingsPool,
                'memberCount'               => $memberCount,
                'fremiumPool'           => $fremiumPool,

            ];
        }



        return view('admin.pay.monthly_payout', [
            'results' => $result,
            'month'  => $startOfMonth->format('F Y'),
            'monthParam' => $month,
            'startMonth' => $startOfMonth,
            'endMonth' => $endOfMonth
        ]);
    }


    protected function getEngagementPoints($userId, $startDate, $endDate)
    {
        $views = UserView::where('poster_user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('type', 'view')
            ->count();

        $likes = UserLike::where('poster_user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('type', 'like')
            ->count();

        $comments = UserComment::where('poster_user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('type', 'comment')
            ->count();

        return $views + $likes + $comments;
    }


    public function levelUserBreakdown(Request $request, $level)
    {
        $month =  now()->format('Y-m'); //$request->query('month');

        // if ($month) {
        //     $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        //     $endOfMonth   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        // } else {
        //     $startOfMonth = Carbon::now()->startOfMonth();
        //     $endOfMonth   = Carbon::now()->endOfMonth();
        // }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $planPrices = [
            'Creator' => 1,
            'Influencer' => 5,
        ];

        // Fetch members WITH level info
        $members = UserLevel::where('status', 'active')
            ->where('plan_name', $level)
            ->where('created_at', '<=', $endOfMonth)
            ->where(function ($q) use ($startOfMonth) {
                $q->whereNull('next_payment_date')
                    ->orWhere('next_payment_date', '>=', $startOfMonth);
            })
            ->with('user:id,name')
            ->get();


        $memberCount = $members->count();
        if ($memberCount === 0) {
            return redirect()->back()->with('error', 'No users found.');
        }

        // Tier pool
        $revenue  = $memberCount * $planPrices[$level];
        $tierPool = round($revenue * 0.50, 2);

        $totalEngagement = 0;
        $userEngagements = [];

        foreach ($members as $member) {
            $points = $this->getEngagementPoints(
                $member->user_id,
                $startOfMonth,
                $endOfMonth
            );

            $userEngagements[] = [
                'user_id'    => $member->user_id,
                'name'       => $member->user->name ?? 'N/A',
                'engagement' => $points
            ];

            $totalEngagement += $points;
        }

        // Pro-rata payout
        foreach ($userEngagements as &$user) {
            $user['percentage'] = $totalEngagement > 0
                ? round(($user['engagement'] / $totalEngagement) * 100, 2)
                : 0;

            $user['payout'] = $totalEngagement > 0
                ? round(($user['engagement'] / $totalEngagement) * $tierPool, 2)
                : 0;
        }

        return view('admin.pay.level_user_payout', [
            'level'            => $level,
            'users'           => $userEngagements,
            'tierPool'        => $tierPool,
            'platformPool'    => round($revenue * 0.30, 2),
            'savingsPool'     => round($revenue * 0.10, 2),
            'fremiumPool'     => round($revenue * 0.10, 2),
            'totalRevenue'    => $revenue,
            'totalEngagement' => $totalEngagement,
            'memberCount'     => $memberCount,
            'month'           => $startOfMonth->format('F Y'),
            'monthParam'      => $month
        ]);
    }
}
