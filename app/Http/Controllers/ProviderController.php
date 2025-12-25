<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ProviderController extends Controller
{
    public function index()
    {
        // Companies and Providers are scoped by Company via our logic?
        // We really should use a scope or just access via relation to be safe.
        // We have 'currentCompany' shared in view, but for logic we use session/middleware.
        
        // Better:
        $company = auth()->user()->companies()->first();
        $providers = $company->providers;
        
        return view('providers.index', compact('providers'));
    }

    public function create()
    {
        return view('providers.create');
    }

    public function store(Request $request)
    {
        // Validation depends on type, but for now generic
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:smtp,ses,mailgun,postmark',
            'credentials' => 'required|array', // We'll expect array from form (e.g. credentials[host], credentials[key])
        ]);

        $company = auth()->user()->companies()->first();
        
        $company->providers()->create($data);

        return redirect()->route('providers.index')->with('success', 'Provider created successfully.');
    }

    public function edit(Provider $provider)
    {
        $this->authorize('view', $provider);
        return view('providers.edit', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        $this->authorize('update', $provider);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:smtp,ses,mailgun,postmark',
            'credentials' => 'required|array',
        ]);

        $provider->update($data);

        return redirect()->route('providers.index')->with('success', 'Provider updated successfully.');
    }

    public function destroy(Provider $provider)
    {
        $this->authorize('delete', $provider);
        $provider->delete();

        return redirect()->route('providers.index')->with('success', 'Provider deleted.');
    }

    public function testConnection(Provider $provider)
    {
        // Simple test logic placeholder
        // In real app, we would configure a temporary mail transport and send a test email
        
        try {
            // Placeholder for success
            // TODO: Implement actual transport configuration and send
            
            $provider->update(['last_tested_at' => now(), 'status' => 'active']);
            return back()->with('success', 'Connection tested successfully (Mock).');
        } catch (\Exception $e) {
             return back()->with('error', 'Connection failed: ' . $e->getMessage());
        }
    }
}
