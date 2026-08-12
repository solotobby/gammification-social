<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminBookmarkAnalyticsService;
use App\Support\AdminDateRange;
use Illuminate\Http\Request;

class BookmarkAnalyticsController extends Controller
{
    public function __construct(private AdminBookmarkAnalyticsService $bookmarks) {}

    public function index(Request $request)
    {
        $dateRange = AdminDateRange::fromRequest($request);

        return view('admin.bookmarks.index', [
            'dateRange' => $dateRange,
            'stats' => $this->bookmarks->dashboardStats($dateRange),
            'topPosts' => $this->bookmarks->topPosts($dateRange),
            'topUsers' => $this->bookmarks->topUsers($dateRange),
            'recent' => $this->bookmarks->recentBookmarks($dateRange),
            'dailyTrend' => $this->bookmarks->dailyTrend($dateRange),
        ]);
    }
}
