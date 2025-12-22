@props(['active'])

@php
$classes = ($active ?? false)
            ? 'h-full inline-flex items-center px-1 pt-1 border-b-[5px] border-white text-sm font-medium leading-5 text-gray-950 focus:outline-none focus:border-emerald-600 hover:border-emerald-600 transition duration-150 ease-in-out'
            : 'h-full inline-flex items-center px-1 pt-1 border-b-[5px] border-transparent text-sm font-medium leading-5 text-gray-800 hover:text-gray-950 hover:border-emerald-600 focus:outline-none focus:text-gray-950 focus:border-emerald-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
