<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Edit Seed List: {{ $seedList->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form action="{{ route('seed-lists.update', $seedList) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <x-input-label for="name" value="List Name" />
                        <x-text-input id="name" name="name" class="block mt-1 w-full" type="text" value="{{ $seedList->name }}" required />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="emails" value="Email Addresses" />
                        <textarea id="emails" name="emails" rows="10" 
                                  class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-violet-600 focus:ring-violet-600 text-gray-900"
                                  placeholder="Enter emails one per line or separated by commas">{{ $emails }}</textarea>
                    </div>

                    <div class="flex items-center justify-end mt-8">
                        <a href="{{ route('seed-lists.index') }}" class="mr-6 text-sm font-bold text-gray-500 hover:text-gray-900 transition">Cancel</a>
                        <x-button-primary>Update Seed List</x-button-primary>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
