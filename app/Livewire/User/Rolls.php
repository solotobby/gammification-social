<?php

namespace App\Livewire\User;

use App\Jobs\ProcessCommentJob;
use App\Jobs\ProcessLikeJob;
use App\Jobs\ProcessViewJob;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Post;
use App\Models\PostVideo;
use App\Models\User;
use App\Notifications\GeneralNotification;
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

    /** userId => whether auth user follows them */
    public array $followingMap = [];

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
            ->join('post_videos', 'post_videos.post_id', '=', 'posts.id')
            ->where('posts.status', 'LIVE')
            ->where('post_videos.processing_status', 'completed')
            ->whereNotNull('post_videos.path')
            ->when($startPost, fn ($q) => $q->where('posts.id', '!=', $startPost->id))
            ->select(
                'posts.*',
                DB::raw('(COALESCE(post_videos.play_count, 0) * 2 + COALESCE(post_videos.avg_watch_time, 0)) AS engagement_score')
            )
            ->withExists(['likes as liked_by_me' => fn ($q) => $q->where('user_id', Auth::id())])
            ->orderByDesc('engagement_score')
            ->orderByDesc('posts.created_at')
            ->limit($this->perPage * $this->page)
            ->get();

        $this->hasMore = $rest->count() >= ($this->perPage * $this->page);

        $this->videos = $startPost
            ? $rest->prepend($startPost)
            : $rest;

        $this->syncFollowingMap();
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

    private function syncFollowingMap(): void
    {
        if (! Auth::check() || ! $this->videos) {
            $this->followingMap = [];

            return;
        }

        $userIds = $this->videos->pluck('user_id')->unique()->filter()->values();
        if ($userIds->isEmpty()) {
            $this->followingMap = [];

            return;
        }

        $following = Follow::query()
            ->where('follower_id', Auth::id())
            ->whereIn('following_id', $userIds)
            ->pluck('following_id')
            ->map(fn ($id) => (string) $id)
            ->flip();

        $map = [];
        foreach ($userIds as $id) {
            $key = (string) $id;
            $map[$key] = isset($following[$key]);
        }

        $this->followingMap = $map;
    }

    #[Renderless]
    public function recordView($postId): void
    {
        $postId = (string) $postId;

        if (! Auth::check()) {
            return;
        }

        if (! Post::whereKey($postId)->exists()) {
            return;
        }

        ProcessViewJob::dispatch($postId, (string) Auth::id());

        $post = Post::find($postId);
        $count = $post ? $post->totalViews() + 1 : 0;

        $this->dispatch(
            'viewCounted',
            postId: $postId,
            count: $count,
        );
    }

    #[Renderless]
    public function recordPlay($postId): void
    {
        $postId = (string) $postId;
        $video = Post::find($postId)?->video;
        if (! $video) {
            return;
        }

        $video->incrementPlays();

        $this->dispatch(
            'playCountUpdated',
            postId: $postId,
            count: (int) $video->fresh()->play_count,
        );
    }

    #[Renderless]
    public function recordWatch($postId, $watchSeconds, $isFirstPlay = false): void
    {
        $postId = (string) $postId;
        $watchSeconds = (float) $watchSeconds;
        $isFirstPlay = (bool) $isFirstPlay;

        $video = Post::find($postId)?->video;
        if (! $video) {
            return;
        }

        // Play count is preferred via recordPlay() on activate; keep this as a
        // safe fallback for beacons that still send is_first_play.
        if ($isFirstPlay) {
            $video->incrementPlays();
            $this->dispatch(
                'playCountUpdated',
                postId: $postId,
                count: (int) $video->fresh()->play_count,
            );
        }

        if ($watchSeconds >= 0.25) {
            $video->updateWatchTime($watchSeconds);
        }
    }

    #[Renderless]
    public function toggleLike($postId): array
    {
        if (! Auth::check()) {
            return ['postId' => (string) $postId, 'liked' => false, 'count' => 0];
        }

        $postId = (string) $postId;

        if (isset($this->likeOverrides[$postId])) {
            $current = $this->likeOverrides[$postId];
            $liked = ! $current['liked'];
            $count = max(0, $current['count'] + ($liked ? 1 : -1));
            $unicode = (string) (Post::find($postId)?->unicode ?? $postId);
        } else {
            $post = Post::find($postId);

            if (! $post) {
                return ['postId' => $postId, 'liked' => false, 'count' => 0];
            }

            $liked = ! $post->isLikedBy(auth()->user());
            $count = max(0, $post->totalLikes() + ($liked ? 1 : -1));
            $unicode = (string) $post->unicode;
        }

        $this->likeOverrides[$postId] = ['liked' => $liked, 'count' => $count];

        ProcessLikeJob::dispatch($unicode, (string) Auth::id());

        $this->dispatch('likeUpdated', postId: $postId, liked: $liked, count: $count);

        return ['postId' => $postId, 'liked' => $liked, 'count' => $count];
    }

    #[Renderless]
    public function toggleFollow($userId): array
    {
        $userId = (string) $userId;

        if (! Auth::check() || $userId === '' || $userId === (string) Auth::id()) {
            return ['userId' => $userId, 'following' => false];
        }

        $authUser = Auth::user();
        $target = User::find($userId);
        if (! $target) {
            return ['userId' => $userId, 'following' => false];
        }

        $existing = Follow::query()
            ->where('follower_id', $authUser->id)
            ->where('following_id', $target->id)
            ->first();

        if ($existing) {
            $existing->delete();

            if ($authUser->following > 0) {
                $authUser->decrement('following');
            }
            if ($target->followers > 0) {
                $target->decrement('followers');
            }

            $following = false;

            $target->notify(new GeneralNotification([
                'title' => displayName($authUser->name) . ' unfollowed you',
                'message' => displayName($authUser->name) . ' unfollowed you',
                'icon' => 'fa-user-minus text-primary',
                'url' => url('profile/' . $authUser->username),
            ]));
        } else {
            Follow::create([
                'follower_id' => $authUser->id,
                'following_id' => $target->id,
            ]);

            $authUser->increment('following');
            $target->increment('followers');

            $following = true;

            $target->notify(new GeneralNotification([
                'title' => displayName($authUser->name) . ' followed you',
                'message' => displayName($authUser->name) . ' started following you',
                'icon' => 'fa-user-plus text-primary',
                'url' => url('profile/' . $authUser->username),
            ]));
        }

        $this->followingMap[$userId] = $following;

        $this->dispatch('followUpdated', userId: $userId, following: $following);

        return ['userId' => $userId, 'following' => $following];
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

        ProcessCommentJob::dispatch($postId, (string) Auth::id(), $text);

        $this->commentPostId = $postId;
        $this->showComments  = true;
        $this->commentText   = '';

        $pending = new Comment([
            'id' => 'pending-'.now()->timestamp,
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'message' => $text,
            'created_at' => now(),
        ]);
        $pending->setRelation('user', Auth::user());

        $this->activeComments = collect([$pending])->concat($this->activeComments ?? collect())->take(50);

        $count = ($this->sheetCommentCount ?? 0) + 1;
        $this->sheetCommentCount = $count;

        $this->dispatch('commentCountUpdated', postId: $postId, count: $count);
    }

    public function render()
    {
        return view('livewire.user.rolls');
    }
}
