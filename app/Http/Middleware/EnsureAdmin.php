<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        if (securityVerification() !== 'OK') {
            abort(404);
        }

        return $next($request);
    }
}
