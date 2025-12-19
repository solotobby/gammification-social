<?php

namespace App\Livewire\User;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserView;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
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
        Comment::create(['user_id' => auth()->user()->id, 'post_id' => $this->postQuery, 'message' =>  $this->message]);
        $pst = Post::with(['postComments'])->where(['id' => $this->postQuery])->first();
        

        $checkUniqueComment = UserComment::where(['user_id' => auth()->user()->id, 'post_id' => $this->postQuery])->first();

        if (!$checkUniqueComment) {
            // if (auth()->user()->id != $pst->user_id) {
                UserComment::create(['user_id' => auth()->user()->id, 'post_id' => $this->postQuery, 'is_paid' => false, 'amount' => calculateUniqueEarningPerComment(), 'poster_user_id' => $pst->user_id]);
                $pst->comments += 1;
                $pst->save();
            // }
        }else{
            //non-unique comment
            $pst->comment_external +=1;
            $pst->save();
        }

        $this->reset('message');
        // $this->timeline->push($pst);
        // $this->dispatch('refreshComments');

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

        }else{
            $post->views_external += 1; //unmonetized view count
            $post->save();
        }   
        

        $comments = Comment::where(['post_id' => $this->postQuery])->take($this->perpage)->orderBy('created_at', 'desc')->get();

        return view('livewire.user.show-new-posts', ['timeline' => $post, 'comments' => $comments]);
    }
}
