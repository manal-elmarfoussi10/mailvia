<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Edit Company</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('companies.update', $company) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Company Name" class="text-gray-900 font-semibold" />
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" :value="old('name', $company->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="domain" value="Domain (Optional)" class="text-gray-900 font-semibold" />
                        <x-text-input id="domain" class="block mt-2 w-full" type="text" name="domain" :value="old('domain', $company->domain)" />
                        <x-input-error :messages="$errors->get('domain')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <form action="{{ route('companies.destroy', $company) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this company? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <x-button-danger type="submit">
                                Delete Company
                            </x-button-danger>
                        </form>

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('companies.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-button-primary>
                                Update Company
                            </x-button-primary>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
