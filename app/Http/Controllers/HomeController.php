<?php

namespace App\Http\Controllers;

use App\Models\AccessCode;
use App\Models\Level;
use App\Models\LevelPlanId;
use App\Models\LoginPoint;
use App\Models\PartnerSlot;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            // return redirect()->route('logout');
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

    // public function accessCodeVerification(Request $request){
    //     access_code
    // }

    public function completeOnboarding(Request $request){
      
        $user = User::where(['id'=>auth()->user()->id])->first();
        $user->is_onboarded = true;
        $user->heard = $request->heard;
        $user->save();
        return redirect('timeline');
    }

//     public function validateApi(){
//          $url = request()->fullUrl();
//        $url_components = parse_url($url);
//        parse_str($url_components['query'], $params);
//        if($params['status'] == 'cancelled'){
//            return redirect('partner');
//        }
//        $ref = $params['transaction_id']; 

//        $response = $this->verifyFlutterwavePayment($ref);

//        if($response['status'] == 'success'){
          
            
//         $transaction= Transaction::create([
//                'user_id' => auth()->user()->id,
//                'ref' => $response['data']['tx_ref'],
//                'amount' => $response['data']['amount'],
//                'currency' => $response['data']['currency'],
//                'status' =>  $response['data']['status'],
//                'type' => 'slot_purchase',
//                'action' => 'Credit',
//                'description' => 'Agent payment by '.auth()->user()->name, 
//                'meta' => json_encode($response['data']['meta']),
//                'customer' => json_encode($response['data']['customer'])
//             ]);

//             return redirect('partner')->with('success', 'Payment received. Your slot will be allocated in less than 3 hours');

//         }

//    }

   public function upgradeApi(){

        $url = request()->fullUrl();
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);


        $reference = $params['reference'];

        //verify Payment

        $res = verifyPaystackPayment($reference); 
        $cusEmail = $res['customer']['email'];
       

        
        //fetch Subscription
        $subData = fetchSubscription($cusEmail);

        
       $nextPaymentDate = Carbon::parse($subData['next_payment_date'])
        ->timezone(config('app.timezone')); // optional
       

       $level =  Level::where('name', $subData['plan']['name'])->first();

        $updatedSub = UserLevel::updateOrCreate(
            [
                'user_id' => auth()->id(),
                
            ],
            [
                'level_id' => $level->id,
                'plan_name' => $subData['plan']['name'],
                'plan_code' => $subData['plan']['plan_code'],
                'subscription_code' => $subData['subscription_code'],
                'email_token' => $subData['email_token'],
                'start_date' => now(),
                'status' => $subData['status'],
                'next_payment_date' => $nextPaymentDate,
            ]
        );

        $transaction= Transaction::create([
                'user_id' => auth()->user()->id,
                'ref' => $reference,
                'amount' => $subData['amount']/100,
                'currency' => $subData['plan']['currency'],
                'status' =>  'successful',
                'type' => 'upgrade_purchase',
                'action' => 'Credit',
                'description' => auth()->user()->name.' upgraded to '.$subData['plan']['name'], 
                'meta' => null,
                'customer' => null
             ]);
 
             return redirect('upgrade')->with('success', 'You Successfully upgraded to '.$subData['plan']['name']. ' your next payment is '. $nextPaymentDate);
 
 

   }

   public function createSubscription($levelId){
        return upgradeLevel($levelId);
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
