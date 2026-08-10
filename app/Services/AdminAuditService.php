<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuditService
{
    public function log(
        string $action,
        ?Model $subject = null,
        array $metadata = [],
        ?Request $request = null
    ): AdminAuditLog {
        $request ??= request();

        return AdminAuditLog::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata ?: null,
            'ip' => $request->ip(),
        ]);
    }
}
