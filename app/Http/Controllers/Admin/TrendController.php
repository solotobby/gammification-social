<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trend;
use Illuminate\Http\Request;

class TrendController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('admin');
    }


    public function index()
    {
        $trends = Trend::all();
        return view('admin.trends.index', compact('trends'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $trend = Trend::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'active',
        ]);

        return redirect()->route('trend.management')->with('success', 'Trend created successfully.');
     }

     public function toggleStatus($id)
    {
        
       $trend = Trend::findOrFail($id);
        $trend->status = $trend->status === 'active' ? 'inactive' : 'active';
        $trend->save();
        return redirect()->route('trend.management')->with('success', 'Trend status updated successfully.');
    }
}
