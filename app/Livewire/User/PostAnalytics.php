<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\PostBoost;
use App\Models\PostBoostClick;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;

class PostAnalytics extends Component
{
    public Post $post;

    #[Url]
    public string $tab = 'monetization';

    public function mount(string $id): void
    {
        $this->post = Post::query()->with('user')->findOrFail($id);

        if ($this->post->user_id !== auth()->id()) {
            abort(403);
        }

        if (request()->query('tab') === 'boost') {
            $this->tab = 'boost';
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['boost', 'monetization'], true)) {
            $this->tab = $tab;
        }
    }

    /**
     * Pause or resume the active boost campaign.
     */
    public function toggleBoostStatus(): void
    {
        $latestBoost = PostBoost::where('post_id', $this->post->id)->latest()->first();

        if (! $latestBoost || $latestBoost->remaining_clicks <= 0) {
            session()->flash('boost_error', 'No active campaign available to update.');
            return;
        }

        if ($latestBoost->status === 'active') {
            $latestBoost->status = 'paused';
            $latestBoost->save();

            // When paused, remove feed priority
            $this->post->update(['is_boosted' => false]);
            session()->flash('boost_success', 'Boost delivery paused. Remaining guaranteed clicks are preserved.');
        } else {
            $latestBoost->status = 'active';
            $latestBoost->save();

            // When resumed, restore feed priority
            $this->post->update(['is_boosted' => true]);
            session()->flash('boost_success', 'Boost delivery resumed! Your post is actively prioritized in feeds.');
        }

        app(\App\Services\TimelineFeedService::class)->clearCache();
    }

    public function render()
    {
        $post = $this->post;

        // --- Creator Monetization Metrics ---
        $monetizedViews = (int) ($post->views ?? 0);
        $unmonetizedViews = (int) ($post->views_external ?? 0);
        $totalViews = sumCounter($monetizedViews, $unmonetizedViews);

        $monetizedLikes = (int) ($post->likes ?? 0);
        $monetizedComments = (int) ($post->comments ?? 0);
        $unmonetizedComments = (int) ($post->comment_external ?? 0);
        $totalComments = sumCounter($monetizedComments, $unmonetizedComments);

        $viewsRevenue = (float) viewsAmountCalculator($post->id);
        $likesRevenue = (float) likesAmountCalculator($post->id);
        $commentsRevenue = (float) commentsAmountCalculator($post->id);
        $totalEarnings = $viewsRevenue + $likesRevenue + $commentsRevenue;

        $monetizedEngagement = $monetizedViews + $monetizedLikes + $monetizedComments;

        // --- Boost Campaign Backend Data ---
        $latestBoost = PostBoost::where('post_id', $post->id)->latest()->first();
        $hasBoost = (bool) $latestBoost;

        $boostDelivered = $hasBoost ? (int) $latestBoost->delivered_clicks : 0;
        $boostTotal = $hasBoost ? (int) $latestBoost->total_clicks : 0;
        $boostRemaining = $hasBoost ? (int) $latestBoost->remaining_clicks : 0;
        $boostPct = ($boostTotal > 0) ? min(100, (int) round(($boostDelivered / $boostTotal) * 100)) : 0;
        $boostRate = $hasBoost ? (int) $latestBoost->rate_pk : 3;
        $boostStatus = $hasBoost ? $latestBoost->status : 'none';

        $boostClicks = collect();
        $partnerClicksCount = 0;
        $payhankeyClicksCount = 0;
        $bookmarksCount = PostBookmark::where('post_id', $post->id)->count();

        $topLocations = collect();
        $deviceBreakdown = [
            'Mobile' => ['count' => 0, 'pct' => 0],
            'Desktop' => ['count' => 0, 'pct' => 0],
            'Tablet' => ['count' => 0, 'pct' => 0],
        ];
        $hourlyBars = collect();

        // Estimated impressions based on feed views + partner impressions
        $adImpressions = max($monetizedViews, $boostDelivered > 0 ? (int) ($boostDelivered * 35) : $monetizedViews);
        $ctr = ($adImpressions > 0 && $boostDelivered > 0)
            ? round(($boostDelivered / $adImpressions) * 100, 1)
            : 0.0;

        if ($hasBoost) {
            $boostClicks = PostBoostClick::where('post_boost_id', $latestBoost->id)
                ->latest()
                ->take(20)
                ->get();

            $partnerClicksCount = PostBoostClick::where('post_boost_id', $latestBoost->id)
                ->where('platform', 'partner')
                ->count();

            $payhankeyClicksCount = PostBoostClick::where('post_boost_id', $latestBoost->id)
                ->where('platform', 'payhankey')
                ->count();

            if ($boostDelivered > 0) {
                // Real Geographic Telemetry Grouping
                $topLocations = PostBoostClick::where('post_boost_id', $latestBoost->id)
                    ->select('country', 'city', DB::raw('count(*) as total'))
                    ->groupBy('country', 'city')
                    ->orderByDesc('total')
                    ->take(5)
                    ->get()
                    ->map(function ($row) use ($boostDelivered) {
                        $label = trim(($row->city ? $row->city . ', ' : '') . ($row->country ?: 'Global'));
                        return [
                            'label' => $label ?: 'Global Visit',
                            'count' => (int) $row->total,
                            'pct' => (int) round(($row->total / $boostDelivered) * 100),
                        ];
                    });

                // Real Device Breakdown
                $rawDevices = PostBoostClick::where('post_boost_id', $latestBoost->id)
                    ->select('device', DB::raw('count(*) as total'))
                    ->groupBy('device')
                    ->pluck('total', 'device')
                    ->toArray();

                foreach (['Mobile', 'Desktop', 'Tablet'] as $deviceType) {
                    $c = (int) ($rawDevices[$deviceType] ?? 0);
                    $deviceBreakdown[$deviceType] = [
                        'count' => $c,
                        'pct' => (int) round(($c / $boostDelivered) * 100),
                    ];
                }

                // Real Hourly Click Delivery Curve (last hours)
                $hourlyClicks = PostBoostClick::where('post_boost_id', $latestBoost->id)
                    ->select(DB::raw('DATE_FORMAT(created_at, "%H:00") as hour_label'), DB::raw('count(*) as total'))
                    ->groupBy('hour_label')
                    ->orderBy('hour_label')
                    ->take(6)
                    ->get();

                $maxHourCount = max(1, (int) $hourlyClicks->max('total'));
                $hourlyBars = $hourlyClicks->map(function ($ch) use ($maxHourCount) {
                    return [
                        'label' => $ch->hour_label,
                        'count' => (int) $ch->total,
                        'height' => min(100, max(20, (int) round(($ch->total / $maxHourCount) * 100))),
                    ];
                });
            }
        }

        $feedCtr = ($monetizedViews > 0 && $payhankeyClicksCount > 0)
            ? round(($payhankeyClicksCount / $monetizedViews) * 100, 2)
            : 0.0;

        return view('livewire.user.post-analytics', [
            'postExcerpt' => Str::limit(plainPostText($post->content ?? ''), 160),
            'monetizedViews' => $monetizedViews,
            'unmonetizedViews' => $unmonetizedViews,
            'totalViews' => $totalViews,
            'monetizedLikes' => $monetizedLikes,
            'monetizedComments' => $monetizedComments,
            'unmonetizedComments' => $unmonetizedComments,
            'totalComments' => $totalComments,
            'viewsRevenue' => $viewsRevenue,
            'likesRevenue' => $likesRevenue,
            'commentsRevenue' => $commentsRevenue,
            'totalEarnings' => $totalEarnings,
            'monetizedEngagement' => $monetizedEngagement,
            'currency' => getCurrencyCode(),
            'isMonetized' => (bool) ($post->is_monetized ?? true),
            'monetizationNote' => $post->monetization_note,
            'canMonetize' => $post->canMonetize(),
            // Boost analytics
            'hasBoost' => $hasBoost,
            'latestBoost' => $latestBoost,
            'boostDelivered' => $boostDelivered,
            'boostTotal' => $boostTotal,
            'boostRemaining' => $boostRemaining,
            'boostPct' => $boostPct,
            'boostRate' => $boostRate,
            'boostStatus' => $boostStatus,
            'adImpressions' => $adImpressions,
            'ctr' => $ctr,
            'feedCtr' => $feedCtr,
            'bookmarksCount' => $bookmarksCount,
            'boostClicks' => $boostClicks,
            'partnerClicksCount' => $partnerClicksCount,
            'payhankeyClicksCount' => $payhankeyClicksCount,
            'topLocations' => $topLocations,
            'deviceBreakdown' => $deviceBreakdown,
            'hourlyBars' => $hourlyBars,
        ]);
    }
}

