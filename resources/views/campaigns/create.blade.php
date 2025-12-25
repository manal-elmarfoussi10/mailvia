<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Create New Campaign</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card class="p-8">
                <div class="mb-8" x-data="{ nameActive: true }">
                    <x-input-label for="campaign_name" value="Step 0: Campaign Name" />
                    <input type="text" id="campaign_name" placeholder="E.g. Summer Sale 2025" 
                           class="mt-1 block w-full text-2xl font-bold bg-transparent border-none focus:ring-0 placeholder-gray-300"
                           @input="$nextTick(() => { document.querySelector('[name=name]').value = $el.value })">
                </div>

                @include('campaigns.wizard', ['action' => route('campaigns.store')])
            </x-card>
        </div>
    </div>
</x-app-layout>
