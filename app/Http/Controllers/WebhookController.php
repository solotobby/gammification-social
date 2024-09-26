<?php

namespace App\Http\Controllers;

use App\Models\ApiResponse;
use App\Models\Partner;
use App\Models\PartnerSlot;
use App\Models\Transaction;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request){

        $event = $request['event'];

        ApiResponse::create(['response' => $event]);

        if($event == 'charge.success'){
            $amount = $request['data']['amount']/100;
            $status = $request['data']['status'];
            $reference = $request['data']['reference'];
            $channel = $request['data']['channel'];
            $currency = $request['data']['currency'];
            $email = $request['data']['customer']['email'];
            $customer_code = $request['data']['customer']['customer_code'];

            $dollarEqv = $amount/1500; //conver to dollar using base rate of 1500
            //fetch partner information
            $partner = Partner::where('customer_code', $customer_code)->first();
            $partner->balance_dollar += number_format($dollarEqv,1);
            $partner->balance_naira += $amount;
            $partner->save();
// '25f5b37e-aff2-44df-9c21-3ad565df09db',
            Transaction::create([
                'user_id' => $partner->user_id,
                'ref' => $reference,
                'amount' =>$amount,
                'currency' => $currency,
                'status' =>  $status,
                'type' => 'partner_wallet_topup',
                'action' => 'Credit',
                'description' => $partner->user->name .' topped up partner wallet', 
                'meta' =>$event,
                'customer' => null
             ]);
             return response()->json(['status' => 'success'], 200);

        }else{

            return response()->json(['status' => 'error'], 500);
        }
        

        // if($event == 'charge.completed'){
            
        //     Webhook::create(['event' => $event, 'content' => $request]);
        //     $transactionRef = $request['data']['tx_ref']; //get tx ref from webhook

        //     //validate transaction
        //     $transaction = Transaction::where('ref', $transactionRef)->first();

        //     $string = $transaction->meta;
        //     $data = json_decode($string, true);
        //     $package = htmlspecialchars($data['package']);
        //     $slotNumber = htmlspecialchars($data['number_of_slot']);

        //     //get per info
        //     $partnerId = @$transaction->user->partner->id;
        //     $partner = PartnerSlot::where('partner_id', $partnerId)->first();
           

        //     if($partner->status == true){
        //         if($package == 'Influencer'){
        //             $partner->influencer += $slotNumber;
        //             $partner->save();

        //             $transaction->status = 'allocated';
        //             $transaction->save();

        //             // return response()->json(['status' => 'success']);
    
        //             // $this->updateTx($request->tx_ref);
    
        //             // return back()->with('success', 'Influencer slot Updated successfully');
    
        //         } elseif($package == 'Creator'){
        //             $partner->creator += $slotNumber;
        //             $partner->save();

        //             $transaction->status = 'allocated';
        //             $transaction->save();

        //             // return response()->json(['status' => 'success']);
    
        //             // $this->updateTx($request->tx_ref);
    
        //             // return back()->with('success', 'Creator slot Updated successfully');
        //         }else{
    
        //             $partner->beginner += $slotNumber;
        //             $partner->save();

        //             $transaction->status = 'allocated';
        //             $transaction->save();

        //             // return response()->json(['status' => 'success']);
    
        //             // $this->updateTx($request->tx_ref);
    
        //             // return back()->with('success', 'Beginner slot Updated successfully');
    
        //         }
        //     }


            
        // }


    }
}
