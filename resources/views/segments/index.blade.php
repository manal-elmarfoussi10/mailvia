<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Segments</h2>
            <a href="{{ route('segments.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Segment
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($segments->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No segments yet"
                        description="Create dynamic segments to target specific groups of contacts based on criteria."
                        :action="route('segments.create')"
                        actionText="Create Your First Segment"
                    />
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($segments as $segment)
                        <x-card class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-black text-gray-900 group-hover:text-indigo-600 transition">{{ $segment->name }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-2xl font-black text-indigo-600">{{ number_format($segment->contact_count) }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Matched Contacts</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-violet-100 to-cyan-100">
                                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">Criteria</p>
                                <div class="space-y-1">
                                    @foreach(($segment->criteria ?? []) as $criterion)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $criterion['field'] ?? '' }} {{ $criterion['operator'] ?? '' }} {{ $criterion['value'] ?? '' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-end pt-4 border-t border-gray-200">
                                <a href="{{ route('segments.edit', $segment) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                                    Edit
                                </a>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
