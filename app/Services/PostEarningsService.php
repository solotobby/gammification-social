<?php

namespace App\Services;

use App\Models\UserComment;
use App\Models\UserLike;
use App\Models\UserView;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PostEarningsService
{
    /**
     * Batch-compute 30-day estimated earnings for many posts in three queries,
     * caching per-post results for 5 minutes.
     *
     * @return array<string, float> keyed by post id
     */
    public function forPosts(Collection $postIds, ?string $currency = null): array
    {
        $postIds = $postIds->filter()->unique()->values();

        if ($postIds->isEmpty()) {
            return [];
        }

        $currency = strtoupper((string) ($currency ?? auth()->user()?->wallet?->currency ?? userBaseCurrency() ?? 'USD'));
        $result = [];
        $uncachedIds = collect();

        foreach ($postIds as $postId) {
            $cacheKey = "post_earnings:{$postId}:{$currency}";
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                $result[$postId] = (float) $cached;
            } else {
                $uncachedIds->push($postId);
            }
        }

        if ($uncachedIds->isEmpty()) {
            return $result;
        }

        $since = now()->subDays(30);

        $views = UserView::query()
            ->whereIn('post_id', $uncachedIds)
            ->where('created_at', '>=', $since)
            ->selectRaw('post_id, COALESCE(SUM(amount), 0) as total')
            ->groupBy('post_id')
            ->pluck('total', 'post_id');

        $likes = UserLike::query()
            ->whereIn('post_id', $uncachedIds)
            ->where('created_at', '>=', $since)
            ->selectRaw('post_id, COALESCE(SUM(amount), 0) as total')
            ->groupBy('post_id')
            ->pluck('total', 'post_id');

        $comments = UserComment::query()
            ->whereIn('post_id', $uncachedIds)
            ->where('created_at', '>=', $since)
            ->selectRaw('post_id, COALESCE(SUM(amount), 0) as total')
            ->groupBy('post_id')
            ->pluck('total', 'post_id');

        foreach ($uncachedIds as $postId) {
            $raw = (float) ($views[$postId] ?? 0)
                + (float) ($likes[$postId] ?? 0)
                + (float) ($comments[$postId] ?? 0);

            $converted = (float) round(convertToBaseCurrency($raw, $currency), 5);
            $result[$postId] = $converted;

            Cache::put("post_earnings:{$postId}:{$currency}", $converted, 300);
        }

        return $result;
    }
}
