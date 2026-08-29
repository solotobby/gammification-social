<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationParticipant extends Model
{
    use UuidTrait;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'last_read_at',
        'pinned_at',
        'muted_at',
        'hidden_at',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'pinned_at' => 'datetime',
        'muted_at' => 'datetime',
        'hidden_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPinned(): bool
    {
        return $this->pinned_at !== null;
    }

    public function isMuted(): bool
    {
        return $this->muted_at !== null;
    }
}
