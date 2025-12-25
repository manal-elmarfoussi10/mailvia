<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DomainController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $domains = $company->domains()->latest()->get();
        return view('domains.index', compact('domains'));
    }

    public function create()
    {
        return view('domains.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'domain' => 'required|string|max:255|unique:domains,domain',
        ]);

        $company = auth()->user()->companies()->first();
        $company->domains()->create($data);

        return redirect()->route('domains.index')->with('success', 'Domain added successfully. Please follow the verification steps.');
    }

    public function show(Domain $domain)
    {
        $this->authorize('view', $domain->company); // simple check
        return view('domains.show', compact('domain'));
    }

    public function destroy(Domain $domain)
    {
        $this->authorize('update', $domain->company);
        $domain->delete();
        return redirect()->route('domains.index')->with('success', 'Domain removed.');
    }

    public function verify(Domain $domain)
    {
        $this->authorize('update', $domain->company);

        // Verification logic
        $spf = false;
        $dmarc = false;
        $ownership = false;

        // Choice 1: TXT record with Mailvia token for ownership
        $txtRecords = @dns_get_record($domain->domain, DNS_TXT);
        if (is_array($txtRecords)) {
            foreach ($txtRecords as $record) {
                if (isset($record['txt'])) {
                    if (str_contains($record['txt'], $domain->verification_token)) {
                        $ownership = true;
                    }
                    if (str_contains($record['txt'], 'v=spf1')) {
                        $spf = true;
                    }
                }
            }
        }

        // DMARC Check
        $dmarcRecords = @dns_get_record("_dmarc." . $domain->domain, DNS_TXT);
        if ($dmarcRecords) {
            foreach ($dmarcRecords as $record) {
                if (isset($record['txt']) && str_contains($record['txt'], 'v=DMARC1')) {
                    $dmarc = true;
                    break;
                }
            }
        }

        $domain->update([
            'spf_verified' => $spf,
            'dmarc_verified' => $dmarc,
            'status' => $ownership ? 'verified' : ($spf || $dmarc ? 'pending' : 'failed'),
        ]);

        return back()->with('success', 'Verification logic executed. Status: ' . $domain->status);
    }
}
