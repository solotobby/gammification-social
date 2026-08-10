<?php

namespace App\Livewire\User;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Analytics extends Component
{
    public string $month;

    public int $postsCount = 0;
    public int $totalViews = 0;
    public int $totalLikes = 0;
    public int $totalComments = 0;
    public int $monetizedViews = 0;
    public int $monetizedLikes = 0;
    public int $monetizedComments = 0;
    public int $totalEngagement = 0;
    public float $estimatedEarnings = 0;

    public function mount(): void
    {
        $this->month = now()->translatedFormat('F Y');

        $stats = Post::query()
            ->where('user_id', Auth::id())
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->selectRaw('
                COUNT(*) as posts_count,
                COALESCE(SUM(views), 0) as views_sum,
                COALESCE(SUM(views_external), 0) as views_ext_sum,
                COALESCE(SUM(likes), 0) as likes_sum,
                COALESCE(SUM(comments), 0) as comments_sum,
                COALESCE(SUM(comment_external), 0) as comments_ext_sum
            ')
            ->first();

        $this->postsCount = (int) ($stats->posts_count ?? 0);
        $this->monetizedViews = (int) ($stats->views_sum ?? 0);
        $this->monetizedLikes = (int) ($stats->likes_sum ?? 0);
        $this->monetizedComments = (int) ($stats->comments_sum ?? 0);
        $this->totalViews = (int) sumCounter($this->monetizedViews, (int) ($stats->views_ext_sum ?? 0));
        $this->totalLikes = $this->monetizedLikes;
        $this->totalComments = (int) sumCounter($this->monetizedComments, (int) ($stats->comments_ext_sum ?? 0));
        $this->totalEngagement = $this->monetizedViews + $this->monetizedLikes + $this->monetizedComments;
        $this->estimatedEarnings = engagementEarnings($this->totalEngagement / 4);
    }

    public function render()
    {
        return view('livewire.user.analytics');
    }
}
