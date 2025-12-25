<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Inbox Placement Tests</h2>
            <a href="{{ route('inbox-tests.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Test
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($inboxTests->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No inbox tests yet"
                        description="Test your email deliverability by sending to seed addresses and checking inbox placement."
                        :action="route('inbox-tests.create')"
                        actionText="Create Your First Test"
                    />
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($inboxTests as $test)
                        <x-card class="p-6 hover:shadow-lg transition-all duration-200 cursor-pointer" onclick="window.location='{{ route('inbox-tests.show', $test) }}'">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $test->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ count($test->seed_emails ?? []) }} seed addresses</p>
                                </div>
                                @if($test->status === 'completed')
                                    <x-badge-success>Completed</x-badge-success>
                                @elseif($test->status === 'sent')
                                    <x-badge-warning>Sent</x-badge-warning>
                                @else
                                    <x-badge-neutral>{{ ucfirst($test->status) }}</x-badge-neutral>
                                @endif
                            </div>

                            @if($test->status === 'completed' && $test->results)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-3 text-center">
                                        <div>
                                            <p class="text-2xl font-bold text-emerald-600">{{ $test->results['inbox'] ?? 0 }}</p>
                                            <p class="text-xs text-gray-500">Inbox</p>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-bold text-rose-600">{{ $test->results['spam'] ?? 0 }}</p>
                                            <p class="text-xs text-gray-500">Spam</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-6 flex items-center justify-end pt-4 border-t border-gray-200">
                                <a href="{{ route('inbox-tests.show', $test) }}" class="text-sm font-medium text-violet-600 hover:text-violet-700 transition" onclick="event.stopPropagation()">
                                    View Results
                                </a>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
