<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Global Company Settings</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- General Details --}}
                    <x-card title="Company Profile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" value="Company Name" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $company->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>
                    </x-card>

                    {{-- Default Senders & Throttling --}}
                    <x-card title="Sending Defaults & Throttling">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="default_provider" value="Default Provider" />
                                <select id="default_provider" name="settings[default_provider_id]" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm text-gray-900">
                                    <option value="">System Select</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}" {{ ($company->settings['default_provider_id'] ?? '') == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="default_sender" value="Default Sender Identity" />
                                <select id="default_sender" name="settings[default_sender_id]" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm text-gray-900">
                                    <option value="">System Select</option>
                                    @foreach($senders as $sender)
                                        <option value="{{ $sender->id }}" {{ ($company->settings['default_sender_id'] ?? '') == $sender->id ? 'selected' : '' }}>
                                            {{ $sender->name }} ({{ $sender->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="hourly_limit" value="Hourly Sending Limit" />
                                <x-text-input id="hourly_limit" name="settings[hourly_limit]" type="number" class="mt-1 block w-full" :value="old('settings.hourly_limit', $company->settings['hourly_limit'] ?? 0)" />
                                <p class="mt-1 text-xs text-gray-500 italic">0 for no limit.</p>
                            </div>
                            <div>
                                <x-input-label for="daily_limit" value="Daily Sending Limit" />
                                <x-text-input id="daily_limit" name="settings[daily_limit]" type="number" class="mt-1 block w-full" :value="old('settings.daily_limit', $company->settings['daily_limit'] ?? 0)" />
                                <p class="mt-1 text-xs text-gray-500 italic">0 for no limit.</p>
                            </div>
                        </div>
                    </x-card>

                    {{-- Tracking & Branding --}}
                    <x-card title="Tracking & Branding">
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="tracking_enabled" name="settings[tracking_enabled]" type="checkbox" value="1" class="focus:ring-violet-500 h-4 w-4 text-violet-600 border-gray-300 rounded" {{ ($company->settings['tracking_enabled'] ?? true) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="tracking_enabled" class="font-medium text-gray-700">Enable Open & Click Tracking</label>
                                    <p class="text-gray-500 italic">Global toggle for engagement tracking on all campaigns.</p>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="branding_footer" value="Branding Footer (HTML/Text)" />
                                <textarea id="branding_footer" name="settings[branding_footer]" rows="4" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-xl shadow-sm text-gray-900">{{ old('settings.branding_footer', $company->settings['branding_footer'] ?? '') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500 italic">This will be appended to the bottom of your emails if the template includes the [[footer]] tag.</p>
                            </div>
                        </div>
                    </x-card>

                    <div class="flex justify-end">
                        <x-button-primary type="submit">Save Settings</x-button-primary>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
