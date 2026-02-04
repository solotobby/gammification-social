<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessCode;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function home()
    {
        $res = securityVerification();
        if ($res == 'OK') {

            $userCount = User::role('user')->orderBy('created_at', 'desc')->count();
            $partnerCount = Partner::where('status', true)->count();
            $accesscodeCount = AccessCode::all()->count();
            $tx = Transaction::where(['status' => 'successful', 'status' => 'allocated'])->get();
            $usd = $tx->where('currency', 'USD')->sum('amount');
            $naira = $tx->where('currency', 'NGN')->sum('amount');

            $nairaInDollar = $naira / 1500;

            $rev = $nairaInDollar + $usd;

            $posts = Post::query()->get(['views', 'views_external', 'likes', 'likes_external', 'comments', 'comment_external']);
            $levelCounts = UserLevel::where('status', 'active')
                ->where('next_payment_date', '>', now())
                ->groupBy('plan_name')
                ->select('plan_name', DB::raw('COUNT(user_id) as total'))
                ->get();

            $onlineUsers = collect(Cache::get('online_users', []))
                ->filter(fn($lastSeen) => now()->diffInMinutes($lastSeen) <= 2)
                ->count();
                
            return view('admin.home', [
                'userCount' => $userCount,
                'partnerCount' => $partnerCount,
                'accesscodeCount' => $accesscodeCount,
                'rev' => $rev,
                'posts' => $posts,
                'levelCounts' => $levelCounts,
                'onlineUsers' => $onlineUsers
            ]);
        }
    }
}
