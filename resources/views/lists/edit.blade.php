<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Edit List</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('lists.update', $list) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="List Name" class="text-gray-900 font-semibold" />
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" :value="old('name', $list->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <form action="{{ route('lists.destroy', $list) }}" method="POST" onsubmit="return confirm('Delete this list? Contacts will not be deleted.');">
                            @csrf
                            @method('DELETE')
                            <x-button-danger type="submit">
                                Delete List
                            </x-button-danger>
                        </form>

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('lists.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-button-primary>
                                Update List
                            </x-button-primary>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
