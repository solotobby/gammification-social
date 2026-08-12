<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Expired CSRF/session on login (and other forms) → friendly redirect, not Whoops/419.
        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Your session expired. Please refresh and try again.',
                ], 419);
            }

            $loginUrl = Route::has('login') ? route('login') : url('/login');

            return redirect()
                ->to($request->headers->get('referer') ?: $loginUrl)
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', 'Your session expired. Please try again.');
        });
    }
}
