<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Template') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('templates.update', $template) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Template Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $template->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Subject -->
                        <div class="mt-4">
                            <x-input-label for="subject" :value="__('Email Subject')" />
                            <x-text-input id="subject" class="block mt-1 w-full" type="text" name="subject" :value="old('subject', $template->subject)" />
                            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                        </div>

                        <!-- HTML Content -->
                        <div class="mt-4">
                            <x-input-label for="content_html" :value="__('HTML Content')" />
                            <textarea id="content_html" name="content_html" rows="10" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono text-sm text-gray-900">{{ old('content_html', $template->content_html) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Use @{{ variable }} for dynamic content (e.g., @{{ first_name }}, @{{ email }})</p>
                            <x-input-error :messages="$errors->get('content_html')" class="mt-2" />
                        </div>

                        <!-- Text Content -->
                        <div class="mt-4">
                            <x-input-label for="content_text" :value="__('Plain Text Content')" />
                            <textarea id="content_text" name="content_text" rows="6" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-900">{{ old('content_text', $template->content_text) }}</textarea>
                            <x-input-error :messages="$errors->get('content_text')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Update Template') }}
                            </x-primary-button>
                        </div>
                    </form>
                    
                    <div class="mt-6 border-t pt-6">
                        <form action="{{ route('templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Delete this template?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete Template</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
