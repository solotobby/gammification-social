<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content', 'views', 'clicks', 'likes_count', 'comment_count', 'status'];


}
