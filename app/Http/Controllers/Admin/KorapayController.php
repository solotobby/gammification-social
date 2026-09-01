<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminKorapayService;
use App\Support\AdminDateRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KorapayController extends Controller
{
    public function __construct(private AdminKorapayService $korapay) {}

    public function index(Request $request)
    {
        $dateRange = AdminDateRange::fromRequest($request);
        $status = $request->string('status')->trim()->toString() ?: null;
        $balances = $this->korapay->fetchBalances();

        return view('admin.korapay.index', [
            'dateRange' => $dateRange,
            'balances' => $balances,
            'stats' => $this->korapay->fundingStats($dateRange),
            'deposits' => $this->korapay->fundingHistory($dateRange, $status),
            'statusLabels' => $this->korapay->statusLabels(),
            'status' => $status ?? '',
        ]);
    }

    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
            'note' => ['nullable', 'string', 'max:255'],
            'validationCode' => ['required', 'string'],
        ]);

        if ($validated['validationCode'] !== config('services.env.validation_code')) {
            return back()->withInput()->with('error', 'Invalid validation code.');
        }

        try {
            $checkoutUrl = $this->korapay->initiateDeposit(
                Auth::user(),
                (float) $validated['amount'],
                $validated['note'] ?? null,
            );

            return redirect()->away($checkoutUrl);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function verify(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (! $reference) {
            return redirect()->route('admin.korapay.index')->with('error', 'Invalid payment reference.');
        }

        try {
            $result = $this->korapay->acknowledgeDepositReturn($reference);

            $flashKey = match ($result['status']) {
                'success' => 'success',
                'failed' => 'error',
                default => 'info',
            };

            return redirect()->route('admin.korapay.index')->with($flashKey, $result['message']);
        } catch (\Throwable $e) {
            return redirect()->route('admin.korapay.index')->with('error', $e->getMessage());
        }
    }
}
