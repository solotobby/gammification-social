<?php

namespace App\Http\Controllers;

use App\Models\StaffInvite;
use App\Models\User;
use App\Models\Wallet;
use App\Services\AdminGateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class StaffInviteController extends Controller
{
    public function show(string $token)
    {
        $invite = $this->findValidInvite($token);

        if (! $invite) {
            abort(404, 'This invite link is invalid or has expired.');
        }

        return view('auth.staff_invite', compact('invite'));
    }

    public function accept(Request $request, string $token)
    {
        $invite = $this->findValidInvite($token);

        if (! $invite) {
            return back()->withErrors(['email' => 'This invite link is invalid or has expired.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (User::query()->where('email', $invite->email)->exists()) {
            return back()->withErrors(['email' => 'An account with this email already exists.']);
        }

        $user = DB::transaction(function () use ($invite, $data) {
            $username = $this->uniqueUsername($data['name'], $invite->email);

            $user = User::create([
                'name' => $data['name'],
                'username' => $username,
                'email' => $invite->email,
                'password' => Hash::make($data['password']),
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
                'referral_code' => Str::upper(Str::random(8)),
            ]);

            Role::findOrCreate('staff', 'web');
            $user->assignRole('staff');

            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => '0.00',
                    'promoter_balance' => '0.00',
                    'referral_balance' => '0.00',
                    'currency' => 'USD',
                ]
            );

            $invite->update([
                'accepted_at' => now(),
                'accepted_user_id' => $user->id,
                'name' => $data['name'],
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();
        app(AdminGateService::class)->markPanelAccess($request);

        return redirect()
            ->route('admin.home')
            ->with('success', 'Welcome — your staff account is ready.');
    }

    protected function findValidInvite(string $token): ?StaffInvite
    {
        if ($token === '') {
            return null;
        }

        $invite = StaffInvite::query()->where('token', $token)->first();

        if (! $invite || ! $invite->isPending()) {
            return null;
        }

        return $invite;
    }

    protected function uniqueUsername(string $name, string $email): string
    {
        $base = Str::slug(Str::before($email, '@')) ?: Str::slug($name) ?: 'staff';
        $base = Str::limit($base, 20, '');
        $candidate = $base;
        $i = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $candidate = $base.$i;
            $i++;
        }

        return $candidate;
    }
}
