<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['user_id', 'content', 'views', 'clicks',
                             'likes', 'likes_external', 'views_external', 
                             'comments', 'status', 'unicode'
                            ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function userLikes(){
        return $this->belongsToMany(User::class, 'user_likes', 'post_id');
    }

    public function userViews(){
        return $this->belongsToMany(User::class, 'user_views', 'post_id');
    }

    public function postComments(){
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

}
