<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\AccessCode;
use App\Models\Level;
use App\Models\Payout;
use App\Models\Post;
use App\Models\SubscriptionStat;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Wallet;
use App\Models\WithdrawalMethod;
use App\Models\Withdrawals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function userList($level = null)
    {
        $res = securityVerification();
        if ($res == 'OK') {

            $users = User::with('activeLevel')
                ->role('user')
                ->byLevel($level)
                ->latest()
                ->paginate(100);

            return view('admin.user.userlist', ['users' => $users, 'level' => $level]);
        }
    }

    public function userSearch(Request $request)
    {
        $query = trim($request->input('query'));

        if (!$query) {
            return back()->with('error', 'Please enter a search term.');
        }

        $users = User::query()
            ->select('id', 'name', 'username', 'email', 'email_verified_at', 'created_at', 'heard')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('username', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->orderByRaw("
            CASE
                WHEN username = ? THEN 1
                WHEN name = ? THEN 2
                WHEN username LIKE ? THEN 3
                ELSE 4
            END
        ", [$query, $query, "%{$query}%"])
            ->paginate(20)
            ->withQueryString();

        return view('admin.user.search-result', compact('users', 'query'));
    }

    public function userInfo($id)
    {
        $res = securityVerification();
        if ($res == 'OK') {
            $levels = Level::all();
            $user = User::find($id);
            $withrawals = Transaction::whereIn('type', ['reg_bonus', 'reg_bonus_admin_assisted'])->where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();
            $posts = Post::where('user_id', $user->id)->get();
            $level = $user?->activeLevel?->plan_name;
            $access = AccessCode::where('email', $user->email)->latest()->first();
            $userLevel = Level::where('name', $level)->first();
            $withdrawalMethod = WithdrawalMethod::where('user_id', $user->id)->first();
            $lastMonth = now()->subMonth()->format('Y-m');
            $payouts = Payout::where('user_id', $user->id)
                ->where('month', $lastMonth)->first();
            //     ->latest()
            //     ->get();

            // $latestPayout = $payouts->first(); 


            return view('admin.user.user_info', [
                'user' => $user,
                'withdrawals' => $withrawals,
                'withdrawalMethod' => $withdrawalMethod,
                'posts' => $posts,
                'level' => $level,
                'levels' => $levels,
                'access' => $access,
                'userLevel' => $userLevel,
                'payouts'          => $payouts,
                // 'latestPayout'     => $latestPayout,
            ]);
        }
    }

    public function updateCurrency(Request $request)
    {

        $wall = Wallet::where('user_id', $request->user_id)->first();
        $wall->currency = $request->currency;
        $wall->save();

        WithdrawalMethod::where('user_id', $request->user_id)->delete();

        return back()->with('success', 'Account Currency Changed to : ' . $wall->currency);
    }

    public function upgradeProcess(Request $request)
    {
        $res = securityVerification();
        if ($res == 'OK') {
            $validation =  env('VALIDATION_CODE');
            if ($request->validationCode == $validation) {
                // return $request;

                $level = Level::find($request->level);
                $user = User::find($request->user_id);

                $nextPaymentDate = now()->addDays(30);
                UserLevel::updateOrCreate(
                    [
                        'user_id' => $request->user_id,

                    ],
                    [
                        'level_id' => $level->id,
                        'plan_name' => $level->name,
                        'plan_code' => $level->id,
                        'subscription_code' => $level->id,
                        'email_token' => $level->id,
                        'start_date' => now(),
                        'status' => 'active',
                        'next_payment_date' => $nextPaymentDate,
                    ]
                );
                $currency = $user->wallet->currency;
                $convertedAmount = convertToBaseCurrency($level->reg_bonus, $currency);

                $wl = Wallet::where('user_id', $user->id)->first();
                $wl->balance = $convertedAmount;
                $wl->save();

                $reference = generateTransactionRef();

                Transaction::create([
                    'user_id' => $user->id,
                    'ref' => $reference,
                    'amount' => $convertedAmount,
                    'currency' => $user->wallet->currency,
                    'status' =>  'successful',
                    'type' => 'upgrade_purchase_admin_assisted',
                    'action' => 'Credit',
                    'description' => $user->name . ' upgraded to ' . $level->name . ' by admin',
                    'meta' => null,
                    'customer' => null
                ]);



                SubscriptionStat::create([
                    'user_id'   => $user->id,
                    'level_id'  => $level->id,
                    'plan_name' => $level->name,
                    'amount'    => $convertedAmount,
                    'currency'  => $currency,
                    'start_date' => now(),
                    'end_date'  => $nextPaymentDate,
                ]);

                return back()->with('success', 'Upgrade Successful: ' . $level->name);
            }
        }
    }

    public function creditBonus($userId, $level)
    {


        if ($level == 'Creator' || $level == 'Influencer') {


            //  $exists = Transaction::where('user_id', $userId)
            //             ->whereIn('type', ['reg_bonus', 'reg_bonus_admin_assisted'])
            //             ->exists();

            //         if ($exists) {
            //             return back()->with('error', 'Bonus already exists for thr user');
            //         }




            $levelInfo = Level::where('name', $level)->first();
            $wl = Wallet::where('user_id', $userId)->first();

            $convertedAmount = convertToBaseCurrency($levelInfo->reg_bonus, $wl->currency);
            $wl->balance = $convertedAmount;
            $wl->save();

            $reference = time() . rand(999, 99999);
            Transaction::create([
                'user_id' => $userId,
                'ref' => $reference,
                'amount' => $convertedAmount,
                'currency' => $wl->currency,
                'status' =>  'successful',
                'type' => 'reg_bonus_admin_assisted',
                'action' => 'Credit',
                'description' => 'Upgrade Bonus by admin for ' . $levelInfo->name,
                'meta' => null,
                'customer' => null
            ]);

            return back()->with('success', 'Upgrade Bonus added for ' . $levelInfo->name . ' at ' . $wl->currency .  $convertedAmount);
        } else {
            return back()->with('error', 'Upgrade bonus is allowed for only Creator and Influencer ');
        }
    }

    public function processWalletCredit(Request $request)
    {
        $res = securityVerification();
        if ($res == 'OK') {

            $wallet = Wallet::where('user_id', $request->user_id)->first();
            $wallet->promoter_balance += $request->amount;
            $wallet->save();

            Transaction::create([
                'user_id' => $request->user_id,
                'ref' => time(),
                'amount' => $request->amount,
                'currency' => 'USD',
                'status' =>  'succesful',
                'type' => 'promoter_credit',
                'action' => 'Credit',
                'description' => 'Promoter Wallet Credited',
                'meta' => null,
                'customer' => null
            ]);

            $user = User::where('id', $request->user_id)->first();
            $subject = 'Promoter Wallet Credited';
            $content = "Your promoter wallet has been credited with $" . $request->amount;

            Mail::to($user->email)->send(new GeneralMail($user, $subject, $content));


            return back()->with('success', 'Wallet Credited');
        }
    }

    public function postList($id)
    {
        $user = User::find($id);
        $posts = Post::where('user_id', $id)->get();
        return view('admin.user.posts', ['posts' => $posts, 'user' => $user]);
    }

    public function transactionList($id)
    {
        $user = User::find($id);
        $transactions = Transaction::where('user_id', $id)->orderBy('created_at', 'DESC')->get();
        return view('admin.user.transactions', ['transactions' => $transactions, 'user' => $user]);
    }

    public function bankInformation()
    {
        $withdrawals = WithdrawalMethod::orderBy('created_at', 'desc')->paginate(50);
        return view('admin.user.bank_info', ['withdrawals' => $withdrawals]);
    }
}
