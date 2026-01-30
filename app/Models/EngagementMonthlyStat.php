<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngagementMonthlyStat extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['user_id', 'level', 'month', 'views', 'likes', 'comments', 'points'];
}
