<?php

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

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
    function processPayment($amount, $currency, $package){

        $payload = [
            "tx_ref"=> Str::random(16),
            "amount"=> $amount,
            "currency"=> $currency,
            "redirect_url"=> url('validate/apidsdsds'),//"https://webhook.site/9d0b00ba-9a69-44fa-a43d-a82c33c36fdc",
            "customer"=> [
                "email"=> auth()->user()->email,
                "name"=> auth()->user()->name
            ],
            "customizations"=> [
                "title"=> "Payment for ".$package,
                // "logo"=> "http://www.piedpiper.com/app/themes/joystick-v27/images/logo.png"
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
