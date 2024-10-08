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
    public function userList(){
        $res = securityVerification();
        if($res == 'OK'){
            $users = User::role('user')->orderBy('created_at', 'desc')->get();
            return view('admin.user.userlist', ['users' => $users]);
        }
    }

    public function userInfo($id){
        $res = securityVerification();
        if($res == 'OK'){

            $user = User::find($id);
            $withrawals = Transaction::where('type', 'withdrawals')->where('user_id', $user->id)->sum('amount');
            $posts = Post::where('user_id', $user->id)->get();
            $level = $user->level;
           

            // "id": "e9d17386-7649-4459-88b9-99a310213030",
            // "name": "Beginner",
            // "amount": "5",
            // "reg_bonus": "1",
            // "ref_bonus": "1",
            // "min_withdrawal": "20",
            // "earning_per_view": "1",
            // "earning_per_like": "0.5",
            // "earning_per_comment": "0.5",

            $perCommentAmount = 0;
            if($user->level->name == 'Influencer'){
                $perCommentAmount = 0.20; //40 //30
             }elseif($user->level->name == 'Creator'){
                $perCommentAmount = 0.15; //30 // 25
             }else{
                $perCommentAmount = 0.10; //25 //20
             }

            // if(auth()->user()->level->name == 'Influencer'){
            //     $perCommentAmount = 0.08; //40 //30
            //  }elseif(auth()->user()->level->name == 'Creator'){
            //     $perCommentAmount = 0.07; //30 // 25
            //  }else{
            //     $perCommentAmount = 0.05; //25 //20
            //  }

             $singleViewExternal = 1/5000;

            return view('admin.user.user_info', ['user' => $user, 'withdrawals' => $withrawals, 'posts' => $posts, 
            'perCommentAmount' => $perCommentAmount, 'singleViewExternal' => $singleViewExternal]);
            
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
