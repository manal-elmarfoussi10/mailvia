@props(['title', 'description', 'action' => null, 'actionText' => null])

<div class="text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
    </svg>
    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $title }}</h3>
    <p class="mt-2 text-sm text-gray-500">{{ $description }}</p>
    @if($action && $actionText)
        <div class="mt-6">
            <a href="{{ $action }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-500 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:from-violet-700 hover:to-cyan-600 transition">
                {{ $actionText }}
            </a>
        </div>
    @endif
</div>
