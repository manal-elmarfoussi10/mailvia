<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Contact Lists</h2>
            <a href="{{ route('lists.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create List
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($lists->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No lists yet"
                        description="Organize your contacts into lists for targeted campaigns."
                        :action="route('lists.create')"
                        actionText="Create Your First List"
                    />
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($lists as $list)
                        <x-card class="p-6 hover:shadow-lg transition-all duration-200 cursor-pointer" onclick="window.location='{{ route('lists.show', $list) }}'">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $list->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $list->contacts_count ?? 0 }} contacts</p>
                                </div>
                                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-violet-100 to-cyan-100">
                                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-200">
                                <a href="{{ route('lists.show', $list) }}" class="text-sm font-medium text-violet-600 hover:text-violet-700 transition" onclick="event.stopPropagation()">
                                    View Contacts
                                </a>
                                
                                <a href="{{ route('lists.edit', $list) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition" onclick="event.stopPropagation()">
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
