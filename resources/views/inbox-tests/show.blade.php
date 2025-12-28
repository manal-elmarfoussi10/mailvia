

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('inbox-tests.index') }}" class="text-gray-400 hover:text-gray-900 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="font-semibold text-2xl text-gray-900">{{ $inboxTest->name }}</h2>
                <x-badge :type="match($inboxTest->status) {
                    'sent' => 'warning',
                    'completed' => 'success',
                    default => 'neutral'
                }">
                    {{ strtoupper($inboxTest->status) }}
                </x-badge>
            </div>

            <div class="flex items-center gap-3">
                @if($inboxTest->status === 'draft')
                    <form action="{{ route('inbox-tests.send', $inboxTest) }}" method="POST">
                        @csrf
                        <x-button-primary>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Start Test
                        </x-button-primary>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($inboxTest->status !== 'draft')
                <!-- Analytics Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <x-card class="p-6 border-emerald-100 bg-emerald-50/30">
                        <span class="text-xs font-bold text-emerald-600 uppercase mb-1 block">Inbox Placement</span>
                        <div class="flex items-end justify-between">
                            <span class="text-4xl font-black text-gray-900">{{ $stats['inbox'] }}%</span>
                            <span class="text-sm text-gray-500 font-bold">{{ $stats['counts']['inbox'] }}/{{ $stats['counts']['total'] }}</span>
                        </div>
                        <div class="mt-4 w-full bg-emerald-100 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $stats['inbox'] }}%"></div>
                        </div>
                    </x-card>
                    <x-card class="p-6 border-rose-100 bg-rose-50/30">
                        <span class="text-xs font-bold text-rose-600 uppercase mb-1 block">Spam Folder</span>
                        <div class="flex items-end justify-between">
                            <span class="text-4xl font-black text-gray-900">{{ $stats['spam'] }}%</span>
                            <span class="text-sm text-gray-500 font-bold">{{ $stats['counts']['spam'] }}/{{ $stats['counts']['total'] }}</span>
                        </div>
                        <div class="mt-4 w-full bg-rose-100 rounded-full h-2">
                            <div class="bg-rose-500 h-2 rounded-full" style="width: {{ $stats['spam'] }}%"></div>
                        </div>
                    </x-card>
                    <x-card class="p-6 border-gray-100 bg-gray-50/30">
                        <span class="text-xs font-bold text-gray-400 uppercase mb-1 block">Missing / Pending</span>
                        <div class="flex items-end justify-between">
                            <span class="text-4xl font-black text-gray-900">{{ $stats['missing'] }}%</span>
                            <span class="text-sm text-gray-500 font-bold">{{ $stats['counts']['missing'] }}/{{ $stats['counts']['total'] }}</span>
                        </div>
                        <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gray-500 h-2 rounded-full" style="width: {{ $stats['missing'] }}%"></div>
                        </div>
                    </x-card>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Details Table & Analysis -->
                    <div class="lg:col-span-2 space-y-8">
                        <x-card title="Provider Breakdown">
                            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                @foreach($providerStats as $provider => $pStats)
                                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="font-bold text-gray-900">{{ $provider }}</span>
                                            <span class="text-xs text-gray-400">{{ $pStats['total'] }} mailboxes</span>
                                        </div>
                                        <div class="space-y-3">
                                            <div>
                                                <div class="flex justify-between text-[10px] mb-1">
                                                    <span class="font-bold text-emerald-600">INBOX</span>
                                                    <span class="text-gray-500">{{ round(($pStats['inbox'] / $pStats['total']) * 100) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                    <div class="bg-emerald-500 h-1.5" style="width: {{ ($pStats['inbox'] / $pStats['total']) * 100 }}%"></div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="flex justify-between text-[10px] mb-1">
                                                    <span class="font-bold text-rose-600">SPAM</span>
                                                    <span class="text-gray-500">{{ round(($pStats['spam'] / $pStats['total']) * 100) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                    <div class="bg-rose-500 h-1.5" style="width: {{ ($pStats['spam'] / $pStats['total']) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>

                        <x-card title="Mailbox Details">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mailbox</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Placement</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Analysis</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($inboxTest->seed_emails as $index => $email)
                                            @php
                                                $placement = $inboxTest->results[$email] ?? 'missing';
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ $email }}
                                                    <div class="text-[10px] text-gray-400 mt-1 uppercase font-bold">
                                                        @if(str_contains($email, 'gmail.com')) Gmail
                                                        @elseif(str_contains($email, 'outlook.com')) Outlook
                                                        @elseif(str_contains($email, 'yahoo.com')) Yahoo
                                                        @else Others
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm">
                                                    <x-badge :type="match($placement) {
                                                        'inbox' => 'success',
                                                        'spam' => 'error',
                                                        'promotions' => 'warning',
                                                        default => 'neutral'
                                                    }">{{ strtoupper($placement) }}</x-badge>
                                                </td>
                                                <td class="px-6 py-4 text-xs text-gray-500">
                                                    @if($placement === 'spam')
                                                        <span class="text-rose-600 font-medium">Triggered content filter</span>
                                                    @elseif($placement === 'inbox')
                                                        <span class="text-emerald-600 font-medium">Clear placement</span>
                                                    @else
                                                        <span class="text-gray-400">Waiting for data...</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </x-card>


                    </div>

                    <!-- Right: Technical Health -->
                    <div class="space-y-8">


                        <x-card title="Test Settings">
                            <div class="p-6 space-y-4">
                                <div class="p-3 bg-gray-50 rounded-xl">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Subject</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $inboxTest->subject }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1 p-3 bg-gray-50 rounded-xl">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Template</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $inboxTest->template->name ?? 'None' }}</span>
                                    </div>
                                    <div class="flex-1 p-3 bg-gray-50 rounded-xl">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Seed List</span>
                                        <span class="text-sm font-medium text-gray-900">{{ count($inboxTest->seed_emails) }} boxes</span>
                                    </div>
                                </div>
                                @if($inboxTest->sent_at)
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Last Sent</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $inboxTest->sent_at->format('M j, Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </x-card>

                        @if($inboxTest->status === 'sent')
                            <x-card title="Manual Results Entry">
                                <div class="p-6">
                                    <p class="text-[10px] text-gray-500 mb-4 italic">Use this form to record placement after manually checking seed mailboxes.</p>
                                    <form action="{{ route('inbox-tests.results', $inboxTest) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div class="max-h-[300px] overflow-y-auto pr-2 space-y-2">
                                            @foreach($inboxTest->seed_emails as $email)
                                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                                                    <span class="text-[10px] font-medium text-gray-600 truncate mr-2">{{ $email }}</span>
                                                    <select name="results[{{ $email }}]" class="text-[10px] rounded-md border-gray-300 py-1">
                                                        <option value="missing" {{ ($inboxTest->results[$email] ?? '') === 'missing' ? 'selected' : '' }}>Missing</option>
                                                        <option value="inbox" {{ ($inboxTest->results[$email] ?? '') === 'inbox' ? 'selected' : '' }}>Inbox</option>
                                                        <option value="spam" {{ ($inboxTest->results[$email] ?? '') === 'spam' ? 'selected' : '' }}>Spam</option>
                                                        <option value="promotions" {{ ($inboxTest->results[$email] ?? '') === 'promotions' ? 'selected' : '' }}>Promotions</option>
                                                    </select>
                                                </div>
                                            @endforeach
                                        </div>
                                        <x-button-primary class="w-full">Update Results</x-button-primary>
                                    </form>
                                </div>
                            </x-card>
                        @endif
                    </div>
                </div>
            @else
                <x-card class="p-12 text-center overflow-hidden relative">
                    <div class="absolute inset-0 opacity-[0.03] pointer-events-none">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <path d="M0 100 C 20 0 50 0 100 100 Z" fill="currentColor" />
                        </svg>
                    </div>
                    <div class="max-w-md mx-auto relative">
                        <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-12">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Ready to verify placement?</h3>
                        <p class="text-gray-500 mb-8 leading-relaxed">This will send test content to your selected seed list of <b>{{ count($inboxTest->seed_emails) }}</b> addresses. You'll be able to analyze placement results across major providers instantly.</p>
                        <form action="{{ route('inbox-tests.send', $inboxTest) }}" method="POST">
                            @csrf
                            <x-button-primary size="lg" class="w-full py-4 rounded-2xl shadow-xl shadow-indigo-100">
                                Launch Placement Test
                            </x-button-primary>
                        </form>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
