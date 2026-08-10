<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\Level;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserLevel;
use App\Models\UserLike;
use App\Models\UserView;
use App\Services\FlutterwavePaymentService;
use App\Services\KorapayService;
use App\Services\TransactionService;
use App\Services\UpgradeSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

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
    ) {
        $this->flutterwavePaymentService = $flutterwavePaymentService;
        $this->transactionService = $transactionService;
        $this->upgradeSubscriptionService = $upgradeSubscriptionService;
        $this->korapayService = $korapayService;
    }

    public function home()
    {
        if (securityVerification() !== 'OK') {
            abort(404);
        }

        $stats = Cache::remember('admin.dashboard.stats.v4', now()->addMinutes(3), function () {
            $revenue = Transaction::query()
                ->whereIn('status', ['successful', 'allocated'])
                ->selectRaw("COALESCE(SUM(CASE WHEN currency = 'USD' THEN amount ELSE 0 END), 0) as usd")
                ->selectRaw("COALESCE(SUM(CASE WHEN currency = 'NGN' THEN amount ELSE 0 END), 0) as ngn")
                ->first();

            $ngnRate = max(1, (float) config('services.exchange.ngn_usd_rate', 1500));
            $totalRevenueUsd = (float) ($revenue->usd ?? 0) + ((float) ($revenue->ngn ?? 0) / $ngnRate);

            $postStats = Post::query()
                ->selectRaw('COUNT(*) as total_posts')
                ->selectRaw('COALESCE(SUM(views), 0) as total_views')
                ->selectRaw('COALESCE(SUM(likes), 0) as total_likes')
                ->selectRaw('COALESCE(SUM(comments), 0) as total_comments')
                ->first();

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
                'activeToday' => fetchActive(),
                'activeWeek' => fetchActive(7),
                'activeMonth' => fetchActive(30),
                'totalRevenueUsd' => $totalRevenueUsd,
                'postStats' => $postStats,
                'newUsersWeek' => User::role('user')->where('created_at', '>=', now()->subDays(7))->count(),
                'signupChart' => $this->dailySignupChart(),
                'engagementChart' => $this->dailyEngagementChart(),
            ];
        });

        $creatorLevel = Level::query()->where('name', 'Creator')->first();

        return view('admin.home', array_merge($stats, [
            'levelId' => $creatorLevel?->id,
            'showTestPayment' => app()->environment(['local', 'staging']) && $creatorLevel,
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

    protected function dailySignupChart(int $days = 30): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = User::role('user')
            ->whereNotNull('email_verified_at')
            ->where('email_verified_at', '>=', $start)
            ->selectRaw('DATE(email_verified_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        return $this->fillDailySeries($start, $days, $rows);
    }

    protected function dailyEngagementChart(int $days = 30): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $views = $this->dailyEventCounts(UserView::class, $start);
        $likes = $this->dailyEventCounts(UserLike::class, $start);
        $comments = $this->dailyEventCounts(UserComment::class, $start);
        $posts = $this->dailyEventCounts(Post::class, $start);

        $labels = [];
        $viewValues = [];
        $likeValues = [];
        $commentValues = [];
        $postValues = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('M j');
            $viewValues[] = (int) ($views[$key] ?? 0);
            $likeValues[] = (int) ($likes[$key] ?? 0);
            $commentValues[] = (int) ($comments[$key] ?? 0);
            $postValues[] = (int) ($posts[$key] ?? 0);
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

    protected function dailyEventCounts(string $model, $start): array
    {
        return $model::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->mapWithKeys(fn ($row) => [
                \Carbon\Carbon::parse($row->day)->format('Y-m-d') => (int) $row->total,
            ])
            ->all();
    }

    protected function fillDailySeries($start, int $days, $rows): array
    {
        $labels = [];
        $values = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('M j');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'total' => array_sum($values),
        ];
    }
}
