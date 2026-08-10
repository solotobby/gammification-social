<?php

namespace App\Http\Middleware;

use App\Services\AdminGateService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminGateAllowed
{
    public function __construct(private AdminGateService $adminGate) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->adminGate->passesEnvironmentCheck()) {
            abort(404);
        }

        return $next($request);
    }
}
