<?php

namespace App\Http\Controllers;

use App\Mail\AccessCodeMail;
use App\Models\AccessCode;
use App\Models\AdminLogin;
use App\Models\Comment;
use App\Models\CommentExternal;
use App\Models\CommentExternalMessage;
use App\Models\Level;
use App\Models\Partner;
use App\Models\PartnerSlot;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Models\ViewsExternal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;

class GeneralController extends Controller
{
    public function devy(){
        // $users = User::role('user')->orderBy('created_at', 'desc')->get();

        // foreach($users as $user){
        //     $levelId = AccessCode::where('id', $user->access_code_id)->first()->level_id;
        //     $newUser = User::where('id', $user->id)->first();
        //     $newUser->level_id = $levelId;
        //     $newUser->save();
        // }

        return 'Okay good';
    }

    public function howToEarn(){
        return view('earn');
    }

    public function admin(){
        return view('admin.home');
    }
    
    public function accessCode($level){
        if($level == 'beginner' || $level == 'creator' || $level == 'influencer' ){

            if($level == 'beginner'){
                $amountDollar = '5';
                $amountNaira = '7,500';
                $nairaLink = 'https://paystack.com/pay/3kzeashpp7';//'https://flutterwave.com/pay/ohd7jfk6wgzq';
                $dollarLink = 'https://buy.stripe.com/9AQ5la2KB7U80zCeUU';
            }elseif($level == 'creator'){
                $amountDollar = '10';
                $amountNaira = '15,000';
                $nairaLink = 'https://paystack.com/pay/ssl60zukg4';
                $dollarLink = 'https://buy.stripe.com/4gwaFu3OF7U82HK8wx';
            }else{
                $amountDollar = '20';
                $amountNaira = '30,000';
                $nairaLink = 'https://paystack.com/pay/h927ynoveq';
                $dollarLink = 'https://buy.stripe.com/eVa9Bqfxn6Q48244gi';
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

    public function dinkyLogin(){

        $res = securityVerification();

        if($res == 'OK'){
           $loc = ipLocation();
           $code = Str::random(128);
            AdminLogin::create(['ip' => $loc['ip'], 'country' => $loc['country'], 'city' => $loc['city'], 'code' => $code, 'status' => true]);
            return redirect('registration/'.$code);
        }

    }

    public function validateLogin(){
        
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

            $chekIfNotRedeemed = AccessCode::where('email', $request->email)->where('is_active', true)->first();
            
            if($chekIfNotRedeemed){
                return redirect('error');
            }

            AccessCode::create(['tx_id' => $ref,'name' =>$level->name, 'email' => $request->email, 'amount' => $level->amount, 'code' => $code, 'level_id' => $level->id]);
            Mail::to($request->email)->send(new AccessCodeMail($code));

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

        return view('successful');

    }

    public function error(){
        return 'Error processing payment';
    }

    public function showPost($id){
        
        // $timeline = Post::with(['postComments' => function($query) {
        //     $query->paginate(15); // Load initial 15 comments
        // }])->where('id', $id)->firstOrFail();

        $timeline = Post::with(['postCommentsExternal' => function($query) {
            $query->paginate(15); // Load initial 15 comments
        }])->where('id', $id)->firstOrFail();



        $timeline->views_external += 1;
        $timeline->save(); 

        //unique view
        $location = ipLocation();
        $checkunique = ViewsExternal::where(['post_id' => $id, 'ip' => $location['ip'], 'city' => $location['city']])->first();
        if(!$checkunique){
            ViewsExternal::create(['post_id' => $id, 'ip' => $location['ip'], 'city' => $location['city']]);
        }

        
        return view('showpost', ['timeline' => $timeline] );
    }

    public function comment(Request $request){
        
          //unique view
        $location = ipLocation();
     
       $timeline = Post::where('id', $request->post_id)->first();

    //    $timeline = Post::with(['postCommentsExternal' => function($query) {
    //     $query->paginate(15); // Load initial 15 comments
    // }])->where('id', $request->post_id)->firstOrFail();


       $timeline->comment_external += 1;
       $timeline->save();

       CommentExternalMessage::create(['post_id' => $request->post_id, 'message'=>$request->message]);
       
        $checkunique = CommentExternal::where(['post_id' => $request->post_id, 'ip' => $location['ip'], 'city' => $location['city']])->first();
        if(!$checkunique){
            CommentExternal::create(['post_id' => $request->post_id, 'message'=>$request->message, 'ip' => $location['ip'], 'city' => $location['city']]);

            // CommentExternal::create(['post_id' => $request->post_id, 'ip' => $location['ip'], 'city' => $location['city']]);
        }

        return  back();

    }

    // public function partner(Request $request){
        
    //     $validated = $request->validate([
    //         'email' => 'bail|required|email|unique:partners|max:255',
    //         'name' => 'required|string',
    //         'phone' => 'required|numeric',
    //         'identification' => 'required|string',
    //         'country' => 'required|string',
    //     ]);

    //     Partner::create(['name' => $validated['name'], 'email' => $validated['email'], 'phone' => $validated['phone'], 'identification' => $validated['identification'], 'country' => $validated['country']]);

    //     return back()->with('success', 'You have successfully applied to become a Partner on Payhankey. An email has been sent to you for a short video call');

    // }

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
