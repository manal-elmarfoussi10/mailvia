<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $companyId = session('company_id') ?: auth()->user()->companies()->first()?->id;

        $alerts = Alert::where('company_id', $companyId)
            ->latest()
            ->paginate(50);

        return view('alerts.index', compact('alerts'));
    }

    public function resolve(Alert $alert)
    {
        $this->authorize('update', $alert->company);

        $alert->update(['resolved_at' => now()]);

        return back()->with('success', 'Alert resolved.');
    }
}
