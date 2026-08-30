<?php

namespace App\Livewire\User;

use App\Jobs\ProcessCommentJob;
use App\Jobs\ProcessLikeJob;
use App\Jobs\ProcessViewJob;
use App\Models\Comment;
use App\Models\Post;
use App\Models\UserComment;
use App\Models\UserLike;
use App\Models\UserView;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class ShowNewPosts extends Component
{

    public $postQuery;
    public $perpage = 10;
    public bool $viewRecorded = false;

    #[Validate('required|string')]
    public $message = '';

    public function mount($query)
    {
        $this->postQuery = $query;

        if (auth()->check()) {
            ProcessViewJob::dispatch(
                (string) $this->postQuery,
                (string) auth()->id(),
            );
            $this->viewRecorded = true;
        }
    }



    public function toggleLike($postId)
    {
        if (! Auth::check()) {
            return;
        }

        ProcessLikeJob::dispatch(
            (string) $postId,
            (string) Auth::id(),
        );
    }

    public function loadMoreComments()
    {
        $this->perpage += 10;
    }

    public function comment()
    {
        $this->validate();

        $message = trim($this->message);

        if ($message === '' || ! auth()->check()) {
            return;
        }

        ProcessCommentJob::dispatch(
            (string) $this->postQuery,
            (string) auth()->id(),
            $message,
        );

        $this->reset('message');
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
        $post = Post::whereKey($this->postQuery)->firstOrFail();
        $comments = Comment::where(['post_id' => $this->postQuery])->take($this->perpage)->orderBy('created_at', 'desc')->get();

        return view('livewire.user.show-new-posts', ['timeline' => $post, 'comments' => $comments]);
    }
}
