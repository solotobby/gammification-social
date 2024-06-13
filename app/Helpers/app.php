<?php

use App\Models\Level;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Stevebauman\Location\Facades\Location;

if(!function_exists('engagement')){
    function engagement(){
        
        return Post::with(['user:id,name'])->select('user_id', \DB::raw('SUM(views + views_external + likes + likes_external + comments) as total'))
        ->groupBy('user_id')
        ->orderByDesc('total')
        ->limit(5)
        ->get();
    }
}

if(!function_exists('processPayment')){
    function processPayment($amount, $currency, $package, $level, $quantity){

        $payload = [
            "tx_ref"=> Str::random(16),
            "amount"=> $amount,
            "currency"=> $currency,
            "redirect_url"=> url('validate/api'),//"https://webhook.site/9d0b00ba-9a69-44fa-a43d-a82c33c36fdc",
            "meta"=> [
                "package" => $package,
                "level_id" =>$level->id,
                "level_name" =>$level->name,
                "number_of_slot" =>$quantity,
                "unitprice" =>$level->amount,
                "amount_paid" =>$amount,
            ],
            "customer"=> [
                "email"=> auth()->user()->email,
                "name"=> auth()->user()->name
            ],
            "customizations"=> [
                "title"=> "Payment for ".$quantity." ".$package." package",
                "logo"=> "https://payhankey.com/logo.png"
               
            ]
        ];
        
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('FL_SECRET_KEY')
        ])->post('https://api.flutterwave.com/v3/payments', $payload)->throw();

        return json_decode($res->getBody()->getContents(), true)['data']['link'];


    }
}


if(!function_exists('maskCode')){
    function maskCode($code) {
        $length = strlen($code);
        if ($length <= 8) {
            return $code; // If the code is 8 characters or less, don't mask it
        }
        $firstFour = substr($code, 0, 4);
        $lastFour = substr($code, -4);
        $masked = str_repeat('*', $length - 8);
        return $firstFour . $masked . $lastFour;
    }
}

if(!function_exists('viewsAmountCalculator')){
    function viewsAmountCalculator($count) {
        
        $earnings_per_1000_view = Level::where('name', auth()->user()->level->name)->first()->earning_per_view;
        
        $singleView = $earnings_per_1000_view / 1000;//0.0009; //dollar

        return $count * $singleView;
        // return floor($count/1000) * 0.9;
        
    }
}

if(!function_exists('likesAmountCalculator')){
    function likesAmountCalculator($count) {
        
        $earnings_per_1000_like = Level::where('name', auth()->user()->level->name)->first()->earning_per_like;
        
        $singleView = $earnings_per_1000_like / 1000;//0.0009; //dollar
        
        return $count * $singleView;
        // return floor($count/1000) * 0.9;
        
    }
}


if(!function_exists('commentsAmountCalculator')){
    function commentsAmountCalculator($count) {
        
        $earnings_per_1000_comment = Level::where('name', auth()->user()->level->name)->first()->earning_per_comment;
        
        $singleView = $earnings_per_1000_comment / 1000; 
        
        return $count * $singleView;
        // return floor($count/1000) * 0.9;
        
    }
}


if(!function_exists('sumCounter')){
    function sumCounter($like, $like_ext) {
        $val1 = $like ?? 0;
        $val2 = $like_ext ?? 0;
        return  $val1+$val2;
        
    }
}

if(!function_exists('bankList')){
    function bankList() {
        $url = 'https://api.paystack.co/bank?country=nigeria';
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('PAYSTACK_SECRET_KEY')
        ])->get($url)->throw();

        return json_decode($res->getBody()->getContents(), true)['data'];
    }
}

if(!function_exists('ipLocation')){
    function ipLocation() {
        // if(env('APP_DEBUG') == true){
        //     $ip = '31.205.133.91';
        // }else{
          
        // }
       
        $ip = request()->getClientIp();
        $location = Location::get($ip);
        
        return ['ip'=>$location->ip, 'country'=>$location->countryName, 'region'=>$location->regionName, 'city'=>$location->cityName];

    }
}

