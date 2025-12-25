<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Edit Provider</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('providers.update', $provider) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Provider Name" class="text-gray-900 font-semibold" />
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" :value="old('name', $provider->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="type" value="Provider Type" class="text-gray-900 font-semibold" />
                        <select id="type" name="type" class="block mt-2 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm">
                            <option value="smtp" {{ $provider->type == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="ses" {{ $provider->type == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                            <option value="mailgun" {{ $provider->type == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="postmark" {{ $provider->type == 'postmark' ? 'selected' : '' }}>Postmark</option>
                        </select>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Credentials</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="host" value="Host / Region" class="text-gray-900 font-semibold" />
                                <x-text-input id="host" class="block mt-2 w-full" type="text" name="credentials[host]" :value="old('credentials.host', $provider->credentials['host'] ?? '')" required />
                            </div>
                            <div>
                                <x-input-label for="port" value="Port" class="text-gray-900 font-semibold" />
                                <x-text-input id="port" class="block mt-2 w-full" type="text" name="credentials[port]" :value="old('credentials.port', $provider->credentials['port'] ?? '')" />
                            </div>
                            <div>
                                <x-input-label for="username" value="Username / API Key" class="text-gray-900 font-semibold" />
                                <x-text-input id="username" class="block mt-2 w-full" type="text" name="credentials[username]" :value="old('credentials.username', $provider->credentials['username'] ?? '')" required />
                            </div>
                            <div>
                                <x-input-label for="password" value="Password / Secret" class="text-gray-900 font-semibold" />
                                <x-text-input id="password" class="block mt-2 w-full" type="password" name="credentials[password]" placeholder="Leave blank to keep current" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <form action="{{ route('providers.destroy', $provider) }}" method="POST" onsubmit="return confirm('Delete this provider?');">
                            @csrf
                            @method('DELETE')
                            <x-button-danger type="submit">
                                Delete Provider
                            </x-button-danger>
                        </form>

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('providers.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-button-primary>
                                Update Provider
                            </x-button-primary>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
