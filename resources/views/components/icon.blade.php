@props([
    'name',
    'type' => 'solid',
])

<i {{ $attributes->merge([
    'class' => "fa-{$type} fa-{$name}"
]) }}></i>