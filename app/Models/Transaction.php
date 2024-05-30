<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['user_id', 'ref', 'amount', 'currency', 'status', 'type', 'action', 'description'];
}
