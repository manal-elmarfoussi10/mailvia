<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Sending Providers</h2>
            <a href="{{ route('providers.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Provider
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($providers->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No providers configured"
                        description="Add your first SMTP or API provider to start sending emails."
                        :action="route('providers.create')"
                        actionText="Add Your First Provider"
                    />
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($providers as $provider)
                        <x-card class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $provider->name }}</h3>
                                    <p class="text-sm text-gray-500 uppercase mt-1">{{ $provider->type }}</p>
                                </div>
                                <x-badge-success>{{ $provider->status }}</x-badge-success>
                            </div>

                            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-200">
                                <form action="{{ route('providers.test', $provider) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium text-violet-600 hover:text-violet-700 transition">
                                        Test Connection
                                    </button>
                                </form>
                                
                                <a href="{{ route('providers.edit', $provider) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
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
