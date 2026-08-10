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

    public function mount(Post $post, ViewService $viewService)
    {
        $this->post = $post;
        $this->loadComments(reset: true);
        $this->processView($viewService);
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

    public function processView(ViewService $viewService): void
    {
        if ($this->viewRecorded) {
            return;
        }

        $this->viewRecorded = true;
        $viewService->recordView($this->post, Auth::id());
        $this->post->refresh();
        $this->dispatch('viewRecorded');
    }

    public function commentFeed(CommentService $service): void
    {
        $this->validate([
            'message' => 'required|string|max:500',
        ]);

        if (trim($this->message) === '') {
            return;
        }

        $service->addComment($this->post->id, Auth::user(), $this->message);

        $this->message = '';
        $this->dispatch('commentAdded');
        $this->loadComments(reset: true);
    }

    public function render()
    {
        return view('livewire.user.timeline-details-comments');
    }
}
