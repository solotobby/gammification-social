<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawals;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(private AdminAuditService $audit) {}

    public function withdrawalList(Request $request)
    {
        $status = $request->query('status');
        $walletType = $request->query('wallet');

        $lists = Withdrawals::query()
            ->with(['user:id,name', 'withdrawalMethod'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($walletType, fn ($q) => $q->where('wallet_type', $walletType))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $statuses = Withdrawals::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('admin.withdrawal.list', compact('lists', 'statuses', 'status', 'walletType'));
    }

    public function withdrawalListUpdate(Withdrawals $withdrawal)
    {
        $update = $withdrawal;
        $update->status = 'Paid';
        $update->save();

        $method = str_replace('_', ' ', $update->method);

        Transaction::create([
            'user_id' => $update->user_id,
            'ref' => time(),
            'amount' => $update->amount,
            'currency' => 'USD',
            'status' => 'successful',
            'type' => 'withdrawals',
            'action' => 'Debit',
            'description' => ucfirst($update->wallet_type) . ' withdrawal via ' . ucwords($method),
            'meta' => null,
            'customer' => null,
        ]);

        $this->audit->log('withdrawal.marked_paid', $update, [
            'amount' => $update->amount,
            'user_id' => $update->user_id,
        ]);

        return back()->with('success', 'Withdrawal updated.');
    }
}
