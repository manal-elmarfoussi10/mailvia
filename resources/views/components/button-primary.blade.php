@props(['href' => null])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-500 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:from-violet-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-500 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:from-violet-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl']) }}>
        {{ $slot }}
    </button>
@endif
