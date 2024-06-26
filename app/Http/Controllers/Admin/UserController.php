<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

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
            return view('admin.user.user_info', ['user' => $user]);
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

             return back()->with('success', 'Wallet Credited');
        }
    }
}
