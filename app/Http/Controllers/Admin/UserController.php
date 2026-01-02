<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function userList($level=null){
        $res = securityVerification();
        if($res == 'OK'){

            $users = User::with('activeLevel')
            ->role('user')
            ->byLevel($level)
            ->latest()
            ->paginate(100);

            return view('admin.user.userlist', ['users' => $users, 'level' => $level]);
        }
    }

    public function userInfo($id){
        $res = securityVerification();
        if($res == 'OK'){

            $user = User::find($id);
            $withrawals = Transaction::where('type', 'withdrawals')->where('user_id', $user->id)->sum('amount');
            $posts = Post::where('user_id', $user->id)->get();
            $level = $user?->activeLevel?->plan_name;

            return view('admin.user.user_info', ['user' => $user, 'withdrawals' => $withrawals, 'posts' => $posts,  'level' => $level]);
            
        }
    }

    public function processWalletCredit(Request $request){
        $res = securityVerification();
        if($res == 'OK'){

            $wallet= Wallet::where('user_id', $request->user_id)->first();
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
             $content = "Your promoter wallet has been credited with $".$request->amount;
             
             Mail::to($user->email)->send(new GeneralMail($user, $subject, $content));


             return back()->with('success', 'Wallet Credited');
        }
    }
}
