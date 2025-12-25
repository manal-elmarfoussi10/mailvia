<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Map Columns') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4 text-gray-600">Map the columns from your file <strong>{{ $import->file_name }}</strong> to the contact fields below.</p>
                    
                    <form method="POST" action="{{ route('imports.map', $import) }}">
                        @csrf

                        <div class="grid grid-cols-2 gap-4 mb-4 font-bold border-b pb-2">
                             <div>System Field</div>
                             <div>File Column</div>
                        </div>

                        @foreach($fields as $fieldKey => $fieldLabel)
                            <div class="grid grid-cols-2 gap-4 mb-4 items-center">
                                <div>
                                    <label for="map_{{ $fieldKey }}" class="block text-sm font-medium text-gray-700">{{ $fieldLabel }}</label>
                                </div>
                                <div>
                                    <select name="mapping[{{ $fieldKey }}]" id="map_{{ $fieldKey }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Ignore --</option>
                                        @foreach($headings as $heading)
                                            <option value="{{ $heading }}" {{ (strtolower($heading) == strtolower($fieldKey) || str_contains(strtolower($heading), strtolower($fieldKey))) ? 'selected' : '' }}>
                                                {{ $heading }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-4">
                                {{ __('Start Import') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
