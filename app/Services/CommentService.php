<?php

namespace App\Services;

use App\Models\User;
use App\Models\Comment;
use App\Models\Post;
use App\Models\UserComment;
use Illuminate\Support\Facades\DB;
use App\Notifications\GeneralNotification;

class CommentService
{

    public $postId;
    public $user;
    public $message;

    public function addComment($postId, User $user, $message)
    {

        $authUserId = $user->id;

        DB::transaction(function () use ($authUserId, $postId, $message, $user) {

          

            // 1️⃣ Create the raw comment
            Comment::create([
                'user_id' => $authUserId,
                'post_id' => $postId,
                'message' => $message,
            ]);

            // 2️⃣ Lock the post to prevent race conditions
            $post = Post::select('id', 'user_id')
                ->whereKey($postId)
                ->lockForUpdate()
                ->firstOrFail();

            $isSelfComment = $authUserId === $post->user_id;

            // 3️⃣ Check if this is the user's first comment
            $isFirstComment = ! UserComment::where([
                'user_id' => $authUserId,
                'post_id' => $postId,
            ])->exists();

            if ($isFirstComment) {

                // 4️⃣ Create a unique comment entry
                UserComment::create([
                    'user_id'        => $authUserId,
                    'post_id'        => $postId,
                    'is_paid'        => false,
                    'amount'         => calculateUniqueEarningPerComment(),
                    'poster_user_id' => $post->user_id,
                    'type'           => $isSelfComment ? 'self-comment' : 'comment',
                ]);

                // 5️⃣ Atomic increment
                Post::whereKey($postId)->increment('comments');


                // 6️⃣ Notify post owner (skip self-comment)
                if (! $isSelfComment) {
                    $postOwner = User::find($post->user_id);
                    $postOwner?->notify(new GeneralNotification([
                        'title'   => displayName($user->name) . ' commented on your post',
                        'message' => displayName($user->name) . ' commented on your post',
                        'icon'    => 'fa-comment text-primary',
                        'url'     => url('show/' . $post->id),
                    ]));
                }


            } else {

                // Non-unique comment
                // Post::whereKey($postId)->increment('comment_external');
                Post::whereKey($postId)->update([
                    'comment_external' => DB::raw('COALESCE(comment_external, 0) + 1'),
                ]);
            }
        });
    }
}
