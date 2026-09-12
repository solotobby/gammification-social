<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostBoost;
use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TimelineFeedService
{
    /**
     * Default cadence: 1 boosted post after every 4 organic posts.
     */
    public const DEFAULT_CADENCE = 4;

    /**
     * Cache duration for active boost candidate post IDs (seconds).
     */
    public const CACHE_TTL_SECONDS = 30;

    public const CACHE_KEY = 'active_timeline_boost_post_ids';

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
    public function buildFeed(
        Builder $organicQuery,
        int $targetCount,
        ?int $seed = null,
        int $cadence = self::DEFAULT_CADENCE,
        array $withRelations = ['user', 'images', 'video', 'activeBoost']
    ): array {
        $cadence = max(1, $cadence);
        $targetCount = max(1, $targetCount);

        $isBoostEnabled = SystemSetting::isBoostEnabled();

        // Calculate maximum boosted posts needed for this targetCount:
        // e.g. targetCount = 20, cadence = 4 -> ceil(20 / 5) = 4 boosted posts
        $maxBoostNeeded = $isBoostEnabled ? (int) ceil($targetCount / ($cadence + 1)) : 0;

        // Step 1: Fetch organic posts (unboosted)
        $rawOrganic = (clone $organicQuery)
            ->where('is_boosted', false)
            ->latest('created_at')
            ->take($targetCount * 2)
            ->get();

        // Step 2: Author-interleave organic posts so a single user does not monopolize consecutive slots
        $grouped = $rawOrganic->groupBy('user_id');
        $organicInterleaved = collect();
        $index = 0;
        do {
            $added = 0;
            foreach ($grouped as $userPosts) {
                if (isset($userPosts[$index])) {
                    $organicInterleaved->push($userPosts[$index]);
                    $added++;
                }
            }
            $index++;
        } while ($added > 0 && $organicInterleaved->count() < $targetCount * 2);

        // Step 3: Fetch boosted posts sampled across all active campaigns (scalable to 50,000+ campaigns)
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
            cadence: $cadence
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

        // Fast cached candidate pool of active boost campaign post IDs.
        // Even with 50,000 active campaigns, an array of IDs in cache takes < 2MB
        // and eliminates full-table sorting or slow ORDER BY RAND().
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

        $posts = $query->get()->keyBy('id');

        // Maintain selection order so stable sampling is preserved
        return collect($selectedIds)
            ->map(fn ($id) => $posts->get($id))
            ->filter()
            ->values();
    }

    /**
     * Interleave boosted posts in between organic posts at a steady cadence.
     *
     * Example with cadence = 4:
     * Organic 1, Organic 2, Organic 3, Organic 4, [Boosted 1],
     * Organic 5, Organic 6, Organic 7, Organic 8, [Boosted 2], ...
     */
    public function interleave(
        Collection $organicPosts,
        Collection $boostedPosts,
        int $targetCount,
        int $cadence = self::DEFAULT_CADENCE
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

        foreach ($organicPosts as $organic) {
            $result->push($organic);
            $organicSinceLastBoost++;

            // Inject one boosted post after every $cadence organic posts
            if ($organicSinceLastBoost >= $cadence && $boostIndex < $boostTotal) {
                $result->push($boostQueue[$boostIndex]);
                $boostIndex++;
                $organicSinceLastBoost = 0;
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
}
