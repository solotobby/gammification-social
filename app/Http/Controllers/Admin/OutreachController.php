<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutreachController extends Controller
{
    public function index(Request $request)
    {
        $yearStart = now()->startOfYear();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : $yearStart->copy();
        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $status = (string) $request->input('status', 'LIVE');
        $sort = (string) $request->input('sort', 'engagement');
        $minPosts = max(0, (int) $request->input('min_posts', 1));
        $media = (string) $request->input('media', 'all'); // all|video|image|text
        $q = trim((string) $request->input('q', ''));
        $perPage = min(100, max(10, (int) $request->input('per_page', 25)));

        $allowedSorts = [
            'engagement' => 'engagement_score',
            'posts' => 'post_count',
            'views' => 'total_views',
            'likes' => 'total_likes',
            'comments' => 'total_comments',
            'clicks' => 'total_clicks',
        ];
        $sortColumn = $allowedSorts[$sort] ?? 'engagement_score';

        $commentsSum = 'COALESCE(SUM(comments),0) + COALESCE(SUM(CAST(comment_external AS UNSIGNED)),0)';
        $engagementExpr = '(COALESCE(SUM(views),0) + COALESCE(SUM(views_external),0))'
            .' + ((COALESCE(SUM(likes),0) + COALESCE(SUM(likes_external),0)) * 2)'
            .' + (('.$commentsSum.') * 3)'
            .' + (COALESCE(SUM(clicks),0) * 2)';

        $matchedUserIds = null;
        if ($q !== '') {
            $matchedUserIds = User::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', '%'.$q.'%')
                        ->orWhere('username', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%');
                })
                ->limit(500)
                ->pluck('id');
        }

        $basePosts = function () use ($from, $to, $status, $media, $matchedUserIds) {
            return Post::query()
                ->from('posts')
                ->whereBetween('created_at', [$from, $to])
                ->when($status !== 'all', fn ($query) => $query->where('status', $status))
                ->when($media === 'video', fn ($query) => $query->where('has_video', true))
                ->when($media === 'image', fn ($query) => $query->where('has_images', true))
                ->when($media === 'text', function ($query) {
                    $query->where(function ($inner) {
                        $inner->where(function ($q) {
                            $q->where('has_video', false)->orWhereNull('has_video');
                        })->where(function ($q) {
                            $q->where('has_images', false)->orWhereNull('has_images');
                        });
                    });
                })
                ->when($matchedUserIds !== null, function ($query) use ($matchedUserIds) {
                    if ($matchedUserIds->isEmpty()) {
                        $query->whereRaw('1 = 0');
                    } else {
                        $query->whereIn('user_id', $matchedUserIds);
                    }
                });
        };

        $leaderboard = $basePosts()
            ->select([
                'user_id',
                DB::raw('COUNT(*) as post_count'),
                DB::raw('COALESCE(SUM(views),0) + COALESCE(SUM(views_external),0) as total_views'),
                DB::raw('COALESCE(SUM(likes),0) + COALESCE(SUM(likes_external),0) as total_likes'),
                DB::raw($commentsSum.' as total_comments'),
                DB::raw('COALESCE(SUM(clicks),0) as total_clicks'),
                DB::raw($engagementExpr.' as engagement_score'),
                DB::raw('SUM(CASE WHEN has_video = 1 THEN 1 ELSE 0 END) as video_posts'),
                DB::raw('SUM(CASE WHEN has_images = 1 THEN 1 ELSE 0 END) as image_posts'),
                DB::raw('MAX(created_at) as last_posted_at'),
            ])
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) >= ?', [$minPosts])
            ->orderByDesc($sortColumn)
            ->orderByDesc('post_count');

        $totals = $basePosts()
            ->selectRaw('COUNT(*) as posts')
            ->selectRaw('COUNT(DISTINCT user_id) as creators')
            ->selectRaw('COALESCE(SUM(views),0) + COALESCE(SUM(views_external),0) as views')
            ->selectRaw('COALESCE(SUM(likes),0) + COALESCE(SUM(likes_external),0) as likes')
            ->selectRaw($commentsSum.' as comments')
            ->first();

        $rows = $leaderboard->paginate($perPage)->withQueryString();

        $userIds = $rows->getCollection()->pluck('user_id')->filter()->unique()->values();
        $users = User::query()
            ->whereIn('id', $userIds)
            ->get(['id', 'name', 'username', 'email', 'avatar', 'phone', 'status'])
            ->keyBy('id');

        return view('admin.outreach.index', [
            'rows' => $rows,
            'users' => $users,
            'totals' => $totals,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'status' => $status,
            'sort' => $sort,
            'minPosts' => $minPosts,
            'media' => $media,
            'q' => $q,
            'perPage' => $perPage,
            'yearLabel' => $yearStart->format('Y'),
        ]);
    }
}
