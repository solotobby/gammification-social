<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! isAdmin($request->user())) {
            abort(403, 'Admins only.');
        }

        return $next($request);
    }
}
