<?php

namespace App\Services;

use App\Models\Community;
use App\Models\User;
use App\Notifications\CommunityMemberJoinedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommunityMembershipService
{
    /**
     * Attach or re-activate a member. Returns false when the user is banned
     * or the community is archived (non-owner joins blocked).
     */
    public function attachMember(Community $community, string $userId, string $role = 'member'): bool
    {
        if ($community->isArchived() && (string) $community->user_id !== (string) $userId) {
            return false;
        }

        $isNewJoin = false;

        $attached = DB::transaction(function () use ($community, $userId, $role, &$isNewJoin) {
            $existing = DB::table('community_users')
                ->where('community_id', $community->id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status === 'banned') {
                return false;
            }

            if ($existing) {
                if ($existing->status !== 'active') {
                    $isNewJoin = true;
                }
                DB::table('community_users')
                    ->where('id', $existing->id)
                    ->update([
                        'role' => $role,
                        'status' => 'active',
                        'updated_at' => now(),
                    ]);
            } else {
                $isNewJoin = true;
                DB::table('community_users')->insert([
                    'id' => (string) Str::uuid(),
                    'community_id' => $community->id,
                    'user_id' => $userId,
                    'role' => $role,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return true;
        });

        if ($attached && $isNewJoin && (string) $community->user_id !== (string) $userId) {
            try {
                $joinedUser = User::find($userId);
                $owner = $community->user ?? User::find($community->user_id);
                if ($owner && $joinedUser) {
                    $owner->notify(new CommunityMemberJoinedNotification($community, $joinedUser));
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to dispatch CommunityMemberJoinedNotification: ' . $e->getMessage());
            }
        }

        return $attached;
    }

    public function leave(Community $community, User $user): bool
    {
        if ($community->user_id === $user->id) {
            return false;
        }

        return DB::transaction(function () use ($community, $user) {
            return (bool) DB::table('community_users')
                ->where('community_id', $community->id)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->delete();
        });
    }

    public function isBanned(Community $community, string $userId): bool
    {
        return DB::table('community_users')
            ->where('community_id', $community->id)
            ->where('user_id', $userId)
            ->where('status', 'banned')
            ->exists();
    }
}
