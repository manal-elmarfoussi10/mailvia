<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Audit Logs</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actor</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Entity</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Details</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $log->created_at->format('M d, H:i:s') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $log->user->name ?? 'System' }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->user->email ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge :type="match($log->action) {
                                            'created' => 'success',
                                            'updated' => 'warning',
                                            'deleted' => 'error',
                                            default => 'neutral'
                                        }">
                                            {{ strtoupper($log->action) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ class_basename($log->auditable_type) }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $log->auditable_id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->action === 'updated' && !empty($log->metadata))
                                            <div class="text-xs space-y-1">
                                                @foreach(($log->metadata['new'] ?? []) as $key => $value)
                                                    @if($key !== 'updated_at' && $key !== 'credentials')
                                                        <div>
                                                            <span class="font-bold text-gray-700">{{ $key }}:</span>
                                                            <span class="text-gray-400 line-through">{{ is_array($log->metadata['old'][$key] ?? null) ? json_encode($log->metadata['old'][$key]) : ($log->metadata['old'][$key] ?? 'null') }}</span>
                                                            <span class="text-emerald-600">→ {{ is_array($value) ? json_encode($value) : $value }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">---</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs font-mono text-gray-500">{{ $log->ip_address }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                        No audit logs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($logs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $logs->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
