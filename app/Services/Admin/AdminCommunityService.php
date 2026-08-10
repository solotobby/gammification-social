<?php

namespace App\Services\Admin;

use App\Models\Community;
use App\Models\CommunityPayout;
use App\Models\CommunityPost;
use App\Models\CommunitySubscription;
use App\Services\AdminAuditService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminCommunityService
{
    public function __construct(
        protected AdminAuditService $audit,
    ) {}

    public function dashboardStats(): array
    {
        $revenueByCurrency = CommunityPayout::query()
            ->selectRaw('currency, COUNT(*) as payments, SUM(gross_amount) as gross, SUM(platform_fee) as platform, SUM(creator_amount) as creator')
            ->groupBy('currency')
            ->get()
            ->keyBy('currency');

        return [
            'total' => Community::count(),
            'paid' => Community::where('type', 'paid')->count(),
            'archived' => Community::whereNotNull('archived_at')->count(),
            'public' => Community::where('type', 'public')->whereNull('archived_at')->count(),
            'activeSubscriptions' => CommunitySubscription::where('status', 'active')->count(),
            'pendingSubscriptions' => CommunitySubscription::where('status', 'pending')->count(),
            'totalPosts' => CommunityPost::count(),
            'totalMembers' => (int) DB::table('community_users')->where('status', 'active')->count(),
            'byCurrency' => Community::query()
                ->selectRaw('currency, COUNT(*) as count')
                ->groupBy('currency')
                ->pluck('count', 'currency'),
            'revenueByCurrency' => $revenueByCurrency,
        ];
    }

    /**
     * Snapshot + charts for the admin home dashboard.
     */
    public function dashboardAnalytics(int $days = 30): array
    {
        $stats = $this->dashboardStats();

        return array_merge($stats, [
            'newCommunitiesWeek' => Community::where('created_at', '>=', now()->subDays(7))->count(),
            'newSubscriptionsWeek' => CommunitySubscription::query()
                ->where('created_at', '>=', now()->subDays(7))
                ->where('status', 'active')
                ->count(),
            'growthChart' => $this->dailyGrowthChart($days),
            'revenueChart' => $this->dailyRevenueChart($days),
            'topByMembers' => $this->topCommunitiesByMembers(5),
            'recentCommunities' => $this->recentCommunities(5),
        ]);
    }

    public function topCommunitiesByMembers(int $limit = 5): Collection
    {
        return Community::query()
            ->with(['user:id,name,username', 'category:id,name'])
            ->withCount('members')
            ->whereNull('archived_at')
            ->orderByDesc('members_count')
            ->limit($limit)
            ->get();
    }

    public function recentCommunities(int $limit = 5): Collection
    {
        return Community::query()
            ->with(['user:id,name,username', 'category:id,name'])
            ->withCount('members')
            ->latest()
            ->limit($limit)
            ->get();
    }

    protected function dailyGrowthChart(int $days = 30): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $communities = Community::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $subscriptions = CommunitySubscription::query()
            ->where('created_at', '>=', $start)
            ->where('status', 'active')
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $communityValues = [];
        $subscriptionValues = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('M j');
            $communityValues[] = (int) ($communities[$key] ?? 0);
            $subscriptionValues[] = (int) ($subscriptions[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'communities' => $communityValues,
            'subscriptions' => $subscriptionValues,
            'communitiesTotal' => array_sum($communityValues),
            'subscriptionsTotal' => array_sum($subscriptionValues),
        ];
    }

    protected function dailyRevenueChart(int $days = 30): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = CommunityPayout::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, currency, COUNT(*) as payments, SUM(platform_fee) as platform_fee')
            ->groupBy('day', 'currency')
            ->orderBy('day')
            ->get();

        $labels = [];
        $paymentCounts = [];
        $ngnPlatform = [];
        $usdPlatform = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('M j');

            $dayRows = $rows->filter(fn ($row) => \Carbon\Carbon::parse($row->day)->format('Y-m-d') === $key);
            $paymentCounts[] = (int) $dayRows->sum('payments');
            $ngnPlatform[] = (float) ($dayRows->firstWhere('currency', 'NGN')?->platform_fee ?? 0);
            $usdPlatform[] = (float) ($dayRows->firstWhere('currency', 'USD')?->platform_fee ?? 0);
        }

        return [
            'labels' => $labels,
            'payments' => $paymentCounts,
            'ngnPlatform' => $ngnPlatform,
            'usdPlatform' => $usdPlatform,
            'paymentsTotal' => array_sum($paymentCounts),
        ];
    }

    public function list(?string $search = null, ?string $type = null, ?string $currency = null): LengthAwarePaginator
    {
        return Community::query()
            ->with(['category:id,name', 'user:id,name,username,email'])
            ->withCount(['members', 'posts', 'subscriptions'])
            ->withSum(['payouts as gross_revenue' => fn ($q) => $q], 'gross_amount')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($owner) use ($search) {
                            $owner->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($type === 'archived', fn ($q) => $q->whereNotNull('archived_at'))
            ->when($type && $type !== 'archived', fn ($q) => $q->where('type', $type)->whereNull('archived_at'))
            ->when($currency, fn ($q) => $q->where('currency', strtoupper($currency)))
            ->latest()
            ->paginate(25)
            ->withQueryString();
    }

    public function show(string $communityId): Community
    {
        return Community::query()
            ->with(['category', 'user'])
            ->withCount([
                'members',
                'posts',
                'subscriptions',
                'subscriptions as active_subscriptions_count' => fn ($q) => $q->where('status', 'active'),
                'subscriptions as pending_subscriptions_count' => fn ($q) => $q->where('status', 'pending'),
                'invites',
                'joinRequests',
                'joinRequests as pending_join_requests_count' => fn ($q) => $q->where('status', 'pending'),
                'bannedMembers',
            ])
            ->findOrFail($communityId);
    }

    public function revenueSummary(Community $community): Collection
    {
        return $community->payouts()
            ->selectRaw('currency, COUNT(*) as payments, SUM(gross_amount) as gross, SUM(platform_fee) as platform, SUM(creator_amount) as creator')
            ->groupBy('currency')
            ->get();
    }

    public function members(Community $community, int $perPage = 25): LengthAwarePaginator
    {
        return $community->allMembers()
            ->select('users.id', 'users.name', 'users.username', 'users.email')
            ->orderByDesc('community_users.created_at')
            ->paginate($perPage, ['*'], 'members_page');
    }

    public function subscriptions(Community $community, int $perPage = 25): LengthAwarePaginator
    {
        return $community->subscriptions()
            ->with('user:id,name,username,email')
            ->latest()
            ->paginate($perPage, ['*'], 'subscriptions_page');
    }

    public function payouts(Community $community, int $perPage = 25): LengthAwarePaginator
    {
        return $community->payouts()
            ->with(['payer:id,name,username', 'subscription:id,status'])
            ->latest()
            ->paginate($perPage, ['*'], 'payouts_page');
    }

    public function posts(Community $community, int $perPage = 20): LengthAwarePaginator
    {
        return $community->posts()
            ->with('user:id,name,username')
            ->latest()
            ->paginate($perPage, ['*'], 'posts_page');
    }

    public function invites(Community $community, int $perPage = 25): LengthAwarePaginator
    {
        return $community->invites()
            ->with(['user:id,name,email', 'inviter:id,name'])
            ->latest()
            ->paginate($perPage, ['*'], 'invites_page');
    }

    public function joinRequests(Community $community, int $perPage = 25): LengthAwarePaginator
    {
        return $community->joinRequests()
            ->with('user:id,name,username,email')
            ->latest()
            ->paginate($perPage, ['*'], 'requests_page');
    }

    public function paymentPlans(Community $community): Collection
    {
        return $community->paymentPlans()->orderBy('currency')->orderBy('billing_interval')->get();
    }

    public function archive(Community $community): void
    {
        $community->update([
            'type' => 'private',
            'archived_at' => now(),
        ]);

        $this->audit->log('community.archived', $community);
    }

    public function unarchive(Community $community): void
    {
        $community->update(['archived_at' => null]);

        $this->audit->log('community.unarchived', $community);
    }

    public function delete(Community $community): void
    {
        DB::transaction(function () use ($community) {
            CommunityPost::query()
                ->where('community_id', $community->id)
                ->with('media')
                ->each(function (CommunityPost $post) {
                    foreach ($post->media as $item) {
                        Storage::disk('spaces')->delete($item->path);
                    }
                });

            if ($community->image) {
                Storage::disk('spaces')->delete($community->image);
            }

            if ($community->banner) {
                Storage::disk('spaces')->delete($community->banner);
            }

            $communityId = $community->id;
            $community->delete();

            Storage::disk('spaces')->deleteDirectory('communities/'.$communityId);

            $this->audit->log('community.deleted', null, ['community_id' => $communityId]);
        });
    }

    public function banMember(Community $community, string $userId): bool
    {
        if ($community->user_id === $userId) {
            return false;
        }

        $updated = DB::table('community_users')
            ->where('community_id', $community->id)
            ->where('user_id', $userId)
            ->update(['status' => 'banned', 'updated_at' => now()]);

        if ($updated) {
            $this->audit->log('community.member_banned', $community, ['user_id' => $userId]);
        }

        return (bool) $updated;
    }

    public function unbanMember(Community $community, string $userId): bool
    {
        $updated = DB::table('community_users')
            ->where('community_id', $community->id)
            ->where('user_id', $userId)
            ->where('status', 'banned')
            ->update(['status' => 'active', 'updated_at' => now()]);

        if ($updated) {
            $this->audit->log('community.member_unbanned', $community, ['user_id' => $userId]);
        }

        return (bool) $updated;
    }

    public function removeMember(Community $community, string $userId): bool
    {
        if ($community->user_id === $userId) {
            return false;
        }

        $deleted = DB::table('community_users')
            ->where('community_id', $community->id)
            ->where('user_id', $userId)
            ->delete();

        if ($deleted) {
            $this->audit->log('community.member_removed', $community, ['user_id' => $userId]);
        }

        return (bool) $deleted;
    }

    public function deletePost(Community $community, string $postId): bool
    {
        $post = CommunityPost::query()
            ->where('community_id', $community->id)
            ->where('id', $postId)
            ->with('media')
            ->first();

        if (! $post) {
            return false;
        }

        foreach ($post->media as $item) {
            Storage::disk('spaces')->delete($item->path);
        }

        $post->delete();

        $this->audit->log('community.post_deleted', $community, ['post_id' => $postId]);

        return true;
    }
}
