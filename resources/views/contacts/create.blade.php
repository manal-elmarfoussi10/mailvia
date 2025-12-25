<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Add Contact</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('contacts.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="first_name" value="First Name" class="text-gray-900 font-semibold" />
                            <x-text-input id="first_name" class="block mt-2 w-full" type="text" name="first_name" :value="old('first_name')" />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last_name" value="Last Name" class="text-gray-900 font-semibold" />
                            <x-text-input id="last_name" class="block mt-2 w-full" type="text" name="last_name" :value="old('last_name')" />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="email" value="Email Address" class="text-gray-900 font-semibold" />
                        <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="tags" value="Tags (comma-separated)" class="text-gray-900 font-semibold" />
                        <x-text-input id="tags" class="block mt-2 w-full" type="text" name="tags" :value="old('tags')" placeholder="customer, vip, newsletter" />
                        <p class="mt-2 text-sm text-gray-500">Separate tags with commas</p>
                        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('contacts.index') }}" class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <x-button-primary>
                            Create Contact
                        </x-button-primary>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
