<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Import Contacts</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('imports.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="file" value="Upload File" class="text-gray-900 font-semibold" />
                        <input 
                            type="file" 
                            id="file" 
                            name="file" 
                            accept=".csv,.xlsx" 
                            required
                            class="block mt-2 w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-violet-600 file:to-cyan-500 file:text-white hover:file:from-violet-700 hover:file:to-cyan-600 file:cursor-pointer"
                        />
                        <p class="mt-2 text-sm text-gray-500">Supported formats: CSV, XLSX (Max 10MB)</p>
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">File Requirements</h4>
                            <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                                <li>First row should contain column headers</li>
                                <li>Required column: email</li>
                                <li>Optional columns: first_name, last_name, tags</li>
                                <li>Duplicate emails will be updated, not duplicated</li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('imports.index') }}" class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <x-button-primary>
                            Upload & Continue
                        </x-button-primary>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
