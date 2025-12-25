<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $automations = $company->automations()->with('template', 'sender')->latest()->paginate(20);
        return view('automations.index', compact('automations'));
    }

    public function create()
    {
        $company = auth()->user()->companies()->first();
        $templates = $company->templates;
        $senders = $company->senders;
        $providers = $company->providers;
        
        return view('automations.create', compact('templates', 'senders', 'providers'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->companies()->first();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string|in:contact_created',
            'template_id' => 'required|exists:templates,id',
            'sender_id' => 'required|exists:senders,id',
            'provider_id' => 'required|exists:providers,id',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $company->automations()->create($data);

        return redirect()->route('automations.index')->with('success', 'Automation created successfully.');
    }

    public function edit(Automation $automation)
    {
        $this->authorize('view', $automation);
        $company = auth()->user()->companies()->first();
        $templates = $company->templates;
        $senders = $company->senders;
        $providers = $company->providers;
        
        return view('automations.edit', compact('automation', 'templates', 'senders', 'providers'));
    }

    public function update(Request $request, Automation $automation)
    {
        $this->authorize('update', $automation);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string|in:contact_created',
            'template_id' => 'required|exists:templates,id',
            'sender_id' => 'required|exists:senders,id',
            'provider_id' => 'required|exists:providers,id',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $automation->update($data);

        return redirect()->route('automations.index')->with('success', 'Automation updated.');
    }

    public function destroy(Automation $automation)
    {
        $this->authorize('delete', $automation);
        $automation->delete();
        return redirect()->route('automations.index')->with('success', 'Automation deleted.');
    }
}
