<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">System Alerts</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Severity</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Campaign</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($alerts as $alert)
                                <tr class="hover:bg-gray-50 transition {{ $alert->resolved_at ? 'opacity-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $alert->created_at->format('M d, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge :type="$alert->severity === 'critical' ? 'error' : ($alert->severity === 'warning' ? 'warning' : 'info')">
                                            {{ strtoupper($alert->severity) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $alert->message }}</div>
                                        <div class="text-xs text-gray-500">{{ strtoupper($alert->type) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(isset($alert->metadata['campaign_id']))
                                            <a href="{{ route('campaigns.show', $alert->metadata['campaign_id']) }}" class="text-violet-600 hover:underline">
                                                {{ $alert->metadata['campaign_name'] ?? 'View Campaign' }}
                                            </a>
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if(!$alert->resolved_at)
                                            <form action="{{ route('alerts.resolve', $alert) }}" method="POST">
                                                @csrf
                                                <x-button-secondary type="submit" class="text-xs">
                                                    Resolve
                                                </x-button-secondary>
                                            </form>
                                        @else
                                            <span class="text-xs text-emerald-600 font-semibold">Resolved</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        No alerts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($alerts->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $alerts->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
