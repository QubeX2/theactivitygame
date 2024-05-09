@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-red-700 rounded-md shadow-sm']) !!}>
{{$slot}}
