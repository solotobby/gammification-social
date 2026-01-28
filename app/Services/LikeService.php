<?php

namespace App\Services;

use App\Mail\GeneralMail;
use App\Models\Post;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\DB;



class LikeService
{

    public function toggle(string $postUnicode, User $user): void
    {
        $post = Post::with('user')
            ->where('unicode', $postUnicode)
            ->firstOrFail();

        DB::transaction(function () use ($post, $user) {

            $isSelfLike = $user->id === $post->user_id;

            $existingLike = $post->likes()
                ->where('user_id', $user->id)
                ->first();

            if ($existingLike) {
                // 👎 Unlike
                $existingLike->delete();
                $post->decrement('likes');

                return;
            }

            // ❤️ Like
            $post->likes()->create([
                'user_id'        => $user->id,
                'poster_user_id' => $post->user_id,
                'is_paid'        => false,
                'amount'         => calculateUniqueEarningPerLike(),
                'type'           => $isSelfLike ? 'self-like' : 'like',
            ]);

            $post->increment('likes');

            // 🔔 Notify post owner (skip self-like)
            if (! $isSelfLike) {
                
                $post->user->notify(
                    (new GeneralNotification([
                        'title'   => displayName($user->name) . ' liked your post',
                        'message' => displayName($user->name) . ' liked your post',
                        'icon'    => 'fa-heart text-danger',
                        'url'     => url('show/' . $post->id),
                    ]))->delay(now()->addSeconds(1))
                );
            }
        });
    }


}


