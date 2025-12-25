<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Edit Segment: {{ $segment->name }}</h2>
            <a href="{{ route('segments.index') }}" class="text-xs font-bold text-gray-400 hover:text-gray-900 uppercase tracking-widest">Back to Segments</a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        name: '{{ $segment->name }}',
        criteria: @json($segment->criteria ?? []),
        count: '...',
        addField() {
            this.criteria.push({ field: 'status', operator: '=', value: '' });
            this.updateCount();
        },
        removeField(index) {
            this.criteria.splice(index, 1);
            this.updateCount();
        },
        async updateCount() {
            this.count = '...';
            try {
                const response = await fetch('{{ route('segments.count') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ criteria: this.criteria })
                });
                const data = await response.json();
                this.count = data.count;
            } catch (e) {
                this.count = 'Error';
            }
        }
    }" x-init="if(criteria.length === 0) criteria.push({ field: 'status', operator: '=', value: 'subscribed' }); updateCount()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Builder -->
                <div class="lg:col-span-2">
                    <x-card class="p-8">
                        <form method="POST" action="{{ route('segments.update', $segment) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-8">
                                <x-input-label for="name" value="Segment Name" class="text-xs font-black text-gray-400 uppercase tracking-wider mb-2" />
                                <input id="name" type="text" name="name" x-model="name" required autofocus 
                                       class="block w-full text-2xl font-black border-none bg-transparent p-0 focus:ring-0 placeholder-gray-200" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="space-y-4">
                                <template x-for="(item, index) in criteria" :key="index">
                                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 animate-in fade-in slide-in-from-top-2 duration-200">
                                        <div class="flex-1 grid grid-cols-3 gap-3">
                                            <select :name="'criteria['+index+'][field]'" x-model="item.field" @change="updateCount()"
                                                    class="text-xs font-bold rounded-xl border-gray-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="status">Status</option>
                                                <option value="tags">Tags</option>
                                                <option value="country">Country</option>
                                                <option value="last_opened_at">Last Engagement</option>
                                                <option value="opens_count">Total Opens</option>
                                                <option value="clicks_count">Total Clicks</option>
                                            </select>
                                            
                                            <select :name="'criteria['+index+'][operator]'" x-model="item.operator" @change="updateCount()"
                                                    class="text-xs font-bold rounded-xl border-gray-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="=" x-show="item.field !== 'tags'">Is</option>
                                                <option value="!=" x-show="item.field !== 'tags'">Is Not</option>
                                                <option value="contains" x-show="item.field === 'tags'">Contains</option>
                                                <option value=">" x-show="item.field !== 'status' && item.field !== 'tags'">After / Greater Than</option>
                                                <option value="<" x-show="item.field !== 'status' && item.field !== 'tags'">Before / Less Than</option>
                                            </select>

                                            <div class="relative">
                                                <template x-if="item.field === 'status'">
                                                    <select :name="'criteria['+index+'][value]'" x-model="item.value" @change="updateCount()"
                                                            class="w-full text-xs font-bold rounded-xl border-gray-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="subscribed">Subscribed</option>
                                                        <option value="unsubscribed">Unsubscribed</option>
                                                        <option value="bounced">Bounced</option>
                                                    </select>
                                                </template>
                                                <template x-if="item.field !== 'status'">
                                                    <input type="text" :name="'criteria['+index+'][value]'" x-model="item.value" @input.debounce.500ms="updateCount()"
                                                           placeholder="Value..."
                                                           class="w-full text-xs font-bold rounded-xl border-gray-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                                </template>
                                            </div>
                                        </div>

                                        <button type="button" @click="removeField(index)" x-show="criteria.length > 1"
                                                class="p-2 text-gray-400 hover:text-rose-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <button type="button" @click="addField()" 
                                    class="mt-6 flex items-center text-xs font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-900 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                Add Rule
                            </button>

                            <div class="mt-12 pt-8 border-t border-gray-100 flex items-center justify-between">
                                <button type="button" @click="$dispatch('open-modal', 'confirm-segment-deletion')"
                                        class="text-xs font-black text-rose-600 uppercase tracking-widest hover:text-rose-900 transition">
                                    Delete Segment
                                </button>

                                <x-button-primary size="lg" class="shadow-xl shadow-indigo-100">
                                    Update Dynamic Segment
                                </x-button-primary>
                            </div>
                        </form>
                    </x-card>
                </div>

                <!-- Preview Sidebar -->
                <div class="space-y-6">
                    <x-card class="p-6 bg-gradient-to-br from-indigo-600 to-violet-700 text-white overflow-hidden relative">
                        <div class="absolute -right-4 -top-4 opacity-10">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div class="relative">
                            <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Live Estimate</span>
                            <div class="flex items-baseline gap-2 mt-2">
                                <span class="text-5xl font-black" x-text="count"></span>
                                <span class="text-sm font-bold opacity-80" x-show="count !== '...'">Contacts</span>
                            </div>
                            <p class="text-[10px] mt-4 opacity-60 leading-relaxed">
                                This count is a real-time estimate based on your current rules. Dynamic segments update automatically as your contacts change.
                            </p>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="confirm-segment-deletion" focusable>
        <form method="post" action="{{ route('segments.destroy', $segment) }}" class="p-6">
            @csrf
            @method('delete')
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tighter">Delete Segment?</h2>
            <p class="mt-1 text-sm text-gray-600 font-medium">This segment will be permanently removed. Campaigns using this segment will no longer be able to resolve its contacts.</p>
            <div class="mt-6 flex justify-end gap-3">
                <x-button-secondary x-on:click="$dispatch('close')">Cancel</x-button-secondary>
                <x-button-danger>Delete Permanently</x-button-danger>
            </div>
        </form>
    </x-modal>
</x-app-layout>
