<?php

namespace App\Services;

use App\Models\CommunityPost;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostLike;
use App\Models\CommunityPostView;
use App\Models\User;
use RuntimeException;

class CommunityPostEngagementService
{
    public function toggleLike(CommunityPost $post, User $user): void
    {
        $existing = CommunityPostLike::where('community_post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');

            return;
        }

        CommunityPostLike::create([
            'community_post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $post->increment('likes_count');
    }

    public function addComment(CommunityPost $post, User $user, string $content): void
    {
        $content = trim($content);

        if ($content === '' || mb_strlen($content) > 500) {
            throw new RuntimeException('Invalid comment.');
        }

        CommunityPostComment::create([
            'community_post_id' => $post->id,
            'user_id' => $user->id,
            'content' => $content,
        ]);

        $post->increment('comments_count');
    }

    public function recordView(CommunityPost $post, User $user, ?string $ipAddress = null): void
    {
        $view = CommunityPostView::firstOrCreate(
            ['community_post_id' => $post->id, 'user_id' => $user->id],
            ['ip_address' => $ipAddress]
        );

        if ($view->wasRecentlyCreated) {
            $post->increment('views_count');
        }
    }
}
