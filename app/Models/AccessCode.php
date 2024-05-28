<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessCode extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['tx_id', 'name', 'email', 'code', 'amount', 'is_active', 'level_id'];
}
