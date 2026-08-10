<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trend;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;

class TrendController extends Controller
{
    public function __construct(private AdminAuditService $audit) {}

    public function index()
    {
        $trends = Trend::query()->latest()->get();

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

        $this->audit->log('trend.created', $trend);

        return redirect()->route('admin.trends.index')->with('success', 'Trend created successfully.');
    }

    public function toggleStatus(Trend $trend)
    {
        $trend->status = $trend->status === 'active' ? 'inactive' : 'active';
        $trend->save();

        $this->audit->log('trend.status_toggled', $trend, [
            'status' => $trend->status,
        ]);

        return redirect()->route('admin.trends.index')->with('success', 'Trend status updated successfully.');
    }
}
