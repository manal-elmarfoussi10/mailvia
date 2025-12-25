<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">New Inbox Placement Test</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form action="{{ route('inbox-tests.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        <div>
                            <x-input-label for="name" value="Test Name" />
                            <x-text-input id="name" name="name" class="block mt-1 w-full" type="text" placeholder="E.g. Summer Promo Placement" required />
                        </div>

                        <div>
                            <x-input-label value="Select Seed List" />
                            <select name="seed_list_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-violet-600 focus:ring-violet-600" required>
                                <option value="">Choose a seed list</option>
                                @foreach($seedLists as $list)
                                    <option value="{{ $list->id }}">{{ $list->name }} ({{ $list->emails_count }} emails)</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-gray-500">
                                Don't have a list? <a href="{{ route('seed-lists.create') }}" class="text-violet-600 font-bold">Create one now</a>.
                            </p>
                        </div>

                        <div>
                            <x-input-label value="Template to Test" />
                            <select name="template_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-violet-600 focus:ring-violet-600">
                                <option value="">Select a template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="subject" value="Email Subject" />
                            <x-text-input id="subject" name="subject" class="block mt-1 w-full" type="text" placeholder="Subject for this test" required />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('inbox-tests.index') }}" class="mr-6 text-sm font-bold text-gray-500 hover:text-gray-900 transition">Cancel</a>
                        <x-button-primary>Create Test</x-button-primary>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
