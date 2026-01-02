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


        $post = Post::where('unicode', $postId)->first();

        if ($post->isLikedBy(Auth::user())) {
            $post->likes()->where('user_id', Auth::id())->delete();
            $post->decrement('likes');
        } else {
            $post->likes()->create(['user_id' => Auth::id()]);
            $post->increment('likes');
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

            // 1️⃣ Create comment
            Comment::create([
                'user_id' => $authUserId,
                'post_id' => $postId,
                'message' => $message,
            ]);

            // 2️⃣ Fetch post owner only
            $post = Post::select('id', 'user_id')
                ->whereKey($postId)
                ->lockForUpdate() // 🔒 prevents race conditions
                ->firstOrFail();

            // 3️⃣ Check unique comment
            $isFirstComment = ! UserComment::where([
                'user_id' => $authUserId,
                'post_id' => $postId,
            ])->exists();

            if ($isFirstComment) {

                // 4️⃣ Record unique comment
                UserComment::create([
                    'user_id'        => $authUserId,
                    'post_id'        => $postId,
                    'is_paid'        => false,
                    'amount'         => calculateUniqueEarningPerComment(),
                    'poster_user_id' => $post->user_id,
                ]);

                // 5️⃣ Atomic increment
                Post::whereKey($postId)->increment('comments');

                // 6️⃣ Notify post owner (no self notification)
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

        // 7️⃣ Reset input AFTER commit
        $this->reset('message');
        
        // $this->reset('message');
        // $this->timeline->push($pst);
        // $this->dispatch('refreshComments');

    }

    public function deletePost($postId){
        
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

        $post = Post::with(['postComments'])->where('id', $this->postQuery)->first();

        $regView = UserView::where(['user_id' => auth()->user()->id, 'post_id' => $this->postQuery])->first();
        if (!$regView) {
            // if (auth()->user()->id != $post->user_id) {
            UserView::create(['user_id' => auth()->user()->id, 'post_id' => $this->postQuery, 'is_paid' => false, 'amount' => calculateUniqueEarningPerView(), 'poster_user_id' => $post->user_id]);
            $post->views += 1;  //UNIQUE VIEW COUNT
            $post->save();
            // }

        } else {
            $post->views_external += 1; //unmonetized view count
            $post->save();
        }


        $comments = Comment::where(['post_id' => $this->postQuery])->take($this->perpage)->orderBy('created_at', 'desc')->get();

        return view('livewire.user.show-new-posts', ['timeline' => $post, 'comments' => $comments]);
    }
}
