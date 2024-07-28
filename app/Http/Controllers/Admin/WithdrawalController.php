<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawals;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function withdrawalList(){
        $res = securityVerification();
        if($res == 'OK'){
    
            $list = Withdrawals::query()->get();
            return view('admin.withdrawal.list', ['lists' => $list]);
            
         }
    }

    public function withdrawalListUpdate($id){
        $res = securityVerification();
        if($res == 'OK'){
            $update = Withdrawals::where('id', $id)->first();
            $update->status = 'Paid';
            $update->save();
            
            $method = str_replace('_', " ", $update->method);

            Transaction::create([
                'user_id' => $update->user_id,
                'ref' => time(),
                'amount' => $update->amount,
                'currency' => 'USD',
                'status' =>  'successful',
                'type' => 'withdrawals',
                'action' => 'Debit',
                'description' => ucfirst($update->wallet_type).' withdrawal via '.ucwords($method), 
                'meta' => null,
                'customer' => null
            ]);

            return back()->with('success', 'Withdrawal Updated');

        }
    }
}
