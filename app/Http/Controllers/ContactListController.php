<?php

namespace App\Http\Controllers;

use App\Models\ContactList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ContactListController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $lists = $company->lists()->withCount('contacts')->get();
        return view('lists.index', compact('lists'));
    }

    public function create()
    {
        return view('lists.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $company = auth()->user()->companies()->first();
        $company->lists()->create($request->only('name'));

        return redirect()->route('lists.index')->with('success', 'List created successfully.');
    }

    public function show(ContactList $list)
    {
        $this->authorize('view', $list);
        
        // Paginate contacts in this list
        $contacts = $list->contacts()->paginate(20);

        // List stats
        $stats = [
            'total_opens' => \App\Models\CampaignEvent::whereIn('contact_id', $list->contacts()->pluck('contacts.id'))
                ->where('type', 'opened')
                ->count(),
            'total_clicks' => \App\Models\CampaignEvent::whereIn('contact_id', $list->contacts()->pluck('contacts.id'))
                ->where('type', 'clicked')
                ->count(),
        ];
        
        return view('lists.show', compact('list', 'contacts', 'stats'));
    }

    public function edit(ContactList $list)
    {
        $this->authorize('view', $list);
        return view('lists.edit', compact('list'));
    }

    public function update(Request $request, ContactList $list)
    {
        $this->authorize('update', $list);
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $list->update($request->only('name'));

        return redirect()->route('lists.index')->with('success', 'List updated.');
    }

    public function destroy(ContactList $list)
    {
        $this->authorize('delete', $list);
        $list->delete();
        return redirect()->route('lists.index')->with('success', 'List deleted.');
    }

    public function addContact(Request $request, ContactList $list)
    {
        $this->authorize('update', $list);

        $request->validate([
            'email' => 'required|email|exists:contacts,email',
        ]);

        $company = auth()->user()->companies()->first();
        $contact = $company->contacts()->where('email', $request->email)->first();

        if (!$contact) {
            return back()->withErrors(['email' => 'Contact not found in this workspace.']);
        }

        // Attach if not exists
        if (!$list->contacts()->where('contact_id', $contact->id)->exists()) {
            $list->contacts()->attach($contact->id);
            return back()->with('success', 'Contact added to list.');
        }

        return back()->with('info', 'Contact already in list.');
    }

    public function removeContact(ContactList $list, \App\Models\Contact $contact)
    {
        $this->authorize('update', $list);
        
        $list->contacts()->detach($contact->id);

        return back()->with('success', 'Contact removed from list.');
    }
}
