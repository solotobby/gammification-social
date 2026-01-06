<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\AccessCode;
use App\Models\Level;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Wallet;
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
            $withrawals = Transaction::where('type', 'withdrawals')->where('user_id', $user->id)->sum('amount');
            $posts = Post::where('user_id', $user->id)->get();
            $level = $user?->activeLevel?->plan_name;
            $access = AccessCode::where('email', $user->email)->latest();

            return view('admin.user.user_info', ['user' => $user, 'withdrawals' => $withrawals, 'posts' => $posts,  'level' => $level, 'levels' => $levels, 'access' => $access]);
        }
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
                $updatedSub = UserLevel::updateOrCreate(
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
                $convertedAmount = convertToBaseCurrency($level->amount, $currency);

                $reference = time() . rand(999, 9999);

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'ref' => $reference,
                    'amount' => $convertedAmount,
                    'currency' => $user->wallet->currency,
                    'status' =>  'successful',
                    'type' => 'upgrade_purchase_admin_assisted',
                    'action' => 'Credit',
                    'description' => $user->name . ' upgraded to ' . $level->name .' by admin',
                    'meta' => null,
                    'customer' => null
                ]);


                 return back()->with('success', 'Upgrade Successful: '.$level->name);
            }
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
}
