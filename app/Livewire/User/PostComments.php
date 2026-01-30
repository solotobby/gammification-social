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
            

            // $this->commentsCount = $this->post->comments ?? $this->post->postComments()->count();
    }

    public function loadComments(){

        $this->comments = $this->post->postComments()
            ->with('user')
            ->latest()   // orders by created_at DESC (newest first)
            ->take(3)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'user_name' => $comment->user->name,
                    'user_avatar' => $comment->user->avatar ?? 'src/assets/media/avatars/avatar3.jpg',
                    'message' => $comment->message,
                    'created_at' => $comment->created_at->toDateTimeString(),
                ];
            });

            $this->commentsCount = $this->post->comments ?? $this->post->comment + $this->post->comments_external;

            


    }



    public function commentFeed(CommentService $service)
    {

         $this->validate([
            'message' => 'required|string|max:500',
        ]);


        if (trim($this->message) === '') return;

        $user = Auth::user();

        $service->addComment($this->post->id,  $user, $this->message); //to be converted to job later


        $this->comments->prepend([
            // // 'id' => $this->post->id, //'tmp-' . uniqid(),
          
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_avatar' => $user->avatar ?? 'src/assets/media/avatars/avatar3.jpg',
            'message' => $this->message,
            'created_at' => now()->toDateTimeString(),
        ]);

         // Notify parent (post card)
        $this->dispatch('commentAdded');

        $this->message = '';
        // $this->emit('refreshComments'); // optional for parent component

        $this->loadComments();
    }




    public function render()
    {
        return view('livewire.user.post-comments');
    }
}
