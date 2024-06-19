<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerSlot;
use App\Models\Transaction;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request){

        $event = $request["event"];
        Webhook::create(['event' => $event, 'content' => $request]);

        if($event == 'charge.completed'){

            $transactionRef = $request['data']['tx_ref']; //get tx ref from webhook

            //validate transaction
            $transaction = Transaction::where('ref', $transactionRef)->first();

            $string = $transaction->meta;
            $data = json_decode($string, true);
            $package = htmlspecialchars($data['package']);
            $slotNumber = htmlspecialchars($data['number_of_slot']);

            //get per info
            $partnerId = @$transaction->user->partner->id;
            $partner = PartnerSlot::find($partnerId);//where('partner_id', $partnerId)->first();
            // $partner = Partner::where('user_id', $transaction->user_id)->first();

            if($partner->status == true){
                if($package == 'Influencer'){
                    $partner->influencer += $slotNumber;
                    $partner->save();

                    $transaction->status = 'allocated';
                    $transaction->save();

                    return response()->json(['status' => 'success']);
    
                    // $this->updateTx($request->tx_ref);
    
                    // return back()->with('success', 'Influencer slot Updated successfully');
    
                } elseif($package == 'Creator'){
                    $partner->creator += $slotNumber;
                    $partner->save();

                    $transaction->status = 'allocated';
                    $transaction->save();

                    return response()->json(['status' => 'success']);
    
                    // $this->updateTx($request->tx_ref);
    
                    // return back()->with('success', 'Creator slot Updated successfully');
                }else{
    
                    $partner->beginner += $slotNumber;
                    $partner->save();

                    $transaction->status = 'allocated';
                    $transaction->save();

                    return response()->json(['status' => 'success']);
    
                    // $this->updateTx($request->tx_ref);
    
                    // return back()->with('success', 'Beginner slot Updated successfully');
    
                }
            }


            
        }


    }
}
