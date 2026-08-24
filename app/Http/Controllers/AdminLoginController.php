<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Services\AdminGateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AdminLoginController extends Controller
{
    public function __construct(private AdminGateService $adminGate) {}

    /**
     * Step 1 — Obfuscated entry (whitelisted IP/country only).
     * GET /seniore/login
     */
    public function createGate(Request $request)
    {
        if ($redirect = $this->redirectIfAlreadyAuthenticatedAdmin($request)) {
            return $redirect;
        }

        $gate = $this->adminGate->issueGate($request);

        return redirect()->to($this->adminGate->registrationUrl($gate));
    }

    /**
     * Step 2 — Show login form for a valid one-time gate code.
     * GET /registration/{code}
     */
    public function showLoginForm(string $code, Request $request)
    {
        if ($redirect = $this->redirectIfAlreadyAuthenticatedAdmin($request)) {
            return $redirect;
        }

        $gate = $this->adminGate->findValidGate($code, $request);

        if (! $gate) {
            abort(404);
        }

        $this->adminGate->bindGateToSession($gate, $request);

        return view('auth.admin_login', [
            'gateCode' => $code,
            'expiresAt' => $gate->expires_at,
        ]);
    }

    /**
     * Step 3 — Authenticate admin and consume the gate.
     * POST /registration
     */
    public function login(AdminLoginRequest $request)
    {
        if ($redirect = $this->redirectIfAlreadyAuthenticatedAdmin($request)) {
            return $redirect;
        }

        $email = strtolower((string) $request->input('email', ''));
        $throttleKey = 'admin-login:'.$request->ip().'|'.$email;
        $maxAttempts = 10;
        $decaySeconds = 900; // 15 minutes after too many failures

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withInput($request->only('email', 'gate_code'))
                ->withErrors(['email' => "Too many failed attempts. Try again in {$seconds} seconds."]);
        }

        $gate = $this->adminGate->findValidGate($request->string('gate_code')->toString(), $request);

        if (! $gate || ! $this->adminGate->gateMatchesSession($gate, $request)) {
            RateLimiter::hit($throttleKey, $decaySeconds);

            return back()
                ->withInput($request->only('email', 'gate_code'))
                ->withErrors(['email' => 'This admin login link has expired or is invalid. Request a new one.']);
        }

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, false)) {
            RateLimiter::hit($throttleKey, $decaySeconds);

            return back()
                ->withInput($request->only('email', 'gate_code'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();

        if (! isAdminPanelUser($user)) {
            Auth::logout();
            RateLimiter::hit($throttleKey, $decaySeconds);

            return back()
                ->withInput($request->only('email', 'gate_code'))
                ->withErrors(['email' => 'You do not have permission to access the admin panel.']);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        $this->adminGate->consumeGate($gate);
        $this->adminGate->clearSession($request);
        $this->adminGate->markPanelAccess($request);

        return redirect()->route('admin.home');
    }

    protected function redirectIfAlreadyAuthenticatedAdmin(Request $request): ?RedirectResponse
    {
        if ($this->adminGate->isAuthenticatedAdmin($request)) {
            return redirect()->route('admin.home');
        }

        return null;
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        abort(404);
    }
}
