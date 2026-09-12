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

            // Manage monetization qualification: post must be monetizable, not self-view, and not shadow-banned
            $isMonetizable = $post->canMonetize() && ! $isSelfView && $user->status !== 'SHADOW_BANNED';

            $type = match (true) {
                $isSelfView => 'self-view',
                $user->status === 'SHADOW_BANNED' => 'self-view',
                ! $post->canMonetize() => 'unmonetized',
                default => 'view',
            };

            $amount = $isMonetizable ? calculateUniqueEarningPerView() : 0.00;

            $view = UserView::firstOrCreate(
                [
                    'user_id' => $userId,
                    'post_id' => $post->id,
                ],
                [
                    'is_paid' => false,
                    'amount' => $amount,
                    'poster_user_id' => $post->user_id,
                    'type' => $type,
                ]
            );

            userActivity('views', $userId);

            $post->video?->increment('view_count');

            if ($view->wasRecentlyCreated) {
                Post::whereKey($post->id)->increment('views');
            } else {
                Post::whereKey($post->id)->increment('views_external');
            }
        });

    }
}