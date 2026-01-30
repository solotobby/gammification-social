<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\CommentService;
use App\Services\ViewService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TimelineDetailsComments extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public Post $post;

    public $message = '';
    public $postId;

    public $comments;
    public $commentsCount;
    public $perPage = 10;
    public $cursor;
    public $hasMoreComments;

    public function mount(Post $post, ViewService $viewService)
    {
        $this->post = $post;

        $this->loadComments();
        $this->processView($viewService);


        // $this->commentsCount = $this->post->comments ?? $this->post->postComments()->count();
    }

    public function loadComments()
    {

        // $this->comments = $this->post->postComments()
        //     ->with('user')
        //     ->latest()   // orders by created_at DESC (newest first)
        //     ->take($this->perPage)
        //     ->get()
        //     ->map(function ($comment) {
        //         return [
        //             'id' => $comment->id,
        //             'user_id' => $comment->user_id,
        //             'user_name' => $comment->user->name,
        //             'user_avatar' => $comment->user->avatar ?? 'src/assets/media/avatars/avatar3.jpg',
        //             'message' => $comment->message,
        //             'created_at' => $comment->created_at->toDateTimeString(),
        //         ];
        //     });

        $query = $this->post->postComments()
            ->with('user')
            ->latest('created_at')
            ->limit($this->perPage + 1); // 👈 fetch one extra

        // Cursor-based pagination
        if ($this->cursor) {
            $query->where('created_at', '<', $this->cursor);
        }

        $results = $query->get();

        // Determine if more exists
        $this->hasMoreComments = $results->count() > $this->perPage;

        // Trim extra record
        $comments = $results->take($this->perPage);

        // Set next cursor (oldest item loaded)
        if ($comments->isNotEmpty()) {
            $this->cursor = $comments->last()->created_at->toDateTimeString();
        }

        // Initialize collection if first load
        if (! $this->comments instanceof \Illuminate\Support\Collection) {
            $this->comments = collect();
        }

        // Append (NOT replace)
        $this->comments = $this->comments->concat(
            $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'name' => $comment->user->name,
                    'username' => $comment->user->username,
                    'avatar' => $comment->user->avatar ?? 'src/assets/media/avatars/avatar3.jpg',
                    'message' => $comment->message,
                    'created_at' => $comment->created_at->toDateTimeString(),
                ];
            })
        );



        $this->commentsCount =
            ($this->post->comments ?? 0)
            + ($this->post->comments_external ?? 0);

        // $this->commentsCount = $this->post->comments ?? $this->post->comment + $this->post->comments_external;

    }

    public function loadMore()
    {
        if (! $this->hasMoreComments) {
            return;
        }

        $this->loadComments();
    }

    public function processView($viewService)
    {

        $user = Auth::user();

        $viewService->recordView($this->post, $user->id);
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
        return view('livewire.user.timeline-details-comments');
    }
}
