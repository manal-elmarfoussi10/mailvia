<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Edit Automation: {{ $automation->name }}</h2>
            <a href="{{ route('automations.index') }}" class="text-xs font-bold text-gray-400 hover:text-gray-900 uppercase tracking-widest">Back to List</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('automations.update', $automation) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-8">
                        <div>
                            <x-input-label for="name" value="Automation Name" class="text-xs font-black text-gray-400 uppercase tracking-wider mb-2" />
                            <input id="name" type="text" name="name" value="{{ $automation->name }}" required autofocus 
                                   class="block w-full text-2xl font-black border-none bg-transparent p-0 focus:ring-0 placeholder-gray-200" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
                            <div>
                                <x-input-label for="trigger_event" value="Trigger Event" class="text-xs font-bold text-gray-700 mb-2" />
                                <select name="trigger_event" id="trigger_event" class="w-full rounded-xl border-gray-200 text-sm font-semibold">
                                    <option value="contact_created" {{ $automation->trigger_event == 'contact_created' ? 'selected' : '' }}>New Contact Created</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="template_id" value="Email Template" class="text-xs font-bold text-gray-700 mb-2" />
                                <select name="template_id" id="template_id" class="w-full rounded-xl border-gray-200 text-sm font-semibold">
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" {{ $automation->template_id == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="provider_id" value="Delivery Provider" class="text-xs font-bold text-gray-700 mb-2" />
                                <select name="provider_id" id="provider_id" class="w-full rounded-xl border-gray-200 text-sm font-semibold">
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}" {{ $automation->provider_id == $provider->id ? 'selected' : '' }}>{{ $provider->name }} ({{ $provider->type }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="sender_id" value="Sender Identity" class="text-xs font-bold text-gray-700 mb-2" />
                                <select name="sender_id" id="sender_id" class="w-full rounded-xl border-gray-200 text-sm font-semibold">
                                    @foreach($senders as $sender)
                                        <option value="{{ $sender->id }}" {{ $automation->sender_id == $sender->id ? 'selected' : '' }}>{{ $sender->from_name }} ({{ $sender->from_email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ $automation->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <span class="ml-2 text-sm font-semibold text-gray-900">Automation is active</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                            <x-button-primary size="lg" class="shadow-xl shadow-indigo-100">
                                Update Automation
                            </x-button-primary>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
