<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900">Queue Monitor</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <x-card class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Pending Jobs</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $pendingJobs->count() }}</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </x-card>

                <x-card class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Failed Jobs</p>
                            <p class="text-3xl font-bold text-rose-600">{{ $failedJobs->count() }}</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-rose-100">
                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </x-card>

                <x-card class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Jobs/Minute</p>
                            <p class="text-3xl font-bold text-violet-600">{{ $jobsPerMinute }}</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-violet-100">
                            <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </x-card>

                <x-card class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Queue Size</p>
                            <p class="text-3xl font-bold text-emerald-600">{{ $queueSizes['default'] ?? 0 }}</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/>
                            </svg>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Horizon Link -->
            <x-card class="p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Laravel Horizon</h3>
                        <p class="text-sm text-gray-500 mt-1">View detailed queue metrics and job history</p>
                    </div>
                    <a href="/horizon" target="_blank">
                        <x-button-primary>
                            Open Horizon Dashboard
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </x-button-primary>
                    </a>
                </div>
            </x-card>

            <!-- Note -->
            @if($jobsPerMinute === 0)
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                    <p class="text-sm text-blue-700">
                        <strong>Note:</strong> Install the php-redis extension for full queue monitoring functionality.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
