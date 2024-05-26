<?php

namespace App\Http\Controllers;

use App\Models\AccessCode;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeneralController extends Controller
{
    public function accessCode($level){
        if($level == 'beginner' || $level == 'creator' || $level == 'influencer' ){
           
            return view('get_access_code', ['level' => $level]);
        }else{
            return redirect('/');
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

    public function validateApi(){
         $url = request()->fullUrl();
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);

        $ref = $params['transaction_id']; 

        $response = $this->verifyFlutterwavePayment($ref);

        // return $response['status'];
        if($response['status'] == 'success'){
             $name = $response['data']['customer']['name'];
             $email = $response['data']['customer']['email'];
             $code = $this->generateCode(7);
            //  return [$name, $email, $this->generateCode(7)];
            $chekIfNotRedeemed = AccessCode::where('email', $email)->where('tx_id', $ref)->where('is_active', true)->first();
            if($chekIfNotRedeemed){
                return redirect('error');
            }
            AccessCode::create(['tx_id' => $ref,'name' => $name, 'email' => $email, 'amount' => '6', 'code' => $code]);
            //send mail to the above email
           
            return redirect('success');
            // return 'ok';

        }else{
            // return 'not ok';
            return redirect('error');
        }
       
    }

    public function verifyFlutterwavePayment($ref){
        
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('FL_SECRET_KEY')
        ])->get('https://api.flutterwave.com/v3/transactions/'.$ref.'/verify')->throw();

        return json_decode($res->getBody()->getContents(), true);
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

        return view('showPost', ['timeline' => $timeline] );
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
}
