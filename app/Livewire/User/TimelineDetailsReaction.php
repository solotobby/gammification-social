<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\LikeService;
use Livewire\Component;

class TimelineDetailsReaction extends Component
{
    public Post $post;

    public bool $likedByMe = false;

    public $likesCount = 0;

    public $commentsCount = 0;

    public $viewsCount = 0;

    public float $estimatedEarnings = 0;

    public $user;

    protected $listeners = [
        'commentAdded' => 'refreshCounts',
        'viewRecorded' => 'refreshCounts',
        'photoViewerUpdated' => 'refreshCounts',
    ];

    public function mount(Post $post, float $estimatedEarnings = 0)
    {
        $this->post = $post;
        $this->estimatedEarnings = $estimatedEarnings;
        $this->user = $post->user;
        $this->syncCounts();
        $this->likedByMe = $post->isLikedBy(auth()->user());
    }

    public function syncCounts(): void
    {
        $this->likesCount = sumCounter($this->post->likes, $this->post->likes_external);
        $this->commentsCount = (int) sumCounter($this->post->comments, $this->post->comment_external);
        $this->viewsCount = sumCounter($this->post->views, $this->post->views_external);
    }

    public function toggleLike()
    {
        $this->likedByMe = ! $this->likedByMe;
        $this->likesCount += $this->likedByMe ? 1 : -1;

        app(LikeService::class)->toggle(
            $this->post->unicode,
            auth()->user()
        );

        $this->post->refresh();
        $this->likesCount = sumCounter($this->post->likes, $this->post->likes_external);
        $this->likedByMe = $this->post->isLikedBy(auth()->user());
    }

    public function refreshCounts(): void
    {
        $this->post->refresh();
        $this->syncCounts();
    }

    public function render()
    {
        return view('livewire.user.timeline-details-reaction');
    }
}
