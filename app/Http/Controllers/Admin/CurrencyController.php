<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    public function __construct(private AdminAuditService $audit) {}

    public function index()
    {
        $currencies = Currency::query()
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.currency.index', compact('currencies'));
    }

    public function changeStatus(Currency $currency)
    {
        $currency->is_active = ! $currency->is_active;
        $currency->save();

        $this->audit->log('currency.status_changed', $currency, [
            'is_active' => $currency->is_active,
        ]);

        return redirect()->back()->with('success', 'Currency status updated successfully.');
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'base_rate' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            $currency->update([
                'base_rate' => $request->base_rate,
            ]);

            DB::commit();

            $this->audit->log('currency.rate_updated', $currency, [
                'base_rate' => $currency->base_rate,
            ]);

            return redirect()
                ->back()
                ->with('success', $currency->code . ' base rate updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Currency update failed', [
                'currency_id' => $currency->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to update currency base rate.');
        }
    }
}
