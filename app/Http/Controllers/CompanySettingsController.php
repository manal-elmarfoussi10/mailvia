<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->companies()->first();
        $providers = $company->providers;
        $senders = $company->senders;
        return view('settings.index', compact('company', 'providers', 'senders'));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->companies()->first();
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'settings.default_provider_id' => 'nullable|exists:providers,id',
            'settings.default_sender_id' => 'nullable|exists:senders,id',
            'settings.hourly_limit' => 'nullable|integer|min:0',
            'settings.daily_limit' => 'nullable|integer|min:0',
            'settings.tracking_enabled' => 'boolean',
            'settings.branding_footer' => 'nullable|string',
        ]);

        $settings = array_merge($company->settings ?? [], $data['settings'] ?? []);
        
        // Ensure tracking_enabled is handled since checkbox might be missing if unchecked
        $settings['tracking_enabled'] = $request->has('settings.tracking_enabled');

        $company->update([
            'name' => $data['name'],
            'settings' => $settings,
        ]);

        return back()->with('success', 'Global settings updated successfully.');
    }
}
