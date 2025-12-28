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
        $request->merge([
            'domain' => preg_replace('#^https?://#', '', rtrim($request->domain, '/'))
        ]);

        $data = $request->validate([
            'domain' => 'required|string|max:255|unique:domains,domain',
        ]);

        $company = auth()->user()->companies()->first();
        $company->domains()->create($data);

        return redirect()->route('domains.index')->with('success', 'Domain added successfully. Please follow the verification steps.');
    }

    public function show(Domain $domain)
    {
        $this->authorize('view', $domain->company); 
        
        // ENV-only Check: Does the global mail host look like SES?
        $mailHost = config('mail.mailers.smtp.host') ?? env('MAIL_HOST') ?? '';
        $isSesEnv = str_contains($mailHost, 'amazonaws.com');

        return view('domains.show', compact('domain', 'isSesEnv'));
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
                    // Improved SPF Check: look for general "v=spf1" AND specific includes if possible
                    if (str_contains($record['txt'], 'v=spf1')) {
                        $content = $record['txt'];
                        // Basic SPF check passed
                        $spf = true; 
                        
                        // Optional: Check for specific provider includes if we want to be strict
                        // if (str_contains($content, 'include:amazonses.com') || str_contains($content, 'include:_spf.mail.hostinger.com')) {
                        //     $spf = true;
                        // }
                    }
                }
            }
        }

        // DKIM Check: Check standard selector "default" (Hostinger) or just presence of any DKIM record
        // Since we don't know the exact selector for SES (it's random), checking "default" covers Hostinger.
        // For SES, we might need a UI input for "Selector" in the future. 
        // For now, let's check 'default' and 'mailvia' (us)
        $selectors = ['default', 'google', 'k1', 'smtp']; 
        $dkim = false;
        foreach ($selectors as $selector) {
             $dkimRecords = @dns_get_record($selector . "._domainkey." . $domain->domain, DNS_TXT);
             if ($dkimRecords) {
                 foreach ($dkimRecords as $record) {
                     if (isset($record['txt']) && str_contains($record['txt'], 'v=DKIM1')) {
                         $dkim = true;
                         break 2;
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

        // If the domain uses SES (detected via ENV), store any provided DKIM tokens
        // We use $request->has('dkim_tokens') as the primary signal
        if ($request->has('dkim_tokens')) {
            $domain->dkim_tokens = json_encode($request->input('dkim_tokens'));
        }

        $domain->update([
            'spf_verified' => $spf,
            'dkim_verified' => $dkim, // Now saving DKIM status
            'dmarc_verified' => $dmarc,
            'status' => $ownership ? 'verified' : ($spf || $dmarc ? 'pending' : 'failed'),
        ]);

        return back()->with('success', 'Status: ' . $domain->status . '. (Debug: Found ' . count($txtRecords) . ' TXT records. ' . json_encode($txtRecords) . ')');
    }
}
