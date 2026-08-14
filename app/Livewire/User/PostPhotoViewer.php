<?php

namespace App\Livewire\User;

use App\Models\CommunityPost;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostLike;
use App\Models\Post;
use App\Services\CommentService;
use App\Services\LikeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PostPhotoViewer extends Component
{
    public bool $open = false;

    public ?string $postId = null;

    public int $imageIndex = 0;

    public string $source = 'post';

    public ?Post $post = null;

    public ?CommunityPost $communityPost = null;

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
    public function openPhotoViewer(string $postId, int $imageIndex = 0, string $source = 'post'): void
    {
        $this->source = $source;
        $this->post = null;
        $this->communityPost = null;

        if ($source === 'community') {
            $post = CommunityPost::with(['user', 'media', 'community'])->find($postId);
            $images = $this->communityImages($post);

            if (! $post || $images->isEmpty()) {
                return;
            }

            $this->communityPost = $post;
            $this->postId = (string) $post->id;
            $this->imageIndex = max(0, min($imageIndex, $images->count() - 1));
        } else {
            $post = Post::with(['user', 'images'])->find($postId);

            if (! $post || $post->images->isEmpty()) {
                return;
            }

            if ($post->status !== 'LIVE' && $post->user_id !== auth()->id()) {
                return;
            }

            $this->post = $post;
            $this->postId = (string) $post->id;
            $this->imageIndex = max(0, min($imageIndex, $post->images->count() - 1));
        }

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
        if ($this->imageIndex <= 0) {
            return;
        }
        $this->imageIndex--;
    }

    public function nextImage(): void
    {
        $images = $this->viewerImages();
        if ($this->imageIndex >= $images->count() - 1) {
            return;
        }
        $this->imageIndex++;
    }

    public function toggleLike(LikeService $likeService): void
    {
        if ($this->source === 'community') {
            if (! $this->communityPost || ! Auth::check()) {
                return;
            }

            $existing = CommunityPostLike::where('community_post_id', $this->communityPost->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existing) {
                $existing->delete();
                $this->communityPost->decrement('likes_count');
                $this->likedByMe = false;
            } else {
                CommunityPostLike::create([
                    'community_post_id' => $this->communityPost->id,
                    'user_id' => auth()->id(),
                ]);
                $this->communityPost->increment('likes_count');
                $this->likedByMe = true;
            }

            $this->communityPost->refresh();
            $this->syncEngagement();
            $this->dispatch('photoViewerUpdated', postId: $this->communityPost->id, source: 'community');

            return;
        }

        if (! $this->post) {
            return;
        }

        $likeService->toggle($this->post->unicode, Auth::user());
        $this->post->refresh();
        $this->syncEngagement();
        $this->dispatch('photoViewerUpdated', postId: $this->post->id, source: 'post');
    }

    public function submitComment(CommentService $commentService): void
    {
        $text = trim($this->commentText);
        if ($text === '' || strlen($text) > 500) {
            return;
        }

        if ($this->source === 'community') {
            if (! $this->communityPost || ! Auth::check()) {
                return;
            }

            CommunityPostComment::create([
                'community_post_id' => $this->communityPost->id,
                'user_id' => auth()->id(),
                'content' => $text,
            ]);

            $this->communityPost->increment('comments_count');
            $this->commentText = '';
            $this->communityPost->refresh();
            $this->syncEngagement();
            $this->loadComments(reset: true);
            $this->dispatch('photoViewerUpdated', postId: $this->communityPost->id, source: 'community');

            return;
        }

        if (! $this->post) {
            return;
        }

        $commentService->addComment($this->post->id, Auth::user(), $text);
        $this->commentText = '';
        $this->post->refresh();
        $this->syncEngagement();
        $this->loadComments(reset: true);
        $this->dispatch('commentAdded');
        $this->dispatch('photoViewerUpdated', postId: $this->post->id, source: 'post');
    }

    public function loadMoreComments(): void
    {
        if (! $this->hasMoreComments) {
            return;
        }
        $this->loadComments(reset: false);
    }

    public function viewerImages(): Collection
    {
        if ($this->source === 'community' && $this->communityPost) {
            return $this->communityImages($this->communityPost);
        }

        return $this->post?->images ?? collect();
    }

    public function imageUrl(mixed $image): string
    {
        if ($this->source === 'community') {
            return $image->url;
        }

        return $image->path;
    }

    public function viewerAuthor(): mixed
    {
        if ($this->source === 'community') {
            return $this->communityPost?->user;
        }

        return $this->post?->user;
    }

    public function viewerCaption(): string
    {
        if ($this->source === 'community') {
            return strip_tags($this->communityPost->content ?? '');
        }

        return strip_tags($this->post->content ?? '');
    }

    public function viewerCreatedAt(): mixed
    {
        if ($this->source === 'community') {
            return $this->communityPost?->created_at;
        }

        return $this->post?->created_at;
    }

    public function viewerDetailUrl(): ?string
    {
        if ($this->source === 'community' && $this->communityPost?->community) {
            return url('c/'.$this->communityPost->community->slug);
        }

        if ($this->post) {
            return url('timeline/'.$this->post->id);
        }

        return null;
    }

    protected function communityImages(?CommunityPost $post): Collection
    {
        if (! $post) {
            return collect();
        }

        return $post->media->where('type', '!=', 'video')->values();
    }

    protected function syncEngagement(): void
    {
        if ($this->source === 'community' && $this->communityPost) {
            $this->likesCount = (int) $this->communityPost->likes_count;
            $this->commentsCount = (int) $this->communityPost->comments_count;
            $this->likedByMe = $this->communityPost->isLikedBy(auth()->id());

            return;
        }

        if (! $this->post) {
            return;
        }

        $this->likesCount = (int) sumCounter($this->post->likes, $this->post->likes_external);
        $this->commentsCount = (int) sumCounter($this->post->comments, $this->post->comment_external);
        $this->likedByMe = $this->post->isLikedBy(Auth::user());
    }

    protected function loadComments(bool $reset): void
    {
        if ($reset) {
            $this->commentsCursor = null;
            $this->comments = collect();
        }

        if ($this->source === 'community') {
            if (! $this->communityPost) {
                return;
            }

            $query = $this->communityPost->comments()
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
                'user_id' => $comment->user_id,
                'username' => $comment->user->username ?? 'user',
                'name' => $comment->user->name ?? 'User',
                'avatar' => $comment->user->avatar,
                'message' => $comment->content,
                'created_at' => $comment->created_at,
            ]);

            $this->comments = $reset ? $mapped : $this->comments->concat($mapped);

            return;
        }

        if (! $this->post) {
            return;
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
            'user_id' => $comment->user_id,
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
