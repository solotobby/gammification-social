<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationMessageAttachment;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageService
{
    public function __construct(private ConversationService $conversations) {}

    public function send(
        Conversation $conversation,
        User $sender,
        ?string $body,
        array $images = [],
    ): ConversationMessage {
        $this->conversations->assertParticipant($conversation->id, $sender->id);

        $other = $this->conversations->otherParticipant($conversation, $sender->id);
        if ($other && $this->conversations->usersAreBlocked($sender->id, $other->id)) {
            abort(403, 'Messaging is not available for this user.');
        }

        $trimmed = trim((string) $body);
        $hasImages = count($images) > 0;

        abort_unless($trimmed !== '' || $hasImages, 422, 'Message cannot be empty.');

        $message = DB::transaction(function () use ($conversation, $sender, $trimmed, $hasImages, $images) {
            $message = ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $sender->id,
                'type' => $hasImages ? 'image' : 'text',
                'body' => $trimmed !== '' ? $trimmed : null,
            ]);

            foreach (array_values($images) as $index => $image) {
                $path = $this->storeImage($conversation->id, $image);
                ConversationMessageAttachment::create([
                    'message_id' => $message->id,
                    'path' => $path,
                    'sort_order' => $index,
                ]);
            }

            $conversation->update(['last_message_at' => now()]);

            ConversationParticipant::query()
                ->where('conversation_id', $conversation->id)
                ->where('user_id', '!=', $sender->id)
                ->update(['hidden_at' => null]);

            return $message->fresh(['user:id,name,username,avatar', 'attachments']);
        });

        $this->notifyParticipants($conversation, $sender, $message);

        return $message;
    }

    public function markConversationRead(Conversation $conversation, User $reader): void
    {
        $participant = $this->conversations->assertParticipant($conversation->id, $reader->id);
        $this->conversations->markRead($participant);
    }

    protected function storeImage(string $conversationId, UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::uuid().'.'.$extension;

        return Storage::disk('spaces')->putFileAs(
            'messages/'.$conversationId,
            $file,
            $filename,
            'public',
        );
    }

    protected function notifyParticipants(
        Conversation $conversation,
        User $sender,
        ConversationMessage $message,
    ): void {
        $participants = ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', '!=', $sender->id)
            ->with('user:id,name,username,avatar')
            ->get();

        foreach ($participants as $participant) {
            if ($participant->user) {
                app(MessagingNotificationService::class)->notifyForMessage(
                    $conversation,
                    $sender,
                    $message,
                    $participant->user,
                );
            }
        }
    }

    public function conversationListItem(
        Conversation $conversation,
        string $forUserId,
        ?ConversationMessage $previewMessage = null,
    ): array {
        $participant = $this->conversations->participant($conversation->id, $forUserId);
        $other = $this->conversations->otherParticipant($conversation, $forUserId);

        $latest = $previewMessage
            ?: $conversation->messages()->latest()->with('attachments')->first();

        $lastRead = $participant?->last_read_at;
        $unread = 0;

        if ($latest && $latest->user_id !== $forUserId) {
            $unread = ConversationMessage::query()
                ->where('conversation_id', $conversation->id)
                ->when($lastRead, fn ($q) => $q->where('created_at', '>', $lastRead))
                ->where('user_id', '!=', $forUserId)
                ->count();
        }

        $preview = $this->previewText($latest, $forUserId);
        $fromMe = $latest && $latest->user_id === $forUserId;

        return [
            'id' => $conversation->id,
            'name' => $other?->name ?? 'Unknown',
            'username' => $other?->username ?? 'user',
            'avatar' => $other?->avatar ?: asset('src/assets/media/avatars/avatar13.jpg'),
            'online' => false,
            'typing' => false,
            'unread' => $unread,
            'muted' => (bool) $participant?->muted_at,
            'pinned' => (bool) $participant?->pinned_at,
            'last_message' => $preview,
            'last_at' => $latest?->created_at?->diffForHumans(short: true) ?? '',
            'last_from_me' => $fromMe,
            'last_status' => $fromMe ? ($unread > 0 ? 'delivered' : 'read') : null,
            'has_image' => $latest?->type === 'image',
        ];
    }

    public function previewText(?ConversationMessage $message, string $viewerId): string
    {
        if (! $message) {
            return 'Start a conversation';
        }

        if ($message->type === 'image') {
            return $message->body ?: 'Photo';
        }

        return Str::limit((string) $message->body, 80);
    }

    public function threadMessages(Conversation $conversation, User $viewer, int $limit = 50): array
    {
        $messages = $conversation->messages()
            ->with(['user:id,name,username,avatar', 'attachments'])
            ->latest()
            ->limit($limit)
            ->get()
            ->sortBy('created_at')
            ->values();

        $otherParticipant = $this->conversations->participant($conversation->id, $viewer->id);
        $otherReadAt = ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', '!=', $viewer->id)
            ->value('last_read_at');

        $rows = [];
        $lastDate = null;

        foreach ($messages as $message) {
            $dateLabel = $message->created_at?->isToday()
                ? 'Today'
                : ($message->created_at?->isYesterday() ? 'Yesterday' : $message->created_at?->format('M j, Y'));

            if ($dateLabel !== $lastDate) {
                $rows[] = ['id' => 'd-'.$message->id, 'type' => 'date', 'label' => $dateLabel];
                $lastDate = $dateLabel;
            }

            $mine = $message->user_id === $viewer->id;
            $status = 'sent';

            if ($mine && $otherReadAt && $message->created_at <= $otherReadAt) {
                $status = 'read';
            } elseif ($mine) {
                $status = 'delivered';
            }

            if ($message->type === 'image') {
                $rows[] = [
                    'id' => $message->id,
                    'type' => 'image',
                    'mine' => $mine,
                    'caption' => $message->body,
                    'images' => $message->attachmentUrls(),
                    'at' => $message->created_at?->format('g:i A'),
                    'status' => $status,
                ];
            } else {
                $rows[] = [
                    'id' => $message->id,
                    'type' => 'text',
                    'mine' => $mine,
                    'body' => $message->body,
                    'at' => $message->created_at?->format('g:i A'),
                    'status' => $status,
                ];
            }
        }

        return $rows;
    }
}
