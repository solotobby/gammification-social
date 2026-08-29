<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ConversationMessageAttachment extends Model
{
    use UuidTrait;

    protected $fillable = [
        'message_id',
        'path',
        'sort_order',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(ConversationMessage::class, 'message_id');
    }

    public function url(): string
    {
        return Storage::disk('spaces')->url($this->path);
    }
}
