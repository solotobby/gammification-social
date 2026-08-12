<?php

namespace App\Livewire\User;

use App\Models\Comment;
use App\Services\CommentService;
use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostComments extends Component
{
    public Post $post;
    public $message = '';
    public $postId;

    public $comments;
    public $commentsCount;

    protected $listeners = ['refreshComments' => '$refresh'];

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->loadComments();
    }

    public function loadComments(): void
    {
        $this->comments = $this->post->postComments()
            ->with('user')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'name' => $comment->user->name,
                    'username' => $comment->user->username,
                    'avatar' => $comment->user->avatar,
                    'message' => $comment->message,
                    'created_at' => $comment->created_at->toDateTimeString(),
                ];
            });

        $this->commentsCount = (int) sumCounter($this->post->comments, $this->post->comment_external);
    }

    public function commentFeed(CommentService $service)
    {
        $this->validate([
            'message' => 'required|string|max:500',
        ]);

        if (trim($this->message) === '') {
            return;
        }

        $user = Auth::user();

        $service->addComment($this->post->id, $user, $this->message);

        $this->message = '';
        $this->post->refresh();
        $this->loadComments();
        $this->dispatch('commentAdded', postId: $this->post->id);
    }

    public function render()
    {
        return view('livewire.user.post-comments');
    }
}
