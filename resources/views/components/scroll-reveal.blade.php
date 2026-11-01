@props([
    'delay' => 0,
    'direction' => 'up',
    'class' => ''
])

@php
    $translate = match($direction) {
        'up' => 'translate-y-8',
        'down' => '-translate-y-8',
        'left' => 'translate-x-8',
        'right' => '-translate-x-8',
        default => 'translate-y-8'
    };
@endphp

<div
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    class="transition-all duration-700 ease-out opacity-0 {{ $translate }} {{ $class }}"
    :class="shown ? '!opacity-100 !translate-y-0 !translate-x-0' : ''"
    @style(["transition-delay: {$delay}ms"])
>
    {{ $slot }}
</div>
