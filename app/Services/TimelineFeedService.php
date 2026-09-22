<?php

namespace App\Services;

use App\Models\Follow;
use App\Models\HiddenPost;
use App\Models\Post;
use App\Models\PostBoost;
use App\Models\SystemSetting;
use App\Models\UserComment;
use App\Models\UserLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TimelineFeedService
{
    /**
     * Dynamic cadence bounds: 1 boosted post every 5 to 8 organic posts.
     */
    public const DYNAMIC_CADENCE_MIN = 5;
    public const DYNAMIC_CADENCE_MAX = 8;

    /**
     * Legacy default cadence (kept for backwards compatibility in explicit tests).
     */
    public const DEFAULT_CADENCE = 4;

    /**
     * Cache duration for active boost candidate post IDs (seconds).
     */
    public const CACHE_TTL_SECONDS = 30;

    public const CACHE_KEY = 'active_timeline_boost_post_ids';

    /**
     * Cache duration for user network graphs (seconds).
     */
    public const NETWORK_CACHE_TTL = 900; // 15 minutes

    /**
     * Build an interleaved feed for a specific tab ('for_you' or 'following').
     *
     * @param  string  $tab 'for_you' | 'following'
     * @param  string|null  $userId Viewer user ID
     * @param  int  $targetCount Total posts desired for current pagination
     * @param  int|null  $seed Deterministic seed for author-interleave and boost pacing
     * @param  int|null  $cadence Optional fixed cadence override; if null, dynamic 5-8 cadence is used
     * @param  array  $withRelations Relations to eager load
     * @param  Builder|null  $baseQuery Optional custom base query
     * @return array{posts: Collection, hasMore: bool}
     */
    public function buildFeedForTab(
        string $tab,
        ?string $userId,
        int $targetCount,
        ?int $seed = null,
        ?int $cadence = null,
        array $withRelations = ['user', 'images', 'video', 'activeBoost'],
        ?Builder $baseQuery = null
    ): array {
        $targetCount = max(1, $targetCount);

        if (! $baseQuery) {
            $baseQuery = Post::query()
                ->where('status', 'LIVE')
                ->with($withRelations);

            if ($userId) {
                $hiddenPostIds = HiddenPost::query()
                    ->where('user_id', $userId)
                    ->pluck('post_id');

                if ($hiddenPostIds->isNotEmpty()) {
                    $baseQuery->whereNotIn('id', $hiddenPostIds);
                }

                $baseQuery->withExists([
                    'likes as liked_by_me' => fn ($q) => $q->where('user_id', $userId),
                    'bookmarks as is_bookmarked_by_me' => fn ($q) => $q->where('user_id', $userId),
                ]);
            }
        }

        if ($tab === 'following') {
            return $this->buildMutualsFeed(
                baseQuery: $baseQuery,
                userId: $userId,
                targetCount: $targetCount,
                seed: $seed,
                cadence: $cadence,
                withRelations: $withRelations
            );
        }

        return $this->buildTieredForYouFeed(
            baseQuery: $baseQuery,
            userId: $userId,
            targetCount: $targetCount,
            seed: $seed,
            cadence: $cadence,
            withRelations: $withRelations
        );
    }

    /**
     * Build 'For You' feed using equal-rights tiered discovery:
     * - Tier 1: Warm Network Circle (followers, following, past likers/commenters) within 4-10 day recency guardrail.
     * - Dynamic seeded author-interleaving so single creators cannot monopolize the feed.
     * - Tier 2: Platform Discovery fallback (high-engagement posts: views, likes, comments, replies).
     * - Dynamic 5-8 boosted post cadence.
     */
    protected function buildTieredForYouFeed(
        Builder $baseQuery,
        ?string $userId,
        int $targetCount,
        ?int $seed = null,
        ?int $cadence = null,
        array $withRelations = ['user', 'images', 'video', 'activeBoost']
    ): array {
        $fetchPoolSize = $targetCount * 2;
        $warmNetworkIds = $userId ? $this->resolveWarmNetworkIds($userId) : [];

        $tier1Posts = collect();

        // ── Tier 1: Warm Network Circle with 4-10 Day Recency Guardrail ──
        if (! empty($warmNetworkIds)) {
            $tenDaysAgo = now()->subDays(10);

            $rawTier1 = (clone $baseQuery)
                ->where('is_boosted', false)
                ->whereIn('user_id', $warmNetworkIds)
                ->where('created_at', '>=', $tenDaysAgo)
                ->latest('created_at')
                ->take($fetchPoolSize)
                ->get();

            $tier1Posts = $this->seededAuthorInterleave($rawTier1, $seed);
        }

        $organicInterleaved = $tier1Posts;

        // ── Tier 2: Platform Discovery (When Warm Posts are Exhausted) ──
        if ($organicInterleaved->count() < $fetchPoolSize) {
            $needed = $fetchPoolSize - $organicInterleaved->count();
            $excludeIds = $organicInterleaved->pluck('id')->filter()->all();

            $discoveryQuery = (clone $baseQuery)
                ->where('is_boosted', false)
                ->when(! empty($excludeIds), fn ($q) => $q->whereNotIn('id', $excludeIds));

            $discoveryPosts = $discoveryQuery
                ->selectRaw('posts.*, (COALESCE(posts.views, 0) * 0.2 + COALESCE(posts.likes, 0) * 2 + COALESCE(posts.comments, 0) * 4 + COALESCE(posts.comment_external, 0) * 2) as ph_engagement_score')
                ->orderByDesc('ph_engagement_score')
                ->orderByDesc('posts.created_at')
                ->take($needed * 2)
                ->get();

            $tier2Interleaved = $this->seededAuthorInterleave($discoveryPosts, $seed);
            $organicInterleaved = $organicInterleaved->concat($tier2Interleaved);
        }

        // ── Fallback: Older Warm Network Posts (> 10 days) if still under target ──
        if ($organicInterleaved->count() < $targetCount && ! empty($warmNetworkIds)) {
            $excludeIds = $organicInterleaved->pluck('id')->filter()->all();
            $tenDaysAgo = now()->subDays(10);

            $olderWarm = (clone $baseQuery)
                ->where('is_boosted', false)
                ->whereIn('user_id', $warmNetworkIds)
                ->where('created_at', '<', $tenDaysAgo)
                ->when(! empty($excludeIds), fn ($q) => $q->whereNotIn('id', $excludeIds))
                ->latest('created_at')
                ->take($targetCount - $organicInterleaved->count())
                ->get();

            if ($olderWarm->isNotEmpty()) {
                $olderInterleaved = $this->seededAuthorInterleave($olderWarm, $seed);
                $organicInterleaved = $organicInterleaved->concat($olderInterleaved);
            }
        }

        // ── Interleave Dynamic Boosted Posts (5 to 8 Cadence) ──
        $isBoostEnabled = SystemSetting::isBoostEnabled();
        $maxBoostNeeded = $isBoostEnabled ? (int) ceil($targetCount / self::DYNAMIC_CADENCE_MIN) : 0;

        $boostedPosts = collect();
        if ($maxBoostNeeded > 0) {
            $boostedPosts = $this->getActiveBoostedPosts(
                count: $maxBoostNeeded,
                seed: $seed,
                excludePostIds: $organicInterleaved->pluck('id')->all(),
                withRelations: $withRelations
            );
        }

        $finalPosts = $this->interleave(
            organicPosts: $organicInterleaved,
            boostedPosts: $boostedPosts,
            targetCount: $targetCount,
            cadence: $cadence,
            seed: $seed
        );

        $hasMore = ($organicInterleaved->count() + $boostedPosts->count()) > $finalPosts->count();

        return [
            'posts' => $finalPosts,
            'hasMore' => $hasMore,
        ];
    }

    /**
     * Build 'Following' feed:
     * - ONLY posts from mutual connections (people I follow who follow me back).
     * - Author-interleaved to give equal creator prominence.
     * - Dynamic 5-8 boosted post cadence.
     */
    protected function buildMutualsFeed(
        Builder $baseQuery,
        ?string $userId,
        int $targetCount,
        ?int $seed = null,
        ?int $cadence = null,
        array $withRelations = ['user', 'images', 'video', 'activeBoost']
    ): array {
        if (! $userId) {
            return ['posts' => collect(), 'hasMore' => false];
        }

        $mutualIds = $this->resolveMutualIds($userId);

        if (empty($mutualIds)) {
            return ['posts' => collect(), 'hasMore' => false];
        }

        $fetchPoolSize = $targetCount * 2;

        $rawMutual = (clone $baseQuery)
            ->where('is_boosted', false)
            ->whereIn('user_id', $mutualIds)
            ->latest('created_at')
            ->take($fetchPoolSize)
            ->get();

        $organicInterleaved = $this->seededAuthorInterleave($rawMutual, $seed);

        $isBoostEnabled = SystemSetting::isBoostEnabled();
        $maxBoostNeeded = $isBoostEnabled ? (int) ceil($targetCount / self::DYNAMIC_CADENCE_MIN) : 0;

        $boostedPosts = collect();
        if ($maxBoostNeeded > 0) {
            $boostedPosts = $this->getActiveBoostedPosts(
                count: $maxBoostNeeded,
                seed: $seed,
                excludePostIds: $organicInterleaved->pluck('id')->all(),
                withRelations: $withRelations
            );
        }

        $finalPosts = $this->interleave(
            organicPosts: $organicInterleaved,
            boostedPosts: $boostedPosts,
            targetCount: $targetCount,
            cadence: $cadence,
            seed: $seed
        );

        $hasMore = ($rawMutual->count() + $boostedPosts->count()) > $finalPosts->count();

        return [
            'posts' => $finalPosts,
            'hasMore' => $hasMore,
        ];
    }

    /**
     * Resolve warm network user IDs for a given user (cached for 15 minutes).
     * Includes following, followers, past post likers, past post commenters, and self.
     *
     * @return array<string>
     */
    public function resolveWarmNetworkIds(?string $userId): array
    {
        if (! $userId) {
            return [];
        }

        return Cache::remember("user_warm_network:{$userId}", self::NETWORK_CACHE_TTL, function () use ($userId) {
            $followingIds = Follow::where('follower_id', $userId)->pluck('following_id');
            $followerIds = Follow::where('following_id', $userId)->pluck('follower_id');

            $likerIds = UserLike::where('poster_user_id', $userId)
                ->where('user_id', '!=', $userId)
                ->distinct()
                ->pluck('user_id');

            $commenterIds = UserComment::where('poster_user_id', $userId)
                ->where('user_id', '!=', $userId)
                ->distinct()
                ->pluck('user_id');

            return $followingIds
                ->concat($followerIds)
                ->concat($likerIds)
                ->concat($commenterIds)
                ->push($userId)
                ->filter()
                ->unique()
                ->values()
                ->all();
        });
    }

    /**
     * Resolve mutual connection IDs (people user follows who ALSO follow user back).
     *
     * @return array<string>
     */
    public function resolveMutualIds(?string $userId): array
    {
        if (! $userId) {
            return [];
        }

        return Cache::remember("user_mutuals:{$userId}", self::NETWORK_CACHE_TTL, function () use ($userId) {
            return DB::table('follows as f1')
                ->join('follows as f2', function ($join) {
                    $join->on('f1.following_id', '=', 'f2.follower_id')
                        ->on('f1.follower_id', '=', 'f2.following_id');
                })
                ->where('f1.follower_id', $userId)
                ->pluck('f1.following_id')
                ->unique()
                ->values()
                ->all();
        });
    }

    /**
     * Build an interleaved feed from an organic query and active boosted posts.
     *
     * @param  Builder  $organicQuery Base query for LIVE, non-hidden posts
     * @param  int  $targetCount Total posts desired for current pagination
     * @param  int|null  $seed Seed for deterministic random rotation across pages
     * @param  int  $cadence Number of organic posts before each boosted post
     * @param  array  $withRelations Relations to eager load
     * @return array{posts: Collection, hasMore: bool}
     */
    /**
     * Round-robin author-interleave posts with deterministic seeded creator shuffle.
     * Prevents prolific creators from dominating consecutive slots; elevates small creators.
     */
    public function seededAuthorInterleave(Collection $posts, ?int $seed = null, int $limit = 0): Collection
    {
        if ($posts->isEmpty()) {
            return collect();
        }

        $grouped = $posts->groupBy('user_id');
        $userIds = $grouped->keys()->all();

        if ($seed !== null && count($userIds) > 1) {
            $userIds = $this->shuffleWithSeed($userIds, $seed);
        }

        $interleaved = collect();
        $maxPostsPerUser = $grouped->map->count()->max();

        for ($i = 0; $i < $maxPostsPerUser; $i++) {
            foreach ($userIds as $uId) {
                $userPosts = $grouped->get($uId);
                if ($userPosts && isset($userPosts[$i])) {
                    $interleaved->push($userPosts[$i]);
                    if ($limit > 0 && $interleaved->count() >= $limit) {
                        break 2;
                    }
                }
            }
        }

        return $interleaved;
    }

    /**
     * Deterministic Fisher-Yates array shuffle using integer seed.
     */
    public function shuffleWithSeed(array $items, int $seed): array
    {
        $count = count($items);
        for ($i = $count - 1; $i > 0; $i--) {
            $hash = crc32($seed . '_perm_' . $i);
            $j = abs($hash) % ($i + 1);
            $temp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $temp;
        }

        return $items;
    }

    /**
     * Calculate next dynamic cadence interval between 5 and 8 posts deterministically.
     */
    public function getNextCadence(?int $seed, int $boostIndex): int
    {
        if ($seed !== null) {
            $hash = crc32($seed . '_boost_cadence_' . $boostIndex);
            return self::DYNAMIC_CADENCE_MIN + (abs($hash) % (self::DYNAMIC_CADENCE_MAX - self::DYNAMIC_CADENCE_MIN + 1));
        }

        return mt_rand(self::DYNAMIC_CADENCE_MIN, self::DYNAMIC_CADENCE_MAX);
    }

    /**
     * Build an interleaved feed from an organic query and active boosted posts.
     *
     * @param  Builder  $organicQuery Base query for LIVE, non-hidden posts
     * @param  int  $targetCount Total posts desired for current pagination
     * @param  int|null  $seed Seed for deterministic random rotation across pages
     * @param  int|null  $cadence Number of organic posts before each boosted post (null for dynamic 5-8)
     * @param  array  $withRelations Relations to eager load
     * @return array{posts: Collection, hasMore: bool}
     */
    public function buildFeed(
        Builder $organicQuery,
        int $targetCount,
        ?int $seed = null,
        ?int $cadence = self::DEFAULT_CADENCE,
        array $withRelations = ['user', 'images', 'video', 'activeBoost']
    ): array {
        $cadenceValue = ($cadence !== null && $cadence > 0) ? $cadence : null;
        $targetCount = max(1, $targetCount);

        $isBoostEnabled = SystemSetting::isBoostEnabled();

        // Calculate maximum boosted posts needed for this targetCount
        $effectiveCadence = $cadenceValue ?? self::DYNAMIC_CADENCE_MIN;
        $maxBoostNeeded = $isBoostEnabled ? (int) ceil($targetCount / ($effectiveCadence + 1)) : 0;

        // Step 1: Fetch organic posts (unboosted)
        $rawOrganic = (clone $organicQuery)
            ->where('is_boosted', false)
            ->latest('created_at')
            ->take($targetCount * 2)
            ->get();

        // Step 2: Author-interleave organic posts so a single user does not monopolize consecutive slots
        $organicInterleaved = $this->seededAuthorInterleave($rawOrganic, $seed, $targetCount * 2);

        // Step 3: Fetch boosted posts sampled across all active campaigns
        $boostedPosts = collect();
        if ($maxBoostNeeded > 0) {
            $boostedPosts = $this->getActiveBoostedPosts(
                count: $maxBoostNeeded,
                seed: $seed,
                excludePostIds: $organicInterleaved->pluck('id')->all(),
                withRelations: $withRelations
            );
        }

        // Step 4: Interleave boosted posts in between organic posts
        $finalPosts = $this->interleave(
            organicPosts: $organicInterleaved,
            boostedPosts: $boostedPosts,
            targetCount: $targetCount,
            cadence: $cadenceValue,
            seed: $seed
        );

        // Step 5: Check if there are more posts available
        $totalAvailableInBatch = $rawOrganic->count() + $boostedPosts->count();
        $hasMore = $totalAvailableInBatch > $finalPosts->count();

        return [
            'posts' => $finalPosts,
            'hasMore' => $hasMore,
        ];
    }

    /**
     * Efficiently select a sample of active boosted posts out of potentially tens of thousands.
     *
     * @param  int  $count Maximum posts to return
     * @param  int|null  $seed Seed for deterministic random rotation
     * @param  array  $excludePostIds Post IDs to exclude to prevent duplicate feed rendering
     * @param  array  $withRelations Relations to eager load on Post
     */
    public function getActiveBoostedPosts(
        int $count,
        ?int $seed = null,
        array $excludePostIds = [],
        array $withRelations = ['user', 'images', 'video', 'activeBoost']
    ): Collection {
        if (! SystemSetting::isBoostEnabled() || $count <= 0) {
            return collect();
        }

        $activePostIds = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function () {
            return PostBoost::query()
                ->where('status', 'active')
                ->where('remaining_clicks', '>', 0)
                ->where('platform_payhankey', true)
                ->pluck('post_id')
                ->all();
        });

        if (empty($activePostIds)) {
            return collect();
        }

        if (! empty($excludePostIds)) {
            $excludeFlip = array_flip($excludePostIds);
            $activePostIds = array_values(array_filter($activePostIds, fn ($id) => ! isset($excludeFlip[$id])));
        }

        if (empty($activePostIds)) {
            return collect();
        }

        // Deterministic hash-based selection: O(count) time complexity
        $selectedIds = $this->selectSample($activePostIds, $count, $seed);

        if (empty($selectedIds)) {
            return collect();
        }

        $query = Post::query()
            ->whereIn('id', $selectedIds)
            ->where('status', 'LIVE');

        if (! empty($withRelations)) {
            $query->with($withRelations);
        }

        if ($userId = auth()->id()) {
            $query->withExists([
                'likes as liked_by_me' => fn ($q) => $q->where('user_id', $userId),
                'bookmarks as is_bookmarked_by_me' => fn ($q) => $q->where('user_id', $userId),
            ]);
        }

        $posts = $query->get()->keyBy('id');

        // Maintain selection order so stable sampling is preserved
        return collect($selectedIds)
            ->map(fn ($id) => $posts->get($id))
            ->filter()
            ->values();
    }

    /**
     * Interleave boosted posts in between organic posts at a dynamic 5-8 cadence (or fixed cadence override).
     *
     * Example with dynamic cadence:
     * Organic 1..5, [Boosted 1],
     * Organic 6..13, [Boosted 2], ...
     */
    public function interleave(
        Collection $organicPosts,
        Collection $boostedPosts,
        int $targetCount,
        ?int $cadence = null,
        ?int $seed = null
    ): Collection {
        if ($boostedPosts->isEmpty()) {
            return $organicPosts->take($targetCount)->values();
        }

        if ($organicPosts->isEmpty()) {
            return $boostedPosts->take($targetCount)->values();
        }

        $result = collect();
        $boostQueue = $boostedPosts->values();
        $boostIndex = 0;
        $boostTotal = $boostQueue->count();
        $organicSinceLastBoost = 0;
        $nextCadence = ($cadence !== null && $cadence > 0)
            ? $cadence
            : $this->getNextCadence($seed, $boostIndex);

        foreach ($organicPosts as $organic) {
            $result->push($organic);
            $organicSinceLastBoost++;

            // Inject one boosted post after dynamic (or fixed) cadence
            if ($organicSinceLastBoost >= $nextCadence && $boostIndex < $boostTotal) {
                $result->push($boostQueue[$boostIndex]);
                $boostIndex++;
                $organicSinceLastBoost = 0;
                $nextCadence = ($cadence !== null && $cadence > 0)
                    ? $cadence
                    : $this->getNextCadence($seed, $boostIndex);
            }

            if ($result->count() >= $targetCount) {
                break;
            }
        }

        // If organic posts are fewer than targetCount, append remaining boosted posts up to targetCount
        while ($result->count() < $targetCount && $boostIndex < $boostTotal) {
            $result->push($boostQueue[$boostIndex]);
            $boostIndex++;
        }

        return $result->values();
    }

    /**
     * Select $count items from $ids deterministically using a seed.
     * O(count) runtime; no in-memory sorting of 50,000 items.
     *
     * @param  array<string>  $ids
     * @return array<string>
     */
    public function selectSample(array $ids, int $count, ?int $seed = null): array
    {
        $total = count($ids);
        if ($total <= $count) {
            return $ids;
        }

        $seedValue = $seed ?? crc32((string) (session()->getId() ?: 'guest_' . date('YmdH')));
        $selected = [];
        $used = [];

        for ($i = 0; $i < $count; $i++) {
            $hash = crc32($seedValue . '_' . $i);
            $idx = abs($hash) % $total;

            $attempts = 0;
            while (isset($used[$idx]) && $attempts < 30) {
                $idx = ($idx + 1) % $total;
                $attempts++;
            }

            $used[$idx] = true;
            $selected[] = $ids[$idx];
        }

        return $selected;
    }

    /**
     * Invalidate candidate pool cache whenever boost status changes.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Invalidate cached network graphs for a user.
     */
    public function clearUserCache(string $userId): void
    {
        Cache::forget("user_warm_network:{$userId}");
        Cache::forget("user_mutuals:{$userId}");
    }
}
