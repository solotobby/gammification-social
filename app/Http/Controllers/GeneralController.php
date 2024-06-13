<?php

namespace App\Http\Controllers;

use App\Mail\AccessCodeMail;
use App\Models\AccessCode;
use App\Models\Level;
use App\Models\Partner;
use App\Models\PartnerSlot;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;

class GeneralController extends Controller
{
    public function accessCode($level){
        if($level == 'beginner' || $level == 'creator' || $level == 'influencer' ){

            if($level == 'beginner'){
                $amountDollar = '5';
                $amountNaira = '7,500';
                $nairaLink = 'https://flutterwave.com/pay/ohd7jfk6wgzq';
                $dollarLink = 'https://flutterwave.com/pay/2o3kzkdj0shm';
            }elseif($level == 'creator'){
                $amountDollar = '10';
                $amountNaira = '15,000';
                $nairaLink = 'https://flutterwave.com/pay/5lzof7tt5ykj';
                $dollarLink = 'https://flutterwave.com/pay/elba42t3nw7m';
            }else{
                $amountDollar = '20';
                $amountNaira = '30,000';
                $nairaLink = 'https://flutterwave.com/pay/kljctf1pziei';
                $dollarLink = 'https://flutterwave.com/pay/4dhkdzur56fz';
            }


            return view('get_access_code', [
                        'level' => $level, 
                        'amountDollar' => $amountDollar, 
                        'amountNaira' => $amountNaira,
                        'nairalink' => $nairaLink,
                        'dollarlink' => $dollarLink
                    ]);
        }else{
            return redirect('/');
        }
       
    }

    public function ipConfig(){
        
        return ipLocation();
    }

    public function validateCode(){
        $level = Level::all();
        return view('send_access_code', ['levels' => $level]);
    }

    public function processValidateCode(Request $request){
        
        // Mail::to('support@payhankey.com')->send(new AccessCodeMail());
        // return [$request->validationCode, env('LOG_CHANNEL')];

        if($request->validationCode == 'LONZETY'){
            $code = $this->generateCode(7);
           
            $ref = time();
            $level = Level::where('id', $request->level)->first();
            $chekIfNotRedeemed = AccessCode::where('email', $request->email)->where('tx_id', $ref)->where('is_active', true)->first();
            
            Mail::to($request->email)->send(new AccessCodeMail($code));

            if($chekIfNotRedeemed){
                return redirect('error');
            }

            AccessCode::create(['tx_id' => $ref,'name' =>$level->name, 'email' => $request->email, 'amount' => $level->amount, 'code' => $code, 'level_id' => $level->id]);
            
            return redirect('success');

        }else{
            return 'no show';
        }
    }

    public function processAccessCode(Request $request){

        $payload = [
            "tx_ref"=> Str::random(16),
            "amount"=> "100",
            "currency"=> "USD",
            "redirect_url"=> url('validate/api'),//"https://webhook.site/9d0b00ba-9a69-44fa-a43d-a82c33c36fdc",
            "customer"=> [
                "email"=> $request->email,
                "name"=> $request->name
            ],
            "customizations"=> [
                "title"=> "One-time Access Code Fee",
                // "logo"=> "http://www.piedpiper.com/app/themes/joystick-v27/images/logo.png"
            ]
                
          
        ];

       $url = $this->initiateFlutterwavePayment($payload);
        return redirect($url);
    }

    public function initiateFlutterwavePayment($payload){
        
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('FL_SECRET_KEY')
        ])->post('https://api.flutterwave.com/v3/payments', $payload)->throw();

        return json_decode($res->getBody()->getContents(), true)['data']['link'];
    }

    

   

    public function generateCode($number){
        $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $code='';

        for($i=0;$i<$number;$i++){
           $code .= $alph[rand(0, 35)];
        }

        return $code;
    }

    public function success(){
        return 'Payment Successful';
    }

    public function error(){
        return 'Error processing payment';
    }

    public function showPost($id){
        
        $timeline = Post::with(['postComments' => function($query) {
            $query->paginate(15); // Load initial 5 comments
        }])->where('id', $id)->firstOrFail();

        $timeline->views_external += 1;
        $timeline->save(); 

        return view('showpost', ['timeline' => $timeline] );
    }

    public function partner(Request $request){
        
        $validated = $request->validate([
            'email' => 'bail|required|email|unique:partners|max:255',
            'name' => 'required|string',
            'phone' => 'required|numeric',
            'identification' => 'required|string',
            'country' => 'required|string',
        ]);

        Partner::create(['name' => $validated['name'], 'email' => $validated['email'], 'phone' => $validated['phone'], 'identification' => $validated['identification'], 'country' => $validated['country']]);

        return back()->with('success', 'You have successfully applied to become a Partner on Payhankey. An email has been sent to you for a short video call');

    }

    public function viewPartner(){
        $partners = Partner::all();//->firstOrFail();
        // $codes = AccessCode::where(['partner_id'=> $id, 'is_active' => true])->get();
        return view('view_partner', ['partners' => $partners]);
    }

    public function viewPartnerActivate($id){
        $part = Partner::find($id);
        $part->status = true;
        $part->save();

        PartnerSlot::create(['partner_id' => $id, 'beginner' => 0, 'creator' => 0, 'influencer' => 0]);

        return redirect('partners/listed/lots');
    }


    public function loadMoreComments(Request $request){
        $postId = $request->post_id;
        $page = $request->page;

        $comments = Post::findOrFail($postId)->postComments()->paginate(5, ['*'], 'page', $page);

        return response()->json([
            'comments' => $comments->items(),
            'next_page_url' => $comments->nextPageUrl()
        ]);
    }

    public function userList(){
        $users = User::role('user')->orderBy('created_at', 'desc')->get();
        return view('user_list', ['users' => $users]);
    }

    public function access(){
        $acc = AccessCode::where('is_active', true)->get();
        return view('access', ['access' => $acc]);
    }
}
