<!DOCTYPE html>
<html lang="en">
<head>
<x-guest-layout>
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
        <div class="mb-4 text-green-500">
            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Unsubscribed</h2>
        <p class="text-gray-600 mb-6">The email address <strong>{{ $contact->email }}</strong> has been removed from our list.</p>
        <p class="text-sm text-gray-500 mb-6">You will no longer receive marketing emails from us.</p>
        <div class="flex items-center justify-center">
            <a href="/" class="text-indigo-600 hover:text-indigo-900 font-medium">
                Return to home
            </a>
        </div>
    </div>
</div>
</x-guest-layout>
