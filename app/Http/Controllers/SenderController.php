<?php

namespace App\Http\Controllers;

use App\Models\Sender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SenderController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $senders = $company->senders()->with(['provider', 'domain'])->get();
        return view('senders.index', compact('senders'));
    }

    public function create()
    {
        $company = auth()->user()->companies()->first();
        $providers = $company->providers;
        $domains = $company->domains()->where('status', 'verified')->get();
        return view('senders.create', compact('providers', 'domains'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'reply_to' => 'nullable|email|max:255',
            'provider_id' => 'nullable|exists:providers,id',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        $company = auth()->user()->companies()->first();
        
        // Ensure provider belongs to company if selected
        if (!empty($data['provider_id'])) {
            $hasProvider = $company->providers()->where('id', $data['provider_id'])->exists();
            if (!$hasProvider) {
                return back()->withErrors(['provider_id' => 'Invalid provider selected.']);
            }
        }

        // Ensure domain belongs to company if selected
        if (!empty($data['domain_id'])) {
            $hasDomain = $company->domains()->where('id', $data['domain_id'])->exists();
            if (!$hasDomain) {
                return back()->withErrors(['domain_id' => 'Invalid domain selected.']);
            }
        }

        $company->senders()->create($data);

        return redirect()->route('senders.index')->with('success', 'Sender created successfully.');
    }

    public function edit(Sender $sender)
    {
        $this->authorize('view', $sender);
        $company = auth()->user()->companies()->first();
        $providers = $company->providers;
        $domains = $company->domains()->where('status', 'verified')->get();
        return view('senders.edit', compact('sender', 'providers', 'domains'));
    }

    public function update(Request $request, Sender $sender)
    {
        $this->authorize('update', $sender);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'reply_to' => 'nullable|email|max:255',
            'provider_id' => 'nullable|exists:providers,id',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        $company = auth()->user()->companies()->first();

        if (!empty($data['provider_id'])) {
             if (!$company->providers()->where('id', $data['provider_id'])->exists()) {
                 return back()->withErrors(['provider_id' => 'Invalid provider.']);
             }
        }

        if (!empty($data['domain_id'])) {
            if (!$company->domains()->where('id', $data['domain_id'])->exists()) {
                return back()->withErrors(['domain_id' => 'Invalid domain.']);
            }
        }

        $sender->update($data);

        return redirect()->route('senders.index')->with('success', 'Sender updated.');
    }

    public function destroy(Sender $sender)
    {
        $this->authorize('delete', $sender);
        $sender->delete();
        return redirect()->route('senders.index')->with('success', 'Sender deleted.');
    }

    public function verify(Sender $sender)
    {
        // ... (Keep existing simple verify logic if needed, though we have Domain verification now)
        $this->authorize('update', $sender);
        
        $domain = substr(strrchr($sender->email, "@"), 1);
        
        $spf = false;
        $dmarc = false;

        $txtRecords = @dns_get_record($domain, DNS_TXT);
        if ($txtRecords) {
            foreach ($txtRecords as $record) {
                if (isset($record['txt']) && str_contains($record['txt'], 'v=spf1')) {
                    $spf = true;
                    break;
                }
            }
        }

        $dmarcRecords = @dns_get_record("_dmarc.$domain", DNS_TXT);
        if ($dmarcRecords) {
            foreach ($dmarcRecords as $record) {
                 if (isset($record['txt']) && str_contains($record['txt'], 'v=DMARC1')) {
                    $dmarc = true;
                    break;
                }
            }
        }

        $status = ($spf && $dmarc) ? 'verified' : 'unverified';
        $sender->update(['status' => $status]);
        
        $msg = "Verification results: SPF: " . ($spf ? 'OK' : 'Missing') . ", DMARC: " . ($dmarc ? 'OK' : 'Missing');

        return back()->with('info', $msg);
    }
}
