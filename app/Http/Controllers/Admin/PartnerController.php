<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerSlot;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function list(){

        $res = securityVerification();
        if($res === 'OK'){
            $partners = Partner::all();
            return view('admin.partner.list', ['partners' => $partners]);

         }

    }

    public function viewPartnerActivate($id){
        $res = securityVerification();
        if($res === 'OK'){
            $part = Partner::find($id);
            $part->status = true;
            $part->save();
            PartnerSlot::create(['partner_id' => $id, 'beginner' => 0, 'creator' => 0, 'influencer' => 0]);
            return back()->with('success', 'Partner Activated');
         }
    }

    public function partnerInfo($id){
        $res = securityVerification();
        if($res === 'OK'){ 
            return $id;
        }
    }

    public function partnerPayments(){
        $res = securityVerification();
        if($res === 'OK'){ 
            $transactions = Transaction::where(['type' => 'slot_purchase', 'status' => 'successful'])->get();
            return view('admin.partner.view_transaction', ['transactions' => $transactions]);
           
        }
    }

    public function validateAgentTransaction($id){
        $res = securityVerification();
        if($res === 'OK'){
             $transaction = Transaction::where('ref', $id)->first();
            
            @$partnerId = $transaction->user->partner->id;
            @$slot = PartnerSlot::where('partner_id', $partnerId)->first();
            return view('admin.partner.view_transaction_info', ['transaction' => $transaction, 'slot' => $slot]);

         }
    }
}
