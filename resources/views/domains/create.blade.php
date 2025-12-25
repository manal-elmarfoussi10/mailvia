<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Add Verified Domain</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card title="Domain Information">
                <form action="{{ route('domains.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="domain" value="Domain Name" />
                        <x-text-input id="domain" name="domain" type="text" class="mt-1 block w-full" placeholder="example.com" required />
                        <p class="mt-1 text-sm text-gray-500 italic">Example: example.com (without http:// or https://)</p>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                        <x-button-secondary href="{{ route('domains.index') }}">Cancel</x-button-secondary>
                        <x-button-primary type="submit">Add Domain</x-button-primary>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
