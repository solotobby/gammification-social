<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ConversationMessage extends Model
{
    use UuidTrait;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'type',
        'body',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ConversationMessageAttachment::class, 'message_id');
    }

    public function attachmentUrls(): array
    {
        return $this->attachments
            ->sortBy('sort_order')
            ->map(fn (ConversationMessageAttachment $attachment) => $attachment->url())
            ->values()
            ->all();
    }

    public function toBroadcastArray(): array
    {
        $this->loadMissing(['user:id,name,username,avatar', 'attachments']);

        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'body' => $this->body,
            'images' => $this->attachmentUrls(),
            'created_at' => $this->created_at?->toIso8601String(),
            'at' => $this->created_at?->format('g:i A'),
            'sender' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'username' => $this->user?->username,
                'avatar' => $this->user?->avatar,
            ],
        ];
    }
}
