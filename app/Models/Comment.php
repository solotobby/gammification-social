<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['user_id', 'post_id', 'message'];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
