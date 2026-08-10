<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\LikeService;
use App\Services\ViewService;
use Livewire\Component;

class PostContent extends Component
{
    public Post $post;
    public $likesCount;
    public $likedByMe;

    public $showPlayer = false;
    public $activeVideoId = null;

    public int $commentCount = 0;

    public float $estimatedEarnings = 0;

    public bool $viewRecorded = false;

    protected $listeners = [
        'commentAdded' => 'incrementCommentCount',
        'photoViewerUpdated' => 'refreshFromViewer',
    ];

    public function mount(Post $post, float $estimatedEarnings = 0)
    {
        $this->post = $post;
        $this->estimatedEarnings = $estimatedEarnings;

        $this->likedByMe = (bool) ($post->liked_by_me ?? $post->isLikedBy(auth()->user()));
        $this->likesCount = sumCounter($post->likes, $post->likes_external);
        $this->commentCount = (int) sumCounter($post->comments, $post->comment_external);
    }

    public function incrementCommentCount()
    {
        $this->post->refresh();
        $this->commentCount = (int) sumCounter($this->post->comments, $this->post->comment_external);
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

        app(LikeService::class)->toggle(
            $this->post->unicode,
            auth()->user()
        );
    }

    public function recordView(ViewService $viewService): void
    {
        if ($this->viewRecorded) {
            return;
        }

        $this->viewRecorded = true;
        $viewService->recordView($this->post, auth()->id());
        $this->post->refresh();
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
