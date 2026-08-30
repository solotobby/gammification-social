<?php

namespace App\Livewire\User;

use App\Livewire\Concerns\SendsPostGifts;
use App\Models\Follow;
use App\Models\HiddenPost;
use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\PostReport;
use App\Notifications\GeneralNotification;
use App\Services\CommentService;
use App\Services\LikeService;
use App\Services\PostDeletionService;
use App\Services\PostEarningsService;
use App\Services\PayKoinService;
use App\Services\ViewService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostContent extends Component
{
    use SendsPostGifts;

    public Post $post;
    public $likesCount;
    public $likedByMe;

    public $showPlayer = false;
    public $activeVideoId = null;

    public int $commentCount = 0;

    public string $commentMessage = '';

    /** @var \Illuminate\Support\Collection<int, array<string, mixed>> */
    public $previewComments;

    public bool $hasMoreComments = false;

    public ?string $commentsCursor = null;

    public float $estimatedEarnings = 0;

    public bool $standalone = false;

    public bool $formatText = true;

    public bool $showPostMenu = true;

    public bool $isFollowing = false;

    public bool $isBookmarked = false;

    public bool $viewRecorded = false;

    public bool $canManagePost = false;

    public bool $editingPost = false;

    public string $editContent = '';

    /** @var array{total: int, recent: array<int, array<string, mixed>>} */
    public array $giftSummary = ['total' => 0, 'recent' => []];

    protected $listeners = [
        'photoViewerUpdated' => 'refreshFromViewer',
    ];

    public function mount(Post $post, float $estimatedEarnings = 0, bool $standalone = false, bool $formatText = true, bool $showPostMenu = true, ?array $giftSummary = null)
    {
        $this->post = $post;
        $this->estimatedEarnings = $estimatedEarnings;
        $this->standalone = $standalone;
        $this->formatText = $formatText;
        $this->showPostMenu = $showPostMenu;
        $this->previewComments = collect();

        $this->likedByMe = (bool) ($post->liked_by_me ?? $post->isLikedBy(auth()->user()));
        $this->likesCount = sumCounter($post->likes, $post->likes_external);
        $this->commentCount = (int) sumCounter($post->comments, $post->comment_external);
        $this->estimatedEarnings = $estimatedEarnings > 0
            ? $estimatedEarnings
            : (float) (app(PostEarningsService::class)->forPosts(collect([$post->id]))[$post->id] ?? 0);

        $this->canManagePost = $this->ownerCanManagePost();

        if ($this->showPostMenu && auth()->check() && auth()->id() !== $post->user_id) {
            $this->isFollowing = auth()->user()->isFollowing($post->user);
            $this->isBookmarked = PostBookmark::where([
                'user_id' => auth()->id(),
                'post_id' => $post->id,
            ])->exists();
        }

        $this->loadPreviewComments(reset: true);

        if ($giftSummary !== null) {
            $this->giftSummary = $giftSummary;
        } else {
            $this->giftSummary = app(PayKoinService::class)->giftsFor('post', $post->id);
        }
    }

    protected function ownerCanManagePost(): bool
    {
        if (! auth()->check() || auth()->id() !== $this->post->user_id) {
            return false;
        }

        return in_array(userLevel(auth()->id()), ['Creator', 'Influencer'], true);
    }

    public function authorCanReceiveGifts(): bool
    {
        return canReceiveGifts($this->post->user);
    }

    public function openEditPost(): void
    {
        if (! $this->canManagePost) {
            return;
        }

        $this->editContent = plainPostText($this->post->content ?? '');
        $this->editingPost = true;
        $this->resetErrorBag();
    }

    public function cancelEditPost(): void
    {
        $this->editingPost = false;
        $this->editContent = '';
        $this->resetErrorBag();
    }

    public function savePost(): void
    {
        if (! $this->canManagePost) {
            return;
        }

        $this->editContent = trim($this->editContent);

        $this->validate([
            'editContent' => 'required|string|max:5000',
        ]);

        Post::where('id', $this->post->id)
            ->where('user_id', auth()->id())
            ->update([
                'content' => $this->editContent,
            ]);

        $this->post->refresh();
        $this->editingPost = false;
        $this->editContent = '';
        $this->dispatch('post-action-toast', message: 'Post updated.');
    }

    public function deleteOwnPost(PostDeletionService $deletion): void
    {
        if (! $this->canManagePost) {
            return;
        }

        $post = Post::query()
            ->whereKey($this->post->id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $post) {
            return;
        }

        $postId = (string) $post->id;
        $standalone = $this->standalone;

        $deletion->delete($post);

        $this->dispatch('postDeleted', postId: $postId);
        $this->dispatch('post-action-toast', message: 'Post deleted. Related earnings were removed.');

        if ($standalone) {
            $this->redirect(url('timeline'), navigate: true);
        }
    }

    public function refreshFromViewer($postId): void
    {
        if ((string) $this->post->id !== (string) $postId) {
            return;
        }

        $this->post->refresh();
        $this->likedByMe = $this->post->isLikedBy(auth()->user());
        $this->likesCount = sumCounter($this->post->likes, $this->post->likes_external);
        $this->commentCount = (int) sumCounter($this->post->comments, $this->post->comment_external);
        $this->loadPreviewComments(reset: true);
    }

    public function submitComment(): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->commentMessage = trim($this->commentMessage);

        $this->validate([
            'commentMessage' => 'required|string|max:500',
        ]);

        $message = $this->commentMessage;
        $this->commentMessage = '';
        $this->commentCount++;

        // ProcessCommentJob::dispatch($this->post->id, (string) Auth::id(), $message);
        app(CommentService::class)->addComment($this->post->id, Auth::user(), $message);

        $this->previewComments = collect($this->previewComments->prepend([
            'id' => 'pending-'.now()->timestamp,
            'user_id' => Auth::id(),
            'name' => Auth::user()->name,
            'username' => Auth::user()->username,
            'avatar' => Auth::user()->avatar,
            'message' => $message,
            'created_at' => now()->toDateTimeString(),
        ])->take($this->commentsPerPage())->values()->all());
    }

    public function loadMoreComments(): void
    {
        if (! $this->standalone || ! $this->hasMoreComments) {
            return;
        }

        $this->loadPreviewComments(reset: false);
    }

    protected function commentsPerPage(): int
    {
        return $this->standalone ? 5 : 3;
    }

    protected function loadPreviewComments(bool $reset = true): void
    {
        if ($reset) {
            $this->commentsCursor = null;
            $this->previewComments = collect();
            $this->hasMoreComments = false;
        }

        $perPage = $this->commentsPerPage();

        $query = $this->post->postComments()
            ->with('user')
            ->latest('created_at')
            ->limit($perPage + 1);

        if ($this->commentsCursor) {
            $query->where('created_at', '<', $this->commentsCursor);
        }

        $results = $query->get();
        $this->hasMoreComments = $this->standalone && $results->count() > $perPage;
        $page = $results->take($perPage);

        if ($page->isNotEmpty()) {
            $this->commentsCursor = $page->last()->created_at->toDateTimeString();
        }

        $mapped = collect($page->map(fn ($comment) => [
            'id' => $comment->id,
            'user_id' => $comment->user_id,
            'name' => $comment->user->name ?? 'User',
            'username' => $comment->user->username ?? 'user',
            'avatar' => $comment->user->avatar,
            'message' => $comment->message,
            'created_at' => $comment->created_at->toDateTimeString(),
        ])->all());

        $this->previewComments = $reset
            ? $mapped
            : $this->previewComments->concat($mapped);
    }

    public function openPhotoViewer(int $imageIndex = 0): void
    {
        $this->dispatch('openPhotoViewer', postId: $this->post->id, imageIndex: $imageIndex);
    }

    public function toggleLike()
    {
        if ($this->likedByMe) {
            $this->likesCount--;
        } else {
            $this->likesCount++;
        }

        $this->likedByMe = ! $this->likedByMe;

        // ProcessLikeJob::dispatch(
        //     (string) $this->post->unicode,
        //     (string) auth()->id(),
        // );
        app(LikeService::class)->toggle((string) $this->post->unicode, auth()->user());
    }

    public function toggleFollow(): void
    {
        if (! Auth::check() || auth()->id() === $this->post->user_id) {
            return;
        }

        $authUser = Auth::user();
        $targetUser = $this->post->user;

        if ($this->isFollowing) {
            Follow::where([
                'follower_id' => $authUser->id,
                'following_id' => $targetUser->id,
            ])->delete();

            if ($authUser->following > 0) {
                $authUser->decrement('following');
            }

            if ($targetUser->followers > 0) {
                $targetUser->decrement('followers');
            }

            $this->isFollowing = false;
        } else {
            Follow::firstOrCreate([
                'follower_id' => $authUser->id,
                'following_id' => $targetUser->id,
            ]);

            $authUser->increment('following');
            $targetUser->increment('followers');
            $this->isFollowing = true;

            $targetUser->notify(new GeneralNotification([
                'title' => displayName($authUser->name).' followed you',
                'message' => displayName($authUser->name).' followed you',
                'icon' => 'fa-user-plus text-primary',
                'url' => url('profile/'.$authUser->username),
            ]));
        }

        $this->dispatch('refreshFeed');
    }

    public function toggleBookmark(): void
    {
        if (! Auth::check() || auth()->id() === $this->post->user_id) {
            return;
        }

        $bookmark = PostBookmark::where([
            'user_id' => auth()->id(),
            'post_id' => $this->post->id,
        ])->first();

        if ($bookmark) {
            $bookmark->delete();
            $this->isBookmarked = false;
            $this->dispatch('post-action-toast', message: 'Bookmark removed.');
            $this->dispatch('bookmarkRemoved', postId: $this->post->id);
        } else {
            PostBookmark::create([
                'user_id' => auth()->id(),
                'post_id' => $this->post->id,
            ]);
            $this->isBookmarked = true;
            $this->dispatch('post-action-toast', message: 'Post bookmarked.');
        }
    }

    public function hidePost(): void
    {
        if (! Auth::check() || auth()->id() === $this->post->user_id) {
            return;
        }

        HiddenPost::firstOrCreate([
            'user_id' => auth()->id(),
            'post_id' => $this->post->id,
        ]);

        $this->dispatch('postHidden', postId: $this->post->id);
        $this->dispatch('post-action-toast', message: 'Post hidden from your feed.');
    }

    public function reportPost(): void
    {
        if (! Auth::check() || auth()->id() === $this->post->user_id) {
            return;
        }

        PostReport::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'post_id' => $this->post->id,
            ],
            [
                'status' => PostReport::STATUS_PENDING,
            ]
        );

        $this->dispatch('post-action-toast', message: 'Post reported. Thanks for letting us know.');
    }

    public function recordView(): void
    {
        if ($this->viewRecorded || ! auth()->check()) {
            return;
        }

        $this->viewRecorded = true;

        // ProcessViewJob::dispatch(
        //     (string) $this->post->id,
        //     (string) auth()->id(),
        // );
        app(ViewService::class)->recordView($this->post, (string) auth()->id());
    }

    public function openVideoPlayer($videoId)
    {
        $this->activeVideoId = $videoId;
        $this->showPlayer = true;
    }

    public function closeVideoPlayer()
    {
        $this->showPlayer = false;
        $this->activeVideoId = null;
    }

    public function render()
    {
        return view('livewire.user.post-content');
    }
}
