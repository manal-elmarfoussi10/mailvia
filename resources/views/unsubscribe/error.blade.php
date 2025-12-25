<x-guest-layout>
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
        <div class="mb-4 text-red-600">
            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Unsubscribe Error</h2>
        <p class="text-gray-600 mb-6">{{ $message ?? 'Something went wrong with your request.' }}</p>
        <div class="flex items-center justify-center">
            <a href="/" class="text-indigo-600 hover:text-indigo-900 font-medium">
                Return to home
            </a>
        </div>
    </div>
</div>
</x-guest-layout>
