<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutPoolTopup extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'level',
        'month',
        'amount',
        'note',
        'admin_id',
        'distributed_count',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
