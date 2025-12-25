<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id') ?: auth()->user()->companies()->first()?->id;

        $logs = AuditLog::where('company_id', $companyId)
            ->with(['user', 'auditable'])
            ->latest()
            ->paginate(50);

        return view('audit_logs.index', compact('logs'));
    }
}
