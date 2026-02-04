<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackUserOnline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        // if (auth()->check()) {
        //     Cache::put(
        //         'user_online_' . auth()->id(),
        //         now(),
        //         now()->addMinutes(5)
        //     );
        // }

        if (Auth::check()) {
            // Store user ID and timestamp
            $onlineUsers = Cache::get('online_users', []);

            $onlineUsers[Auth::id()] = now();

            // Save cache for 5 minutes
            Cache::put('online_users', $onlineUsers, now()->addMinutes(2));
        }

        return $next($request);
    }
}
