<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Campaigns</h2>
            <a href="{{ route('campaigns.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Campaign
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($campaigns->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No campaigns yet"
                        description="Start your first email outreach by creating a campaign."
                        :action="route('campaigns.create')"
                        actionText="Create Campaign"
                    />
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($campaigns as $campaign)
                        <x-card class="hover:shadow-xl transition-all duration-300 border-gray-100 group overflow-hidden">
                            <!-- Campaign Visual Header -->
                            <div class="h-24 bg-gradient-to-br from-gray-900 to-gray-800 p-4 relative">
                                <div class="absolute top-4 right-4">
                                    <x-badge :type="match($campaign->status) {
                                        'sending' => 'warning',
                                        'completed' => 'success',
                                        'paused' => 'neutral',
                                        'stopped' => 'error',
                                        default => 'neutral'
                                    }">
                                        {{ strtoupper($campaign->status) }}
                                    </x-badge>
                                </div>
                                <div class="mt-6">
                                    <h3 class="text-white font-bold text-lg truncate pr-16">{{ $campaign->name }}</h3>
                                    <p class="text-white/50 text-xs truncate">{{ $campaign->subject }}</p>
                                </div>
                            </div>

                            <div class="p-5">
                                @if($campaign->status === 'sending' || $campaign->status === 'paused')
                                    <div class="mb-4">
                                        <div class="flex justify-between text-xs font-semibold mb-1">
                                            <span class="text-gray-500">Progress</span>
                                            <span class="text-violet-600">{{ $campaign->getProgressPercentage() }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-violet-600 h-1.5 rounded-full" style="width: {{ $campaign->getProgressPercentage() }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="p-2 bg-gray-50 rounded-xl">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase block leading-none mb-1">Recipients</span>
                                        <span class="text-sm font-bold text-gray-900">{{ number_format($campaign->total_recipients) }}</span>
                                    </div>
                                    <div class="p-2 bg-gray-800 rounded-xl">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase block leading-none mb-1 text-white/50">Delivered</span>
                                        <span class="text-sm font-bold text-white">{{ number_format($campaign->sent_count) }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $campaign->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('campaigns.duplicate', $campaign) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="Duplicate" class="p-2 text-gray-400 hover:text-violet-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex items-center px-4 py-2 bg-gray-50 text-gray-900 text-xs font-bold rounded-lg hover:bg-violet-600 hover:text-white transition-all duration-200">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
