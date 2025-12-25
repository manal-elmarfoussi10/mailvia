<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Companies</h2>
            <a href="{{ route('companies.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Company
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($companies->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No companies yet"
                        description="Create your first company workspace to get started with Mailvia."
                        :action="route('companies.create')"
                        actionText="Create Your First Company"
                    />
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($companies as $company)
                        <x-card class="p-6 hover:shadow-lg transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $company->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $company->domain ?? 'No domain set' }}</p>
                                </div>
                                @if(session('company_id') == $company->id)
                                    <x-badge-success>Current</x-badge-success>
                                @endif
                            </div>
                            
                            <div class="mt-6 flex items-center justify-between">
                                @if(session('company_id') != $company->id)
                                    <form action="{{ route('companies.switch', $company) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-sm font-medium text-violet-600 hover:text-violet-700 transition">
                                            Switch to this workspace
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm text-gray-400">Active workspace</span>
                                @endif
                                
                                <a href="{{ route('companies.edit', $company) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
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
