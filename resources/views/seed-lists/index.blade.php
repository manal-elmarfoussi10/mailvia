<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Seed Lists</h2>
            <a href="{{ route('seed-lists.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Seed List
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($seedLists as $list)
                    <x-card class="hover:shadow-lg transition">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $list->name }}</h3>
                            <p class="text-sm text-gray-500 mb-6">{{ $list->emails_count }} seed email addresses</p>
                            
                            <div class="flex items-center justify-between mt-auto">
                                <div class="flex gap-1">
                                    <a href="{{ route('seed-lists.edit', $list) }}" class="text-sm font-semibold text-violet-600 hover:text-violet-700 p-2">Edit</a>
                                    <form action="{{ route('seed-lists.destroy', $list) }}" method="POST" onsubmit="return confirm('Delete this seed list?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-700 p-2">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </x-card>
                @endforeach
                @if($seedLists->isEmpty())
                    <div class="lg:col-span-3">
                        <x-card class="p-12">
                            <x-empty-state 
                                title="No seed lists yet"
                                description="Create a list of email addresses you control to test your inbox placement."
                                :action="route('seed-lists.create')"
                                actionText="Create Your First Seed List"
                            />
                        </x-card>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
