<?php

namespace App\Services\Admin;

use App\Mail\GeneralMail;
use App\Models\Community;
use App\Models\CommunityPaymentPlan;
use App\Models\CommunityPayout;
use App\Models\CommunityPost;
use App\Models\CommunitySubscription;
use App\Models\Currency;
use App\Notifications\GeneralNotification;
use App\Services\AdminAuditService;
use App\Support\AdminDateRange;
use App\Support\StoredMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminCommunityService
{
    public function __construct(
        protected AdminAuditService $audit,
    ) {}

    public function dashboardStats(?AdminDateRange $range = null): array
    {
        $revenueQuery = CommunityPayout::query()
            ->selectRaw('currency, COUNT(*) as payments, SUM(gross_amount) as gross, SUM(platform_fee) as platform, SUM(creator_amount) as creator')
            ->groupBy('currency');

        if ($range) {
            $revenueQuery->whereBetween('created_at', [$range->start, $range->end]);
        }

        $revenueByCurrency = $revenueQuery->get()->keyBy('currency');

        $postsQuery = CommunityPost::query();
        $membersQuery = DB::table('community_users')->where('status', 'active');

        if ($range) {
            $postsQuery->whereBetween('created_at', [$range->start, $range->end]);
            $membersQuery->whereBetween('created_at', [$range->start, $range->end]);
        }

        return [
            'total' => Community::count(),
            'paid' => Community::where('type', 'paid')->count(),
            'archived' => Community::whereNotNull('archived_at')->count(),
            'public' => Community::where('type', 'public')->whereNull('archived_at')->count(),
            'activeSubscriptions' => CommunitySubscription::where('status', 'active')->count(),
            'pendingSubscriptions' => CommunitySubscription::where('status', 'pending')->count(),
            'totalPosts' => $postsQuery->count(),
            'totalMembers' => (int) $membersQuery->count(),
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
    public function dashboardAnalytics(?AdminDateRange $range = null): array
    {
        $range ??= AdminDateRange::fromRequest(request());
        $stats = $this->dashboardStats($range);

        return array_merge($stats, [
            'newCommunities' => Community::query()
                ->whereBetween('created_at', [$range->start, $range->end])
                ->count(),
            'newSubscriptions' => CommunitySubscription::query()
                ->whereBetween('created_at', [$range->start, $range->end])
                ->where('status', 'active')
                ->count(),
            'growthChart' => $this->dailyGrowthChart($range),
            'revenueChart' => $this->dailyRevenueChart($range),
            'topByMembers' => $this->topCommunitiesByMembers(5),
            'recentCommunities' => $this->recentCommunities(5, $range),
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

    public function recentCommunities(int $limit = 5, ?AdminDateRange $range = null): Collection
    {
        return Community::query()
            ->with(['user:id,name,username', 'category:id,name'])
            ->withCount('members')
            ->when($range, fn ($q) => $q->whereBetween('created_at', [$range->start, $range->end]))
            ->latest()
            ->limit($limit)
            ->get();
    }

    protected function dailyGrowthChart(AdminDateRange $range): array
    {
        $communities = Community::query()
            ->whereBetween('created_at', [$range->start, $range->end])
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $subscriptions = CommunitySubscription::query()
            ->whereBetween('created_at', [$range->start, $range->end])
            ->where('status', 'active')
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $communityValues = [];
        $subscriptionValues = [];

        $cursor = $range->start->copy()->startOfDay();
        $endDay = $range->end->copy()->startOfDay();

        while ($cursor->lte($endDay)) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('M j');
            $communityValues[] = (int) ($communities[$key] ?? 0);
            $subscriptionValues[] = (int) ($subscriptions[$key] ?? 0);
            $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'communities' => $communityValues,
            'subscriptions' => $subscriptionValues,
            'communitiesTotal' => array_sum($communityValues),
            'subscriptionsTotal' => array_sum($subscriptionValues),
        ];
    }

    protected function dailyRevenueChart(AdminDateRange $range): array
    {
        $rows = CommunityPayout::query()
            ->whereBetween('created_at', [$range->start, $range->end])
            ->selectRaw('DATE(created_at) as day, currency, COUNT(*) as payments, SUM(platform_fee) as platform_fee')
            ->groupBy('day', 'currency')
            ->orderBy('day')
            ->get();

        $labels = [];
        $paymentCounts = [];
        $ngnPlatform = [];
        $usdPlatform = [];

        $cursor = $range->start->copy()->startOfDay();
        $endDay = $range->end->copy()->startOfDay();

        while ($cursor->lte($endDay)) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('M j');

            $dayRows = $rows->filter(fn ($row) => \Carbon\Carbon::parse($row->day)->format('Y-m-d') === $key);
            $paymentCounts[] = (int) $dayRows->sum('payments');
            $ngnPlatform[] = (float) ($dayRows->firstWhere('currency', 'NGN')?->platform_fee ?? 0);
            $usdPlatform[] = (float) ($dayRows->firstWhere('currency', 'USD')?->platform_fee ?? 0);
            $cursor->addDay();
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

    public function activeCurrencies(): Collection
    {
        return Currency::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'base_rate']);
    }

    /**
     * Change community currency and convert list price / payment-plan amounts.
     *
     * @return array{community: Community, from: string, to: string, old_fee: float, new_fee: float}
     */
    public function updateCurrency(
        Community $community,
        string $currency,
        string $reason,
        ?float $amount = null,
    ): array {
        if ($community->type !== 'paid') {
            throw ValidationException::withMessages([
                'currency' => 'Currency can only be changed for paid communities.',
            ]);
        }

        $from = Community::normaliseCurrency($community->currency);
        $to = Community::normaliseCurrency($currency);
        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages([
                'reason' => 'A reason is required when changing community currency.',
            ]);
        }

        if ($from === $to) {
            throw ValidationException::withMessages([
                'currency' => 'Choose a different currency from the current one.',
            ]);
        }

        if (! Currency::query()->where('is_active', true)->where('code', $to)->exists()) {
            throw ValidationException::withMessages([
                'currency' => 'Selected currency is not active.',
            ]);
        }

        $oldFee = (float) $community->monthly_fee;

        try {
            $converted = $amount !== null
                ? round($amount, 2)
                : (float) convertCurrency(max($oldFee, 0), $from, $to);
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'currency' => $e->getMessage(),
            ]);
        }

        $minimum = communityMinimumPrice($to);
        if ($converted < $minimum) {
            throw ValidationException::withMessages([
                'amount' => "Converted price must be at least {$to} ".number_format($minimum, communityPriceDecimals($to)).'.',
            ]);
        }

        $newFee = $converted;

        DB::transaction(function () use ($community, $from, $to, $oldFee, $newFee, $reason) {
            $community->update([
                'currency' => $to,
                'monthly_fee' => $newFee,
            ]);

            // Invalidate Flutterwave plans tied to the previous currency/amount.
            CommunityPaymentPlan::query()
                ->where('community_id', $community->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);

            $this->audit->log('community.currency_updated', $community->fresh(), [
                'from_currency' => $from,
                'to_currency' => $to,
                'old_monthly_fee' => $oldFee,
                'new_monthly_fee' => $newFee,
                'reason' => $reason,
            ]);
        });

        $community = $community->fresh(['user']);
        $this->notifyCreatorOfCurrencyChange($community, $from, $to, $oldFee, $newFee, $reason);

        return [
            'community' => $community,
            'from' => $from,
            'to' => $to,
            'old_fee' => $oldFee,
            'new_fee' => $newFee,
        ];
    }

    protected function notifyCreatorOfCurrencyChange(
        Community $community,
        string $from,
        string $to,
        float $oldFee,
        float $newFee,
        string $reason,
    ): void {
        $owner = $community->user;
        if (! $owner) {
            return;
        }

        $priceLine = sprintf(
            'Price updated from %s %s to %s %s.',
            $from,
            number_format($oldFee, 2),
            $to,
            number_format($newFee, 2),
        );

        $message = "An admin changed the currency for your paid community \"{$community->name}\". {$priceLine} Reason: {$reason}";

        $owner->notify(new GeneralNotification([
            'title' => 'Community currency updated',
            'message' => $message,
            'icon' => 'fa-coins text-warning',
            'url' => url('community/'.$community->slug),
        ]));

        if ($owner->email) {
            Mail::to($owner->email)->send(new GeneralMail(
                (object) ['name' => $owner->name, 'email' => $owner->email],
                'Community currency updated — '.$community->name,
                $message.' You can review billing details in your community settings.'
            ));
        }
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
                        StoredMedia::delete($item->path);
                    }
                });

            StoredMedia::delete($community->image);
            StoredMedia::delete($community->banner);

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
            StoredMedia::delete($item->path);
        }

        $post->delete();

        $this->audit->log('community.post_deleted', $community, ['post_id' => $postId]);

        return true;
    }
}
