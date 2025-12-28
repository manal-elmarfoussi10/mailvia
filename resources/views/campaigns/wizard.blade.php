<div x-data="{ 
    step: 1, 
    totalSteps: 6,
    formData: {
        name: "{{ old('name', $campaign->name ?? '') }}",
        subject: "{{ old('subject', $campaign->subject ?? '') }}",
        preheader: "{{ old('preheader', $campaign->preheader ?? '') }}",
        template_id: "{{ old('template_id', $campaign->template_id ?? '') }}",
        from_name: "{{ old('from_name', $campaign->from_name ?? config('mail.from.name')) }}",
        from_email: "{{ old('from_email', $campaign->from_email ?? config('mail.from.address')) }}",
        audience_type: "{{ old('audience.type', $campaign->audience['type'] ?? 'all') }}",
        audience_ids: @json(old('audience.ids', $campaign->audience['ids'] ?? [])),
        exclude_suppressed: {{ old('audience.exclude_suppressed', $campaign->audience['exclude_suppressed'] ?? 1) ? 'true' : 'false' }},
        throttle_rate: {{ old('throttle_rate', $campaign->throttle_rate ?? 10) }},
        throttle_concurrency: {{ old('throttle_concurrency', $campaign->throttle_concurrency ?? 3) }},
        scheduled_at: "{{ old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}",
        is_ab_test: {{ old('is_ab_test', $campaign->is_ab_test ?? false) ? 'true' : 'false' }},
        ab_variations: @json(old('ab_variations', $campaign->ab_variations ?? [])),
        ab_winner_criteria: "{{ old('ab_winner_criteria', $campaign->ab_winner_criteria ?? 'open_rate') }}",
        ab_test_duration: {{ old('ab_test_duration', $campaign->ab_test_duration ?? 24) }},
        ab_test_sample_size: {{ old('ab_test_sample_size', $campaign->ab_test_sample_size ?? 25) }},
    },
    nextStep() { if(this.step < this.totalSteps) this.step++; },
    prevStep() { if(this.step > 1) this.step--; }
}">
    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            @foreach(['Audience', 'Content', 'Provider', 'Throttling', 'Schedule', 'Review'] as $i => $label)
                <div class="flex flex-col items-center">
                    <div :class="step > {{ $i + 1 }} ? 'bg-emerald-500' : (step == {{ $i + 1 }} ? 'bg-violet-600' : 'bg-gray-200')" 
                         class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold transition-colors">
                        <template x-if="step > {{ $i + 1 }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="step <= {{ $i + 1 }}">
                            <span x-text="{{ $i + 1 }}"></span>
                        </template>
                    </div>
                    <span class="text-xs mt-2 font-medium" :class="step == {{ $i + 1 }} ? 'text-violet-600' : 'text-gray-500'">{{ $label }}</span>
                </div>
                @if($i < 5)
                    <div class="flex-1 h-1 mx-4 rounded" :class="step > {{ $i + 1 }} ? 'bg-emerald-500' : 'bg-gray-200'" style="margin-top: -1.25rem"></div>
                @endif
            @endforeach
        </div>
    </div>

    <form action="{{ $action }}" method="POST" id="wizard-form">
        @csrf
        @if($method ?? false) @method($method) @endif

        <input type="hidden" name="name" x-model="formData.name">

        <!-- Step 1: Audience -->
        <div x-show="step == 1">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Select Your Audience</h3>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Who should receive this campaign?</label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="formData.audience_type == 'all' ? 'border-violet-600 bg-violet-50' : 'border-gray-200'">
                            <input type="radio" name="audience[type]" value="all" x-model="formData.audience_type" class="sr-only">
                            <svg class="w-8 h-8 text-gray-400 mb-2" :class="formData.audience_type == 'all' && 'text-violet-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-sm font-semibold">All Contacts</span>
                        </label>
                        <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="formData.audience_type == 'lists' ? 'border-violet-600 bg-violet-50' : 'border-gray-200'">
                            <input type="radio" name="audience[type]" value="lists" x-model="formData.audience_type" class="sr-only">
                            <svg class="w-8 h-8 text-gray-400 mb-2" :class="formData.audience_type == 'lists' && 'text-violet-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span class="text-sm font-semibold">Specific Lists</span>
                        </label>
                        <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="formData.audience_type == 'segments' ? 'border-violet-600 bg-violet-50' : 'border-gray-200'">
                            <input type="radio" name="audience[type]" value="segments" x-model="formData.audience_type" class="sr-only">
                            <svg class="w-8 h-8 text-gray-400 mb-2" :class="formData.audience_type == 'segments' && 'text-violet-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span class="text-sm font-semibold">Dynamic Segments</span>
                        </label>
                    </div>
                </div>

                <div x-show="formData.audience_type == 'lists'" class="p-4 bg-gray-50 rounded-xl">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Lists</label>
                    <div class="space-y-2">
                        @foreach($lists as $list)
                            <label class="flex items-center">
                                <input type="checkbox" name="audience[ids][]" value="{{ $list->id }}" x-model="formData.audience_ids" class="rounded border-gray-300 text-violet-600 focus:ring-violet-600">
                                <span class="ml-2 text-sm text-gray-700">{{ $list->name }} ({{ $list->contacts_count }} contacts)</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="formData.audience_type == 'segments'" class="p-4 bg-gray-50 rounded-xl">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Segments</label>
                    <div class="space-y-2">
                        @foreach($segments as $segment)
                            <label class="flex items-center">
                                <input type="checkbox" name="audience[ids][]" value="{{ $segment->id }}" x-model="formData.audience_ids" class="rounded border-gray-300 text-violet-600 focus:ring-violet-600">
                                <span class="ml-2 text-sm text-gray-700">{{ $segment->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center p-4 bg-amber-50 text-amber-800 rounded-xl border border-amber-200">
                    <input type="checkbox" name="audience[exclude_suppressed]" value="1" x-model="formData.exclude_suppressed" class="rounded border-amber-300 text-amber-600 focus:ring-amber-600">
                    <span class="ml-2 text-sm font-medium">Auto-exclude suppressed and unsubscribed contacts (Recommended)</span>
                </div>
            </div>
        </div>

        <!-- Step 2: Content -->
        <div x-show="step == 2">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Email Content</h3>
                <label class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" class="sr-only" x-model="formData.is_ab_test" name="is_ab_test" value="1">
                        <div class="block bg-gray-200 w-10 h-6 rounded-full" :class="formData.is_ab_test && 'bg-violet-600'"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition" :style="formData.is_ab_test ? 'transform: translateX(100%)' : ''"></div>
                    </div>
                    <div class="ml-3 text-sm font-black text-gray-400 uppercase tracking-widest" :class="formData.is_ab_test && 'text-violet-600'">
                        A/B Testing
                    </div>
                </label>
            </div>
            
            <div class="space-y-6">
                <!-- Control Version -->
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 rounded-full bg-gray-900 text-white text-[10px] font-black flex items-center justify-center">A</span>
                        <span class="text-xs font-black uppercase tracking-widest text-gray-900">Control Version</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="subject" value="Subject Line" class="text-[10px] mb-1" />
                            <x-text-input id="subject" name="subject" class="block w-full text-sm font-semibold" type="text" x-model="formData.subject" placeholder="What your recipients will see" />
                        </div>
                        <div>
                            <x-input-label for="preheader" value="Preheader Text" class="text-[10px] mb-1" />
                            <x-text-input id="preheader" name="preheader" class="block w-full text-sm font-semibold" type="text" x-model="formData.preheader" placeholder="The teaser text" />
                        </div>
                        <div>
                            <x-input-label value="Control Template" class="text-[10px] mb-1" />
                            <select name="template_id" x-model="formData.template_id" class="w-full text-sm font-semibold rounded-xl border-gray-200">
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Variations -->
                <div x-show="formData.is_ab_test" class="space-y-6 animate-in fade-in slide-in-from-top-4 duration-300">
                    <template x-for="(v, vIndex) in formData.ab_variations" :key="vIndex">
                        <div class="p-4 border-2 border-dashed border-gray-200 rounded-2xl relative">
                            <button type="button" @click="formData.ab_variations.splice(vIndex, 1)" class="absolute -top-2 -right-2 w-6 h-6 bg-rose-600 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-rose-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>

                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-6 h-6 rounded-full bg-violet-600 text-white text-[10px] font-black flex items-center justify-center" x-text="String.fromCharCode(66 + vIndex)"></span>
                                <span class="text-xs font-black uppercase tracking-widest text-violet-600">Variation Version</span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label value="Variation Subject" class="text-[10px] mb-1" />
                                    <input type="text" :name="'ab_variations['+vIndex+'][subject]'" x-model="v.subject" 
                                           class="w-full text-sm font-semibold rounded-xl border-gray-200" placeholder="Alternate Subject Line">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label value="Variation Template" class="text-[10px] mb-1" />
                                        <select :name="'ab_variations['+vIndex+'][template_id]'" x-model="v.template_id" class="w-full text-sm font-semibold rounded-xl border-gray-200">
                                            @foreach($templates as $template)
                                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label value="Success Metric" class="text-[10px] mb-1" />
                                        <select name="ab_winner_criteria" x-model="formData.ab_winner_criteria" class="w-full text-sm font-semibold rounded-xl border-gray-200">
                                            <option value="open_rate">Highest Open Rate</option>
                                            <option value="click_rate">Highest Click Rate</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="formData.ab_variations.push({ subject: formData.subject, template_id: formData.template_id })"
                            x-show="formData.ab_variations.length < 3"
                            class="w-full py-3 border-2 border-dashed border-gray-200 rounded-2xl text-xs font-black text-gray-400 uppercase tracking-widest hover:border-violet-300 hover:text-violet-600 transition">
                        + Add Variation
                    </button>
                    
                    <div class="grid grid-cols-2 gap-6 p-4 bg-indigo-50 rounded-2xl text-indigo-900">
                        <div>
                            <x-input-label value="Test Sample Size (%)" class="text-indigo-900 text-[10px] mb-1" />
                            <input type="number" name="ab_test_sample_size" x-model="formData.ab_test_sample_size" class="w-full text-sm font-bold bg-white rounded-xl border-indigo-100">
                        </div>
                        <div>
                            <x-input-label value="Test Duration (Hours)" class="text-indigo-900 text-[10px] mb-1" />
                            <input type="number" name="ab_test_duration" x-model="formData.ab_test_duration" class="w-full text-sm font-bold bg-white rounded-xl border-indigo-100">
                        </div>
                        <p class="col-span-2 text-[10px] font-medium opacity-70 italic">
                            The test will run on <span x-text="formData.ab_test_sample_size"></span>% of your audience. After <span x-text="formData.ab_test_duration"></span> hours, the winner will be sent to the remaining contacts.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: From Settings -->
        <div x-show="step == 3">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Sender Identity</h3>
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label value="From Name" />
                        <x-text-input name="from_name" x-model="formData.from_name" class="mt-1 block w-full" placeholder="E.g. Support Team" />
                    </div>
                    <div>
                        <x-input-label value="From Email" />
                         <x-text-input name="from_email" x-model="formData.from_email" class="mt-1 block w-full" placeholder="E.g. hello@yourdomain.com" />
                    </div>
                </div>
                <div class="p-4 bg-blue-50 text-blue-800 rounded-xl border border-blue-200 text-sm">
                    <strong>Note:</strong> Emails will be sent using the global verified SES configuration. Ensure this "From Email" is authorized or aligns with your domain settings.
                </div>
            </div>
        </div>

        <!-- Step 4: Throttling -->
        <div x-show="step == 4">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Throttling & Speed</h3>
            
            <div class="space-y-6">
                <div>
                    <x-input-label value="Sending Rate (Emails per second)" />
                    <div class="flex items-center gap-4 mt-1">
                        <input type="range" min="1" max="100" step="1" x-model="formData.throttle_rate" name="throttle_rate" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-violet-600">
                        <span class="w-16 text-center font-bold text-violet-600 px-2 py-1 bg-violet-50 rounded" x-text="formData.throttle_rate"></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Recommended: 10-20/sec for most shared providers. Lower if you have a new domain.</p>
                </div>

                <div>
                    <x-input-label value="Concurrency (Parallel threads)" />
                    <select name="throttle_concurrency" x-model="formData.throttle_concurrency" class="mt-1 block w-24 rounded-xl border-gray-300 shadow-sm focus:border-violet-600 focus:ring-violet-600">
                        @foreach([1,2,3,5,10] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-2">Number of parallel workers dedicated to this campaign.</p>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <x-input-label value="Engagement Tracking" />
                    <div class="flex gap-8 mt-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="track_opens" value="1" x-model="formData.track_opens" class="rounded border-gray-300 text-violet-600 focus:ring-violet-600">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Track Email Opens</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="track_clicks" value="1" x-model="formData.track_clicks" class="rounded border-gray-300 text-violet-600 focus:ring-violet-600">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Track Link Clicks</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 5: Schedule -->
        <div x-show="step == 5">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Scheduled Delivery</h3>
            
            <div class="space-y-6">
                <div class="flex flex-col gap-4">
                    <label class="flex items-center p-4 border rounded-xl cursor-pointer" :class="!formData.scheduled_at ? 'border-violet-600 bg-violet-50' : 'border-gray-200'">
                        <input type="radio" name="schedule_type" value="now" :checked="!formData.scheduled_at" @click="formData.scheduled_at = ''" class="sr-only">
                        <svg class="w-6 h-6 text-violet-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <div>
                            <span class="block font-bold">Send Immediately</span>
                            <span class="text-xs text-gray-500">Campaign will start as soon as you launch it</span>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border rounded-xl cursor-pointer" :class="formData.scheduled_at ? 'border-violet-600 bg-violet-50' : 'border-gray-200'">
                        <input type="radio" name="schedule_type" value="later" :checked="formData.scheduled_at" class="sr-only">
                        <svg class="w-6 h-6 text-gray-400 mr-3" :class="formData.scheduled_at && 'text-violet-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span class="block font-bold">Schedule for Later</span>
                            <input type="datetime-local" name="scheduled_at" x-model="formData.scheduled_at" class="mt-1 text-sm border-gray-300 rounded-lg focus:ring-violet-600 focus:border-violet-600">
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Step 6: Review -->
        <div x-show="step == 6">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Final Review</h3>
            
            <div class="bg-gray-50 rounded-2xl p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Name</span>
                        <p class="font-medium text-gray-900" x-text="formData.name"></p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Schedule</span>
                        <p class="font-medium text-gray-900" x-text="formData.scheduled_at ? formData.scheduled_at : 'Immediate'"></p>
                    </div>
                    <div class="col-span-2 border-t pt-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase">Subject</span>
                        <p class="font-medium text-gray-900" x-text="formData.subject"></p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Audience</span>
                        <p class="font-medium text-gray-900" x-text="formData.audience_type.toUpperCase()"></p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Speed</span>
                        <p class="font-medium text-gray-900" x-text="formData.throttle_rate + ' messages/sec'"></p>
                    </div>
                </div>

                <!-- Warnings -->
                <div class="mt-6 space-y-2">
                    <template x-if="!formData.subject">
                        <div class="flex items-center text-rose-600 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Subject line is missing
                        </div>
                    </template>
                    <template x-if="!formData.template_id">
                        <div class="flex items-center text-rose-600 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            No template selected
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-12 pt-6 border-t border-gray-200 flex items-center justify-between">
            <button type="button" @click="prevStep" x-show="step > 1" class="px-6 py-2 text-sm font-bold text-gray-700 hover:bg-gray-100 rounded-xl transition">
                Previous
            </button>
            <div x-show="step == 1"></div><!-- Filler -->

            <div class="flex items-center gap-3">
                <button type="button" @click="nextStep" x-show="step < totalSteps" class="px-8 py-3 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition">
                    Next Step
                </button>
                <button type="submit" x-show="step == totalSteps" class="px-8 py-3 bg-gradient-to-r from-violet-600 to-cyan-500 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-violet-200 transition-all duration-200">
                    Save Campaign
                </button>
            </div>
        </div>
    </form>
</div>
