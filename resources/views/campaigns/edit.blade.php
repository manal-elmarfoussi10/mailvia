<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Edit Campaign: {{ $campaign->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card class="p-8">
                @include('campaigns.wizard', [
                    'action' => route('campaigns.update', $campaign),
                    'method' => 'PUT',
                    'campaign' => $campaign
                ])
            </x-card>
        </div>
    </div>
</x-app-layout>
