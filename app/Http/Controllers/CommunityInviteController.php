<?php

namespace App\Http\Controllers;

use App\Mail\GeneralMail;
use App\Models\CommunityInvite;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\CommunityMembershipService;
use Illuminate\Support\Str;

class CommunityInviteController extends Controller
{
    /**
     * Accept an invite via its token (link or direct).
     */
    public function accept(string $token)
    {
        $invite = CommunityInvite::where('token', $token)->firstOrFail();

        if (! $invite->isUsable()) {
            return redirect()->route('community')->with('error', 'This invite link is no longer valid.');
        }

        if ($invite->type === 'direct' && $invite->user_id !== auth()->id()) {
            return redirect()->route('community')->with('error', 'This invite was sent to another user.');
        }

        $community = $invite->community;

        if (app(CommunityMembershipService::class)->isBanned($community, auth()->id())) {
            return redirect()->route('community')->with('error', 'You cannot rejoin this community.');
        }

        if ($community->members()->where('users.id', auth()->id())->exists()) {
            return redirect()->route('community.show', $community)
                ->with('status', 'You are already a member of this community.');
        }

        DB::transaction(function () use ($invite) {
            if (! app(CommunityMembershipService::class)->attachMember($invite->community, auth()->id())) {
                return;
            }

            if ($invite->type === 'direct') {
                $invite->update([
                    'status' => 'accepted',
                    'accepted_at' => now(),
                ]);
            } else {
                $invite->increment('uses_count');
                $invite->update(['accepted_at' => now()]);
            }
        });

        return redirect()->route('community.show', $community)
            ->with('status', 'Welcome to ' . $community->name . '!');
    }

    /**
     * Invite an existing user by username or email (called from Livewire or API).
     * Kept as static helper-style methods used by CommunityDetails.
     */
    public static function createDirectInvite(
        $community,
        User $inviter,
        User $invitee,
    ): CommunityInvite {
        CommunityInvite::where('community_id', $community->id)
            ->where('user_id', $invitee->id)
            ->where('type', 'direct')
            ->where('status', 'pending')
            ->update(['status' => 'revoked']);

        $invite = CommunityInvite::create([
            'id' => (string) Str::uuid(),
            'community_id' => $community->id,
            'invited_by' => $inviter->id,
            'user_id' => $invitee->id,
            'token' => Str::random(48),
            'type' => 'direct',
            'status' => 'pending',
            'expires_at' => now()->addDays(30),
        ]);

        $acceptUrl = route('community.invite.accept', $invite->token);

        $invitee->notify(new GeneralNotification([
            'title' => displayName($inviter->name) . ' invited you to ' . $community->name,
            'message' => 'You have been invited to join the private community "' . $community->name . '".',
            'icon' => 'fa-envelope text-primary',
            'url' => $acceptUrl,
        ]));

        if ($invitee->email) {
            Mail::to($invitee->email)->send(new GeneralMail(
                (object) ['name' => $invitee->name, 'email' => $invitee->email],
                'Invitation to join ' . $community->name,
                displayName($inviter->name) . ' invited you to join the private community "' . $community->name
                    . '". Click the link below to accept.'
            ));
        }

        return $invite;
    }

    public static function regenerateLinkInvite($community, User $inviter): CommunityInvite
    {
        CommunityInvite::where('community_id', $community->id)
            ->where('type', 'link')
            ->where('status', 'pending')
            ->update(['status' => 'revoked']);

        return CommunityInvite::create([
            'id' => (string) Str::uuid(),
            'community_id' => $community->id,
            'invited_by' => $inviter->id,
            'user_id' => null,
            'token' => Str::random(48),
            'type' => 'link',
            'status' => 'pending',
            'expires_at' => null,
        ]);
    }

    public static function activeLinkInvite($community): ?CommunityInvite
    {
        return CommunityInvite::where('community_id', $community->id)
            ->where('type', 'link')
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    public static function pendingDirectInviteFor($community, User $user): ?CommunityInvite
    {
        return CommunityInvite::where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->where('type', 'direct')
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();
    }
}
