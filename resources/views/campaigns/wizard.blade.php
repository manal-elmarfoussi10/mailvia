<div x-data="{
    step: 1,
    totalSteps: 6,
    formData: {
        name: '{{ old('name', $campaign->name ?? '') }}',
        description: '{{ old('description', $campaign->description ?? '') }}',
        audience_type: '{{ old('audience.type', $campaign->audience['type'] ?? 'lists') }}',
        audience_ids: @json(old('audience.ids', $campaign->audience['ids'] ?? [])),
        from_name: '{{ old('from_name', $campaign->from_name ?? config('mail.from.name')) }}',
        from_email: '{{ old('from_email', $campaign->from_email ?? config('mail.from.address')) }}',
        reply_to: '{{ old('reply_to', $campaign->reply_to ?? '') }}',
        template_id: '{{ old('template_id', $campaign->template_id ?? '') }}',
        subject: '{{ old('subject', $campaign->subject ?? '') }}',
        eps: {{ old('eps', $campaign->eps ?? 10) }},
        warmup: {{ old('warmup', $campaign->warmup ?? false) ? 'true' : 'false' }},
        batch_size: {{ old('batch_size', $campaign->batch_size ?? 100) }},
        scheduled_at: '{{ old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}',
        test_emails: @json(old('test_emails', []))
    },
    nextStep() { if(this.step < this.totalSteps) this.step++; },
    prevStep() { if(this.step > 1) this.step--; },
    getAudienceCount() {
        if (this.formData.audience_type === 'all') return 'All contacts';
        if (this.formData.audience_type === 'lists') {
            const selectedLists = @json($lists->whereIn('id', old('audience.ids', $campaign->audience['ids'] ?? []))->pluck('name', 'id'));
            return Object.values(selectedLists).join(', ');
        }
        if (this.formData.audience_type === 'segments') {
            const selectedSegments = @json($segments->whereIn('id', old('audience.ids', $campaign->audience['ids'] ?? []))->pluck('name', 'id'));
            return Object.values(selectedSegments).join(', ');
        }
        return 'No audience selected';
    }
}">
    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            @foreach(['Basics', 'Audience', 'Sender & Content', 'Throttling', 'Schedule', 'Review'] as $i => $label)
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
        <input type="hidden" name="description" x-model="formData.description">

        <!-- Step 1: Basics -->
        <div x-show="step == 1">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Campaign Basics</h3>

            <div class="space-y-6">
                <div>
                    <x-input-label for="name" value="Campaign Name" />
                    <x-text-input id="name" name="name" class="block mt-1 w-full" x-model="formData.name" placeholder="E.g. Summer Sale 2025" />
                </div>

                <div>
                    <x-input-label for="description" value="Description (Optional)" />
                    <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-violet-600 focus:ring-violet-600 rounded-md shadow-sm" x-model="formData.description" placeholder="Brief description of this campaign"></textarea>
                </div>
            </div>
        </div>

        <!-- Step 2: Audience -->
        <div x-show="step == 2">
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

                <div class="p-4 bg-blue-50 text-blue-800 rounded-xl border border-blue-200">
                    <strong>Note:</strong> Suppressed and unsubscribed contacts will be automatically excluded from this campaign.
                </div>
            </div>
        </div>

        <!-- Step 3: Sender & Content -->
        <div x-show="step == 3">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Sender & Content</h3>

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

                <div>
                    <x-input-label value="Reply-To (Optional)" />
                    <x-text-input name="reply_to" x-model="formData.reply_to" class="mt-1 block w-full" placeholder="E.g. replies@yourdomain.com" />
                </div>

                <div>
                    <x-input-label value="Email Template" />
                    <select name="template_id" x-model="formData.template_id" class="mt-1 block w-full border-gray-300 focus:border-violet-600 focus:ring-violet-600 rounded-md shadow-sm">
                        <option value="">Select a template...</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label value="Subject Line" />
                    <x-text-input name="subject" x-model="formData.subject" class="mt-1 block w-full" placeholder="What your recipients will see" />
                </div>

                <div class="p-4 bg-blue-50 text-blue-800 rounded-xl border border-blue-200 text-sm">
                    <strong>Note:</strong> Emails will be sent using SES SMTP. Ensure your "From Email" is authorized in your SES settings.
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
                        <input type="range" min="1" max="100" step="1" x-model="formData.eps" name="eps" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-violet-600">
                        <span class="w-16 text-center font-bold text-violet-600 px-2 py-1 bg-violet-50 rounded" x-text="formData.eps"></span>
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
            <h3 class="text-xl font-bold text-gray-900 mb-6">Schedule Delivery</h3>

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
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">From</span>
                        <p class="font-medium text-gray-900" x-text="formData.from_name + ' <' + formData.from_email + '>'"></p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Subject</span>
                        <p class="font-medium text-gray-900" x-text="formData.subject"></p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Audience</span>
                        <p class="font-medium text-gray-900" x-text="getAudienceCount()"></p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Speed</span>
                        <p class="font-medium text-gray-900" x-text="formData.eps + ' messages/sec'"></p>
                    </div>
                </div>

                <!-- Test Emails -->
                <div class="border-t pt-4">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Test Emails</span>
                    <div class="mt-2">
                        <template x-for="(email, index) in formData.test_emails" :key="index">
                            <span class="inline-block bg-violet-100 text-violet-800 text-xs px-2 py-1 rounded mr-2 mb-2" x-text="email"></span>
                        </template>
                        <template x-if="formData.test_emails.length === 0">
                            <p class="text-sm text-gray-500 italic">No test emails added</p>
                        </template>
                    </div>
                </div>

                <!-- Warnings -->
                <div class="mt-6 space-y-2">
                    <template x-if="!formData.name">
                        <div class="flex items-center text-rose-600 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Campaign name is missing
                        </div>
                    </template>
                    <template x-if="!formData.from_name || !formData.from_email">
                        <div class="flex items-center text-rose-600 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            From name and email are required
                        </div>
                    </template>
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
                    <template x-if="formData.audience_type === 'lists' && formData.audience_ids.length === 0">
                        <div class="flex items-center text-rose-600 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            No lists selected for audience
                        </div>
                    </template>
                    <template x-if="formData.audience_type === 'segments' && formData.audience_ids.length === 0">
                        <div class="flex items-center text-rose-600 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            No segments selected for audience
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
