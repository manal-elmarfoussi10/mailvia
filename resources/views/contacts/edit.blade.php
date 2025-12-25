<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Edit Contact</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('contacts.update', $contact) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="first_name" value="First Name" class="text-gray-900 font-semibold" />
                            <x-text-input id="first_name" class="block mt-2 w-full" type="text" name="first_name" :value="old('first_name', $contact->first_name)" />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last_name" value="Last Name" class="text-gray-900 font-semibold" />
                            <x-text-input id="last_name" class="block mt-2 w-full" type="text" name="last_name" :value="old('last_name', $contact->last_name)" />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="email" value="Email Address" class="text-gray-900 font-semibold" />
                        <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email', $contact->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="tags" value="Tags (comma-separated)" class="text-gray-900 font-semibold" />
                        <x-text-input id="tags" class="block mt-2 w-full" type="text" name="tags" :value="old('tags', is_array($contact->tags) ? implode(', ', $contact->tags) : '')" />
                        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="status" value="Status" class="text-gray-900 font-semibold" />
                        <select id="status" name="status" class="block mt-2 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm">
                            <option value="subscribed" {{ $contact->status == 'subscribed' ? 'selected' : '' }}>Subscribed</option>
                            <option value="unsubscribed" {{ $contact->status == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                            <option value="bounced" {{ $contact->status == 'bounced' ? 'selected' : '' }}>Bounced</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete this contact?');">
                            @csrf
                            @method('DELETE')
                            <x-button-danger type="submit">
                                Delete Contact
                            </x-button-danger>
                        </form>

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('contacts.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-button-primary>
                                Update Contact
                            </x-button-primary>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
