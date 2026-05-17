<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::
        // orderBy('is_active', 'desc')
            orderBy('name', 'asc')
            ->get();
        return view('admin.currency.index', compact('currencies'));
    }

    public function changeStatus($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->is_active = !$currency->is_active;
        $currency->save();

        return redirect()->back()->with('success', 'Currency status updated successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'base_rate' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();

        try {

            $currency = Currency::findOrFail($id);

            $currency->update([
                'base_rate' => $request->base_rate,
            ]);

            DB::commit();

            // Log::info('Currency base rate updated', [
            //     'currency_id' => $currency->id,
            //     'currency_code' => $currency->code,
            //     'base_rate' => $currency->base_rate,
            //     'updated_by' => auth()->id(),
            // ]);

            return redirect()
                ->back()
                ->with('success', $currency->code . ' base rate updated successfully.');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Currency update failed', [
                'currency_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to update currency base rate.');
        }
    }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'country' => 'required|unique:currencies,country',
    //         'name' => 'required|unique:currencies,name',
    //         'code' => 'required|unique:currencies,code',
    //         'base_rate' => 'required|numeric|min:0',
    //     ]);

    //     Currency::create([
    //         'country' => $request->country,
    //         'name' => $request->name,
    //         'code' => $request->code,
    //         'base_rate' => $request->base_rate,
    //     ]);

    //     return redirect()->back()->with('success', 'Currency added successfully.');
    // }
}
