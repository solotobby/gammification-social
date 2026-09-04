<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminFlutterwaveService;
use App\Support\AdminDateRange;
use Illuminate\Http\Request;

class FlutterwaveController extends Controller
{
    public function __construct(private AdminFlutterwaveService $flutterwave) {}

    public function index(Request $request)
    {
        $dateRange = AdminDateRange::fromRequest($request);
        $tab = $request->string('tab')->trim()->toString() ?: 'overview';
        $allowedTabs = ['overview', 'transactions', 'subscriptions', 'wallets'];

        if (! in_array($tab, $allowedTabs, true)) {
            $tab = 'overview';
        }

        $status = $request->string('status')->trim()->toString() ?: null;
        $type = $request->string('type')->trim()->toString() ?: null;
        $flow = $request->string('flow')->trim()->toString() ?: null;
        $currency = $request->string('currency')->trim()->toString() ?: null;
        $billingType = $request->string('billing')->trim()->toString() ?: null;
        $search = $request->string('q')->trim()->toString() ?: null;
        $levelSearch = $request->string('lq')->trim()->toString() ?: null;
        $levelPayment = $request->string('payment')->trim()->toString() ?: null;
        $levelStatus = $request->string('lstatus')->trim()->toString() ?: null;
        $filterOptions = $this->flutterwave->transactionFilterOptions();

        if ($levelPayment && ! in_array($levelPayment, ['with', 'without'], true)) {
            $levelPayment = null;
        }

        $balances = $this->flutterwave->fetchBalances();
        $stats = $this->flutterwave->flowStats($dateRange);
        $transfers = $tab === 'overview'
            ? $this->flutterwave->fetchTransfers($dateRange)
            : ['ok' => true, 'error' => null, 'items' => collect(), 'totalAmount' => 0.0, 'count' => 0];

        return view('admin.flutterwave.index', [
            'dateRange' => $dateRange,
            'tab' => $tab,
            'balances' => $balances,
            'stats' => $stats,
            'transfers' => $transfers,
            'transactions' => match ($tab) {
                'transactions' => $this->flutterwave->transactionHistory(
                    $dateRange,
                    $status,
                    $type,
                    $search,
                    $flow,
                    $currency,
                ),
                'overview' => $this->flutterwave->recentTransactions($dateRange, 10),
                default => null,
            },
            'subscriptions' => $tab === 'subscriptions'
                ? $this->flutterwave->subscriptionHistory($dateRange, $status, $search, $billingType)
                : null,
            'levelPlans' => $tab === 'subscriptions'
                ? $this->flutterwave->levelSubscriptionPlans($dateRange, $levelSearch, $levelStatus, $levelPayment)
                : null,
            'walletTotals' => $tab === 'wallets'
                ? $this->flutterwave->platformWalletTotals()
                : collect(),
            'statusLabels' => $this->flutterwave->statusLabels(),
            'subscriptionStatusLabels' => $this->flutterwave->subscriptionStatusLabels(),
            'typeLabels' => $this->flutterwave->typeLabels(),
            'filterOptions' => $filterOptions,
            'status' => $status ?? '',
            'type' => $type ?? '',
            'flow' => $flow ?? '',
            'currency' => $currency ?? '',
            'billingType' => $billingType ?? '',
            'search' => $search ?? '',
            'levelSearch' => $levelSearch ?? '',
            'levelPayment' => $levelPayment ?? '',
            'levelStatus' => $levelStatus ?? '',
        ]);
    }

    public function showSubscription(string $kind, string $id)
    {
        $detail = match ($kind) {
            'community' => $this->flutterwave->communitySubscriptionDetail($id),
            'level' => $this->flutterwave->levelPlanDetail($id),
            default => abort(404),
        };

        return view('admin.flutterwave.subscription', $detail);
    }
}
