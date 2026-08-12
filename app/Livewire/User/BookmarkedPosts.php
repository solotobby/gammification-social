<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\PostEarningsService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BookmarkedPosts extends Component
{
    use WithPagination;

    #[On('bookmarkRemoved')]
    public function handleBookmarkRemoved(string $postId): void
    {
        $this->resetPage();
    }

    #[On('postDeleted')]
    public function handlePostDeleted(string $postId): void
    {
        $this->resetPage();
    }

    #[On('post-action-toast')]
    public function handlePostActionToast(string $message): void
    {
        session()->flash('success', $message);
    }

    public function render()
    {
        $userId = auth()->id();

        $posts = Post::query()
            ->select('posts.*')
            ->join('post_bookmarks', 'posts.id', '=', 'post_bookmarks.post_id')
            ->where('post_bookmarks.user_id', $userId)
            ->where('posts.status', 'LIVE')
            ->with(['user', 'trends', 'images', 'video'])
            ->withExists(['likes as liked_by_me' => fn ($q) => $q->where('user_id', $userId)])
            ->orderByDesc('post_bookmarks.created_at')
            ->paginate(10);

        $earnings = app(PostEarningsService::class)->forPosts(
            $posts->getCollection()->pluck('id')
        );

        return view('livewire.user.bookmarked-posts', [
            'posts' => $posts,
            'earnings' => $earnings,
        ])->layout('layouts.app');
    }
}
