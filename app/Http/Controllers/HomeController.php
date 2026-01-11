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
use App\Models\Wallet;
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
        $wl = Wallet::where('user_id', auth()->user()->id)->first();
        $wl->currency = $request->currency;
        $wl->save();
        
        return redirect('timeline');
    }

   public function upgradeApi(){

        $url = request()->fullUrl();
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);


        $reference = $params['reference'];

        //verify Payment
     $res = verifyPaystackPayment($reference); 
        $cusEmail = $res['customer']['email'];
        $cusEmail = $res['metadata']['user_id'];
        $cusLevel = $res['metadata']['level'];
        $cusLevelId = $res['metadata']['level_id'];

        [$cusEmail, $cusLevel, $cusLevelId];
       
        //fetch Subscription
    //    $subData = fetchSubscription($cusEmail);

        
       $nextPaymentDate = Carbon::now()->addDays(30);
       
    //    Carbon::parse($subData['next_payment_date'])
    //     ->timezone(config('app.timezone')); 
       
      $user = Auth::user();
      $level =  Level::where('name', $cusLevel)->first();

      $regBonus = $level->reg_bonus; //in dollars
       $convertRegBonus = convertToBaseCurrency($regBonus, $user->wallet->currency);



        $updatedSub = UserLevel::updateOrCreate(
            [
                'user_id' => auth()->id(),
                
            ],
            [
                'level_id' => $level->id,
                'plan_name' => $cusLevel,
                'plan_code' => $cusLevelId,
                'subscription_code' => $cusLevelId,
                'email_token' => $cusLevelId,
                'start_date' => now(),
                'status' => 'active',
                'next_payment_date' => $nextPaymentDate,
            ]
        );

        if($updatedSub){

            $wl = Wallet::where('user_id', $user->id)->first();
            $wl->balance = $convertRegBonus;
            $wl->save();

            Transaction::create([
                'user_id' => $user->id,
                'ref' => $reference,
                'amount' => $res['amount']/100,
                'currency' => $user->wallet->currency,
                'status' =>  'successful',
                'type' => 'upgrade_purchase',
                'action' => 'Credit',
                'description' => $user->name.' upgraded to '.$cusLevel, 
                'meta' => null,
                'customer' => null
             ]);

            Transaction::create([
                'user_id' => $user->id,
                'ref' => $reference,
                'amount' => $convertRegBonus,
                'currency' => $user->wallet->currency,
                'status' =>  'successful',
                'type' => 'reg_bonus',
                'action' => 'Credit',
                'description' => 'Upgrade Bonus for '.$cusLevel, 
                'meta' => null,
                'customer' => null
             ]);

             return redirect('upgrade')->with('success', 'You Successfully upgraded to '.$cusLevel. ' your next payment is '. $nextPaymentDate);
        }
        
 
             

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
