<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Email Templates</h2>
            <a href="{{ route('templates.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Template
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($templates->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No templates yet"
                        description="Create reusable email templates with dynamic variables for your campaigns."
                        :action="route('templates.create')"
                        actionText="Create Your First Template"
                    />
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($templates as $template)
                        <x-card class="p-6 hover:shadow-lg transition-all duration-200">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($template->subject, 50) }}</p>
                            </div>

                            <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="text-xs text-gray-600 font-mono line-clamp-3">
                                    {{ Str::limit(strip_tags($template->content_html), 100) }}
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-200">
                                <a href="{{ route('templates.show', $template) }}" class="text-sm font-medium text-violet-600 hover:text-violet-700 transition">
                                    Preview
                                </a>
                                
                                <a href="{{ route('templates.edit', $template) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                                    Edit
                                </a>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
