<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Team Members</h2>
            <a href="{{ route('users.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($users->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No team members"
                        description="Add team members to collaborate on your email campaigns."
                        :action="route('users.create')"
                        actionText="Add Your First User"
                    />
                </x-card>
            @else
                <x-card>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($users as $user)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end space-x-3">
                                                <a href="{{ route('users.edit', $user) }}" class="text-sm font-medium text-violet-600 hover:text-violet-700 transition">
                                                    Edit
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Remove this user?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700 transition">
                                                            Remove
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
