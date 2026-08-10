<?php

namespace App\Livewire\User;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostVideo;
use App\Services\CommentService;
use App\Services\LikeService;
use App\Services\ViewService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.rolls')]
class Rolls extends Component
{
    // ── Feed state ────────────────────────────────────────────
    public string $startVideoId;
    public int    $page     = 1;
    public int    $perPage  = 10;
    public bool   $hasMore  = true;
    public $videos;

    // ── Comments ──────────────────────────────────────────────
    public string $commentText    = '';
    public string $commentPostId  = '';
    public bool   $showComments   = false;
    public $activeComments;
    public int $sheetCommentCount = 0;

    /** Persist like UI across Livewire re-renders (e.g. opening comments). */
    public array $likeOverrides = [];

    // ─────────────────────────────────────────────────────────
    public function mount(PostVideo $video): void
    {
        $this->startVideoId = (string) $video->id;
        $this->loadVideos();
    }

    public function loadVideos(): void
    {
        $startPost = PostVideo::where('id', $this->startVideoId)
            ->where('processing_status', 'completed')
            ->whereHas('post', fn ($q) => $q->where('status', 'LIVE'))
            ->with(['post' => fn ($q) => $q->with($this->eagerLoads())
                ->withExists(['likes as liked_by_me' => fn ($lq) => $lq->where('user_id', Auth::id())])])
            ->first()
            ?->post;

        $rest = Post::with($this->eagerLoads())
            ->withExists(['likes as liked_by_me' => fn ($q) => $q->where('user_id', Auth::id())])
            ->join('post_videos', 'post_videos.post_id', '=', 'posts.id')
            ->where('posts.status', 'LIVE')
            ->where('post_videos.processing_status', 'completed')
            ->whereNotNull('post_videos.path')
            ->when($startPost, fn ($q) => $q->where('posts.id', '!=', $startPost->id))
            ->select(
                'posts.*',
                DB::raw('(COALESCE(post_videos.play_count, 0) * 2 + COALESCE(post_videos.avg_watch_time, 0)) AS engagement_score')
            )
            ->orderByDesc('engagement_score')
            ->orderByDesc('posts.created_at')
            ->limit($this->perPage * $this->page)
            ->get();

        $this->hasMore = $rest->count() >= ($this->perPage * $this->page);

        $this->videos = $startPost
            ? $rest->prepend($startPost)
            : $rest;
    }

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
        $this->loadVideos();
    }

    private function eagerLoads(): array
    {
        return [
            'user',
            'video',
        ];
    }

    #[Renderless]
    public function recordView($postId, ViewService $viewService): void
    {
        $postId = (string) $postId;

        if (! Auth::check()) {
            return;
        }

        $post = Post::with('video')->find($postId);
        if (! $post) {
            return;
        }

        $viewService->recordView($post, Auth::id());
        $post->refresh();

        $this->dispatch(
            'viewCounted',
            postId: $postId,
            count: $post->totalViews(),
        );
    }

    #[Renderless]
    public function recordWatch($postId, $watchSeconds, $isFirstPlay): void
    {
        $postId       = (string) $postId;
        $watchSeconds = (float) $watchSeconds;
        $isFirstPlay  = (bool) $isFirstPlay;

        $video = Post::find($postId)?->video;
        if (! $video) {
            return;
        }

        if ($isFirstPlay) {
            $video->increment('play_count');
            $this->dispatch(
                'playCountUpdated',
                postId: $postId,
                count: (int) $video->fresh()->play_count,
            );
        }

        if ($watchSeconds > 0.5) {
            $video->updateWatchTime($watchSeconds);
        }
    }

    #[Renderless]
    public function toggleLike($postId, LikeService $likeService): array
    {
        if (! Auth::check()) {
            return ['postId' => (string) $postId, 'liked' => false, 'count' => 0];
        }

        $post = Post::find($postId);
        if (! $post) {
            return ['postId' => (string) $postId, 'liked' => false, 'count' => 0];
        }

        $postId = (string) $post->id;

        $likeService->toggle((string) $post->unicode, Auth::user());

        $post->refresh();
        $liked = $post->isLikedBy(auth()->user());
        $count = $post->totalLikes();

        $this->likeOverrides[$postId] = ['liked' => $liked, 'count' => $count];

        $this->dispatch('likeUpdated', postId: $postId, liked: $liked, count: $count);

        return ['postId' => $postId, 'liked' => $liked, 'count' => $count];
    }

    public function openComments($postId): void
    {
        $postId = (string) $postId;

        $this->commentPostId = $postId;
        $this->commentText   = '';
        $this->showComments  = true;

        $this->activeComments = Comment::with('user')
            ->where('post_id', $postId)
            ->latest()
            ->limit(50)
            ->get();

        $post = Post::find($postId);
        $this->sheetCommentCount = $post ? $post->totalComments() : 0;
    }

    public function closeComments(): void
    {
        $this->showComments   = false;
        $this->commentPostId  = '';
        $this->commentText    = '';
        $this->activeComments = null;
        $this->sheetCommentCount = 0;
    }

    public function submitComment($commentText, $postId = null): void
    {
        $text   = trim((string) $commentText);
        $postId = (string) ($postId ?? $this->commentPostId);

        if ($text === '' || strlen($text) > 500 || $postId === '' || ! Auth::check()) {
            return;
        }

        app(CommentService::class)->addComment($postId, Auth::user(), $text);

        $this->commentPostId = $postId;
        $this->showComments  = true;
        $this->commentText   = '';

        $this->activeComments = Comment::with('user')
            ->where('post_id', $postId)
            ->latest()
            ->limit(50)
            ->get();

        $post = Post::find($postId);
        $count = $post ? $post->totalComments() : 0;
        $this->sheetCommentCount = $count;

        $this->dispatch('commentCountUpdated', postId: $postId, count: $count);
    }

    public function render()
    {
        return view('livewire.user.rolls');
    }
}
