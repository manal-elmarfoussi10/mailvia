<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-900">{{ $list->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $contacts->total() }} contacts in this list</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('lists.edit', $list) }}">
                    <x-button-secondary>
                        Edit List
                    </x-button-secondary>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- List Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-card class="bg-violet-50 border-violet-100">
                    <div class="p-4 text-center">
                        <span class="text-xs font-bold text-violet-400 uppercase block mb-1">List Total Opens</span>
                        <span class="text-2xl font-black text-violet-700">{{ number_format($stats['total_opens']) }}</span>
                    </div>
                </x-card>
                <x-card class="bg-cyan-50 border-cyan-100">
                    <div class="p-4 text-center">
                        <span class="text-xs font-bold text-cyan-400 uppercase block mb-1">List Total Clicks</span>
                        <span class="text-2xl font-black text-cyan-700">{{ number_format($stats['total_clicks']) }}</span>
                    </div>
                </x-card>
            </div>
            <!-- Add Contact Form -->
            <x-card class="p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Contact to List</h3>
                <form method="POST" action="{{ route('lists.add-contact', $list) }}" class="flex items-end space-x-4">
                    @csrf
                    <div class="flex-1">
                        <x-input-label for="email" value="Contact Email" class="text-gray-900 font-semibold" />
                        <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" placeholder="contact@example.com" required />
                    </div>
                    <x-button-primary type="submit">
                        Add Contact
                    </x-button-primary>
                </form>
            </x-card>

            <!-- Contacts Table -->
            @if($contacts->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No contacts in this list"
                        description="Add contacts to this list using the form above."
                        :action="null"
                        actionText=""
                    />
                </x-card>
            @else
                <x-card>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($contacts as $contact)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div>
                                                <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                                    {{ $contact->first_name }} {{ $contact->last_name }}
                                                </a>
                                                <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($contact->status === 'subscribed')
                                                <x-badge-success>Subscribed</x-badge-success>
                                            @else
                                                <x-badge-neutral>{{ ucfirst($contact->status) }}</x-badge-neutral>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('lists.remove-contact', [$list, $contact]) }}" method="POST" class="inline" onsubmit="return confirm('Remove this contact from the list?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700 transition">
                                                    Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $contacts->links() }}
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
