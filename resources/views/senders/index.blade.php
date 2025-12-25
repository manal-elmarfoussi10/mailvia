<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Sender Identities</h2>
            <a href="{{ route('senders.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Sender
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($senders->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No sender identities"
                        description="Add your first sender identity to start sending emails."
                        :action="route('senders.create')"
                        actionText="Add Your First Sender"
                    />
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($senders as $sender)
                        <x-card class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $sender->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $sender->email }}</p>
                                </div>
                                @if($sender->dns_verified)
                                    <x-badge-success>Verified</x-badge-success>
                                @else
                                    <x-badge-warning>Pending</x-badge-warning>
                                @endif
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">Provider</p>
                                <p class="text-sm font-medium text-gray-900">{{ $sender->provider->name ?? 'N/A' }}</p>
                            </div>

                            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-200">
                                <form action="{{ route('senders.check-dns', $sender) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium text-violet-600 hover:text-violet-700 transition">
                                        Check DNS
                                    </button>
                                </form>
                                
                                <a href="{{ route('senders.edit', $sender) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
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
