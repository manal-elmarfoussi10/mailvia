<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('campaigns.index') }}" class="text-gray-400 hover:text-gray-900 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="font-semibold text-2xl text-gray-900">{{ $campaign->name }}</h2>
                <x-badge :type="match($campaign->status) {
                    'sending' => 'warning',
                    'completed' => 'success',
                    'paused' => 'neutral',
                    'stopped' => 'error',
                    default => 'neutral'
                }">
                    {{ strtoupper($campaign->status) }}
                </x-badge>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('campaigns.export', $campaign) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M16 10l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </a>

                @if($campaign->status === \App\Models\Campaign::STATUS_DRAFT)
                    <a href="{{ route('campaigns.edit', $campaign) }}">
                        <x-button-secondary>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </x-button-secondary>
                    </a>
                @endif

                @if($campaign->canBeLaunched())
                    <form action="{{ route('campaigns.launch', $campaign) }}" method="POST">
                        @csrf
                        <x-button-primary>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ $campaign->scheduled_at ? 'Schedule' : 'Launch' }}
                        </x-button-primary>
                    </form>
                @endif

                @if($campaign->status === \App\Models\Campaign::STATUS_SCHEDULED)
                    <form action="{{ route('campaigns.cancel_schedule', $campaign) }}" method="POST">
                        @csrf
                        <x-button-secondary type="submit">
                            Cancel Schedule
                        </x-button-secondary>
                    </form>
                @endif

                <!-- Pause/Resume/Stop Controls -->
                @if($campaign->status === 'sending')
                    <form action="{{ route('campaigns.pause', $campaign) }}" method="POST">
                        @csrf
                        <x-button-secondary type="submit" class="text-amber-600 bg-amber-50 hover:bg-amber-100 border-amber-200">
                            Pause Sending
                        </x-button-secondary>
                    </form>
                    <form action="{{ route('campaigns.stop', $campaign) }}" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                        @csrf
                        <x-button-secondary type="submit" class="text-rose-600 bg-rose-50 hover:bg-rose-100 border-rose-200">
                            Stop Permanently
                        </x-button-secondary>
                    </form>
                @endif

                @if($campaign->status === 'paused')
                    <form action="{{ route('campaigns.resume', $campaign) }}" method="POST">
                        @csrf
                        <x-button-primary type="submit">
                            Resume Sending
                        </x-button-primary>
                    </form>
                @endif
                
                <!-- Test Send Button -->
                <div x-data="{ open: false }">
                    <button @click="open = true" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Test Send
                    </button>
                    
                    <!-- Modal -->
                    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="open" @click="open = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>
                            <div x-show="open" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <form action="{{ route('campaigns.test_send', $campaign) }}" method="POST" class="p-6">
                                    @csrf
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Send Test Email</h3>
                                    <div class="mb-4">
                                        <x-input-label for="test_email" value="Recipient Email" />
                                        <x-text-input id="test_email" name="email" type="email" class="block w-full mt-1" :value="auth()->user()->email" required />
                                    </div>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="open = false" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                                        <x-button-primary>Send Test</x-button-primary>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Progress Tracking -->
            @if(in_array($campaign->status, ['sending', 'paused', 'completed', 'stopped']))
                <x-card class="p-8 mb-8 border-violet-100 bg-gradient-to-br from-white to-violet-50/30">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Sending Progress</h3>
                        <span class="text-2xl font-black text-violet-600">{{ $campaign->getProgressPercentage() }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-violet-600 to-cyan-500 h-4 rounded-full transition-all duration-1000" style="width: {{ $campaign->getProgressPercentage() }}%"></div>
                    </div>
                    
                    <div class="grid grid-cols-2 lg:grid-cols-6 gap-6">
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                            <span class="text-xs text-gray-500 font-medium block mb-1">Total Recipients</span>
                            <span class="text-xl font-bold text-gray-900">{{ number_format($campaign->total_recipients) }}</span>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                            <span class="text-xs text-gray-500 font-medium block mb-1">Sent</span>
                            <span class="text-xl font-bold text-emerald-600">{{ number_format($campaign->sent_count) }}</span>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                            <span class="text-xs text-gray-500 font-medium block mb-1">Delivered</span>
                            <span class="text-xl font-bold text-emerald-700">{{ number_format($campaign->delivered_count) }}</span>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                            <span class="text-xs text-gray-500 font-medium block mb-1">Bounced</span>
                            <span class="text-xl font-bold text-amber-600">{{ number_format($campaign->bounced_count) }}</span>
                        </div>
                        <div class="bg-violet-600 p-4 rounded-2xl border border-violet-500 shadow-sm">
                            <span class="text-xs text-white/70 font-medium block mb-1">Opens</span>
                            <span class="text-xl font-bold text-white">{{ number_format($campaign->open_count) }}</span>
                        </div>
                        <div class="bg-cyan-600 p-4 rounded-2xl border border-cyan-500 shadow-sm">
                            <span class="text-xs text-white/70 font-medium block mb-1">Clicks</span>
                            <span class="text-xl font-bold text-white">{{ number_format($campaign->click_count) }}</span>
                        </div>
                    </div>
                </x-card>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Analytics & Charts -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Rates Grid -->
                    <x-card title="Engagement Rates">
                        <div class="p-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
                            @php
                                $delivered = $campaign->delivered_count ?: 1;
                                $openRate = ($campaign->open_count / $delivered) * 100;
                                $clickRate = ($campaign->click_count / $delivered) * 100;
                                $bounceRate = ($campaign->bounced_count / ($campaign->sent_count ?: 1)) * 100;
                                $complaintRate = ($campaign->complaint_count / $delivered) * 100;
                            @endphp
                            <div class="text-center p-4 rounded-xl bg-violet-50 border border-violet-100">
                                <span class="text-xs font-bold text-violet-400 uppercase block mb-1">Open Rate</span>
                                <span class="text-2xl font-black text-violet-700">{{ round($openRate, 1) }}%</span>
                            </div>
                            <div class="text-center p-4 rounded-xl bg-cyan-50 border border-cyan-100">
                                <span class="text-xs font-bold text-cyan-400 uppercase block mb-1">Click Rate</span>
                                <span class="text-2xl font-black text-cyan-700">{{ round($clickRate, 1) }}%</span>
                            </div>
                            <div class="text-center p-4 rounded-xl bg-amber-50 border border-amber-100">
                                <span class="text-xs font-bold text-amber-400 uppercase block mb-1">Bounce Rate</span>
                                <span class="text-2xl font-black text-amber-700">{{ round($bounceRate, 1) }}%</span>
                            </div>
                            <div class="text-center p-4 rounded-xl bg-rose-50 border border-rose-100">
                                <span class="text-xs font-bold text-rose-400 uppercase block mb-1">Complaints</span>
                                <span class="text-2xl font-black text-rose-700">{{ round($complaintRate, 2) }}%</span>
                            </div>
                        </div>
                    </x-card>

                    <!-- Engagement Chart -->
                    <x-card title="Engagement Timeline">
                        <div class="p-6" style="height: 300px;">
                            <canvas id="engagementChart"></canvas>
                        </div>
                    </x-card>

                    <!-- Recipients Table -->
                    <x-card title="Recipients">
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between gap-4">
                            <form method="GET" class="flex items-center gap-4 flex-1">
                                <div class="relative flex-1 max-w-xs">
                                    <x-text-input name="search" placeholder="Search email..." class="w-full pl-10" />
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                </div>
                                <select name="status" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Statuses</option>
                                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>Bounced</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </form>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Engagement</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recipients as $send)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $send->contact->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-badge :type="match($send->status) {
                                                    'delivered' => 'success',
                                                    'bounced' => 'warning',
                                                    'failed' => 'error',
                                                    default => 'neutral'
                                                }">{{ strtoupper($send->status) }}</x-badge>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    @php
                                                        $opens = $send->events->where('type', 'opened')->count();
                                                        $clicks = $send->events->where('type', 'clicked')->count();
                                                    @endphp
                                                    <span title="Opens" class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded {{ $opens > 0 ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-400' }}">
                                                        O: {{ $opens }}
                                                    </span>
                                                    <span title="Clicks" class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded {{ $clicks > 0 ? 'bg-cyan-100 text-cyan-700' : 'bg-gray-100 text-gray-400' }}">
                                                        C: {{ $clicks }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                                {{ $send->created_at->format('M j, H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">No recipients found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($recipients->hasPages())
                            <div class="p-6 border-t border-gray-100">
                                {{ $recipients->links() }}
                            </div>
                        @endif
                    </x-card>
                </div>

                <!-- Right: Insights & Activity -->
                <div class="space-y-8">
                    <!-- Infrastructure Insights -->
                    <x-card title="Infrastructure Insights">
                        <div class="p-4 bg-gray-50 rounded-xl mb-4">
                            <span class="text-xs font-bold text-gray-400 uppercase block mb-1">Sender</span>
                            <span class="text-sm font-semibold text-gray-900 block">SES SMTP</span>
                            <span class="text-xs text-gray-500">{{ $campaign->from_name ?? 'None' }} ({{ $campaign->from_email ?? 'None' }})</span>
                        </div>

                        @if($failingDomains->isNotEmpty())
                            <div class="mt-6">
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Top Failing Domains</h4>
                                <div class="space-y-3">
                                    @foreach($failingDomains as $failure)
                                        <div>
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-bold text-gray-700">{{ $failure->domain }}</span>
                                                <span class="text-gray-500">{{ $failure->count }} events</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-rose-500 h-1.5" style="width: {{ min(100, ($failure->count / ($campaign->bounced_count ?: 1)) * 100) }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </x-card>

                    @if($bounceReasons->isNotEmpty())
                        <x-card title="Bounce Reasons">
                            <div class="p-6 space-y-4">
                                @foreach($bounceReasons as $reason)
                                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-xs font-bold text-gray-900">{{ $reason['label'] }}</span>
                                            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">{{ $reason['count'] }}</span>
                                        </div>
                                        <p class="text-[10px] text-gray-500 font-mono leading-tight break-words">
                                            {{ Str::limit($reason['example_code'], 100) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endif

                    <!-- Timeline -->
                    <x-card title="Live Feed">
                        <div class="flow-root p-6 max-h-[500px] overflow-y-auto">
                            <ul role="list" class="-mb-8">
                                @forelse($recentEvents as $event)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white {{ match($event->type) {
                                                    'delivered' => 'bg-green-500',
                                                    'opened' => 'bg-violet-500',
                                                    'clicked' => 'bg-cyan-500',
                                                    'bounced' => 'bg-amber-500',
                                                    'complained' => 'bg-red-500',
                                                    'unsubscribed' => 'bg-gray-500',
                                                    default => 'bg-gray-400'
                                                } }}">
                                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        @if($event->type === 'delivered') <path d="M5 13l4 4L19 7" />
                                                        @elseif($event->type === 'opened') <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        @elseif($event->type === 'clicked') <path d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5" />
                                                        @else <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        @endif
                                                    </svg>
                                                </span>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            <span class="font-medium text-gray-900">{{ $event->contact->email }}</span>
                                                            {{ $event->type }}
                                                        </p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                        {{ $event->created_at->diffForHumans(null, true) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-center py-4 text-gray-400">Waiting for activity...</li>
                                @endforelse
                            </ul>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('engagementChart').getContext('2d');
        
        const timelineLabels = {!! json_encode($timelineData->first()?->pluck('hour') ?? []) !!};
        const openData = {!! json_encode($timelineData->get('opened')?->pluck('count') ?? []) !!};
        const clickData = {!! json_encode($timelineData->get('clicked')?->pluck('count') ?? []) !!};

        const engagementChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: timelineLabels.map(h => h.split(' ')[1].substring(0, 5)),
                datasets: [
                    {
                        label: 'Opens',
                        data: openData,
                        borderColor: '#7c3aed',
                        backgroundColor: 'rgba(124, 58, 237, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Clicks',
                        data: clickData,
                        borderColor: '#0891b2',
                        backgroundColor: 'rgba(8, 145, 178, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Real-time Progress Polling
        const campaignId = {{ $campaign->id }};
        const campaignStatus = '{{ $campaign->status }}';
        let pollingInterval = null;
        let pollingActive = false;

        function updateProgressUI(data) {
            // Update progress bar
            const progressBar = document.querySelector('.bg-gradient-to-r');
            const progressText = document.querySelector('.text-2xl.font-black.text-violet-600');
            if (progressBar && progressText) {
                progressBar.style.width = data.progress_percentage + '%';
                progressText.textContent = data.progress_percentage + '%';
            }

            // Update metric cards
            const metrics = data.metrics;
            const updateMetric = (selector, value) => {
                const el = document.querySelector(selector);
                if (el) el.textContent = Number(value).toLocaleString();
            };

            updateMetric('.text-xl.font-bold.text-gray-900', metrics.total_recipients);
            updateMetric('.text-xl.font-bold.text-emerald-600', metrics.sent_count);
            updateMetric('.text-xl.font-bold.text-emerald-700', metrics.delivered_count);
            updateMetric('.text-xl.font-bold.text-amber-600', metrics.bounced_count);
            updateMetric('.text-xl.font-bold.text-white', metrics.open_count);
            document.querySelectorAll('.text-xl.font-bold.text-white')[1]?.textContent = Number(metrics.click_count).toLocaleString();

            // Update engagement rates
            const rates = data.rates;
            const rateElements = document.querySelectorAll('.text-2xl.font-black');
            if (rateElements[1]) rateElements[1].textContent = rates.open_rate + '%';
            if (rateElements[2]) rateElements[2].textContent = rates.click_rate + '%';
            if (rateElements[3]) rateElements[3].textContent = rates.bounce_rate + '%';
            if (rateElements[4]) rateElements[4].textContent = rates.complaint_rate + '%';

            // Update chart
            if (data.timeline.hours.length > 0) {
                engagementChart.data.labels = data.timeline.hours.map(h => h.split(' ')[1].substring(0, 5));
                engagementChart.data.datasets[0].data = data.timeline.opens;
                engagementChart.data.datasets[1].data = data.timeline.clicks;
                engagementChart.update('none'); // Update without animation for smoother experience
            }

            // Update live feed (prepend new events)
            if (data.recent_events && data.recent_events.length > 0) {
                const liveFeed = document.querySelector('.flow-root ul');
                if (liveFeed) {
                    // Store existing event emails to avoid duplicates
                    const existingEvents = Array.from(liveFeed.querySelectorAll('.font-medium.text-gray-900'))
                        .map(el => el.textContent);
                    
                    // Add only new events
                    data.recent_events.slice(0, 5).reverse().forEach(event => {
                        if (!existingEvents.includes(event.email)) {
                            const eventHTML = createEventHTML(event);
                            liveFeed.insertAdjacentHTML('afterbegin', eventHTML);
                        }
                    });

                    // Limit to 20 events
                    const allEvents = liveFeed.querySelectorAll('li');
                    if (allEvents.length > 20) {
                        for (let i = 20; i < allEvents.length; i++) {
                            allEvents[i].remove();
                        }
                    }
                }
            }

            // Stop polling if campaign completed
            if (data.status === 'completed' || data.status === 'stopped') {
                stopPolling();
            }
        }

        function createEventHTML(event) {
            const colors = {
                'delivered': 'bg-green-500',
                'opened': 'bg-violet-500',
                'clicked': 'bg-cyan-500',
                'bounced': 'bg-amber-500',
                'complained': 'bg-red-500',
                'unsubscribed': 'bg-gray-500'
            };
            const color = colors[event.type] || 'bg-gray-400';

            return `
                <li>
                    <div class="relative pb-8">
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        <div class="relative flex space-x-3">
                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white ${color}">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        <span class="font-medium text-gray-900">${event.email}</span>
                                        ${event.type}
                                    </p>
                                </div>
                                <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                    ${event.created_at}
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            `;
        }

        function pollProgress() {
            fetch(`/campaigns/${campaignId}/progress`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                updateProgressUI(data);
            })
            .catch(error => {
                console.error('Polling error:', error);
                // Continue polling even on error (might be temporary network issue)
            });
        }

        function startPolling() {
            if (pollingActive) return;
            
            pollingActive = true;
            // Add "Live" indicator
            const header = document.querySelector('.font-semibold.text-2xl.text-gray-900');
            if (header && !document.getElementById('live-indicator')) {
                header.insertAdjacentHTML('afterend', `
                    <span id="live-indicator" class="inline-flex items-center ml-3 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        Live
                    </span>
                `);
            }

            // Poll every 3 seconds
            pollingInterval = setInterval(pollProgress, 3000);
            // Initial poll
            pollProgress();
        }

        function stopPolling() {
            if (!pollingActive) return;
            
            pollingActive = false;
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }

            // Remove "Live" indicator
            const liveIndicator = document.getElementById('live-indicator');
            if (liveIndicator) liveIndicator.remove();
        }

        // Start polling if campaign is active
        if (['sending', 'paused'].includes(campaignStatus)) {
            startPolling();
        }

        // Stop polling when page is hidden/closed
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopPolling();
            } else if (['sending', 'paused'].includes(campaignStatus)) {
                startPolling();
            }
        });
    </script>

</x-app-layout>
