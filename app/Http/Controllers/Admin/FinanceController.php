<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminFinanceService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function __construct(private AdminFinanceService $finance) {}

    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString() ?: null;
        $type = $request->string('type')->trim()->toString() ?: null;
        $status = $request->string('status')->trim()->toString() ?: null;
        $action = $request->string('action')->trim()->toString() ?: null;

        return view('admin.finance.index', [
            'stats' => $this->finance->dashboardStats(),
            'transactions' => $this->finance->searchLedger($search, $type, $status, $action),
            'anomalies' => $this->finance->anomalousWallets(),
            'reconciliation' => $this->finance->reconciliation(),
            'transactionTypes' => $this->finance->transactionTypes(),
            'search' => $search ?? '',
            'type' => $type ?? '',
            'status' => $status ?? '',
            'action' => $action ?? '',
        ]);
    }
}
