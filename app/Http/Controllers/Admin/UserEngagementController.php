<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\AdminUserService;

class UserEngagementController extends Controller
{
    public function __construct(
        protected AdminUserService $users,
    ) {}

    public function engagementAnalytics(User $user)
    {
        return view('admin.user.engagement', $this->users->engagementAnalyticsData($user));
    }
}
