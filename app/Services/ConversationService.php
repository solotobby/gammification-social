<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    public function usersAreBlocked(string $userA, string $userB): bool
    {
        return UserBlock::query()
            ->where(function ($query) use ($userA, $userB) {
                $query->where('blocker_id', $userA)->where('blocked_id', $userB);
            })
            ->orWhere(function ($query) use ($userA, $userB) {
                $query->where('blocker_id', $userB)->where('blocked_id', $userA);
            })
            ->exists();
    }

    public function isBlockedBy(User $blocker, User $blocked): bool
    {
        return UserBlock::query()
            ->where('blocker_id', $blocker->id)
            ->where('blocked_id', $blocked->id)
            ->exists();
    }

    public function findOrCreateDirect(User $current, User $other): Conversation
    {
        if ($current->id === $other->id) {
            abort(422, 'You cannot message yourself.');
        }

        if ($this->usersAreBlocked($current->id, $other->id)) {
            abort(403, 'Messaging is not available for this user.');
        }

        $directKey = Conversation::directKey($current->id, $other->id);

        $existing = Conversation::query()->where('direct_key', $directKey)->first();
        if ($existing) {
            $this->unhideForUsers($existing, [$current->id, $other->id]);

            return $existing;
        }

        return DB::transaction(function () use ($current, $other, $directKey) {
            $conversation = Conversation::create([
                'type' => 'direct',
                'direct_key' => $directKey,
            ]);

            foreach ([$current, $other] as $user) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                ]);
            }

            return $conversation;
        });
    }

    public function participant(string $conversationId, string $userId): ?ConversationParticipant
    {
        return ConversationParticipant::query()
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();
    }

    public function assertParticipant(string $conversationId, string $userId): ConversationParticipant
    {
        $participant = $this->participant($conversationId, $userId);

        abort_unless($participant && $participant->hidden_at === null, 403);

        return $participant;
    }

    public function otherParticipant(Conversation $conversation, string $userId): ?User
    {
        return $conversation->participants()
            ->where('user_id', '!=', $userId)
            ->with('user:id,name,username,avatar,status')
            ->first()
            ?->user;
    }

    public function markRead(ConversationParticipant $participant): void
    {
        $participant->update(['last_read_at' => now()]);
    }

    public function togglePin(ConversationParticipant $participant): void
    {
        $participant->update([
            'pinned_at' => $participant->pinned_at ? null : now(),
        ]);
    }

    public function toggleMute(ConversationParticipant $participant): void
    {
        $participant->update([
            'muted_at' => $participant->muted_at ? null : now(),
        ]);
    }

    public function hide(ConversationParticipant $participant): void
    {
        $participant->update(['hidden_at' => now()]);
    }

    public function blockUser(User $blocker, User $blocked): void
    {
        UserBlock::query()->firstOrCreate([
            'blocker_id' => $blocker->id,
            'blocked_id' => $blocked->id,
        ]);

        $directKey = Conversation::directKey($blocker->id, $blocked->id);
        $conversation = Conversation::query()->where('direct_key', $directKey)->first();

        if ($conversation) {
            ConversationParticipant::query()
                ->where('conversation_id', $conversation->id)
                ->where('user_id', $blocker->id)
                ->update(['hidden_at' => now()]);
        }
    }

    public function unblockUser(User $blocker, User $blocked): void
    {
        UserBlock::query()
            ->where('blocker_id', $blocker->id)
            ->where('blocked_id', $blocked->id)
            ->delete();

        $directKey = Conversation::directKey($blocker->id, $blocked->id);
        $conversation = Conversation::query()->where('direct_key', $directKey)->first();

        if ($conversation) {
            ConversationParticipant::query()
                ->where('conversation_id', $conversation->id)
                ->where('user_id', $blocker->id)
                ->update(['hidden_at' => null]);
        }
    }

    protected function unhideForUsers(Conversation $conversation, array $userIds): void
    {
        ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->whereIn('user_id', $userIds)
            ->update(['hidden_at' => null]);
    }
}
