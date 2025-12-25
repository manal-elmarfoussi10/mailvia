<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 leading-tight">Dashboard</h2>
                <p class="mt-1 text-sm text-gray-500">Overview of contacts, campaigns and deliverability.</p>
            </div>

            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('campaigns.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white
                          bg-gradient-to-r from-violet-600 to-cyan-500
                          hover:from-violet-500 hover:to-cyan-400
                          shadow-md hover:shadow-lg transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5v14M5 12h14"/>
                    </svg>
                    New Campaign
                </a>

                <a href="{{ route('imports.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold
                          text-gray-900 bg-white border border-gray-200
                          hover:bg-gray-50 hover:border-gray-300 transition">
                    <svg class="h-4 w-4 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3v12m0 0l4-4m-4 4l-4-4M4 21h16"/>
                    </svg>
                    Import
                </a>
            </div>
        </div>
    </x-slot>

    {{-- HERO STRIP --}}
    <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="absolute inset-0 opacity-[0.9]"
             style="background:
                radial-gradient(circle at 20% 20%, rgba(124,58,237,.10), transparent 40%),
                radial-gradient(circle at 80% 10%, rgba(34,211,238,.10), transparent 45%),
                radial-gradient(circle at 70% 90%, rgba(124,58,237,.07), transparent 45%);">
        </div>

        <div class="relative p-6 sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold tracking-wider text-gray-500 uppercase">Mailvia Status</p>
                    <h3 class="mt-2 text-lg sm:text-xl font-semibold text-gray-900">
                        Deliverability in the last 30 days
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Keep bounce & complaints low — the system will help you monitor trends.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="rounded-2xl border border-gray-200 bg-white px-4 py-3">
                        <div class="text-xs text-gray-500">Delivery Rate</div>
                        <div class="mt-1 text-xl font-bold {{ $deliveryRate >= 95 ? 'text-emerald-600' : 'text-gray-900' }}">
                            {{ $deliveryRate }}%
                        </div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white px-4 py-3">
                        <div class="text-xs text-gray-500">Bounce Rate</div>
                        <div class="mt-1 text-xl font-bold {{ $bounceRate > 5 ? 'text-rose-600' : 'text-gray-900' }}">
                            {{ $bounceRate }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Contacts --}}
        <div class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Contacts</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalContacts) }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ number_format($subscribedContacts) }} subscribed</p>
                </div>
                <div class="h-11 w-11 rounded-2xl bg-violet-50 text-violet-600 grid place-items-center
                            group-hover:bg-violet-100 transition">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M16 3.13a4 4 0 010 7.75M21 21v-2a4 4 0 00-3-3.87"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Campaigns --}}
        <div class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Campaigns</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalCampaigns) }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ number_format($activeCampaigns) }} active</p>
                </div>
                <div class="h-11 w-11 rounded-2xl bg-cyan-50 text-cyan-700 grid place-items-center
                            group-hover:bg-cyan-100 transition">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h5"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Sent --}}
        <div class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Sent (30 days)</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalSent) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Delivered: {{ number_format($totalDelivered) }}</p>
                </div>
                <div class="h-11 w-11 rounded-2xl bg-gray-100 text-gray-700 grid place-items-center
                            group-hover:bg-gray-200 transition">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Complaints --}}
        <div class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Complaints (30 days)</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalComplaints) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Bounced: {{ number_format($totalBounced) }}</p>
                </div>
                <div class="h-11 w-11 rounded-2xl bg-amber-50 text-amber-700 grid place-items-center
                            group-hover:bg-amber-100 transition">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v4m0 4h.01M10.29 3.86l-8.08 14A2 2 0 004 21h16a2 2 0 001.79-3.14l-8.08-14a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- 2-COLUMN SECTION --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Email Stats (left) --}}
        <div class="lg:col-span-1 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Email Statistics</h3>
                <span class="text-xs text-gray-500">Last 30 days</span>
            </div>

            <div class="mt-5 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Sent</span>
                    <span class="font-semibold text-gray-900">{{ number_format($totalSent) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Delivered</span>
                    <span class="font-semibold text-emerald-600">{{ number_format($totalDelivered) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Bounced</span>
                    <span class="font-semibold text-rose-600">{{ number_format($totalBounced) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Complaints</span>
                    <span class="font-semibold text-amber-600">{{ number_format($totalComplaints) }}</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-2">
                <a href="{{ route('contacts.create') }}"
                   class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-center text-sm font-semibold text-gray-900
                          hover:bg-gray-50 hover:border-gray-300 transition">
                    Add Contact
                </a>
                <a href="{{ route('queue.monitor') }}"
                   class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-center text-sm font-semibold text-gray-900
                          hover:bg-gray-50 hover:border-gray-300 transition">
                    Queue
                </a>
            </div>
        </div>

        {{-- Trend (right) --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Delivery Trend</h3>
                <span class="text-xs text-gray-500">Last 7 days</span>
            </div>

            <div class="mt-5 space-y-3">
                @php
                    $max = max($trendData->max('count') ?? 0, 1);
                @endphp

                @foreach($trendData as $trend)
                    @php
                        $pct = min(100, ($trend->count / $max) * 100);
                    @endphp

                    <div class="flex items-center gap-4">
                        <div class="w-16 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($trend->date)->format('M d') }}
                        </div>

                        <div class="flex-1">
                            <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                                <div class="h-2 rounded-full bg-gradient-to-r from-violet-600 to-cyan-500"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        <div class="w-20 text-right text-sm font-semibold text-gray-900">
                            {{ number_format($trend->count) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex flex-col sm:flex-row gap-2">
                <a href="{{ route('campaigns.create') }}"
                   class="flex-1 rounded-xl px-4 py-3 text-center text-sm font-semibold text-white
                          bg-gradient-to-r from-violet-600 to-cyan-500
                          hover:from-violet-500 hover:to-cyan-400 transition shadow-sm">
                    Create Campaign
                </a>

                <a href="{{ route('imports.create') }}"
                   class="flex-1 rounded-xl px-4 py-3 text-center text-sm font-semibold text-gray-900
                           bg-white border border-gray-200
                           hover:bg-gray-50 hover:border-gray-300 transition">
                    Import Contacts
                </a>
            </div>
        </div>
    </div>

    {{-- RECENT ACTIVITY --}}
    <div class="mt-6">
        <x-card title="Recent Global Activity">
            <div class="flow-root p-6">
                <ul role="list" class="-mb-8">
                    @forelse($recentEvents as $event)
                        <li>
                            <div class="relative pb-8">
                                @if (!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white {{ match($event->type) {
                                            'delivered' => 'bg-green-500',
                                            'opened' => 'bg-violet-500',
                                            'clicked' => 'bg-cyan-500',
                                            'bounced' => 'bg-amber-500',
                                            'complained' => 'bg-red-500',
                                            'unsubscribed' => 'bg-gray-500',
                                            default => 'bg-gray-400'
                                        } }}">
                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                @if($event->type === 'delivered') <path d="M5 13l4 4L19 7" />
                                                @elseif($event->type === 'opened') <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                @elseif($event->type === 'clicked') <path d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5" />
                                                @else <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                @endif
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                <a href="{{ route('contacts.show', $event->contact) }}" class="font-bold text-gray-900 hover:text-indigo-600">
                                                    {{ $event->contact->email }}
                                                </a>
                                                {{ $event->type }} the email
                                                @if($event->campaign)
                                                    in campaign <a href="{{ route('campaigns.show', $event->campaign) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $event->campaign->name }}</a>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                            <time datetime="{{ $event->created_at }}">{{ $event->created_at->diffForHumans() }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="p-6 text-center text-gray-400">No recent activity.</li>
                    @endforelse
                </ul>
            </div>
        </x-card>
    </div>
</x-app-layout>