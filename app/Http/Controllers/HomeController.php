<?php

namespace App\Http\Controllers;

use App\Models\LoginPoint;
use App\Models\PartnerSlot;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            // return 'admin';
            return redirect()->route('admin.home');
        }else{
            $this->loginPoints(auth()->user());
            return redirect('timeline');
          
            // return redirect()->route('user.home');
        }
       
        // return redirect('timeline');
        // return view('home');
    }

    public function userHome(){
        $this->loginPoints(auth()->user());
        return view('user.home');
    }

    public static function loginPoints($user){
        
        $date = \Carbon\Carbon::today()->toDateString();
        
        $check = LoginPoint::where('user_id', $user->id)->where('date', $date)->first();
        
        if(!$check)
        {
            LoginPoint::create(['user_id' => $user->id, 'date' => $date, 'point' => '20']);
        }
    }

    public function completeOnboarding(){
        $user = User::where(['id'=>auth()->user()->id])->first();
        $user->is_onboarded = true;
        $user->save();
        return redirect('timeline');
    }

    public function validateApi(){
         $url = request()->fullUrl();
       $url_components = parse_url($url);
       parse_str($url_components['query'], $params);
       if($params['status'] == 'cancelled'){
           return redirect('partner');
       }
       $ref = $params['transaction_id']; 

       $response = $this->verifyFlutterwavePayment($ref);

       if($response['status'] == 'success'){
          
            
        $transaction= Transaction::create([
               'user_id' => auth()->user()->id,
               'ref' => $response['data']['tx_ref'],
               'amount' => $response['data']['amount'],
               'currency' => $response['data']['currency'],
               'status' =>  $response['data']['status'],
               'type' => 'slot_purchase',
               'action' => 'Credit',
               'description' => 'Agent payment by '.auth()->user()->name, 
               'meta' => json_encode($response['data']['meta']),
               'customer' => json_encode($response['data']['customer'])
            ]);

            if($transaction->status == 'successful'){

            // return $tx;

            $string = $transaction->meta;
            $data = json_decode($string, true);
            $package = htmlspecialchars($data['package']);
            $slotNumber = htmlspecialchars($data['number_of_slot']);

            // return [$package, $slotNumber];

             $partnerId = @$transaction->user->partner->id;

             $partner = PartnerSlot::where('partner_id', $partnerId)->first();

             if($partner->status == true){
                if($package == 'Influencer'){
                    $partner->influencer += $slotNumber;
                    $partner->save();

                    $transaction->status = 'allocated';
                    $transaction->save();

                    // return response()->json(['status' => 'success']);
    
                    // $this->updateTx($request->tx_ref);
    
                    // return back()->with('success', 'Influencer slot Updated successfully');
    
                } elseif($package == 'Creator'){
                    $partner->creator += $slotNumber;
                    $partner->save();

                    $transaction->status = 'allocated';
                    $transaction->save();

                    // return response()->json(['status' => 'success']);
    
                    // $this->updateTx($request->tx_ref);
    
                    // return back()->with('success', 'Creator slot Updated successfully');
                }else{
    
                    $partner->beginner += $slotNumber;
                    $partner->save();

                    $transaction->status = 'allocated';
                    $transaction->save();

                    // return response()->json(['status' => 'success']);
    
                    // $this->updateTx($request->tx_ref);
    
                    // return back()->with('success', 'Beginner slot Updated successfully');
    
                }
            }

            return redirect('partner')->with('success', 'Payment received. Your slot will be allocated in less than 3 hours');

            }else{
                return redirect('partner')->with('error', 'Payment already processed!');
            }




           
       }

       

       // return $response['status'];
       // if($response['status'] == 'success'){
       //      $name = $response['data']['customer']['name'];
       //      $email = $response['data']['customer']['email'];
       //      $code = $this->generateCode(7);
       //     //  return [$name, $email, $this->generateCode(7)];
       //     $chekIfNotRedeemed = AccessCode::where('email', $email)->where('tx_id', $ref)->where('is_active', true)->first();
       //     if($chekIfNotRedeemed){
       //         return redirect('error');
       //     }
       //     AccessCode::create(['tx_id' => $ref,'name' => $name, 'email' => $email, 'amount' => '6', 'code' => $code]);
       //     //send mail to the above email
          
       //     return redirect('success');
       //     // return 'ok';

       // }else{
       //     // return 'not ok';
       //     return redirect('error');
       // }
      
   }


   public function verifyFlutterwavePayment($ref){
        
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('FL_SECRET_KEY')
        ])->get('https://api.flutterwave.com/v3/transactions/'.$ref.'/verify')->throw();

        return json_decode($res->getBody()->getContents(), true);
    }
}
