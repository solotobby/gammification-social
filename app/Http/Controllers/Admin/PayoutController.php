<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\Wallet;
use App\Models\WithdrawalMethod;
use App\Services\Admin\AdminPayoutService;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function __construct(protected AdminPayoutService $payouts) {}

    public function index(string $level)
    {
        $lastMonth = now()->subMonth()->format('Y-m');
        $levelTabs = ['Influencer', 'Creator', 'Basic'];

        if (! in_array($level, $levelTabs, true)) {
            return view('admin.payouts.index', [
                'status' => 'error',
                'message' => 'Invalid payout level selected.',
                'level' => $level,
                'lastmonth' => $lastMonth,
                'levelTabs' => $levelTabs,
            ]);
        }

        $result = $level === 'Basic'
            ? $this->payouts->processBasic($lastMonth)
            : $this->payouts->processPremium($level, $lastMonth);

        if (isset($result['error'])) {
            return view('admin.payouts.index', [
                'status' => 'error',
                'message' => $result['error'],
                'level' => $level,
                'lastmonth' => $lastMonth,
                'levelTabs' => $levelTabs,
            ]);
        }

        return view('admin.payouts.index', [
            'status' => 'success',
            'payouts' => $result['userEngagement'],
            'totalEngagement' => $result['totalEngagement'],
            'revenue' => $result['revenue'],
            'levelPool' => $result['levelPool'],
            'poolLabel' => $result['poolLabel'] ?? 'Level pool',
            'memberCount' => $result['memberCount'],
            'componentAnalytics' => $result['componentAnalytics'] ?? [],
            'level' => $level,
            'lastmonth' => $lastMonth,
            'levelTabs' => $levelTabs,
        ]);
    }

    public function queuePayout(string $engagementStat)
    {
        try {
            $this->payouts->queuePayout($engagementStat);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.payouts.show', $engagementStat);
    }

    public function viewPayoutInformation(string $engagementStat)
    {
        $payout = Payout::query()
            ->where('engagement_monthly_stats_id', $engagementStat)
            ->firstOrFail();

        return view('admin.payouts.show', [
            'payout' => $payout->load('user'),
            'withdrawals' => WithdrawalMethod::query()->where('user_id', $payout->user_id)->first(),
            'wallet' => Wallet::query()->where('user_id', $payout->user_id)->first(),
        ]);
    }

    public function updatePayoutStatus(Payout $payout)
    {
        $this->payouts->markPayoutPaid($payout->id);

        return back()->with('success', 'Payment updated.');
    }

    public function storePayoutComponent(Request $request)
    {
        $validated = $request->validate([
            'engagement_stat_id' => 'required|uuid|exists:engagement_monthly_stats,id',
            'type' => 'required|in:revenue,bonus',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
            'validationCode' => 'required|string',
        ]);

        if ($validated['validationCode'] !== config('services.env.validation_code')) {
            return back()->withInput()->with('error', 'Invalid validation code.');
        }

        try {
            $this->payouts->addComponent(
                $validated['engagement_stat_id'],
                $validated['type'],
                (float) $validated['amount'],
                $validated['note'] ?? null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', ucfirst($validated['type']).' payout added.');
    }

    public function updatePayoutComponent(Request $request, string $component)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
            'validationCode' => 'required|string',
        ]);

        if ($validated['validationCode'] !== config('services.env.validation_code')) {
            return back()->withInput()->with('error', 'Invalid validation code.');
        }

        try {
            $this->payouts->updateComponent($component, (float) $validated['amount'], $validated['note'] ?? null);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payout component updated.');
    }

    public function destroyPayoutComponent(Request $request, string $component)
    {
        $validated = $request->validate([
            'validationCode' => 'required|string',
        ]);

        if ($validated['validationCode'] !== config('services.env.validation_code')) {
            return back()->with('error', 'Invalid validation code.');
        }

        try {
            $this->payouts->deleteComponent($component);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payout component removed.');
    }

    public function updateEngagementPayout(Request $request, string $engagementStat)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|in:NGN,USD',
            'validationCode' => 'required|string',
        ]);

        if ($validated['validationCode'] !== config('services.env.validation_code')) {
            return back()->withInput()->with('error', 'Invalid validation code.');
        }

        try {
            $this->payouts->updateEngagementPayoutAmount(
                $engagementStat,
                (float) $validated['amount'],
                $validated['currency'] ?? 'NGN'
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Engagement payout amount updated.');
    }

    public function fundTransfer(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'payout_id' => 'required|uuid|exists:payouts,id',
            'bank_code' => 'required|string|max:20',
            'validationCode' => 'required|string',
        ]);

        if ($validated['validationCode'] !== config('services.env.validation_code')) {
            return response()->json(['status' => 'error', 'message' => 'Invalid validation code'], 422);
        }

        try {
            $response = $this->payouts->fundTransfer($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Fund transfer initiated successfully.',
                'data' => $response,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Transfer failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
