<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawals;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function withdrawalList(){
        $res = securityVerification();
        if($res == 'OK'){

            $list = Withdrawals::where('status', 'Queued')->get();
            return view('admin.withdrawal.list', ['lists' => $list]);
            
         }
    }
}
