<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessCode;
use App\Models\Partner;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function home(){
        $res = securityVerification();
        if($res == 'OK'){
            
            $userCount = User::role('user')->orderBy('created_at', 'desc')->count();
            $partnerCount = Partner::where('status', true)->count();
            $accesscodeCount = AccessCode::all()->count();
            $tx = Transaction::where(['status' => 'successful', 'status'=>'allocated'])->get();
            $usd = $tx->where('currency', 'USD')->sum('amount');
            $naira = $tx->where('currency', 'NGN')->sum('amount');

            $nairaInDollar = $naira/1500;

            $rev= $nairaInDollar+$usd;

            return view('admin.home', [
                'userCount' => $userCount, 
                'partnerCount' =>$partnerCount, 
                'accesscodeCount' => $accesscodeCount,
                'rev' => $rev
            ]);
        }
       
    }

    
}
