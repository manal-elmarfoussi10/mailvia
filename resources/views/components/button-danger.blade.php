@props(['href' => null])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-6 py-3 bg-rose-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-3 bg-rose-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl']) }}>
        {{ $slot }}
    </button>
@endif
