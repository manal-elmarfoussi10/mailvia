<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Add Sender Identity</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('senders.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Sender Name" class="text-gray-900 font-semibold" />
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="John Doe" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="email" value="Email Address" class="text-gray-900 font-semibold" />
                        <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required placeholder="sender@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="reply_to" value="Reply-To Email (Optional)" class="text-gray-900 font-semibold" />
                        <x-text-input id="reply_to" class="block mt-2 w-full" type="email" name="reply_to" :value="old('reply_to')" placeholder="replies@example.com" />
                        <x-input-error :messages="$errors->get('reply_to')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="domain_id" value="Verified Domain (Recommended)" class="text-gray-900 font-semibold" />
                        <select id="domain_id" name="domain_id" class="block mt-2 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm italic text-gray-900">
                            <option value="">Select a verified domain (or leave blank)</option>
                            @foreach($domains as $domain)
                                <option value="{{ $domain->id }}" {{ old('domain_id') == $domain->id ? 'selected' : '' }}>{{ $domain->domain }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 italic">Linking a verified domain helps bypass strict SPF/DKIM checks.</p>
                        <x-input-error :messages="$errors->get('domain_id')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="provider_id" value="Provider" class="text-gray-900 font-semibold" />
                        <select id="provider_id" name="provider_id" class="block mt-2 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm text-gray-900" required>
                            <option value="">Select a provider</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                            @endforeach
                        </select>
                        @if($providers->isEmpty())
                            <p class="mt-2 text-xs text-rose-600 font-medium">
                                No providers found. <a href="{{ route('providers.create') }}" class="underline font-bold">Create your first provider</a> before adding a sender.
                            </p>
                        @endif
                        <x-input-error :messages="$errors->get('provider_id')" class="mt-2" />
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">DNS Configuration Required</h4>
                            <p class="text-sm text-blue-700">After creating this sender, you'll need to configure SPF, DKIM, and DMARC records for your domain to ensure deliverability.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('senders.index') }}" class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <x-button-primary>
                            Create Sender
                        </x-button-primary>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
