<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Add New Provider</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('providers.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Provider Name" class="text-gray-900 font-semibold" />
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="My SMTP Provider" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="type" value="Provider Type" class="text-gray-900 font-semibold" />
                        <select id="type" name="type" class="block mt-2 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm">
                            <option value="smtp">SMTP</option>
                            <option value="ses">Amazon SES</option>
                            <option value="mailgun">Mailgun</option>
                            <option value="postmark">Postmark</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200" x-data="{ type: 'smtp' }">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Credentials</h4>
                        
                        <!-- Sync Type Select -->
                        <script>
                            document.getElementById('type').addEventListener('change', function(e) {
                                this.closest('[x-data]').__x.$data.type = e.target.value;
                            });
                        </script>

                        <!-- SMTP Fields -->
                        <div x-show="type === 'smtp'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="host" value="SMTP Host" />
                                <x-text-input id="host" class="block mt-2 w-full" type="text" name="credentials[host]" placeholder="smtp.example.com" />
                            </div>
                            <div>
                                <x-input-label for="port" value="SMTP Port" />
                                <x-text-input id="port" class="block mt-2 w-full" type="text" name="credentials[port]" placeholder="587" />
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <x-input-label for="encryption" value="Encryption" />
                                <select id="encryption" name="credentials[encryption]" class="block mt-2 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm">
                                    <option value="tls">TLS (Recommended)</option>
                                    <option value="ssl">SSL (Port 465)</option>
                                    <option value="">None (Not Secure)</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="username" value="Username" />
                                <x-text-input id="username" class="block mt-2 w-full" type="text" name="credentials[username]" />
                            </div>
                            <div>
                                <x-input-label for="password" value="Password" />
                                <x-text-input id="password" class="block mt-2 w-full" type="password" name="credentials[password]" />
                            </div>
                        </div>

                        <!-- SES Fields -->
                        <div x-show="type === 'ses'" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <x-input-label for="region" value="AWS Region" />
                                <x-text-input id="region" class="block mt-2 w-full" type="text" name="credentials[region]" placeholder="us-east-1" />
                            </div>
                            <div>
                                <x-input-label for="key" value="Access Key ID" />
                                <x-text-input id="key" class="block mt-2 w-full" type="text" name="credentials[key]" placeholder="AKIA..." />
                            </div>
                            <div>
                                <x-input-label for="secret" value="Secret Access Key" />
                                <x-text-input id="secret" class="block mt-2 w-full" type="password" name="credentials[secret]" />
                            </div>
                        </div>

                        <!-- Mailgun Fields -->
                        <div x-show="type === 'mailgun'" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="domain" value="Mailgun Domain" />
                                <x-text-input id="domain" class="block mt-2 w-full" type="text" name="credentials[domain]" placeholder="mg.yourdomain.com" />
                            </div>
                            <div>
                                <x-input-label for="endpoint" value="Endpoint" />
                                <x-text-input id="endpoint" class="block mt-2 w-full" type="text" name="credentials[endpoint]" placeholder="api.mailgun.net" />
                            </div>
                            <div class="col-span-2">
                                <x-input-label for="mg_secret" value="API Key (Secret)" />
                                <x-text-input id="mg_secret" class="block mt-2 w-full" type="password" name="credentials[secret]" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('providers.index') }}" class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <x-button-primary>
                            Save Provider
                        </x-button-primary>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
