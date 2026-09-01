<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngagementMonthlyStat extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['user_id', 'level', 'month', 'views', 'likes', 'comments', 'points', 'amount', 'amount_manual', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function withdrawalMethod(){
        return $this->belongsTo(WithdrawalMethod::class, 'user_id', 'user_id');
    }

    public function wallet(){
        return $this->belongsTo(Wallet::class, 'user_id');
    }

    public function payoutComponents()
    {
        return $this->hasMany(EngagementPayoutComponent::class, 'engagement_monthly_stats_id');
    }
}
