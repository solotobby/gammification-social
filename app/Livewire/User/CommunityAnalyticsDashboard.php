<?php

namespace App\Livewire\User;

use App\Models\Community;
use App\Models\CommunityJoinRequest;
use App\Models\CommunityPost;
use App\Models\CommunitySubscription;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CommunityAnalyticsDashboard extends Component
{
    public Community $community;

    public function mount(Community $community): void
    {
        abort_unless(
            $community->user_id === auth()->id()
                || $community->members()
                    ->where('users.id', auth()->id())
                    ->wherePivot('role', 'admin')
                    ->exists(),
            403
        );

        $this->community = $community;
    }

    public function stats(): array
    {
        $id = $this->community->id;

        return [
            'members_total' => (int) DB::table('community_users')
                ->where('community_id', $id)->where('status', 'active')->count(),
            'members_7d' => (int) DB::table('community_users')
                ->where('community_id', $id)->where('status', 'active')
                ->where('created_at', '>=', now()->subDays(7))->count(),
            'members_30d' => (int) DB::table('community_users')
                ->where('community_id', $id)->where('status', 'active')
                ->where('created_at', '>=', now()->subDays(30))->count(),
            'posts_total' => (int) CommunityPost::where('community_id', $id)->count(),
            'posts_7d' => (int) CommunityPost::where('community_id', $id)
                ->where('created_at', '>=', now()->subDays(7))->count(),
            'posts_30d' => (int) CommunityPost::where('community_id', $id)
                ->where('created_at', '>=', now()->subDays(30))->count(),
            'likes_total' => (int) CommunityPost::where('community_id', $id)->sum('likes_count'),
            'comments_total' => (int) CommunityPost::where('community_id', $id)->sum('comments_count'),
            'views_total' => (int) CommunityPost::where('community_id', $id)->sum('views_count'),
            'pending_requests' => CommunityJoinRequest::where('community_id', $id)->where('status', 'pending')->count(),
            'active_subscribers' => $this->community->type === 'paid'
                ? CommunitySubscription::where('community_id', $id)
                    ->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->count()
                : 0,
            'invite_link_uses' => (int) DB::table('community_invites')
                ->where('community_id', $id)
                ->where('type', 'link')
                ->sum('uses_count'),
        ];
    }

    public function topPosts()
    {
        return CommunityPost::where('community_id', $this->community->id)
            ->with('user')
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->limit(5)
            ->get();
    }

    public function recentMembers()
    {
        return $this->community->members()
            ->orderByDesc('community_users.created_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.user.community-analytics-dashboard', [
            'stats' => $this->stats(),
            'topPosts' => $this->topPosts(),
            'recentMembers' => $this->recentMembers(),
        ]);
    }
}
