<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\PostEarningsService;
use App\Services\PayKoinService;
use Livewire\Attributes\On;
use Livewire\Component;

class BookmarkedPosts extends Component
{
    public int $page = 1;

    public int $perPage = 10;

    public bool $hasMore = false;

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    #[On('bookmarkRemoved')]
    public function handleBookmarkRemoved(string $postId): void
    {
        $this->page = 1;
    }

    #[On('postDeleted')]
    public function handlePostDeleted(string $postId): void
    {
        $this->page = 1;
    }

    #[On('post-action-toast')]
    public function handlePostActionToast(string $message): void
    {
        session()->flash('success', $message);
    }

    public function render()
    {
        $userId = auth()->id();
        $limit = $this->perPage * $this->page;

        $posts = Post::query()
            ->select('posts.*')
            ->join('post_bookmarks', 'posts.id', '=', 'post_bookmarks.post_id')
            ->where('post_bookmarks.user_id', $userId)
            ->where('posts.status', 'LIVE')
            ->with(['user', 'trends', 'images', 'video'])
            ->withExists(['likes as liked_by_me' => fn ($q) => $q->where('user_id', $userId)])
            ->orderByDesc('post_bookmarks.created_at')
            ->take($limit + 1)
            ->get();

        $this->hasMore = $posts->count() > $limit;
        $posts = $posts->take($limit);

        $earnings = app(PostEarningsService::class)->forPosts($posts->pluck('id'));

        $postGiftSummaries = app(PayKoinService::class)->giftSummariesForIds(
            'post',
            $posts->pluck('id')->all(),
        );

        return view('livewire.user.bookmarked-posts', [
            'posts' => $posts,
            'earnings' => $earnings,
            'postGiftSummaries' => $postGiftSummaries,
        ])->layout('layouts.app');
    }
}
