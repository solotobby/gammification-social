<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\StaffInvite;
use App\Models\User;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function __construct(private AdminAuditService $audit) {}

    public function index()
    {
        $staff = User::role('staff')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'username', 'created_at', 'status']);

        $invites = StaffInvite::query()
            ->with('inviter:id,name')
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.staff.index', compact('staff', 'invites'));
    }

    public function invite(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
        ]);

        $email = strtolower(trim($data['email']));

        if (User::query()->where('email', $email)->exists()) {
            $existing = User::query()->where('email', $email)->first();
            if ($existing->hasRole('admin') || $existing->hasRole('staff')) {
                return back()->with('error', 'That email already has panel access.');
            }

            return back()->with('error', 'That email belongs to an existing user account. Use a dedicated staff email.');
        }

        $pending = StaffInvite::query()
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($pending) {
            return back()->with('error', 'A pending invite already exists for that email.');
        }

        $ttlDays = max(1, (int) config('admin.staff_invite_ttl_days', 7));

        $invite = StaffInvite::create([
            'name' => $data['name'],
            'email' => $email,
            'token' => StaffInvite::issueToken(),
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays($ttlDays),
        ]);

        $acceptUrl = $invite->acceptUrl();
        $content = '<p>You have been invited to join the Payhankey staff panel.</p>'
            .'<p>Use the button below to create your staff password and activate access.</p>'
            .'<p><a class="btn" href="'.e($acceptUrl).'">Accept invite</a></p>'
            .'<p>This link expires in '.$ttlDays.' days.</p>'
            .'<p>If the button does not work, copy this URL:<br>'.e($acceptUrl).'</p>';

        Mail::to($email)->send(new GeneralMail(
            (object) ['name' => $data['name']],
            'Payhankey staff invitation',
            $content
        ));

        $this->audit->log('staff.invited', $invite, ['email' => $email]);

        return back()->with('success', 'Invite sent to '.$email.'.');
    }

    public function revokeInvite(StaffInvite $invite)
    {
        if ($invite->accepted_at) {
            return back()->with('error', 'This invite was already accepted.');
        }

        $invite->delete();
        $this->audit->log('staff.invite_revoked', null, ['email' => $invite->email]);

        return back()->with('success', 'Invite revoked.');
    }

    public function remove(User $user)
    {
        if (! $user->hasRole('staff') || $user->hasRole('admin')) {
            return back()->with('error', 'Only staff accounts can be removed here.');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot remove yourself.');
        }

        $user->removeRole('staff');
        $this->audit->log('staff.removed', $user);

        return back()->with('success', $user->email.' is no longer staff.');
    }
}
