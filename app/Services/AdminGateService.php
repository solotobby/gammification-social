<?php

namespace App\Services;

use App\Models\AdminLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminGateService
{
    public const SESSION_GATE_ID = 'admin_gate_id';

    public const SESSION_FINGERPRINT = 'admin_gate_fingerprint';

    public const SESSION_PANEL_ACCESS = 'admin_panel_access';

    public function passesEnvironmentCheck(): bool
    {
        if ($this->shouldBypassIpCheck()) {
            return true;
        }

        return securityVerification() === 'OK';
    }

    public function clientIp(Request $request): string
    {
        if ($this->shouldBypassIpCheck()) {
            return (string) config('admin.local_client_ip', '127.0.0.1');
        }

        return (string) $request->ip();
    }

    protected function shouldBypassIpCheck(): bool
    {
        return app()->environment('local')
            && (bool) config('admin.bypass_ip_check_on_local', true);
    }

    public function markPanelAccess(Request $request): void
    {
        $request->session()->put(self::SESSION_PANEL_ACCESS, true);
    }

    public function hasPanelAccess(Request $request): bool
    {
        return (bool) $request->session()->get(self::SESSION_PANEL_ACCESS, false);
    }

    public function isAuthenticatedAdmin(Request $request): bool
    {
        $user = $request->user();

        return $user !== null
            && $user->hasRole('admin')
            && $this->hasPanelAccess($request);
    }

    public function fingerprint(Request $request): string
    {
        return hash('sha256', $this->clientIp($request) . '|' . (string) $request->userAgent());
    }

    public function issueGate(Request $request): AdminLogin
    {
        $location = ipLocation();
        $ttl = max(1, (int) config('admin.gate_ttl_minutes', 15));

        return AdminLogin::create([
            'ip' => $this->clientIp($request),
            'country' => $location['country'] ?? '',
            'city' => $location['city'] ?? '',
            'code' => Str::random(128),
            'status' => true,
            'expires_at' => now()->addMinutes($ttl),
            'user_agent_hash' => $this->fingerprint($request),
        ]);
    }

    public function findValidGate(string $code, Request $request): ?AdminLogin
    {
        if ($code === '') {
            return null;
        }

        $gate = AdminLogin::query()
            ->where('code', $code)
            ->where('status', true)
            ->whereNull('used_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $gate) {
            return null;
        }

        if ($gate->ip !== $this->clientIp($request)) {
            return null;
        }

        if ($gate->user_agent_hash && $gate->user_agent_hash !== $this->fingerprint($request)) {
            return null;
        }

        return $gate;
    }

    public function bindGateToSession(AdminLogin $gate, Request $request): void
    {
        $request->session()->put([
            self::SESSION_GATE_ID => $gate->id,
            self::SESSION_FINGERPRINT => $this->fingerprint($request),
        ]);
    }

    public function gateMatchesSession(?AdminLogin $gate, Request $request): bool
    {
        if (! $gate) {
            return false;
        }

        return $request->session()->get(self::SESSION_GATE_ID) === $gate->id
            && $request->session()->get(self::SESSION_FINGERPRINT) === $this->fingerprint($request);
    }

    public function consumeGate(AdminLogin $gate): void
    {
        $gate->forceFill([
            'status' => false,
            'used_at' => now(),
        ])->save();
    }

    public function clearSession(Request $request): void
    {
        $request->session()->forget([
            self::SESSION_GATE_ID,
            self::SESSION_FINGERPRINT,
            self::SESSION_PANEL_ACCESS,
        ]);
    }

    public function registrationUrl(AdminLogin $gate): string
    {
        return url(config('admin.registration_path', 'registration') . '/' . $gate->code);
    }
}
