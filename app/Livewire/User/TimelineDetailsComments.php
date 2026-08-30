<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\CommentService;
use App\Services\ViewService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TimelineDetailsComments extends Component
{
    public Post $post;

    public $message = '';

    public $comments;

    public $commentsCount;

    public $perPage = 10;

    public $cursor;

    public $hasMoreComments;

    public bool $viewRecorded = false;

    protected $listeners = [
        'commentAdded' => 'onCommentAdded',
    ];

    public function onCommentAdded(): void
    {
        $this->loadComments(reset: true);
    }

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->loadComments(reset: true);
        $this->processView();
    }

    public function loadComments(bool $reset = false): void
    {
        if ($reset) {
            $this->cursor = null;
            $this->comments = collect();
        }

        $query = $this->post->postComments()
            ->with('user')
            ->latest('created_at')
            ->limit($this->perPage + 1);

        if ($this->cursor) {
            $query->where('created_at', '<', $this->cursor);
        }

        $results = $query->get();

        $this->hasMoreComments = $results->count() > $this->perPage;
        $page = $results->take($this->perPage);

        if ($page->isNotEmpty()) {
            $this->cursor = $page->last()->created_at->toDateTimeString();
        }

        $mapped = $page->map(function ($comment) {
            return [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'name' => $comment->user->name,
                'username' => $comment->user->username,
                'avatar' => $comment->user->avatar ?? 'src/assets/media/avatars/avatar3.jpg',
                'message' => $comment->message,
                'created_at' => $comment->created_at->toDateTimeString(),
            ];
        });

        $this->comments = ($this->comments ?? collect())->concat($mapped);

        $this->post->refresh();
        $this->commentsCount = (int) sumCounter($this->post->comments, $this->post->comment_external);
    }

    public function loadMore(): void
    {
        if (! $this->hasMoreComments) {
            return;
        }

        $this->loadComments(reset: false);
    }

    public function processView(): void
    {
        if ($this->viewRecorded || ! Auth::check()) {
            return;
        }

        $this->viewRecorded = true;

        // ProcessViewJob::dispatch(
        //     (string) $this->post->id,
        //     (string) Auth::id(),
        // );
        app(ViewService::class)->recordView($this->post, (string) Auth::id());

        $this->dispatch('viewRecorded');
    }

    public function commentFeed(): void
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

        // ProcessCommentJob::dispatch(
        //     (string) $this->post->id,
        //     (string) Auth::id(),
        //     $text,
        // );
        app(CommentService::class)->addComment((string) $this->post->id, Auth::user(), $text);

        $this->comments = collect([[
            'id' => 'pending-'.now()->timestamp,
            'user_id' => Auth::id(),
            'name' => Auth::user()->name,
            'username' => Auth::user()->username,
            'avatar' => Auth::user()->avatar ?? 'src/assets/media/avatars/avatar3.jpg',
            'message' => $text,
            'created_at' => now()->toDateTimeString(),
        ]])->concat($this->comments ?? collect());

        $this->dispatch('commentAdded');
    }

    public function render()
    {
        return view('livewire.user.timeline-details-comments');
    }
}
