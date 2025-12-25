<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900">Import Center</h2>
            <a href="{{ route('imports.create') }}">
                <x-button-primary>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    New Import
                </x-button-primary>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($imports->isEmpty())
                <x-card class="p-12">
                    <x-empty-state 
                        title="No imports yet"
                        description="Import contacts from CSV or XLSX files to quickly build your audience."
                        :action="route('imports.create')"
                        actionText="Start Your First Import"
                    />
                </x-card>
            @else
                <div class="space-y-4">
                    @foreach ($imports as $import)
                        <x-card class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-violet-100 to-cyan-100">
                                            <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $import->filename }}</h3>
                                            <p class="text-sm text-gray-500 mt-1">{{ $import->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-6">
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-gray-900">{{ $import->total_rows ?? 0 }}</p>
                                        <p class="text-xs text-gray-500">Total Rows</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-emerald-600">{{ $import->imported_rows ?? 0 }}</p>
                                        <p class="text-xs text-gray-500">Imported</p>
                                    </div>
                                    <div>
                                        @if($import->status === 'completed')
                                            <x-badge-success>Completed</x-badge-success>
                                        @elseif($import->status === 'processing')
                                            <x-badge-warning>Processing</x-badge-warning>
                                        @elseif($import->status === 'failed')
                                            <x-badge-error>Failed</x-badge-error>
                                        @else
                                            <x-badge-neutral>{{ ucfirst($import->status) }}</x-badge-neutral>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($import->status === 'processing')
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-violet-600 to-cyan-500 h-2 rounded-full transition-all duration-300" style="width: {{ $import->total_rows > 0 ? ($import->imported_rows / $import->total_rows * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
