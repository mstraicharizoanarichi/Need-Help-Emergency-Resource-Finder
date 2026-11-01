@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-slate-900', 'dropdownClasses' => ''])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'origin-top-left left-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'none':
    case 'false':
        $alignmentClasses = '';
        break;
    case 'right':
    default:
        $alignmentClasses = 'origin-top-right right-0';
        break;
}

switch ($width) {
    case '48':
        $width = 'w-48';
        break;
    case 'auto':
        $width = 'w-auto';
        break;
}
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95 blur-sm"
            x-transition:enter-end="opacity-100 scale-100 blur-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100 blur-0"
            x-transition:leave-end="opacity-0 scale-95 blur-sm"
            class="absolute z-50 mt-2 {{ $width }} rounded-2xl shadow-2xl border border-white/20 dark:border-slate-700/50 {{ $alignmentClasses }} {{ $dropdownClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-2xl ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
