<aside
    x-data="{ open: false }"
    class="hidden lg:flex w-72 flex-col bg-black border-r border-white/10"
>
    {{-- Brand --}}
    <div class="h-16 px-5 flex items-center gap-3 border-b border-white/10">
        <img src="{{ asset('logo.png') }}" class="h-8 w-auto" alt="Mailvia">
     
    </div>

    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
        <nav class="mt-5 flex-1 px-3 space-y-1">
            {{-- PRIMARY --}}
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <!-- Companies -->
            <a href="{{ route('companies.index') }}" 
               class="group flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl {{ request()->routeIs('companies.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Companies
            </a>

            {{-- Divider --}}
            <div class="border-t border-white/10 my-4"></div>

            {{-- INFRASTRUCTURE --}}
            <div class="px-3 py-2 text-xs font-semibold text-white/40 uppercase tracking-wider">Infrastructure</div>

            <!-- Providers (Hidden for ENV-only mode) -->
            <!--
            <a href="{{ route('providers.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('providers.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
                Providers
            </a>
            -->

            <!-- Senders -->
            <a href="{{ route('senders.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('senders.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Senders
            </a>

            <!-- Domains -->
            <a href="{{ route('domains.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('domains.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                Domains
            </a>

            {{-- Divider --}}
            <div class="border-t border-white/10 my-4"></div>

            {{-- CONTACTS --}}
            <div class="px-3 py-2 text-xs font-semibold text-white/40 uppercase tracking-wider">Contacts</div>

            <!-- Contacts -->
            <a href="{{ route('contacts.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('contacts.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Contacts
            </a>

            <!-- Lists -->
            <a href="{{ route('lists.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('lists.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Lists
            </a>

            <!-- Segments -->
            <a href="{{ route('segments.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('segments.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Segments
            </a>

            <!-- Import Center -->
            <a href="{{ route('imports.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('imports.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Import Center
            </a>

            <!-- Suppression List -->
            <a href="{{ route('suppressions.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('suppressions.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                Suppression List
            </a>

            {{-- Divider --}}
            <div class="border-t border-white/10 my-4"></div>

            {{-- CAMPAIGNS --}}
            <div class="px-3 py-2 text-xs font-semibold text-white/40 uppercase tracking-wider">Campaigns</div>

            <!-- Templates -->
            <a href="{{ route('templates.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('templates.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
                Templates
            </a>

            <!-- Campaigns -->
            <a href="{{ route('campaigns.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('campaigns.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Campaigns
            </a>

            {{-- Divider --}}
            <div class="border-t border-white/10 my-4"></div>

            {{-- MONITORING --}}
            <div class="px-3 py-2 text-xs font-semibold text-white/40 uppercase tracking-wider">Monitoring</div>

            <!-- Inbox Tests -->
            <a href="{{ route('inbox-tests.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('inbox-tests.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Inbox Tests
            </a>

            <!-- Seed Lists -->
            <a href="{{ route('seed-lists.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('seed-lists.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Seed Lists
            </a>

            <!-- Queue Monitor -->
            <a href="{{ route('queue.monitor') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('queue.monitor') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Queue Monitor
            </a>

            <!-- Alerts -->
            @php
                $navCompanyId = session('company_id') ?: auth()->user()->companies()->first()?->id;
            @endphp
            <a href="{{ route('alerts.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('alerts.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Alerts
                @php
                    $activeAlertsCount = \App\Models\Alert::active()->where('company_id', session('company_id'))->count();
                @endphp
                @if($activeAlertsCount > 0)
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-500 text-white animate-pulse">
                        {{ $activeAlertsCount }}
                    </span>
                @endif
            </a>

            <!-- Audit Logs -->
            <a href="{{ route('audit-logs.index') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('audit-logs.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Audit Logs
            </a>

            {{-- Settings --}}
            <div class="px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">
                Administration
            </div>

            <!-- Global Settings -->
            <a href="{{ route('settings.edit') }}" 
               class="group flex items-center pl-6 pr-3 py-2 text-sm font-medium rounded-xl {{ request()->routeIs('settings.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-all duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37-2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>

            <!-- Advanced: Horizon -->
            <a href="/horizon" target="_blank"
               class="group flex items-center pl-9 pr-3 py-1.5 text-xs font-medium rounded-xl text-white/50 hover:bg-white/5 hover:text-white/70 transition-all duration-150">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Advanced ▸ Horizon
                <svg class="ml-auto h-3 w-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </nav>
    </div>

    {{-- Bottom user card --}}
    <div class="mt-auto p-4 border-t border-white/10">
        <div class="rounded-2xl bg-white/5 p-3">
            <div class="text-sm font-semibold">{{ Auth::user()->name }}</div>
            <div class="text-xs text-white/60">{{ Auth::user()->email }}</div>
        </div>
    </div>
</aside>