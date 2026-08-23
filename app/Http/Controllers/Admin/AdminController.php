<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\Blog;
use App\Models\Community;
use App\Models\Feedback;
use App\Models\Level;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\PostVideo;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserComment;
use App\Models\UserLevel;
use App\Models\UserLike;
use App\Models\UserView;
use App\Services\Admin\AdminCommunityService;
use App\Services\FlutterwavePaymentService;
use App\Services\KorapayService;
use App\Services\TransactionService;
use App\Services\UpgradeSubscriptionService;
use App\Support\AdminDateRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    protected FlutterwavePaymentService $flutterwavePaymentService;
    protected TransactionService $transactionService;
    protected UpgradeSubscriptionService $upgradeSubscriptionService;
    protected KorapayService $korapayService;

    public function __construct(
        FlutterwavePaymentService $flutterwavePaymentService,
        TransactionService $transactionService,
        UpgradeSubscriptionService $upgradeSubscriptionService,
        KorapayService $korapayService,
        protected AdminCommunityService $communityAnalytics,
    ) {
        $this->flutterwavePaymentService = $flutterwavePaymentService;
        $this->transactionService = $transactionService;
        $this->upgradeSubscriptionService = $upgradeSubscriptionService;
        $this->korapayService = $korapayService;
    }

    public function home(Request $request)
    {
        if (securityVerification() !== 'OK') {
            abort(404);
        }

        $dateRange = AdminDateRange::fromRequest($request);

        if (isStaff() && ! isAdmin()) {
            return $this->staffHome($dateRange);
        }

        $stats = Cache::remember(
            'admin.dashboard.stats.v6.'.$dateRange->cacheKey(),
            now()->addMinutes(3),
            function () use ($dateRange) {
                $revenue = Transaction::query()
                    ->whereIn('status', ['successful', 'allocated'])
                    ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                    ->selectRaw("COALESCE(SUM(CASE WHEN currency = 'USD' THEN amount ELSE 0 END), 0) as usd")
                    ->selectRaw("COALESCE(SUM(CASE WHEN currency = 'NGN' THEN amount ELSE 0 END), 0) as ngn")
                    ->first();

                $ngnRate = max(1, (float) config('services.exchange.ngn_usd_rate', 1500));
                $totalRevenueUsd = (float) ($revenue->usd ?? 0) + ((float) ($revenue->ngn ?? 0) / $ngnRate);

                $postsInRange = Post::query()
                    ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                    ->count();

                $likesInRange = UserLike::query()
                    ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                    ->count();

                $commentsInRange = UserComment::query()
                    ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                    ->count();

                $viewsInRange = UserView::query()
                    ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                    ->count();

                return [
                    'userCount' => User::role('user')->count(),
                    'levelCounts' => UserLevel::query()
                        ->active()
                        ->valid()
                        ->selectRaw('level_id, COUNT(*) as total')
                        ->groupBy('level_id')
                        ->with('level:id,name')
                        ->get(),
                    'onlineUsers' => collect(Cache::get('online_users', []))
                        ->filter(fn ($lastSeen) => now()->diffInMinutes($lastSeen) <= 2)
                        ->count(),
                    'activeUsers' => UserActivity::query()
                        ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                        ->distinct('user_id')
                        ->count('user_id'),
                    'totalRevenueUsd' => $totalRevenueUsd,
                    'postsInRange' => $postsInRange,
                    'viewsInRange' => $viewsInRange,
                    'engagementInRange' => $likesInRange + $commentsInRange,
                    'newUsers' => User::role('user')
                        ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                        ->count(),
                    'signupChart' => $this->dailySignupChart($dateRange),
                    'engagementChart' => $this->dailyEngagementChart($dateRange),
                    'communityAnalytics' => $this->communityAnalytics->dashboardAnalytics($dateRange),
                ];
            }
        );

        $creatorLevel = Level::query()->where('name', 'Creator')->first();

        return view('admin.home', array_merge($stats, [
            'dateRange' => $dateRange,
            'levelId' => $creatorLevel?->id,
            'showTestPayment' => app()->environment(['local', 'staging']) && $creatorLevel,
        ]));
    }

    protected function staffHome(AdminDateRange $dateRange)
    {
        $stats = Cache::remember(
            'staff.dashboard.stats.v1.'.$dateRange->cacheKey(),
            now()->addMinutes(3),
            function () use ($dateRange) {
                $bookmarkCount = 0;
                if (Schema::hasTable('post_bookmarks')) {
                    $bookmarkCount = (int) DB::table('post_bookmarks')
                        ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                        ->count();
                }

                return [
                    'userCount' => User::role('user')->count(),
                    'newUsers' => User::role('user')
                        ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                        ->count(),
                    'onlineUsers' => collect(Cache::get('online_users', []))
                        ->filter(fn ($lastSeen) => now()->diffInMinutes($lastSeen) <= 2)
                        ->count(),
                    'postsInRange' => Post::query()
                        ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                        ->count(),
                    'livePosts' => Post::query()->where('status', 'LIVE')->count(),
                    'pendingReports' => PostReport::query()->pending()->count(),
                    'awaitingFeedback' => Feedback::query()
                        ->where('last_message_by', 'user')
                        ->whereNotIn('status', ['closed'])
                        ->count(),
                    'newFeedback' => Feedback::query()->where('status', 'new')->count(),
                    'communities' => Community::query()->count(),
                    'rollsReady' => PostVideo::query()
                        ->where('processing_status', 'completed')
                        ->whereNotNull('path')
                        ->count(),
                    'rollsInRange' => PostVideo::query()
                        ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                        ->count(),
                    'bookmarksInRange' => $bookmarkCount,
                    'blogPosts' => Blog::query()->count(),
                ];
            }
        );

        return view('admin.staff-home', array_merge($stats, [
            'dateRange' => $dateRange,
        ]));
    }

    public function testSubscription($levelId)
    {
        if (! app()->environment(['local', 'staging'])) {
            abort(404);
        }

        return redirect($this->korapayService->initiatePayment($levelId, 200));
    }

    public function verifyFlutterwaveAdminCharge(Request $request)
    {
        $reference = $request->query('tx_ref');
        $status = $request->query('status');

        if ($status == 'cancelled') {
            return redirect()->route('admin.home')->with('error', 'Subscription payment was cancelled.');
        }

        if ($status == 'successful' || $status == 'completed') {
            $transaction = Transaction::where('ref', $reference)->first();

            $this->transactionService->markProcessing($transaction, ['verification_attempted_at' => now()]);
            $level = Level::findOrFail($transaction->meta['level_id']);

            $this->upgradeSubscriptionService->upgradeSubscription(
                $transaction->user,
                $level,
                $transaction,
                ['verification_attempted_at' => now()]
            );

            Mail::to('solotob3@gmail.com')->send(new GeneralMail(
                (object) ['name' => 'Oluwatobi Solomon', 'email' => 'solotob3@gmail.com'],
                'Core Operation: Upgrade Processed Successfully',
                "Upgrade processed successfully. Transaction ref: {$reference} has been marked successful."
            ));

            return redirect()->route('admin.home')->with('success', 'Subscription payment was successful.');
        }

        return redirect()->route('admin.home')->with('error', 'Unknown payment status.');
    }

    protected function dailySignupChart(AdminDateRange $range): array
    {
        $rows = User::role('user')
            ->whereNotNull('email_verified_at')
            ->whereBetween('email_verified_at', [$range->start, $range->end])
            ->selectRaw('DATE(email_verified_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        return $this->fillDailySeries($range, $rows);
    }

    protected function dailyEngagementChart(AdminDateRange $range): array
    {
        $views = $this->dailyEventCounts(UserView::class, $range);
        $likes = $this->dailyEventCounts(UserLike::class, $range);
        $comments = $this->dailyEventCounts(UserComment::class, $range);
        $posts = $this->dailyEventCounts(Post::class, $range);

        $labels = [];
        $viewValues = [];
        $likeValues = [];
        $commentValues = [];
        $postValues = [];

        $cursor = $range->start->copy()->startOfDay();
        $endDay = $range->end->copy()->startOfDay();

        while ($cursor->lte($endDay)) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('M j');
            $viewValues[] = (int) ($views[$key] ?? 0);
            $likeValues[] = (int) ($likes[$key] ?? 0);
            $commentValues[] = (int) ($comments[$key] ?? 0);
            $postValues[] = (int) ($posts[$key] ?? 0);
            $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'views' => $viewValues,
            'likes' => $likeValues,
            'comments' => $commentValues,
            'posts' => $postValues,
            'total' => array_sum($viewValues) + array_sum($likeValues) + array_sum($commentValues),
            'postsTotal' => array_sum($postValues),
        ];
    }

    protected function dailyEventCounts(string $model, AdminDateRange $range): array
    {
        return $model::query()
            ->whereBetween('created_at', [$range->start, $range->end])
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->mapWithKeys(fn ($row) => [
                \Carbon\Carbon::parse($row->day)->format('Y-m-d') => (int) $row->total,
            ])
            ->all();
    }

    protected function fillDailySeries(AdminDateRange $range, $rows): array
    {
        $labels = [];
        $values = [];

        $cursor = $range->start->copy()->startOfDay();
        $endDay = $range->end->copy()->startOfDay();

        while ($cursor->lte($endDay)) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('M j');
            $values[] = (int) ($rows[$key] ?? 0);
            $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'total' => array_sum($values),
        ];
    }
}
