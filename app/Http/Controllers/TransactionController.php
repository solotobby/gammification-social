<?php

namespace App\Http\Controllers;

use App\Models\PartnerSlot;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function viewAgentTransaction(){
        $transactions = Transaction::where(['type' => 'slot_purchase', 'status' => 'successful'])->get();
        return view('agent_transaction', ['transactions' => $transactions]);
    }

    public function validateAgentTransaction($id){
        $transaction = Transaction::where('ref', $id)->first();
        @$partnerId = $transaction->user->partner->id;
        @$slot = PartnerSlot::where('partner_id', $partnerId)->first();
        return view('agent_info', ['transaction' => $transaction, 'slot' => $slot]);
    }

    public function validateSlot(Request $request){
          
        $partner = PartnerSlot::find($request->partner_id);

        if($partner->status == true){
            if($request->package == 'Influencer'){
                $partner->influencer += $request->slot_number;
                $partner->save();

                $this->updateTx($request->tx_ref);

                return back()->with('success', 'Influencer slot Updated successfully');

            } elseif($request->package == 'Creator'){
                $partner->creator += $request->slot_number;
                $partner->save();

                $this->updateTx($request->tx_ref);

                return back()->with('success', 'Creator slot Updated successfully');
            }else{

                $partner->beginner += $request->slot_number;
                $partner->save();

                $this->updateTx($request->tx_ref);

                return back()->with('success', 'Beginner slot Updated successfully');

            }
        }
    }

    private function updateTx($ref){
        $tx = Transaction::where('ref',$ref)->first();
        $tx->status = 'allocated';
        $tx->save();

        return $tx;
    }
}
