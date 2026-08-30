<?php

namespace App\Livewire\User;

use App\Models\Hashtag as ModelsHashtag;
use App\Services\PostEarningsService;
use App\Services\PayKoinService;
use Livewire\Attributes\On;
use Livewire\Component;

class Hashtag extends Component
{
    public string $tag;

    public int $page = 1;

    public int $perPage = 10;

    public bool $hasMore = false;

    public function mount($tag): void
    {
        $this->tag = $tag;
    }

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    #[On('postDeleted')]
    public function handlePostDeleted(string $postId): void
    {
        // Render re-queries posts.
    }

    #[On('post-action-toast')]
    public function handlePostActionToast(string $message): void
    {
        session()->flash('success', $message);
    }

    public function render()
    {
        $hashtag = ModelsHashtag::query()
            ->where('name', $this->tag)
            ->firstOrFail();

        $limit = $this->perPage * $this->page;

        $posts = $hashtag
            ->posts()
            ->with([
                'user',
                'hashtags',
                'images',
                'video',
                'trends',
            ])
            ->latest()
            ->take($limit + 1)
            ->get();

        $this->hasMore = $posts->count() > $limit;
        $posts = $posts->take($limit);

        $earnings = app(PostEarningsService::class)->forPosts($posts->pluck('id'));

        $postGiftSummaries = app(PayKoinService::class)->giftSummariesForIds(
            'post',
            $posts->pluck('id')->all(),
        );

        return view('livewire.user.hashtag', [
            'hashtag' => $hashtag,
            'posts' => $posts,
            'earnings' => $earnings,
            'postGiftSummaries' => $postGiftSummaries,
            'trendingMembers' => engagement(),
            'trendingTopics' => trendingTopics(),
        ])->layout('layouts.app');
    }
}
