<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AdminAuditLog::query()
            ->with('admin:id,name,email')
            ->when($request->filled('action'), fn ($q) => $q->where('action', 'like', '%' . $request->action . '%'))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $actions = AdminAuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.audit.index', compact('logs', 'actions'));
    }
}
