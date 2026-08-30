<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['user_id', 'promoter_balance', 'referral_balance', 'balance', 'currency', 'level', 'usdt_wallet_address', 'currency_updated_at', 'paykoin_spendable', 'paykoin_earned'];

    protected $casts = [
        'currency_updated_at' => 'datetime',
        'paykoin_spendable' => 'integer',
        'paykoin_earned' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
