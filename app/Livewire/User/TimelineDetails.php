<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\PostEarningsService;
use App\Services\PayKoinService;
use Livewire\Component;

class TimelineDetails extends Component
{
    public Post $post;

    public $user;

    public function mount(Post $post)
    {
        if ($post->status !== 'LIVE' && $post->user_id !== auth()->id()) {
            abort(404);
        }

        $this->post = $post->load(['user', 'images', 'video', 'trends']);
        $this->user = $this->post->user;
    }

    public function render()
    {
        $earnings = app(PostEarningsService::class)->forPosts(collect([$this->post->id]));

        $postGiftSummaries = app(PayKoinService::class)->giftSummariesForIds(
            'post',
            [$this->post->id],
        );

        return view('livewire.user.timeline-details', [
            'estimatedEarnings' => $earnings[$this->post->id] ?? 0,
            'giftSummary' => $postGiftSummaries[$this->post->id] ?? ['total' => 0, 'recent' => []],
        ])->layout('layouts.app');
    }
}
