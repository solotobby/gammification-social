<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\AccessCode;
use App\Models\Level;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use App\Services\FlutterwavePaymentService;
use App\Services\KorapayService;
use App\Services\TransactionService;
use App\Services\UpgradeSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    protected FlutterwavePaymentService $flutterwavePaymentService;
    protected TransactionService $transactionService;
    protected UpgradeSubscriptionService $upgradeSubscriptionService;
    protected KorapayService $korapayService;

    public function __construct(FlutterwavePaymentService $flutterwavePaymentService, TransactionService $transactionService, UpgradeSubscriptionService $upgradeSubscriptionService, KorapayService $korapayService)
    {
        $this->flutterwavePaymentService = $flutterwavePaymentService;
        $this->transactionService = $transactionService;
        $this->upgradeSubscriptionService = $upgradeSubscriptionService;
        $this->korapayService = $korapayService;
    }

    public function home()
    {
        $res = securityVerification();
        if ($res == 'OK') {

            

            $userCount = User::role('user')->orderBy('created_at', 'desc')->count();
            // // $partnerCount = Partner::where('status', true)->count();
            // // $accesscodeCount = AccessCode::all()->count();
            $tx = Transaction::where(['status' => 'successful', 'status' => 'allocated'])->get(['currency', 'amount']);
            $usd = $tx->where('currency', 'USD')->sum('amount');
            $naira = $tx->where('currency', 'NGN')->sum('amount');

            $nairaInDollar = $naira / 1500;

            $rev = $nairaInDollar + $usd;

            // $posts = Post::query()->get(['views', 'views_external', 'likes', 'likes_external', 'comments', 'comment_external']);
            // // $levelCounts = UserLevel::where('status', 'active')
            // //     ->where('next_payment_date', '>', now())
            // //     ->groupBy('plan_name')
            // //     ->select('plan_name', DB::raw('COUNT(user_id) as total'))
            // //     ->get();

            $levelCounts = Cache::remember(
                'dashboard.plan-counts',
                now()->addMinutes(5),
                fn() => UserLevel::query()
                    ->active()
                    ->valid()
                    ->selectRaw('plan_id, COUNT(*) as total')
                    ->groupBy('plan_id')
                    ->with('plan:id,name')
                    ->get()
            );


            // $onlineUsers = collect(Cache::get('online_users', []))
            //     ->filter(fn($lastSeen) => now()->diffInMinutes($lastSeen) <= 2)
            //     ->count();

            // $levelId = Level::where('name', 'Creator')->first()->id;

            return [
                // 'levelId' => $levelId,
                'userCount' => $userCount,
                // 'partnerCount' => $partnerCount,
                // 'accesscodeCount' => $accesscodeCount,
                'rev' => $rev,
                // 'posts' => $posts,
                'levelCounts' => $levelCounts,
                // 'onlineUsers' => $onlineUsers
            ];

            // return view('admin.home', [
            //     'levelId' => $levelId,
            //     'userCount' => $userCount,
            //     'partnerCount' => $partnerCount,
            //     'accesscodeCount' => $accesscodeCount,
            //     'rev' => $rev,
            //     'posts' => $posts,
            //     // 'levelCounts' => $levelCounts,
            //     'onlineUsers' => $onlineUsers
            // ]);
        }
    }

    public function testSubscription($levelId)
    {
        $url = $this->korapayService->initiatePayment($levelId, 200);
        // $url = $this->flutterwavePaymentService->createAdminCharge($levelId);
        return redirect($url);
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

            $this->upgradeSubscriptionService->upgradeSubscription($transaction->user, $level, $transaction, ['verification_attempted_at' => now()]);


            $subject = 'Core Operation: Upgrade Processed Successfully';
            $content = "Upgrade processed successfully for event: {$request['event']}. Transaction ref: {$reference} has been marked successful and subscription upgraded.";

            Mail::to('solotob3@gmail.com')
                ->send(new GeneralMail(
                    (object)[
                        'name' => 'Oluwatobi Solomon',
                        'email' => 'solotob3@gmail.com'
                    ],
                    $subject,
                    $content
                ));



            return redirect()->route('admin.home')->with('success', 'Subscription payment was successful.');
        }

        return redirect()->route('admin.home')->with('error', 'Unknown payment status.');
    }
}
