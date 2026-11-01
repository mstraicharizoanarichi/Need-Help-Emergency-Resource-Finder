@props(['name', 'show' => false, 'maxWidth' => '2xl'])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center"
    @if($show)
        style="display: flex;"
    @else
        style="display: none;"
    @endif
>
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 backdrop-blur-none"
        x-transition:enter-end="opacity-100 backdrop-blur-sm"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 backdrop-blur-sm"
        x-transition:leave-end="opacity-0 backdrop-blur-none"
    >
        <div class="absolute inset-0 bg-slate-900/75 dark:bg-slate-900/80 backdrop-blur-sm"></div>
    </div>

    <div
        x-show="show"
        class="mb-6 bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-white/20 dark:border-slate-700/50 transform transition-all sm:w-full sm:mx-auto {{ $maxWidth }}"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 blur-sm"
        x-transition:enter-end="opacity-100 scale-100 blur-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 blur-0"
        x-transition:leave-end="opacity-0 scale-95 blur-sm"
    >
        {{ $slot }}
    </div>
</div>
