<?php

namespace App\Livewire\User;

use App\Jobs\ProcessCommentJob;
use App\Models\Comment;
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

    public function commentFeed()
    {
        $this->validate([
            'message' => 'required|string|max:500',
        ]);

        if (trim($this->message) === '') {
            return;
        }

        $text = $this->message;
        $this->message = '';
        $this->commentsCount++;

        ProcessCommentJob::dispatch(
            (string) $this->post->id,
            (string) Auth::id(),
            $text,
        );

        $this->comments = collect([[
            'id' => 'pending-'.now()->timestamp,
            'user_id' => Auth::id(),
            'name' => Auth::user()->name,
            'username' => Auth::user()->username,
            'avatar' => Auth::user()->avatar,
            'message' => $text,
            'created_at' => now()->toDateTimeString(),
        ]])->concat($this->comments ?? collect());

        $this->dispatch('commentAdded', postId: $this->post->id);
    }

    public function render()
    {
        return view('livewire.user.post-comments');
    }
}
