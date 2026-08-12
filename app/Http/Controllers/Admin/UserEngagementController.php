<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\AdminUserService;
use App\Support\AdminDateRange;
use Illuminate\Http\Request;

class UserEngagementController extends Controller
{
    public function __construct(
        protected AdminUserService $users,
    ) {}

    public function engagementAnalytics(Request $request, User $user)
    {
        $dateRange = AdminDateRange::fromRequest($request);

        return view('admin.user.engagement', $this->users->engagementAnalyticsData($user, $dateRange));
    }
}
