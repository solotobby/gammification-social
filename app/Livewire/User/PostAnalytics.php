<?php

namespace App\Livewire\User;

use App\Models\Post;
use Livewire\Component;

class PostAnalytics extends Component
{
    public Post $post;

    public function mount(string $id): void
    {
        $this->post = Post::query()->with('user')->findOrFail($id);

        if ($this->post->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function render()
    {
        $post = $this->post;

        $monetizedViews = (int) ($post->views ?? 0);
        $unmonetizedViews = (int) ($post->views_external ?? 0);
        $totalViews = sumCounter($monetizedViews, $unmonetizedViews);

        $monetizedLikes = (int) ($post->likes ?? 0);
        $monetizedComments = (int) ($post->comments ?? 0);
        $unmonetizedComments = (int) ($post->comment_external ?? 0);
        $totalComments = sumCounter($monetizedComments, $unmonetizedComments);

        $viewsRevenue = (float) viewsAmountCalculator($post->id);
        $likesRevenue = (float) likesAmountCalculator($post->id);
        $commentsRevenue = (float) commentsAmountCalculator($post->id);
        $totalEarnings = $viewsRevenue + $likesRevenue + $commentsRevenue;

        $monetizedEngagement = $monetizedViews + $monetizedLikes + $monetizedComments;

        return view('livewire.user.post-analytics', [
            'postExcerpt' => \Illuminate\Support\Str::limit(plainPostText($post->content ?? ''), 160),
            'monetizedViews' => $monetizedViews,
            'unmonetizedViews' => $unmonetizedViews,
            'totalViews' => $totalViews,
            'monetizedLikes' => $monetizedLikes,
            'monetizedComments' => $monetizedComments,
            'unmonetizedComments' => $unmonetizedComments,
            'totalComments' => $totalComments,
            'viewsRevenue' => $viewsRevenue,
            'likesRevenue' => $likesRevenue,
            'commentsRevenue' => $commentsRevenue,
            'totalEarnings' => $totalEarnings,
            'monetizedEngagement' => $monetizedEngagement,
            'currency' => getCurrencyCode(),
        ]);
    }
}
