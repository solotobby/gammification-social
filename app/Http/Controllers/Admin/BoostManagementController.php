<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostBoost;
use App\Models\PostBoostClick;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoostManagementController extends Controller
{
    /**
     * Display the Boost Management dashboard and list of boosted posts.
     */
    public function index(Request $request): View
    {
        $isBoostEnabled = SystemSetting::isBoostEnabled();
        $tab = $request->string('tab')->trim()->toString() ?: 'all';
        $search = $request->string('q')->trim()->toString();

        if (! in_array($tab, ['all', 'active', 'paused', 'completed', 'cancelled'], true)) {
            $tab = 'all';
        }

        // Metrics computation
        $totalCampaigns = PostBoost::count();
        $activeCampaigns = PostBoost::where('status', 'active')->count();
        $pausedCampaigns = PostBoost::where('status', 'paused')->count();
        $completedCampaigns = PostBoost::where('status', 'completed')->count();
        $cancelledCampaigns = PostBoost::where('status', 'cancelled')->count();

        $totalClicksBooked = (int) PostBoost::sum('total_clicks');
        $totalClicksDelivered = (int) PostBoost::sum('delivered_clicks');
        $totalPkRevenue = (int) PostBoost::sum('pk_cost');
        $totalNairaRevenue = $totalPkRevenue * 10;

        $totalLoggedClicks = PostBoostClick::count();
        $payhankeyClicks = PostBoostClick::where('platform', 'payhankey')->count();
        $partnerClicks = PostBoostClick::where('platform', 'partner')->count();

        // Query campaigns
        $query = PostBoost::query()->with(['user', 'post', 'post.images']);

        if ($tab !== 'all') {
            $query->where('status', $tab);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('target_url', 'like', "%{$search}%")
                    ->orWhere('cta', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('post', function ($pq) use ($search) {
                        $pq->where('content', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%");
                    });
            });
        }

        $boosts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.boosts.index', [
            'isBoostEnabled' => $isBoostEnabled,
            'tab' => $tab,
            'search' => $search,
            'boosts' => $boosts,
            'metrics' => [
                'totalCampaigns' => $totalCampaigns,
                'activeCampaigns' => $activeCampaigns,
                'pausedCampaigns' => $pausedCampaigns,
                'completedCampaigns' => $completedCampaigns,
                'cancelledCampaigns' => $cancelledCampaigns,
                'totalClicksBooked' => $totalClicksBooked,
                'totalClicksDelivered' => $totalClicksDelivered,
                'deliveryRate' => $totalClicksBooked > 0 ? round(($totalClicksDelivered / $totalClicksBooked) * 100, 1) : 0,
                'totalPkRevenue' => $totalPkRevenue,
                'totalNairaRevenue' => $totalNairaRevenue,
                'totalLoggedClicks' => $totalLoggedClicks,
                'payhankeyClicks' => $payhankeyClicks,
                'partnerClicks' => $partnerClicks,
            ],
        ]);
    }

    /**
     * Toggle the global Post Boost feature ON or OFF.
     */
    public function toggleSystem(Request $request): RedirectResponse
    {
        if ($request->has('state')) {
            $enable = $request->boolean('state');
        } else {
            $enable = ! SystemSetting::isBoostEnabled();
        }

        if ($enable) {
            SystemSetting::enableBoost();
            $msg = '🚀 Post Boost system has been TURNED ON. Users can now boost posts and view analytics from the timeline.';
        } else {
            SystemSetting::disableBoost();
            $msg = '⏸️ Post Boost system has been TURNED OFF. The boost buttons and analytics links are now hidden on the timeline.';
        }

        app(\App\Services\TimelineFeedService::class)->clearCache();

        return back()->with('success', $msg);
    }

    /**
     * Update an individual campaign's status (active, paused, cancelled, completed).
     */
    public function updateStatus(Request $request, PostBoost $boost): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,paused,completed,cancelled',
        ]);

        $newStatus = $validated['status'];
        $boost->status = $newStatus;
        $boost->save();

        if ($boost->post) {
            $boost->post->update([
                'is_boosted' => ($newStatus === 'active'),
            ]);
        }

        app(\App\Services\TimelineFeedService::class)->clearCache();

        return back()->with('success', "Campaign #{$boost->id} status successfully updated to " . ucfirst($newStatus) . '.');
    }
}
