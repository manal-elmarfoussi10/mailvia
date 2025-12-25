    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Suppression List</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('suppressions.export') }}">
                    <x-button-secondary>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M16 9l-4-4-4 4M12 5v13"/></svg>
                        Export
                    </x-button-secondary>
                </a>
                
                <button x-data="" x-on:click="$dispatch('open-modal', 'import-suppressions')">
                    <x-button-secondary>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 17l4-4-4-4M12 17V5"/></svg>
                        Import CSV
                    </x-button-secondary>
                </button>

                <form action="{{ route('suppressions.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <x-text-input name="email" placeholder="Manual suppress..." class="w-48 text-sm" required />
                    <x-button-primary>
                        Suppress
                    </x-button-primary>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                @php
                    $reasonColors = [
                        'unsubscribed' => 'gray',
                        'bounced' => 'amber',
                        'complained' => 'rose',
                        'manual' => 'sky',
                        'manual-import' => 'violet'
                    ];
                @endphp
                @foreach(['unsubscribed', 'bounced', 'complained', 'manual'] as $reason)
                    <x-card class="p-6">
                        <span class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">{{ $reason }}</span>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-black text-gray-900">{{ number_format($stats[$reason] ?? 0) }}</span>
                            <div class="w-2 h-2 rounded-full bg-{{ $reasonColors[$reason] ?? 'gray' }}-500 mb-2"></div>
                        </div>
                    </x-card>
                @endforeach
            </div>

            <x-card class="overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <form method="GET" class="relative max-w-xs w-full">
                        <x-text-input name="search" placeholder="Search emails..." class="w-full pl-10 text-sm" value="{{ request('search') }}" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </form>
                    
                    <div class="flex items-center gap-4">
                        <select name="reason" onchange="this.form.submit()" class="text-xs rounded-lg border-gray-300">
                            <option value="">All Reasons</option>
                            @foreach($stats as $reason => $count)
                                <option value="{{ $reason }}" {{ request('reason') === $reason ? 'selected' : '' }}>{{ strtoupper($reason) }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('suppressions.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-900">Reset</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Address</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Suppressed At</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($suppressions as $suppression)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $suppression->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge :type="match($suppression->reason) {
                                            'unsubscribed' => 'neutral',
                                            'bounced' => 'warning',
                                            'complained' => 'error',
                                            'manual' => 'info',
                                            default => 'neutral'
                                        }">
                                            {{ strtoupper($suppression->reason) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $suppression->suppressed_at->format('M j, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('suppressions.destroy', $suppression) }}" method="POST" onsubmit="return confirm('Remove this email from the suppression list?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-bold">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <p class="text-gray-500 text-lg">No suppressed emails found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($suppressions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $suppressions->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    <x-modal name="import-suppressions" :show="false" focusable>
        <form method="post" action="{{ route('suppressions.import') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 mb-2">Import Suppression List</h2>
            <p class="text-sm text-gray-500 mb-6">Upload a CSV file with emails in the first column and an optional reason in the second column.</p>
            
            <div class="mb-6">
                <x-input-label for="file" value="CSV File" class="sr-only" />
                <input type="file" name="file" id="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required />
            </div>

            <div class="flex justify-end gap-3">
                <x-button-secondary x-on:click="$dispatch('close')">Cancel</x-button-secondary>
                <x-button-primary>Upload & Process</x-button-primary>
            </div>
        </form>
    </x-modal>
</x-app-layout>
