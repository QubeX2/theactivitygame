@props(['active', 'icon', 'iconcolor'])

@php
$classes = ($active ?? false)
            ? 'inline-flex gap-x-1 items-center px-1 pt-1 border-b-4 border-gray-900 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'inline-flex gap-x-1 items-center px-1 pt-1 border-b-4 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon ?? null)
        <i class="material-icons {{$iconcolor ?? ''}}">{{$icon}}</i>
    @endif
    {{ $slot }}
</a>
