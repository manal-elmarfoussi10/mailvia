<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('contacts.index') }}" class="text-gray-400 hover:text-gray-900 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="font-semibold text-2xl text-gray-900">{{ $contact->first_name }} {{ $contact->last_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $contact->email }}</p>
                </div>
                <x-badge :type="match($contact->status) {
                    'subscribed' => 'success',
                    'unsubscribed' => 'neutral',
                    'bounced' => 'warning',
                    'complained' => 'error',
                    default => 'neutral'
                }">
                    {{ strtoupper($contact->status) }}
                </x-badge>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('contacts.edit', $contact) }}">
                    <x-button-secondary>
                        Edit Contact
                    </x-button-secondary>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Stats & History -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Engagement Stats -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <x-card class="bg-violet-50 border-violet-100">
                            <div class="p-4 text-center">
                                <span class="text-xs font-bold text-violet-400 uppercase block mb-1">Total Opens</span>
                                <span class="text-2xl font-black text-violet-700">{{ number_format($stats['total_opens']) }}</span>
                            </div>
                        </x-card>
                        <x-card class="bg-cyan-50 border-cyan-100">
                            <div class="p-4 text-center">
                                <span class="text-xs font-bold text-cyan-400 uppercase block mb-1">Total Clicks</span>
                                <span class="text-2xl font-black text-cyan-700">{{ number_format($stats['total_clicks']) }}</span>
                            </div>
                        </x-card>
                        <x-card class="bg-gray-50 border-gray-100 col-span-2 sm:col-span-1">
                            <div class="p-4 text-center">
                                <span class="text-xs font-bold text-gray-400 uppercase block mb-1">Last Active</span>
                                <span class="text-sm font-bold text-gray-900">{{ $stats['last_active'] ? $stats['last_active']->diffForHumans() : 'Never' }}</span>
                            </div>
                        </x-card>
                    </div>

                    <!-- Campaign History -->
                    <x-card title="Campaign History">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Campaign</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($contact->campaignSends as $send)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                <a href="{{ route('campaigns.show', $send->campaign) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $send->campaign->name }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <x-badge :type="match($send->status) {
                                                    'delivered' => 'success',
                                                    'bounced' => 'warning',
                                                    'failed' => 'error',
                                                    default => 'neutral'
                                                }">{{ strtoupper($send->status) }}</x-badge>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $send->created_at->format('M j, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-400">No campaigns sent to this contact yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>

                <!-- Right Column: Activity Timeline -->
                <div class="space-y-8">
                    <x-card title="Recent Activity">
                        <div class="flow-root p-6">
                            <ul role="list" class="-mb-8">
                                @forelse($events as $event)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
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
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            <span class="font-bold text-gray-900">{{ ucfirst($event->type) }}</span>
                                                            @if($event->campaign)
                                                                in <b>{{ $event->campaign->name }}</b>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                        {{ $event->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-center py-4 text-gray-400">No activity logged yet.</li>
                                @endforelse
                            </ul>
                        </div>
                        @if($events->hasPages())
                            <div class="p-6 border-t border-gray-100">
                                {{ $events->links() }}
                            </div>
                        @endif
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
