<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content', 'views', 'clicks',
                             'likes', 'likes_external', 'views_external', 
                             'comments', 'status'
                            ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function userLikes(){
        return $this->belongsToMany(User::class, 'user_likes', 'post_id');
    }

}
