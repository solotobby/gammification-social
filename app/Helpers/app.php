<?php

use App\Livewire\User\Posts;
use App\Models\CommentExternal;
use App\Models\Level;
use App\Models\Post;
use App\Models\UserComment;
use App\Models\UserLike;
use App\Models\UserView;
use App\Models\ViewsExternal;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
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
if(!function_exists('generateCode')){
    function generateCode($number){
        $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $code='';
        for($i=0;$i<$number;$i++){
        $code .= $alph[rand(0, 35)];
        }
        return $code;
    }
}

if(!function_exists('upgradePayment')){

    function upgradePayment($amount, $currency, $package){

        $payload = [
            "tx_ref"=> Str::random(16),
            "amount"=> $amount,
            "currency"=> $currency,
            "redirect_url"=> url('upgrade/api'),//"https://webhook.site/9d0b00ba-9a69-44fa-a43d-a82c33c36fdc",
            "meta"=> [
                "package" => $package
                // "level_id" =>$level->id,
                // "level_name" =>$package,
                // "number_of_slot" =>$quantity,
                // "unitprice" =>$level->amount,
                // "amount_paid" =>$amount,
            ],
            "customer"=> [
                "email"=> auth()->user()->email,
                "name"=> auth()->user()->name
            ],
            "customizations"=> [
                "title"=> "Upgrade payment to ".$package." package",
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
    function viewsAmountCalculator($unpaidViews,$unpaidExternalViews) {
       
        $earnings_per_1000_view = Level::where('name', auth()->user()->level->name)->first()->earning_per_view;
        
        $singleView = $earnings_per_1000_view / 1000;
        $singleViewExternal = 1 /5000;

        $paidExternalView = $unpaidExternalViews * $singleViewExternal;
        $paidInternalViews = $unpaidViews * $singleView;

        return $paidExternalView+$paidInternalViews;
        
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
        if(env('APP_DEBUG') == true){
            $ip = '31.205.133.91';
        }else{
            $ip = request()->getClientIp();
        }
       
        $location = Location::get($ip);
        
        return ['ip'=>$location->ip, 'country'=>$location->countryName, 'region'=>$location->regionName, 'city'=>$location->cityName];

    }
}


if(!function_exists('securityVerification')){
    function securityVerification() {

        $myLocation = ipLocation();

        $countryList = explode(',', env('COUNTRY'));

        $ipList = explode(',', env('IP'));

        $myIp =  $myLocation['ip'];
        $myCountry =  $myLocation['country'];


       $ipIsContained = in_array($myIp, $ipList);

       $countryIsContained = in_array($myCountry, $countryList);

    //    return [$ipIsContained, $countryIsContained];

       if($ipIsContained == true && $countryIsContained == true){
            return 'OK';
       }else{
            return 'not_okay';
       }


    }
}


if(!function_exists('displayName')){
    function displayName($name) {
        $bk = explode(' ', $name);
        return $bk[0];
    }
}

if(!function_exists('refreshWallet')){
    function refreshWallet() {
        $user = Auth::user();

       
        //get user Wallet
        $wallet = Wallet::where('user_id', $user->id)->first();
        //get all posts this guy has
        $postIds = Post::where('user_id', $user->id)->get(['id']);
        $userLevel = Level::where('name', auth()->user()->level->name)->first(['name','earning_per_comment', 'earning_per_view', 'earning_per_like']);
        
        //processviews  - internal
        $singleViewInternal =  $userLevel->earning_per_view / 1000; //amount per internal view
        $singleViewExternal = 1/5000; //amount per external view
        //fetch/update all unpaid views
        $internalViews=UserView::whereIn('post_id', $postIds)->where('is_paid', false)->get();
        //updatewallet 
        $wallet->balance +=  $singleViewInternal*$internalViews->count();
        $wallet->save();
        //reset to paid
        foreach ($internalViews as $view) {
            $view->is_paid = true;
            $view->save();
        }

        //external
        $externalViews = ViewsExternal::whereIn('post_id', $postIds)->where('is_paid', false)->get();
        //updatewallet 
        $wallet->balance +=  $singleViewExternal*$externalViews->count();
        $wallet->save();
        //reset to paid
        foreach ($externalViews as $view) {
            $view->is_paid = true;
            $view->save();
        }


        //process Likes
        $singleLikeInternal = $userLevel->earning_per_like / 1000;

         //fetch/update all unpaid views
         $internalLikes=UserLike::whereIn('post_id', $postIds)->where('is_paid', false)->get();
         //updatewallet 
         $wallet->balance +=  $singleLikeInternal*$internalLikes->count();
         $wallet->save();
         //reset to paid
         foreach ($internalLikes as $view) {
             $view->is_paid = true;
             $view->save();
         }

         //process comments
         $singleCommentInternal = $userLevel->earning_per_comment / 1000;
         //fetch/update all unpaid views
         $internalComments=UserComment::whereIn('post_id', $postIds)->where('is_paid', false)->get();
         //updatewallet 
         $wallet->balance += $singleCommentInternal*$internalComments->count();
         $wallet->save();
         //reset to paid
         foreach ($internalComments as $view) {
             $view->is_paid = true;
             $view->save();
         }

         //external comment 
         $externalComments = CommentExternal::whereIn('post_id', $postIds)->where('is_paid', false)->get();
         $perCommentAmount = '';
         if(auth()->user()->level->name == 'Influencer'){
            $perCommentAmount = 0.40;
         }elseif(auth()->user()->level->name == 'Creator'){
            $perCommentAmount = 0.30;
         }else{
            $perCommentAmount = 0.25;
         }

         $counts = $externalComments->count() * 0.6;
         
         $wallet->balance += $perCommentAmount*$counts;
         $wallet->save();
         //reset to paid
         foreach ($externalComments as $view){
            $view->is_paid = true;
            $view->save();
        }

        return $wallet;

    }
}


