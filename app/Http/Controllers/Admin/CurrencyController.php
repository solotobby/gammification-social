<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

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
