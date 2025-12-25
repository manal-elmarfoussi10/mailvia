<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">{{ $template->name }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Subject Line</h3>
                    <p class="text-lg text-gray-900">{{ $template->subject }}</p>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="showTab('html')" id="html-tab" class="tab-button border-b-2 border-violet-600 py-4 px-1 text-sm font-medium text-violet-600">
                            HTML Preview
                        </button>
                        <button onclick="showTab('text')" id="text-tab" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Plain Text
                        </button>
                    </nav>
                </div>

                <!-- HTML Preview -->
                <div id="html-content" class="tab-content">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 overflow-auto max-h-96">
                        {!! $template->content_html !!}
                    </div>
                </div>

                <!-- Text Preview -->
                <div id="text-content" class="tab-content hidden">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                        <pre class="text-sm text-gray-700 whitespace-pre-wrap font-mono">{{ $template->content_text }}</pre>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                    <a href="{{ route('templates.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        Back to Templates
                    </a>
                    <a href="{{ route('templates.edit', $template) }}">
                        <x-button-primary>
                            Edit Template
                        </x-button-primary>
                    </a>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function showTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(el => {
                el.classList.remove('border-violet-600', 'text-violet-600');
                el.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab
            document.getElementById(tab + '-content').classList.remove('hidden');
            document.getElementById(tab + '-tab').classList.remove('border-transparent', 'text-gray-500');
            document.getElementById(tab + '-tab').classList.add('border-violet-600', 'text-violet-600');
        }
    </script>
</x-app-layout>
