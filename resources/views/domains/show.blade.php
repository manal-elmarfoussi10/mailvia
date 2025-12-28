<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-1">
                    <a href="{{ route('domains.index') }}" class="hover:text-violet-600 transition">Domains</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900 font-medium">Verify</span>
                </nav>
                <h2 class="font-semibold text-2xl text-gray-900 leading-tight">{{ $domain->domain }}</h2>
            </div>
            <form action="{{ route('domains.verify', $domain) }}" method="POST">
                @csrf
                <x-button-primary type="submit">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh Status
                </x-button-primary>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            {{-- Status Banner --}}
            @if($domain->status === 'verified')
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800 italic">This domain is successfully verified and ready for use.</p>
                        </div>
                    </div>
                </div>
            @elseif($domain->status === 'failed')
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-rose-800 italic">Ownership verification failed. Please check your DNS records below.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    {{-- Ownership Card --}}
                    <x-card title="1. Domain Ownership Verification">
                        <p class="text-sm text-gray-500 mb-4 italic">Add this TXT record to your domain's DNS settings to prove you own it.</p>
                        <div class="bg-gray-50 rounded-xl p-4 font-mono text-sm border border-gray-100 mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-400">Type</span>
                                <span>TXT</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-400">Host / Name</span>
                                <span>@ / {{ $domain->domain }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Value</span>
                                <span class="text-violet-600 font-semibold break-all">{{ $domain->verification_token }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-badge :type="$domain->status === 'verified' ? 'success' : 'error'">
                                {{ $domain->status === 'verified' ? 'Verified' : 'Unverified' }}
                            </x-badge>
                        </div>
                    </x-card>

                    {{-- SPF Card --}}
                    <x-card title="2. SPF (Sender Policy Framework)">
                        <p class="text-sm text-gray-500 mb-4 italic">SPF prevents spoofing by specifying which mail servers are permitted to send email on behalf of your domain.</p>
                        <div class="bg-gray-50 rounded-xl p-4 font-mono text-sm border border-gray-100 mb-4">
                            @if(isset($isSesEnv) && $isSesEnv)
                                <div class="mt-6">
                                    <h4 class="text-md font-semibold mb-2">SES DNS Configuration (via ENV)</h4>
                                    <p class="mb-1"><strong>SPF Record (add as TXT):</strong></p>
                                    <pre class="bg-gray-100 p-2 rounded">v=spf1 include:amazonses.com ~all</pre>
                                </div>
                            @else
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-400">Type</span>
                                    <span>TXT</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-400">Value</span>
                                    <span class="text-gray-800 break-all">v=spf1 include:amazonses.com ~all</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <x-badge :type="$domain->spf_verified ? 'success' : 'warning'">
                                {{ $domain->spf_verified ? 'Record Found' : 'Missing or Incorrect' }}
                            </x-badge>
                        </div>
                    </x-card>

                    {{-- DMARC Card --}}
                    <x-card title="3. DMARC (Optional but Recommended)">
                        <p class="text-sm text-gray-500 mb-4 italic">DMARC uses SPF and DKIM to tell receiving servers how to handle emails that fail authentication.</p>
                        <div class="bg-gray-50 rounded-xl p-4 font-mono text-sm border border-gray-100 mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-400">Host / Name</span>
                                <span>_dmarc</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Value</span>
                                <span class="text-gray-800 break-all">v=DMARC1; p=none;</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-badge :type="$domain->dmarc_verified ? 'success' : 'warning'">
                                {{ $domain->dmarc_verified ? 'Record Found' : 'Missing or Incorrect' }}
                            </x-badge>
                        </div>
                    </x-card>
                </div>

                <div class="space-y-6">
                    <x-card title="Why verify?">
                        <ul class="space-y-4">
                            <li class="flex gap-3">
                                <div class="flex-shrink-0 h-5 w-5 text-violet-500">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="text-sm text-gray-600 italic leading-relaxed">Verification ensures that your emails are not marked as spam.</span>
                            </li>
                            <li class="flex gap-3">
                                <div class="flex-shrink-0 h-5 w-5 text-violet-500">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <span class="text-sm text-gray-600 italic leading-relaxed">Verified domains have higher delivery rates and better sender reputation.</span>
                            </li>
                        </ul>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
