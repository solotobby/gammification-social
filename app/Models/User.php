<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'referral_code',
        'password',
        'access_code_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function level(){
       return $this->belongsTo(AccessCode::class, 'access_code_id');
        // return $this->hasMany(Level::class, 'user_levels', 'user_id');
    }

    public function social(){
        return $this->hasOne(Social::class, 'user_id');
    }

    public function scopeWithPostStats(Builder $query, $userId)
    {
        return $query->where('id', $userId)
                     ->withCount(['posts as total_likes' => function ($query) {
                         $query->select(DB::raw('sum(likes)'));
                     }])
                     ->withCount(['posts as total_views' => function ($query) {
                         $query->select(DB::raw('sum(views)'));
                     }])
                     ->withCount(['posts as total_comments' => function ($query) {
                         $query->select(DB::raw('count(comments)'));
                     }]);
    }



    public function getTotalLikesAttribute()
    {
        return $this->posts()->sum('likes');
    }

    public function getTotalViewsAttribute()
    {
        return $this->posts()->sum('views');
    }

    public function getTotalCommentsAttribute()
    {
        return $this->posts()->sum('comments');
    }

    //like methods 
    public function likes()
    {
        return $this->hasMany(UserLike::class);
    }

    public function like(Post $post)
    {
        return $this->likes()->create(['post_id' => $post->id]);
    }

    public function unlike(Post $post)
    {
        return $this->likes()->where('post_id', $post->id)->delete();
    }


    
}
