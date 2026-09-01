<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngagementPayoutComponent extends Model
{
    use UuidTrait;

    protected $fillable = [
        'engagement_monthly_stats_id',
        'user_id',
        'level',
        'month',
        'type',
        'amount',
        'currency',
        'note',
        'admin_id',
        'payout_id',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function engagementStat(): BelongsTo
    {
        return $this->belongsTo(EngagementMonthlyStat::class, 'engagement_monthly_stats_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }
}
