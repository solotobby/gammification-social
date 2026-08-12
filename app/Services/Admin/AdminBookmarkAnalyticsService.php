<?php

namespace App\Services\Admin;

use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\User;
use App\Support\AdminDateRange;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminBookmarkAnalyticsService
{
    public function dashboardStats(AdminDateRange $range): array
    {
        $base = PostBookmark::query()
            ->whereBetween('created_at', [$range->start, $range->end]);

        return [
            'total_bookmarks' => (clone $base)->count(),
            'unique_posts' => (int) (clone $base)->selectRaw('COUNT(DISTINCT post_id) as aggregate')->value('aggregate'),
            'unique_users' => (int) (clone $base)->selectRaw('COUNT(DISTINCT user_id) as aggregate')->value('aggregate'),
            'avg_per_day' => round(
                (clone $base)->count() / max(1, $range->days()),
                1
            ),
        ];
    }

    public function topPosts(AdminDateRange $range, int $limit = 25): Collection
    {
        return Post::query()
            ->with(['user:id,name,username,avatar,status'])
            ->withCount(['bookmarks' => fn ($q) => $q->whereBetween('created_at', [$range->start, $range->end])])
            ->whereHas('bookmarks', fn ($q) => $q->whereBetween('created_at', [$range->start, $range->end]))
            ->orderByDesc('bookmarks_count')
            ->limit($limit)
            ->get();
    }

    public function topUsers(AdminDateRange $range, int $limit = 15): Collection
    {
        return User::query()
            ->select('users.id', 'users.name', 'users.username', 'users.email', 'users.avatar', 'users.status')
            ->selectSub(
                PostBookmark::query()
                    ->selectRaw('count(*)')
                    ->whereColumn('post_bookmarks.user_id', 'users.id')
                    ->whereBetween('created_at', [$range->start, $range->end]),
                'bookmarks_count'
            )
            ->whereExists(function ($q) use ($range) {
                $q->select(DB::raw(1))
                    ->from('post_bookmarks')
                    ->whereColumn('post_bookmarks.user_id', 'users.id')
                    ->whereBetween('created_at', [$range->start, $range->end]);
            })
            ->orderByDesc('bookmarks_count')
            ->limit($limit)
            ->get();
    }

    public function recentBookmarks(AdminDateRange $range): LengthAwarePaginator
    {
        return PostBookmark::query()
            ->with([
                'user:id,name,username,avatar',
                'post:id,user_id,content,status,created_at',
                'post.user:id,name,username',
            ])
            ->whereBetween('created_at', [$range->start, $range->end])
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    public function dailyTrend(AdminDateRange $range): Collection
    {
        $rows = PostBookmark::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$range->start, $range->end])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $trend = collect();
        $cursor = $range->start->copy()->startOfDay();
        $endDay = $range->end->copy()->startOfDay();

        while ($cursor->lte($endDay)) {
            $key = $cursor->format('Y-m-d');
            $trend->push((object) [
                'day' => $key,
                'total' => (int) ($rows[$key] ?? 0),
            ]);
            $cursor->addDay();
        }

        return $trend;
    }
}
