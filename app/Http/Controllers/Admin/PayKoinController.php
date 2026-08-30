<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminPayKoinService;
use App\Support\AdminDateRange;
use Illuminate\Http\Request;

class PayKoinController extends Controller
{
    public function __construct(private AdminPayKoinService $payKoin) {}

    public function index(Request $request)
    {
        $dateRange = AdminDateRange::fromRequest($request);
        $search = $request->string('q')->trim()->toString() ?: null;
        $type = $request->string('type')->trim()->toString() ?: null;
        $tab = $request->string('tab')->trim()->toString() ?: 'overview';

        if (! in_array($tab, ['overview', 'transactions', 'gifts', 'wallets'], true)) {
            $tab = 'overview';
        }

        return view('admin.paykoin.index', [
            'dateRange' => $dateRange,
            'tab' => $tab,
            'stats' => $this->payKoin->managementStats($dateRange),
            'transactions' => $tab === 'transactions'
                ? $this->payKoin->searchTransactions($dateRange, $search, $type)
                : null,
            'gifts' => $tab === 'gifts'
                ? $this->payKoin->searchGifts($dateRange, $search)
                : null,
            'topWallets' => $this->payKoin->topWallets(),
            'transactionTypes' => $this->payKoin->transactionTypes(),
            'search' => $search ?? '',
            'type' => $type ?? '',
            'rates' => config('payhankey.paykoin.rates', []),
            'artifactLabel' => fn (string $id) => $this->payKoin->artifactLabel($id),
        ]);
    }
}
