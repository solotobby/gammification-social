<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccessCodeMail;
use App\Models\AccessCode;
use App\Models\Level;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AccessCodeController extends Controller
{
    public function __construct(private AdminAuditService $audit) {}

    public function sendAccessCode()
    {
        $res = securityVerification();
        if ($res !== 'OK') {
            abort(404);
        }

        return view('admin.accesscode.send_code', [
            'levels' => Level::query()->orderBy('name')->get(['id', 'name', 'amount']),
        ]);
    }

    public function processValidateCode(Request $request)
    {
        $res = securityVerification();
        if ($res !== 'OK') {
            abort(404);
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'level' => 'required|exists:levels,id',
            'validationCode' => 'required|string',
        ]);

        if ($validated['validationCode'] !== config('services.env.validation_code')) {
            return back()->with('error', 'Invalid validation code.');
        }

        $existing = AccessCode::query()
            ->where('email', $validated['email'])
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return back()->with('error', 'This email already has an active access code.');
        }

        $level = Level::query()->findOrFail($validated['level']);
        $code = Str::upper(Str::random(7));

        $accessCode = AccessCode::create([
            'tx_id' => (string) time(),
            'name' => $level->name,
            'email' => $validated['email'],
            'amount' => $level->amount,
            'code' => $code,
            'level_id' => $level->id,
            'is_active' => true,
        ]);

        $this->audit->log('access_code.created', $accessCode, [
            'email' => $accessCode->email,
            'level' => $level->name,
        ]);

        try {
            Mail::to($validated['email'])->send(new AccessCodeMail($code));
        } catch (\Throwable) {
            // Email delivery is best-effort; code is still created.
        }

        return back()->with('success', 'Access code created and sent to ' . $validated['email']);
    }

    public function listAccessCode(Request $request)
    {
        $status = $request->query('status');
        $level = $request->query('level');
        $email = trim((string) $request->query('email'));

        $lists = AccessCode::query()
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'redeemed', fn ($q) => $q->where('is_active', false))
            ->when($level, fn ($q) => $q->where('name', $level))
            ->when($email, fn ($q) => $q->where('email', 'like', "%{$email}%"))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $levels = AccessCode::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        return view('admin.accesscode.list', compact('lists', 'levels', 'status', 'level', 'email'));
    }
}
