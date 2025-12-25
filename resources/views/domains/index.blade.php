<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Verified Domains</h2>
            <x-button-primary href="{{ route('domains.create') }}">
                Add Domain
            </x-button-primary>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Domain</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">DNS Health</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($domains as $domain)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $domain->domain }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge :type="$domain->status === 'verified' ? 'success' : ($domain->status === 'pending' ? 'warning' : 'error')">
                                            {{ strtoupper($domain->status) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex gap-2">
                                            <span class="inline-flex items-center gap-1 text-xs {{ $domain->spf_verified ? 'text-emerald-600' : 'text-gray-400' }}">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                                SPF
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-xs {{ $domain->dkim_verified ? 'text-emerald-600' : 'text-gray-400' }}">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                                DKIM
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-xs {{ $domain->dmarc_verified ? 'text-emerald-600' : 'text-gray-400' }}">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                                DMARC
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('domains.show', $domain) }}" class="text-violet-600 hover:text-violet-900 font-medium">Verify</a>
                                            <form action="{{ route('domains.destroy', $domain) }}" method="POST" onsubmit="return confirm('Remove domain?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-900">Remove</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                        No domains added yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
