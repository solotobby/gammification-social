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
            'level' => $level,
            'lastmonth' => $lastMonth,
            'levelTabs' => $levelTabs,
        ]);
    }

    public function queuePayout(string $engagementStat)
    {
        $this->payouts->queuePayout($engagementStat);

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
