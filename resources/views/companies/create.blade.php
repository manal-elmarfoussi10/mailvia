<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Create Company</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('companies.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Company Name" class="text-gray-900 font-semibold" />
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="domain" value="Domain (Optional)" class="text-gray-900 font-semibold" />
                        <x-text-input id="domain" class="block mt-2 w-full" type="text" name="domain" :value="old('domain')" placeholder="example.com" />
                        <p class="mt-2 text-sm text-gray-500">Your company's primary domain for email sending.</p>
                        <x-input-error :messages="$errors->get('domain')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('companies.index') }}" class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <x-button-primary>
                            Create Company
                        </x-button-primary>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
