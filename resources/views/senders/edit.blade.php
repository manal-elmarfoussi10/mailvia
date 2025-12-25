<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Edit Sender Identity</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('senders.update', $sender) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Sender Name" class="text-gray-900 font-semibold" />
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" :value="old('name', $sender->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="email" value="Email Address" class="text-gray-900 font-semibold" />
                        <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email', $sender->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="reply_to" value="Reply-To Email (Optional)" class="text-gray-900 font-semibold" />
                        <x-text-input id="reply_to" class="block mt-2 w-full" type="email" name="reply_to" :value="old('reply_to', $sender->reply_to)" />
                        <x-input-error :messages="$errors->get('reply_to')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="domain_id" value="Verified Domain" class="text-gray-900 font-semibold" />
                        <select id="domain_id" name="domain_id" class="block mt-2 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm text-gray-900">
                            <option value="">Select a verified domain (or leave blank)</option>
                            @foreach($domains as $domain)
                                <option value="{{ $domain->id }}" {{ old('domain_id', $sender->domain_id) == $domain->id ? 'selected' : '' }}>{{ $domain->domain }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('domain_id')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="provider_id" value="Provider" class="text-gray-900 font-semibold" />
                        <select id="provider_id" name="provider_id" class="block mt-2 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm text-gray-900" required>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('provider_id', $sender->provider_id) == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('provider_id')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <form action="{{ route('senders.destroy', $sender) }}" method="POST" onsubmit="return confirm('Delete this sender?');">
                            @csrf
                            @method('DELETE')
                            <x-button-danger type="submit">
                                Delete Sender
                            </x-button-danger>
                        </form>

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('senders.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-button-primary>
                                Update Sender
                            </x-button-primary>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
