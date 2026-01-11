<?php

use App\Livewire\User\Posts;
use App\Models\CommentExternal;
use App\Models\Level;
use App\Models\LevelPlanId;
use App\Models\Partner;
use App\Models\Post;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserLike;
use App\Models\UserView;
use App\Models\ViewsExternal;
use App\Models\Wallet;
use Brick\Math\BigInteger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\DB;
use Symfony\Polyfill\Uuid\Uuid;

if (!function_exists('engagement')) {
    function engagement()
    {

        return Post::with(['user:id,name,username'])->select('user_id', \DB::raw('SUM(views + views_external + likes + likes_external + comments) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }
}

if (!function_exists('generateCode')) {
    function generateCode($number)
    {
        $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $code = '';
        for ($i = 0; $i < $number; $i++) {
            $code .= $alph[rand(0, 35)];
        }
        return $code;
    }
}

if (!function_exists('getCurrencyCode')) {
    function getCurrencyCode($currency = null)
    {
        $codes = [
            'USD' => '$',
            'NGN' => '₦',
            'EUR' => '€',
            'GBP' => '£',
        ];

        if ($currency == null) {
            $userCurrency = Wallet::where('user_id', auth()->user()->id)->first();
            return $codes[$userCurrency->currency] ?? null;
        } else {
            return $codes[$currency] ?? null;
        }
    }
}

if (!function_exists('userBaseCurrency')) {
    function userBaseCurrency($userId = null): ?string
    {
        $userId ??= auth()->id();

        return Wallet::where('user_id', $userId)->value('currency');
    }
}


if (!function_exists('userLevel')) {
    function userLevel($userId = null)
    {
        $user = $userId ? User::find($userId) : auth()->user();

        return $user?->activeLevel?->plan_name ?? 'Basic';

        // return $userId ? User::find($userId)->activeLevel->plan_name : auth()->user()->activeLevel->plan_name;
    }
}







////PAYMENT HELPERS//// ---- DEPRECIATED ---
if (!function_exists('upgradePayment')) {

    function upgradePayment($amount, $currency, $package)
    {



        // $payload = [
        //     "tx_ref" => Str::random(16),
        //     "amount" => $amount,
        //     "currency" => $currency,
        //     "redirect_url" => url('upgrade/api'), //"https://webhook.site/9d0b00ba-9a69-44fa-a43d-a82c33c36fdc",
        //     "meta" => [
        //         "package" => $package
        //         // "level_id" =>$level->id,
        //         // "level_name" =>$package,
        //         // "number_of_slot" =>$quantity,
        //         // "unitprice" =>$level->amount,
        //         // "amount_paid" =>$amount,
        //     ],
        //     "customer" => [
        //         "email" => auth()->user()->email,
        //         "name" => auth()->user()->name
        //     ],
        //     "customizations" => [
        //         "title" => "Upgrade payment to " . $package . " package",
        //         "logo" => "https://payhankey.com/logo.png"

        //     ]
        // ];

        // $res = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Content-Type' => 'application/json',
        //     'Authorization' => 'Bearer ' . env('FL_SECRET_KEY')
        // ])->post('https://api.flutterwave.com/v3/payments', $payload)->throw();

        // return json_decode($res->getBody()->getContents(), true)['data']['link'];
    }
}

if (!function_exists('processPayment')) {
    function processPayment($amount, $currency, $package, $level, $quantity)
    {

        $payload = [
            "tx_ref" => Str::random(16),
            "amount" => $amount,
            "currency" => $currency,
            "redirect_url" => url('validate/api'), //"https://webhook.site/9d0b00ba-9a69-44fa-a43d-a82c33c36fdc",
            "meta" => [
                "package" => $package,
                "level_id" => $level->id,
                "level_name" => $level->name,
                "number_of_slot" => $quantity,
                "unitprice" => $level->amount,
                "amount_paid" => $amount,
            ],
            "customer" => [
                "email" => auth()->user()->email,
                "name" => auth()->user()->name
            ],
            "customizations" => [
                "title" => "Payment for " . $quantity . " " . $package . " package",
                "logo" => "https://payhankey.com/logo.png"

            ]
        ];

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('FL_SECRET_KEY')
        ])->post('https://api.flutterwave.com/v3/payments', $payload)->throw();

        return json_decode($res->getBody()->getContents(), true)['data']['link'];
    }
}



//this will be called to update the unique view earnings
if (!function_exists('calculateUniqueEarningPerView')) {
    function calculateUniqueEarningPerView()
    {
        $user = Auth::user();
        $level = Level::where('name', userLevel())->first();

        $earningPer1000Views = $level->earning_per_view;
        $earningPerView = $earningPer1000Views / 1000;
        return $earningPerView;
    }
}

if (!function_exists('calculateUniqueEarningPerLike')) {
    function calculateUniqueEarningPerLike()
    {
        $user = Auth::user();
        $level = Level::where('name', userLevel())->first();

        $earningPer1000Likes = $level->earning_per_like;
        $earningPerLike = $earningPer1000Likes / 1000;
        return $earningPerLike;
    }
}

if (!function_exists('calculateUniqueEarningPerComment')) {
    function calculateUniqueEarningPerComment()
    {
        $user = Auth::user();
        $level = Level::where('name', userLevel())->first();

        $earningPer1000Comments = $level->earning_per_comment;
        $earningPerComment = $earningPer1000Comments / 1000;
        return $earningPerComment;
    }
}
if (!function_exists('getLevels')) {
    function getLevels()
    {
        return Level::orderBy('name', 'asc')->get();
    }
}


if (!function_exists('updatesLikeEarnings')) {
    function updatesLikeEarnings(): float
    {

        $user = Auth::user();

        if (!$user) {
            return 0.00;
        }

        return DB::transaction(function () use ($user) {

            $baseQuery = UserLike::whereHas('post', function ($query) use ($user) {
                $query->where('poster_user_id', $user->id);
            })->where('is_paid', false);

            // Aggregate before update
            $result = (clone $baseQuery)
                ->selectRaw('COUNT(*) as total_likes, COALESCE(SUM(amount), 0) as total_amount')
                ->first();

            // Mark as paid
            $baseQuery->update(['is_paid' => true]);

            return (float) $result->total_amount;
        });
    }
}

if (!function_exists('updatesCommentEarnings')) {
    function updatesCommentEarnings(): float
    {
        $user = Auth::user();

        if (!$user) {
            return 0.00;
        }

        return DB::transaction(function () use ($user) {

            $baseQuery = UserComment::whereHas('post', function ($query) use ($user) {
                $query->where('poster_user_id', $user->id);
            })->where('is_paid', false);

            // Aggregate before update
            $result = (clone $baseQuery)
                ->selectRaw('COUNT(*) as total_comments, COALESCE(SUM(amount), 0) as total_amount')
                ->first();

            // Mark as paid
            $baseQuery->update(['is_paid' => true]);

            return (float) $result->total_amount;
        });
    }
}

if (!function_exists('updatesViewEarnings')) {
    function updatesViewEarnings(): float
    {
        $user = Auth::user();

        if (!$user) {
            return 0.00;
        }

        return DB::transaction(function () use ($user) {

            $baseQuery = UserView::whereHas('post', function ($query) use ($user) {
                $query->where('poster_user_id', $user->id);
            })->where('is_paid', false);

            // Aggregate before update
            $result = (clone $baseQuery)
                ->selectRaw('COUNT(*) as total_views, COALESCE(SUM(amount), 0) as total_amount')
                ->first();

            // Mark as paid
            $baseQuery->update(['is_paid' => true]);

            return (float) $result->total_amount;
        });
    }
}

//master function to update wallet earnings
if (!function_exists('updateWalletEarnings')) {
    function updateWalletEarnings(): ?Wallet
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $totalEarnings = updatesViewEarnings() + updatesCommentEarnings() + updatesLikeEarnings();

        //    return [
        //         'views'    => updatesViewEarnings(),
        //         'comments' => updatesCommentEarnings(),
        //         'likes'    => updatesLikeEarnings(),
        //         'total'    => $totalEarnings,
        //     ];



        if ($totalEarnings <= 0) {
            return Wallet::where('user_id', $user->id)->first();
        }

        return DB::transaction(function () use ($user, $totalEarnings) {

            // Earnings in USD (or system currency)
            $viewEarnings = $totalEarnings;

            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            if (! $wallet) {
                return null;
            }

            $convertedAmount = convertToBaseCurrency(
                $viewEarnings,
                $wallet->currency
            );
            $wallet->balance += round($convertedAmount, 2);
            $wallet->save();
            return $wallet;
        });
    }
}

if (!function_exists('estimatedEarnings')) {
    function estimatedEarnings($postId): float
    {
        // $post = Post::find($postId);

        if (!$postId) {
            return 0.00;
        }

        return DB::transaction(function () use ($postId) {

            $allearnings = UserView::where('post_id', $postId)->where('created_at', '>=', now()->subDays(30))->sum('amount') +
                UserLike::where('post_id', $postId)->where('created_at', '>=', now()->subDays(30))->sum('amount') +
                UserComment::where('post_id', $postId)->where('created_at', '>=', now()->subDays(30))->sum('amount');

            $convertedAmount = convertToBaseCurrency(
                $allearnings,
                auth()->user()->wallet->currency
            );

            return (float) round($convertedAmount, 5);
        });
    }
}


if (!function_exists('convertToBaseCurrency')) {
    function convertToBaseCurrency($amount, $currency)
    {

        $rates = [
            'USD' => 1,
            'NGN' => 1500,
            'EUR' => 0.91,
            'GBP' => 0.81,
        ];

        $rate = $rates[$currency] ?? 1;
        $convertedAmount = $amount * $rate;

        return $convertedAmount;
    }
}


if (!function_exists('viewsAmountCalculator')) {
    function viewsAmountCalculator($postId): float
    {

        if (!$postId) {
            return 0.0;
        }

        return DB::transaction(function () use ($postId) {
            $viewsEarnings = UserView::where('post_id', $postId)->sum('amount');

            $convertedAmount = convertToBaseCurrency(
                $viewsEarnings,
                auth()->user()->wallet->currency
            );

            return (float) round($convertedAmount, 5);
        });
    }
}


if (!function_exists('likesAmountCalculator')) {
    function likesAmountCalculator($postId): float
    {
        if (!$postId) {
            return 0.0;
        }
        return DB::transaction(function () use ($postId) {
            $likesEarnings = UserLike::where('post_id', $postId)->sum('amount');

            $convertedAmount = convertToBaseCurrency(
                $likesEarnings,
                auth()->user()->wallet->currency
            );

            return (float) round($convertedAmount, 5);
        });
    }
}


if (!function_exists('commentsAmountCalculator')) {
    function commentsAmountCalculator($postId): float
    {


        if (!$postId) {
            return 0.0;
        }
        return DB::transaction(function () use ($postId) {
            $commentsEarnings = UserComment::where('post_id', $postId)->sum('amount');

            $convertedAmount = convertToBaseCurrency(
                $commentsEarnings,
                auth()->user()->wallet->currency
            );

            return (float) round($convertedAmount, 5);
        });
    }
}



if (!function_exists('sumCounter')) {
    function sumCounter($like, $like_ext)
    {
        $val1 = $like ?? 0;
        $val2 = $like_ext ?? 0;
        return  $val1 + $val2;
    }
}


///IP LOCATION HELPERS////
if (!function_exists('ipLocation')) {
    function ipLocation()
    {
        if (env('APP_DEBUG') == true) {
            $ip = '31.205.133.91';
        } else {
            $ip = request()->getClientIp();
        }

        $location = Location::get($ip);

        return ['ip' => $location->ip, 'country' => $location->countryName, 'region' => $location->regionName, 'city' => $location->cityName];
    }
}
////SECURITY VERIFICATION HELPERS////
if (!function_exists('securityVerification')) {
    function securityVerification()
    {

        $myLocation = ipLocation();

        $countryList = explode(',', env('COUNTRY'));

        $ipList = explode(',', env('IP'));

        $myIp =  $myLocation['ip'];
        $myCountry =  $myLocation['country'];

        $ipIsContained = in_array($myIp, $ipList);

        $countryIsContained = in_array($myCountry, $countryList);

        //    return [$ipIsContained, $countryIsContained];

        if ($ipIsContained == true || $countryIsContained == true) {
            return 'OK';
        } else {
            return 'not_okay';
        }
    }
}


////TEXT HELPERS////
if (!function_exists('displayName')) {
    function displayName($name)
    {
        $bk = explode(' ', $name);
        return $bk[0];
    }
}

if (!function_exists('normalizeText')) {
    function normalizeText($text)
    {
        $text = preg_replace('/[^\w\s]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return strtolower(trim($text));
    }
}

if (!function_exists('isSimilar')) {
    function isSimilar($newData, $existingData, $threshold = 4)
    {
        $normalizedNewData = normalizeText($newData);

        foreach ($existingData as $data) {
            $normalizedData = normalizeText($data);
            $levenshteinDistance = levenshtein($normalizedNewData, $normalizedData);

            if ($levenshteinDistance <= $threshold) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('maskCode')) {
    function maskCode($code)
    {
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





/////PAYSTACK INTEGRATION////

if (!function_exists('bankList')) {
    function bankList()
    {
        $url = 'https://api.paystack.co/bank?country=nigeria';
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->get($url)->throw();

        return json_decode($res->getBody()->getContents(), true)['data'];
    }
}

if (!function_exists('generateVirtualAccount')) {
    function generateVirtualAccount($partner)
    {

        //check if user exist, if yes, update informatioon
        //$fetchCustomer = fetchCustomer($partner->email);

        // if($fetchCustomer['status'] == true){

        //     //update customer
        //     $customerPayload = [
        //         "first_name"=> $partner->name,//auth()->user()->name,
        //         "last_name"=> 'Payhankey',
        //         "phone"=> "+".$phone_number
        //     ];

        //     $updateCustomer = updateCustomer($user->email, $customerPayload);

        //     if($updateCustomer['status'] == true){

        //         $data = [
        //             "customer"=> $updateCustomer['data']['customer_code'], 
        //             "preferred_bank"=>env('PAYSTACK_BANK')
        //         ];

        //         $response = virtualAccount($data);

        //         $VirtualAccount = VirtualAccount::where('user_id', $user->id)->first();
        //         if($VirtualAccount){

        //             $VirtualAccount->bank_name = $response['data']['bank']['name'];
        //             $VirtualAccount->account_name = $response['data']['account_name'];
        //             $VirtualAccount->account_number = $response['data']['account_number'];
        //             $VirtualAccount->account_name = $response['data']['account_name'];
        //             $VirtualAccount->currency = 'NGN';
        //             $VirtualAccount->save();

        //         }else{


        //             $VirtualAccount = VirtualAccount::create([
        //                 'user_id' => $user->id, 
        //                 'channel' => 'paystack', 
        //                 'customer_id'=>$updateCustomer['data']['customer_code'], 
        //                 'customer_intgration'=> $updateCustomer['data']['integration'],
        //                 'bank_name' => $response['data']['bank']['name'],
        //                 'account_name' => $response['data']['account_name'],
        //                 'account_number' => $response['data']['account_number'],
        //                 'account_name' => $response['data']['account_name'],
        //                 'currency' => 'NGN'
        //             ]);

        //         }

        //         $data['res']=$response;
        //         $data['va']=$VirtualAccount; //back()->with('success', 'Account Created Succesfully');
        //         return $data;
        //     }


        // }else{

        $phone = '+234' . substr($partner->phone, 1);
        $payload = [
            "email" => $partner->email,
            "first_name" => 'Payhankey',
            "last_name" => $partner->name,
            "phone" => $phone
        ];

        $customer = createCustomer($payload);

        if ($customer['status'] == true) {

            $data = [
                "customer" => $customer['data']['customer_code'],
                "preferred_bank" => env('PAYSTACK_BANK') //"wema-bank"
            ];

            $va = virtualAccount($data);

            if ($va['status'] == true) {

                $updateVA_info = Partner::where('user_id', $partner->user_id)->first();

                $updateVA_info->customer_code = $va['data']['customer']['customer_code'];
                $updateVA_info->bank_name = $va['data']['bank']['name'];
                $updateVA_info->account_number = $va['data']['account_number'];
                $updateVA_info->account_name = $va['data']['account_name'];
                $updateVA_info->currency = 'NGN';
                $updateVA_info->save();

                $data['status'] = true;
                $data['customers'] = $customer;
                $data['virtual_account'] = $va;
                $data['partner'] = $partner;
            } else {
                return response()->json(['status' => false, 'data' => $data], 403);
            }




            // $data['res']=$customer;
            // $data['va']=$va; 
            // return $data;

            // if($VirtualAccount){

            //     // $VirtualAccount->bank_name = $response['data']['bank']['name'];
            //     // $VirtualAccount->account_name = $response['data']['account_name'];
            //     // $VirtualAccount->account_number = $response['data']['account_number'];
            //     // $VirtualAccount->account_name = $response['data']['account_name'];
            //     // $VirtualAccount->currency = 'NGN';
            //     // $VirtualAccount->save();

            // }else{

            //     // $VirtualAccount = VirtualAccount::create([
            //     //     'user_id' => $user->id, 
            //     //     'channel' => 'paystack', 
            //     //     'customer_id'=>$res['data']['customer_code'], 
            //     //     'customer_intgration'=> $res['data']['integration'],
            //     //     'bank_name' => $response['data']['bank']['name'],
            //     //     'account_name' => $response['data']['account_name'],
            //     //     'account_number' => $response['data']['account_number'],
            //     //     'account_name' => $response['data']['account_name'],
            //     //     'currency' => 'NGN'
            //     // ]);

            // }


        } else {
            return response()->json('Could not create customer account', 403);
        }
    }
}

if (!function_exists('fetchCustomer')) {
    function  fetchCustomer($email)
    {

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->get('https://api.paystack.co/customer/' . $email);

        return json_decode($res->getBody()->getContents(), true);
    }
}

if (!function_exists('updateCustomer')) {
    function  updateCustomer($email, $payload)
    {

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->put('https://api.paystack.co/customer/' . $email, $payload);

        return json_decode($res->getBody()->getContents(), true);
    }
}

if (!function_exists('virtualAccount')) {
    function  virtualAccount($data)
    {
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->post('https://api.paystack.co/dedicated_account', $data);

        return json_decode($res->getBody()->getContents(), true);
    }
}

if (!function_exists('createCustomer')) {
    function  createCustomer($data)
    {

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->post('https://api.paystack.co/customer', $data);

        return json_decode($res->getBody()->getContents(), true);
    }
}


if (!function_exists('createPlan')) {

    function createPlan($name, $amount)
    {
        $url = 'https://api.paystack.co/plan';
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->post($url, [
            "name" => $name,
            "amount" => $amount * 100,
            "interval" => "monthly"
        ])->throw();

        return json_decode($res->getBody()->getContents(), true);
    }
}

if (!function_exists('verifyPaystackPayment')) {

    function verifyPaystackPayment($reference)
    {
        $url = 'https://api.paystack.co/transaction/verify/' . $reference;
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->get($url)->throw();

        return json_decode($res->getBody()->getContents(), true)['data'];
    }
}

if (!function_exists('upgradeLevel')) {

    function upgradeLevel($levelId)
    {
        $user = Auth::user();
        $level = Level::find($levelId);

        if (!$level) {
            session()->flash('error', 'Invalid Level Selected');
            return;
        }

        $userCurrency = userBaseCurrency($user->id);

        //get plan code based on currency and plan
        // $levelPlan = LevelPlanId::where('level_name', $level->name)->where('currency', $userCurrency)->first();


        $convertedAmount = convertToBaseCurrency($level->amount, 'NGN'); ///convert all currency to NGN Via route

        
        if ($level) {

            if ($userCurrency == 'NGN' || $userCurrency == 'USD' || $userCurrency == 'EUR' || $userCurrency == 'GBP') {
                return createSubscriptionNGN($convertedAmount, $level);
            }
        }
    }
}

if (!function_exists('createSubscriptionNGN')) {

    function createSubscriptionNGN( $amount, $level)
    {
        $user = Auth::user();


        $url = 'https://api.paystack.co/transaction/initialize';
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->post($url, [
            // "plan" => $planCode,
            'email' => $user->email,
            'amount' => $amount * 100, // first charge
            'callback_url' => route('upgrade.api'),
            'channel' => ["card", "bank", "bank_transfer", "payattitude"],
            'metadata' => [
                'user_id' => $user->id,
                'level' => $level->name,
                'level_id' => $level->id,
                'name' => $user->name
            ],
        ])->throw();

        if (!$res->successful()) {
            session()->flash('error', 'Unable to initialize payment.');
            return;
        }

        return redirect($res['data']['authorization_url']);
    }
}




// PLN_jpan26fg9bz60p7
//create subscription
if (!function_exists('fetchSubscription')) {

    function fetchSubscription($customerEmail)
    {
        $url = 'https://api.paystack.co/subscription';
        $subData = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->get($url, [
            // "plan" => $cusPlan,
            // "customer" => $cusCode,
            "email" => $customerEmail,
            // 'authorization' => $authCode, //$customerCode
        ])->throw();

        return json_decode($subData->getBody()->getContents(), true)['data'][0];
    }
}


if(!function_exists('engagementEarnings')){
    function engagementEarnings(int $total): float
    {
            return round($total / 1000, 2);
    }
}
