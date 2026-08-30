<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\UserView;
use Illuminate\Support\Facades\DB;

class ViewService
{
    public function recordView(?Post $post, $userId): void
    {
        if (! $post || ! $userId) {
            return;
        }

        DB::transaction(function () use ($post, $userId) {

            $user = User::find($userId);

            if (! $user) {
                return;
            }

            $isSelfView = $userId === $post->user_id;

            //manage account monetization 
            $type = match (true) {
                $isSelfView => 'self-view',
                $user->status === 'SHADOW_BANNED' => 'self-view',
                default => 'view',
            };

            $view = UserView::firstOrCreate(
                [
                    'user_id' => $userId,
                    'post_id' => $post->id,
                ],
                [
                    'is_paid' => false,
                    'amount' => calculateUniqueEarningPerView(),
                    'poster_user_id' => $post->user_id,
                    'type' => $type, //$isSelfView ? 'self-view' : 'view',
                ]
            );

            userActivity('views', $userId);

            $post->video?->increment('view_count');

            if ($view->wasRecentlyCreated) {
                $post->increment('views');
            } else {
                $post->increment('views_external');
            }
        });

    }
}