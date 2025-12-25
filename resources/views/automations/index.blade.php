<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Automations</h2>
            <a href="{{ route('automations.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Automation
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($automations->isEmpty())
                <x-card class="p-12">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-2">Engage contacts automatically</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-8">Send welcome emails or sequences triggered by specific events like new signups.</p>
                        <a href="{{ route('automations.create') }}">
                            <x-button-primary>Create Your First Automation</x-button-primary>
                        </a>
                    </div>
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($automations as $automation)
                        <x-card class="p-6 relative group overflow-hidden border-none shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="absolute top-0 right-0 p-4">
                                <span class="px-2 py-1 text-[10px] font-black uppercase tracking-widest rounded-full {{ $automation->is_active ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $automation->is_active ? 'Active' : 'Paused' }}
                                </span>
                            </div>

                            <div class="mb-6">
                                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-900 group-hover:text-indigo-600 transition">{{ $automation->name }}</h3>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Trigger: {{ str_replace('_', ' ', $automation->trigger_event) }}</p>
                            </div>

                            <div class="space-y-3 mb-8">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span class="text-gray-600 font-medium">{{ $automation->template->name }}</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="text-gray-600 font-medium">{{ $automation->sender->from_name }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                <a href="{{ route('automations.edit', $automation) }}" class="text-xs font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-900 transition">
                                    Configure
                                </a>
                                
                                <form action="{{ route('automations.destroy', $automation) }}" method="POST" onsubmit="return confirm('Delete this automation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs font-black text-gray-300 uppercase tracking-widest hover:text-rose-600 transition">Delete</button>
                                </form>
                            </div>
                        </x-card>
                    @endforeach
                </div>
                
                <div class="mt-8">
                    {{ $automations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
