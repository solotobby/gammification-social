<?php

namespace App\Livewire\User;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserLike;
use App\Models\UserView;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;

class ShowNewPosts extends Component
{

    public $postQuery;
    public $perpage = 10;

    #[Validate('required|string')]
    public $message = '';

    public function mount($query)
    {
        $this->postQuery = $query;
    }



    public function toggleLike($postId)
    {

        $user = Auth::user();
        $post = Post::where('unicode', $postId)->first();

        // 🚫 Self-like: label as self-like
        $type = 'like';

        if ($user->id === $post->user_id) {
            $type = 'self-like';
        } else {
            // Increment external likes for analytics
            $type = 'like';
        }



        if ($post->isLikedBy(Auth::user())) {
            $post->likes()->where('user_id', Auth::id())->delete();
            $post->decrement('likes');
        } else {
            $post->likes()->create([
                'user_id' => Auth::id(),
                'is_paid' => false,
                'amount'  => calculateUniqueEarningPerLike(),
                'poster_user_id' => $post->user_id,
                'type' => $type
            ]);
            $post->increment('likes');

            // Queue the notification for async processing
            $post->user->notify((new GeneralNotification([
                'title'   => displayName($user->name) . ' liked your post',
                'message' => displayName($user->name) . ' liked your post',
                'icon'    => 'fa-thumbs-up text-primary',
                'url'     => url('show/' . $post->id),
            ]))->delay(now()->addSeconds(1))); // small delay to decouple DB write

        }

        // $this->timeline($post->id);

        // $this->dispatch('user.timeline');
    }

    public function loadMoreComments()
    {
        $this->perpage += 10;
    }

    public function comment()
    {

        $authUserId = auth()->id();
        $postId     = $this->postQuery;
        $message    = $this->message;

        DB::transaction(function () use ($authUserId, $postId, $message) {
            
            Comment::create([
                'user_id' => $authUserId,
                'post_id' => $postId,
                'message' => $message,
            ]);

           
            $post = Post::select('id', 'user_id')
                ->whereKey($postId)
                ->lockForUpdate() // 🔒 prevents race conditions
                ->firstOrFail();

            $isSelfComment = $authUserId === $post->user_id;

         
            $isFirstComment = ! UserComment::where([
                'user_id' => $authUserId,
                'post_id' => $postId,
            ])->exists();

            if ($isFirstComment) {

            
                UserComment::create([
                    'user_id'        => $authUserId,
                    'post_id'        => $postId,
                    'is_paid'        => false,
                    'amount'         => calculateUniqueEarningPerComment(),
                    'poster_user_id' => $post->user_id,
                    'type'           => $isSelfComment ? 'self-comment' : 'comment',
                ]);

                // Atomic increment
                Post::whereKey($postId)->increment('comments');

                // Notify post owner (no self notification)
                if ($authUserId !== $post->user_id) {
                    User::whereKey($post->user_id)->first()
                        ->notify(new GeneralNotification([
                            'title'   => displayName(auth()->user()->name) . ' commented on your post',
                            'message' => displayName(auth()->user()->name) . ' commented on your post',
                            'icon'    => 'fa-comment text-primary',
                            'url'     => url('show/' . $post->id),
                        ]));
                }
            } else {
                // Non-unique comment
                Post::whereKey($postId)->increment('comment_external');
            }
        });

        //  Reset input AFTER commit
        $this->reset('message');

        // $this->reset('message');
        // $this->timeline->push($pst);
        // $this->dispatch('refreshComments');

    }

    public function deletePost($postId)
    {

        $post = Post::where('unicode', $postId)->first();
        $post->delete();

        UserView::where('post_id', $post->id)->delete();
        UserComment::where('post_id', $post->id)->delete();
        UserLike::where('post_id', $post->id)->delete();

        redirect('timeline');

        session()->flash('success', "Post deleted");
    }


    public function render()
    {

        $userId = auth()->id();
        $post = Post::whereKey($this->postQuery)->firstOrFail();

        DB::transaction(function () use ($post, $userId) {

            $isSelfView = $userId === $post->user_id;

            $view = UserView::firstOrCreate(
                [
                    'user_id' => $userId,
                    'post_id' => $post->id,
                ],
                [
                    'is_paid' => false,
                    'amount' => calculateUniqueEarningPerView(),
                    'poster_user_id' => $post->user_id,
                    'type' => $isSelfView ? 'self-view' : 'view',
                ]
            );

            if ($view->wasRecentlyCreated) {
                $post->increment('views');
            } else {
                $post->increment('views_external');
            }
        });



        $comments = Comment::where(['post_id' => $this->postQuery])->take($this->perpage)->orderBy('created_at', 'desc')->get();

        return view('livewire.user.show-new-posts', ['timeline' => $post, 'comments' => $comments]);
    }
}
