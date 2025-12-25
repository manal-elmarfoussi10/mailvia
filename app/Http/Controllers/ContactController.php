<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $company = auth()->user()->companies()->first();
        
        $query = $company->contacts();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $contacts = $query->latest()->paginate(20);

        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $company = auth()->user()->companies()->first();

        $data = $request->validate([
            'email' => [
                'required', 
                'email', 
                'max:255',
                // Unique per company
                function ($attribute, $value, $fail) use ($company) {
                    if ($company->contacts()->where('email', $value)->exists()) {
                        $fail('This email is already registered in this workspace.');
                    }
                },
            ],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'status' => 'required|in:subscribed,unsubscribed,bounced,complained',
            'tags' => 'nullable|string', // comma separated input
        ]);

        // Process Tags
        if (!empty($data['tags'])) {
            $tags = array_map('trim', explode(',', $data['tags']));
            $data['tags'] = $tags;
        } else {
            $data['tags'] = [];
        }

        $company->contacts()->create($data);

        return redirect()->route('contacts.index')->with('success', 'Contact added successfully.');
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);
        
        $contact->load(['campaignSends.campaign']);
        
        $events = $contact->campaignEvents()
            ->with('campaign')
            ->latest()
            ->paginate(30);

        $stats = [
            'total_opens' => $contact->campaignEvents()->where('type', 'opened')->count(),
            'total_clicks' => $contact->campaignEvents()->where('type', 'clicked')->count(),
            'last_active' => $contact->campaignEvents()->latest()->first()?->created_at,
        ];

        return view('contacts.show', compact('contact', 'events', 'stats'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('view', $contact);
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $company = auth()->user()->companies()->first();

        $data = $request->validate([
            'email' => [
                'required', 
                'email', 
                'max:255',
                function ($attribute, $value, $fail) use ($company, $contact) {
                    if ($company->contacts()->where('email', $value)->where('id', '!=', $contact->id)->exists()) {
                        $fail('This email is already registered in this workspace.');
                    }
                },
            ],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'status' => 'required|in:subscribed,unsubscribed,bounced,complained',
            'tags' => 'nullable|string',
        ]);

        if (isset($data['tags'])) {
             $tags = array_map('trim', explode(',', $data['tags']));
             $data['tags'] = $tags;
        }

        $contact->update($data);

        return redirect()->route('contacts.index')->with('success', 'Contact updated.');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted.');
    }
}
