<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Contacts</h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('imports.create') }}">
                    <x-button-secondary>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Import
                    </x-button-secondary>
                </a>
                <a href="{{ route('contacts.create') }}">
                    <x-button-primary>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Contact
                    </x-button-primary>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search & Filter -->
            <x-card class="p-6 mb-6">
                <form method="GET" action="{{ route('contacts.index') }}" class="flex items-center space-x-4">
                    <div class="flex-1">
                        <x-text-input 
                            type="text" 
                            name="search" 
                            placeholder="Search contacts by email or name..." 
                            :value="request('search')"
                            class="w-full"
                        />
                    </div>
                    <select name="status" class="border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm">
                        <option value="">All Statuses</option>
                        <option value="subscribed" {{ request('status') == 'subscribed' ? 'selected' : '' }}>Subscribed</option>
                        <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                        <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>Bounced</option>
                    </select>
                    <x-button-primary type="submit">Search</x-button-primary>
                </form>
            </x-card>

            <!-- Contacts Table -->
            @if($contacts->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No contacts found"
                        description="Start building your audience by adding contacts or importing a list."
                        :action="route('contacts.create')"
                        actionText="Add Your First Contact"
                    />
                </x-card>
            @else
                <x-card>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tags</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($contacts as $contact)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $contact->first_name }} {{ $contact->last_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(($contact->tags ?? []) as $tag)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                        {{ $tag }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($contact->status === 'subscribed')
                                                <x-badge-success>Subscribed</x-badge-success>
                                            @elseif($contact->status === 'unsubscribed')
                                                <x-badge-neutral>Unsubscribed</x-badge-neutral>
                                            @elseif($contact->status === 'bounced')
                                                <x-badge-error>Bounced</x-badge-error>
                                            @else
                                                <x-badge-warning>{{ ucfirst($contact->status) }}</x-badge-warning>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('contacts.edit', $contact) }}" class="text-sm font-medium text-violet-600 hover:text-violet-700 transition">
                                                Edit
                                            </a>
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
