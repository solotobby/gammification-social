<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\CommentService;
use App\Services\LikeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PostPhotoViewer extends Component
{
    public bool $open = false;

    public ?string $postId = null;

    public int $imageIndex = 0;

    public ?Post $post = null;

    public bool $likedByMe = false;

    public int $likesCount = 0;

    public int $commentsCount = 0;

    public string $commentText = '';

    /** @var \Illuminate\Support\Collection<int, array<string, mixed>> */
    public $comments;

    public bool $hasMoreComments = false;

    public ?string $commentsCursor = null;

    public int $commentsPerPage = 15;

    public function mount(): void
    {
        $this->comments = collect();
    }

    #[On('openPhotoViewer')]
    public function openPhotoViewer(string $postId, int $imageIndex = 0): void
    {
        $post = Post::with(['user', 'images'])->find($postId);

        if (! $post || $post->images->isEmpty()) {
            return;
        }

        if ($post->status !== 'LIVE' && $post->user_id !== auth()->id()) {
            return;
        }

        $this->postId = (string) $post->id;
        $this->post = $post;
        $this->imageIndex = max(0, min($imageIndex, $post->images->count() - 1));
        $this->commentText = '';
        $this->syncEngagement();
        $this->loadComments(reset: true);
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function prevImage(): void
    {
        if (! $this->post || $this->imageIndex <= 0) {
            return;
        }
        $this->imageIndex--;
    }

    public function nextImage(): void
    {
        if (! $this->post || $this->imageIndex >= $this->post->images->count() - 1) {
            return;
        }
        $this->imageIndex++;
    }

    public function toggleLike(LikeService $likeService): void
    {
        if (! $this->post) {
            return;
        }

        $likeService->toggle($this->post->unicode, Auth::user());
        $this->post->refresh();
        $this->syncEngagement();
        $this->dispatch('photoViewerUpdated', postId: $this->post->id);
    }

    public function submitComment(CommentService $commentService): void
    {
        $text = trim($this->commentText);
        if (! $this->post || $text === '' || strlen($text) > 500) {
            return;
        }

        $commentService->addComment($this->post->id, Auth::user(), $text);
        $this->commentText = '';
        $this->post->refresh();
        $this->syncEngagement();
        $this->loadComments(reset: true);
        $this->dispatch('commentAdded');
        $this->dispatch('photoViewerUpdated', postId: $this->post->id);
    }

    public function loadMoreComments(): void
    {
        if (! $this->hasMoreComments) {
            return;
        }
        $this->loadComments(reset: false);
    }

    protected function syncEngagement(): void
    {
        if (! $this->post) {
            return;
        }

        $this->likesCount = (int) sumCounter($this->post->likes, $this->post->likes_external);
        $this->commentsCount = (int) sumCounter($this->post->comments, $this->post->comment_external);
        $this->likedByMe = $this->post->isLikedBy(Auth::user());
    }

    protected function loadComments(bool $reset): void
    {
        if (! $this->post) {
            return;
        }

        if ($reset) {
            $this->commentsCursor = null;
            $this->comments = collect();
        }

        $query = $this->post->postComments()
            ->with('user')
            ->latest('created_at')
            ->limit($this->commentsPerPage + 1);

        if ($this->commentsCursor) {
            $query->where('created_at', '<', $this->commentsCursor);
        }

        $results = $query->get();
        $this->hasMoreComments = $results->count() > $this->commentsPerPage;
        $page = $results->take($this->commentsPerPage);

        if ($page->isNotEmpty()) {
            $this->commentsCursor = $page->last()->created_at->toDateTimeString();
        }

        $mapped = $page->map(fn ($comment) => [
            'id' => $comment->id,
            'username' => $comment->user->username ?? 'user',
            'name' => $comment->user->name ?? 'User',
            'avatar' => $comment->user->avatar,
            'message' => $comment->message,
            'created_at' => $comment->created_at,
        ]);

        $this->comments = $reset ? $mapped : $this->comments->concat($mapped);
    }

    public function render()
    {
        return view('livewire.user.post-photo-viewer');
    }
}
