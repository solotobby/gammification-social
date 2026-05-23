<?php

namespace App\Livewire\User;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostVideo;
use App\Models\UserLike;
use App\Models\UserView;
use App\Models\UserComment;
use App\Services\CommentService;
use App\Services\LikeService;
use App\Services\ViewService;
use Livewire\Component;
use Livewire\Attributes\Layout;
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
    public string $commentPostId  = '';   // UUID string, not int
    public bool   $showComments   = false;
    public $activeComments;

    // ─────────────────────────────────────────────────────────
    public function mount(PostVideo $video): void
    {
        $this->startVideoId = (string) $video->id;
        $this->loadVideos();
    }

    // public function loadVideos(): void
    // {
    //     // ── First video: the one user clicked ──────────────────────
    //     $startPost = PostVideo::query()
    //         ->where('id', $this->startVideoId)
    //         ->whereHas('post', fn($q) => $q->where('status', 'LIVE'))
    //         ->with(['post' => fn($q) => $q->with($this->eagerLoads())])
    //         ->first()
    //         ?->post;

    //     // ── Remaining feed: ranked by engagement ───────────────────
    //     $rest = Post::query()
    //         ->with($this->eagerLoads())
    //         ->join('post_videos', 'post_videos.post_id', '=', 'posts.id')
    //         ->where('posts.status', 'LIVE')
    //         ->whereNotNull('post_videos.path')
    //         ->when(
    //             $startPost,
    //             fn($q) => $q->where('posts.id', '!=', $startPost->id)
    //         )
    //         ->select([
    //             'posts.*',
    //             DB::raw('
    //             (
    //                 COALESCE(post_videos.play_count, 0) * 2
    //                 + COALESCE(post_videos.avg_watch_time, 0)
    //             ) as engagement_score
    //         ')
    //         ])
    //         ->orderByDesc('engagement_score')
    //         ->orderByDesc('posts.created_at')
    //         ->limit($this->perPage * $this->page)
    //         ->get();

    //     // ── Final feed order ───────────────────────────────────────
    //     // 1. User-clicked video first
    //     // 2. Everything else by engagement score
    //     $this->videos = $startPost
    //         ? collect([$startPost])->merge($rest)
    //         : $rest;

    //     // ── Pagination state ───────────────────────────────────────
    //     $this->hasMore = $rest->count() >= ($this->perPage * $this->page);
    // }

    // ─────────────────────────────────────────────────────────
    // FEED LOADING
    // ─────────────────────────────────────────────────────────
    public function loadVideos(): void
    {

        // ── Step 1: Load the exact video the user tapped ──────────
        // This ALWAYS comes first regardless of engagement score.
        $startPost = PostVideo::where('id', $this->startVideoId)
            ->whereHas('post', fn($q) => $q->where('status', 'LIVE'))
            ->with(['post' => $this->eagerLoads()])
            ->first()
            ?->post;

        // Post::with($this->eagerLoads())
        //     ->whereHas('video', fn($q) => $q->where('id', $this->startVideoId))
        //     ->first();

        // ── Step 2: Load the rest, ranked by engagement ───────────
        // Join post_videos so we can ORDER BY play_count + avg_watch_time.
        // Higher engagement = more compelling = shown sooner in the feed.
        $rest = Post::with($this->eagerLoads())
            ->join('post_videos', 'post_videos.post_id', '=', 'posts.id')
            ->where('posts.status', 'LIVE')
            // ->where('posts.post_type', 'video')
            ->whereNotNull('post_videos.path')
            ->when($startPost, fn($q) =>
                $q->where('posts.id', '!=', $startPost->id)
            )
            ->select(
                'posts.*',
                // Composite engagement score:
                // play_count × 2 + avg_watch_time
                // COALESCE handles nulls from brand-new videos (score = 0, falls to date sort)
                DB::raw('(COALESCE(post_videos.play_count, 0) * 2 + COALESCE(post_videos.avg_watch_time, 0)) AS engagement_score')
            )
            ->orderByDesc('engagement_score')
            ->orderByDesc('posts.created_at')   // tie-break: newest first
            ->limit($this->perPage * $this->page)
            ->get();

        $this->hasMore = $rest->count() >= ($this->perPage * $this->page);

        // ── Step 3: Prepend the clicked video ─────────────────────
        $this->videos = $startPost
            ? $rest->prepend($startPost)
            : $rest;


    // // The video the user tapped always comes first
    // $startPost = Post::with($this->eagerLoads())
    //     ->whereHas('video', fn($q) => $q->where('id', $this->startVideoId))
    //     ->first();

    // $rest = Post::with($this->eagerLoads())
    //     ->where('status', 'LIVE')
    //     // ->where('post_type', 'video')
    //     ->whereHas('video')
    //     ->when($startPost, fn($q) => $q->where('id', '!=', $startPost->id))
    //     ->latest()
    //     ->limit($this->perPage * $this->page)
    //     ->get();

    // $this->hasMore = $rest->count() >= ($this->perPage * $this->page);

    // $this->videos = $startPost
    //     ? $rest->prepend($startPost)
    //     : $rest;
    }

    public function loadMore(): void
    {
        if (!$this->hasMore) return;
        $this->page++;
        $this->loadVideos();
    }

    private function eagerLoads(): array
    {
        return [
            // No column constraints — UUID primary keys require the full row
            // to be loaded so Laravel can hydrate the relationship correctly.
            // Column-constrained eager loads like 'likes:id,post_id,user_id'
            // silently fail with UUID PKs because the implicit key column
            // (often 'id') may not match what Laravel expects for joining.
            'user',
            'video',
            'likes',
            // 'comments',
            // 'views',
        ];
    }

    // ─────────────────────────────────────────────────────────
    // ENGAGEMENT: VIEW
    // Called by JS IntersectionObserver when a video enters viewport.
    // Only records once per user per post (firstOrCreate).
    // Also increments PostVideo.view_count.
    // ─────────────────────────────────────────────────────────
    public function recordView($postId, ViewService $viewService = null): void
    {
        $postId = (string) $postId;
        $userId = Auth::id();

        $viewService->recordView(Post::find($postId), $userId);

        $this->dispatch('viewCounted', postId: $postId);

        // $result  = UserView::firstOrCreate(
        //     ['user_id' => $userId, 'post_id' => $postId]
        // );

        // Only increment and dispatch on a brand-new view
        // if ($result->wasRecentlyCreated) {
        // Post::find($postId)?->video?->increment('view_count');

        // Tell JS the view was new so it can bump the counter
        // $this->dispatch('viewCounted', postId: $postId);
        // }
        // If already viewed, do nothing — JS will not update the count
    }

    public function recordWatch($postId, $watchSeconds, $isFirstPlay): void
    {
        $postId       = (string) $postId;
        $watchSeconds = (float)  $watchSeconds;
        $isFirstPlay  = (bool)   $isFirstPlay;

        $video = Post::find($postId)?->video;
        if (!$video) return;

        if ($isFirstPlay) {
            $video->increment('play_count');
        }

        if ($watchSeconds > 0.5) {
            $video->updateWatchTime($watchSeconds);
        }
    }

    public function toggleLike($postId, LikeService $likeService): void
    {
        $post = Post::find($postId);
        if (!$post) return;
        $unicode = $post->unicode;

        $postId = (string) $postId;
        $userId = Auth::id();

        $likeService->toggle($unicode, Auth::user());

        // $existing = UserLike::where('user_id', $userId)
        //                     ->where('post_id', $postId)
        //                     ->first();

        // if ($existing) {
        //     $existing->delete();
        //     $liked = false;
        // } else {
        //     UserLike::create(['user_id' => $userId, 'post_id' => $postId]);
        //     $liked = true;
        // }

        $liked = $post->isLikedBy(auth()->user()) ? true : false;
        $count = $post->likes;

        // $count = UserLike::where('post_id', $postId)->count();
        $this->dispatch('likeConfirmed', postId: $postId, liked: $liked, count: $count);
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
    }

    public function closeComments(): void
    {
        $this->showComments   = false;
        $this->commentPostId  = '';
        $this->commentText    = '';
        $this->activeComments = null;
    }

    public function submitComment($commentText, $postId = null, CommentService $commentService = null): void
    {
        $text   = trim((string) $commentText);
        $postId = (string) ($postId ?? $this->commentPostId);

        if (empty($text) || strlen($text) > 500 || empty($postId)) return;

        //fetch Post
        // $post = Post::find($postId);
        // if (!$post) return;

        $commentService?->addComment($postId, Auth::user(), $text); //to be converted to job later


        // Keep PHP state in sync
        $this->commentPostId = $postId;

        // Reload comments for this post
        $this->activeComments = Comment::with('user')
            ->where('post_id', $postId)
            ->latest()
            ->limit(50)
            ->get();

        $count = Comment::where('post_id', $postId)->count();
        $this->dispatch('commentCountUpdated', postId: $postId, count: $count);
    }

    public function render()
    {
        return view('livewire.user.rolls');
    }
}
