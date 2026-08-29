<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Str;

class MessagingNotificationService
{
    public function notifyForMessage(
        Conversation $conversation,
        User $sender,
        ConversationMessage $message,
        User $recipient,
    ): void {
        $participant = app(ConversationService::class)->participant($conversation->id, $recipient->id);

        if ($participant?->muted_at) {
            return;
        }

        $isFirstMessage = ! ConversationMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('id', '!=', $message->id)
            ->exists();

        if (! $isFirstMessage && userIsOnline($recipient->id)) {
            return;
        }

        $senderName = displayName($sender->name);
        $preview = $this->preview($message);
        $url = route('messages.show', $conversation->id);

        if ($isFirstMessage) {
            $title = "{$senderName} started a conversation with you";
        } else {
            $title = "New message from {$senderName}";
        }

        $recipient->notify(new GeneralNotification([
            'title' => $title,
            'message' => $preview,
            'icon' => 'fa-comment text-primary',
            'url' => $url,
        ], sendMail: true));
    }

    protected function preview(ConversationMessage $message): string
    {
        if ($message->type === 'image') {
            return $message->body ?: 'Sent you a photo';
        }

        return Str::limit((string) $message->body, 80);
    }
}
