@props(['type' => 'neutral'])

@php
    $classes = match($type) {
        'success' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'warning' => 'bg-amber-100 text-amber-700 border-amber-200',
        'error', 'danger' => 'bg-rose-100 text-rose-700 border-rose-200',
        'info', 'blue' => 'bg-blue-100 text-blue-700 border-blue-200',
        'violet' => 'bg-violet-100 text-violet-700 border-violet-200',
        default => 'bg-gray-100 text-gray-700 border-gray-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border $classes"]) }}>
    {{ $slot }}
</span>
