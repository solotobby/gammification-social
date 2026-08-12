<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentExternal;
use App\Models\CommentExternalMessage;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\UserComment;
use App\Models\UserLike;
use App\Models\UserView;
use App\Models\ViewsExternal;
use App\Models\Wallet;
use App\Support\StoredMedia;
use Illuminate\Support\Facades\DB;

class PostDeletionService
{
    /**
     * Permanently delete a timeline post and strip related monetization.
     * Claws back already-paid engagement amounts from the post owner's wallet.
     */
    public function delete(Post $post): array
    {
        return DB::transaction(function () use ($post) {
            $post = Post::query()
                ->whereKey($post->id)
                ->lockForUpdate()
                ->with(['images', 'video'])
                ->firstOrFail();

            $postId = (string) $post->id;
            $ownerId = (string) $post->user_id;

            $paidUsd = (float) UserView::where('post_id', $postId)->where('is_paid', true)->sum('amount')
                + (float) UserLike::where('post_id', $postId)->where('is_paid', true)->sum('amount')
                + (float) UserComment::where('post_id', $postId)->where('is_paid', true)->sum('amount');

            UserView::where('post_id', $postId)->delete();
            UserLike::where('post_id', $postId)->delete();
            UserComment::where('post_id', $postId)->delete();

            Comment::where('post_id', $postId)->delete();
            CommentExternal::where('post_id', $postId)->delete();
            CommentExternalMessage::where('post_id', $postId)->delete();
            ViewsExternal::where('post_id', $postId)->delete();
            PostReport::where('post_id', $postId)->delete();

            foreach ($post->images as $image) {
                StoredMedia::delete($image->path);
                $image->delete();
            }

            if ($post->video) {
                StoredMedia::delete($post->video->path);
                StoredMedia::delete($post->video->thumbnail_path);
                $post->video->forceDelete();
            }

            $clawedBack = 0.0;

            if ($paidUsd > 0) {
                $wallet = Wallet::where('user_id', $ownerId)->lockForUpdate()->first();

                if ($wallet) {
                    $converted = (float) convertToBaseCurrency($paidUsd, $wallet->currency);
                    $clawedBack = min((float) $wallet->balance, round($converted, 2));
                    $wallet->balance = max(0, round((float) $wallet->balance - $converted, 2));
                    $wallet->save();
                }
            }

            $post->delete();

            return [
                'post_id' => $postId,
                'owner_id' => $ownerId,
                'paid_usd_removed' => round($paidUsd, 4),
                'wallet_clawback' => $clawedBack,
            ];
        });
    }
}
