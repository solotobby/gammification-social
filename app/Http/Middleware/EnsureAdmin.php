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

        if (! $user || ! isAdminPanelUser($user)) {
            abort(403, 'Unauthorized.');
        }

        if (securityVerification() !== 'OK') {
            abort(404);
        }

        // Staff may only hit shared moderation/content routes.
        if (isStaff($user) && ! isAdmin($user) && ! staffCanAccessRoute($request)) {
            abort(403, 'Staff cannot access this area.');
        }

        return $next($request);
    }
}
