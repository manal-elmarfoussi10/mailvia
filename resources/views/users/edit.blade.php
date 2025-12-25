<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Edit User</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card class="p-8">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Name" class="text-gray-900 font-semibold" />
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" :value="old('name', $user->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="email" value="Email" class="text-gray-900 font-semibold" />
                        <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password (Optional)</h3>
                        
                        <div>
                            <x-input-label for="password" value="New Password" class="text-gray-900 font-semibold" />
                            <x-text-input id="password" class="block mt-2 w-full" type="password" name="password" />
                            <p class="mt-2 text-sm text-gray-500">Leave blank to keep current password</p>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="password_confirmation" value="Confirm New Password" class="text-gray-900 font-semibold" />
                            <x-text-input id="password_confirmation" class="block mt-2 w-full" type="password" name="password_confirmation" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('users.index') }}" class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <x-button-primary>
                            Update User
                        </x-button-primary>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
