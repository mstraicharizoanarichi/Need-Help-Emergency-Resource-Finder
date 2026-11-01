@props([
    'variant' => 'dropdown', // 'dropdown', 'segmented', 'mobile', 'icon-only'
    'align' => 'right',      // 'right', 'left'
    'class' => '',
])

@php
    $id = 'theme-toggle-' . uniqid();
@endphp

@if ($variant === 'dropdown')
    {{-- Dropdown Theme Switcher --}}
    <div class="relative inline-block text-left {{ $class }}" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false" data-theme-dropdown-container>
        <button
            type="button"
            @click="open = !open"
            data-theme-toggle-trigger="{{ $id }}-menu"
            class="inline-flex items-center justify-center p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-700 shadow-none hover:shadow-sm"
            title="Theme Mode (Alt + T)"
            aria-label="Select theme mode"
            :aria-expanded="open"
            aria-haspopup="true"
        >
            <span data-theme-trigger-icon class="w-5 h-5 flex items-center justify-center">
                {{-- Sun Icon (Light) --}}
                <svg class="w-5 h-5 theme-icon-sun text-amber-500 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2"/>
                    <path d="M12 20v2"/>
                    <path d="m4.93 4.93 1.41 1.41"/>
                    <path d="m17.66 17.66 1.41 1.41"/>
                    <path d="M2 12h2"/>
                    <path d="M20 12h2"/>
                    <path d="m6.34 17.66-1.41 1.41"/>
                    <path d="m19.07 4.93-1.41 1.41"/>
                </svg>

                {{-- Moon Icon (Dark) --}}
                <svg class="w-5 h-5 theme-icon-moon text-indigo-400 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                </svg>

                {{-- System Monitor Icon --}}
                <svg class="w-5 h-5 theme-icon-system text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="14" x="2" y="3" rx="2"/>
                    <line x1="8" x2="16" y1="21" y2="21"/>
                    <line x1="12" x2="12" y1="17" y2="21"/>
                </svg>
            </span>
            <span class="sr-only">Toggle theme</span>
        </button>

        {{-- Floating Dropdown Menu --}}
        <div
            id="{{ $id }}-menu"
            data-theme-dropdown-menu
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95 blur-sm"
            x-transition:enter-end="opacity-100 scale-100 blur-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100 blur-0"
            x-transition:leave-end="opacity-0 scale-95 blur-sm"
            style="display: none;"
            class="absolute {{ $align === 'left' ? 'left-0' : 'right-0' }} mt-2 w-44 origin-top-right rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl p-1.5 shadow-2xl border border-slate-200/90 dark:border-slate-800 focus:outline-none z-50"
            role="menu"
            aria-orientation="vertical"
        >
            <div class="px-2.5 py-1.5 border-b border-slate-100 dark:border-slate-800/80 mb-1">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Appearance</p>
            </div>

            {{-- Light Option --}}
            <button
                type="button"
                @click="open = false"
                data-theme-option="light"
                class="w-full flex items-center justify-between px-2.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors text-left group"
                role="menuitem"
            >
                <span class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                        </svg>
                    </span>
                    <span>Light Mode</span>
                </span>
                <svg class="w-4 h-4 text-red-600 dark:text-red-400 theme-check-icon hidden" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>

            {{-- Dark Option --}}
            <button
                type="button"
                @click="open = false"
                data-theme-option="dark"
                class="w-full flex items-center justify-between px-2.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors text-left group"
                role="menuitem"
            >
                <span class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                        </svg>
                    </span>
                    <span>Dark Mode</span>
                </span>
                <svg class="w-4 h-4 text-red-600 dark:text-red-400 theme-check-icon hidden" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>

            {{-- System Option --}}
            <button
                type="button"
                @click="open = false"
                data-theme-option="system"
                class="w-full flex items-center justify-between px-2.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors text-left group"
                role="menuitem"
            >
                <span class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/>
                        </svg>
                    </span>
                    <span>System Auto</span>
                </span>
                <svg class="w-4 h-4 text-red-600 dark:text-red-400 theme-check-icon hidden" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>

            <div class="px-2.5 pt-1.5 pb-0.5 border-t border-slate-100 dark:border-slate-800/80 mt-1 flex items-center justify-between text-[10px] text-slate-400">
                <span>Quick switch</span>
                <kbd class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-mono text-[9px] border border-slate-200 dark:border-slate-700">Alt+T</kbd>
            </div>
        </div>
    </div>

@elseif ($variant === 'segmented')
    {{-- 3-Button Segmented Switcher (For Dashboard / Settings) --}}
    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 {{ $class }}">
        <button
            type="button"
            data-theme-option="light"
            class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-all"
        >
            <svg class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
            </svg>
            <span>Light</span>
        </button>

        <button
            type="button"
            data-theme-option="dark"
            class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-all"
        >
            <svg class="w-4 h-4 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
            </svg>
            <span>Dark</span>
        </button>

        <button
            type="button"
            data-theme-option="system"
            class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-all"
        >
            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/>
            </svg>
            <span>System</span>
        </button>
    </div>

@elseif ($variant === 'mobile')
    {{-- Mobile Drawer Compact Theme Row --}}
    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 {{ $class }}">
        <div class="flex items-center gap-2.5">
            <span data-theme-trigger-icon class="w-7 h-7 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 theme-icon-sun text-amber-500 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                <svg class="w-4 h-4 theme-icon-moon text-indigo-400 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                <svg class="w-4 h-4 theme-icon-system text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
            </span>
            <div>
                <p class="text-xs font-bold text-slate-800 dark:text-white">Theme Preference</p>
                <p class="text-[10px] text-slate-400" data-theme-label>System Auto</p>
            </div>
        </div>

        <div class="flex items-center gap-1 bg-white dark:bg-slate-900 p-1 rounded-lg border border-slate-200 dark:border-slate-700">
            <button type="button" data-theme-option="light" class="p-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:text-amber-500" title="Light">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
            </button>
            <button type="button" data-theme-option="dark" class="p-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:text-indigo-400" title="Dark">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            </button>
            <button type="button" data-theme-option="system" class="p-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:text-red-500" title="System">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
            </button>
        </div>
    </div>

@elseif ($variant === 'icon-only')
    {{-- Fast Cycle Button (Alt+T action) --}}
    <button
        type="button"
        data-theme-cycle
        class="inline-flex items-center justify-center p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-700 {{ $class }}"
        title="Toggle Light/Dark Theme (Alt + T)"
        aria-label="Toggle theme"
    >
        <span data-theme-trigger-icon class="w-5 h-5 flex items-center justify-center">
            <svg class="w-5 h-5 theme-icon-sun text-amber-500 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
            <svg class="w-5 h-5 theme-icon-moon text-indigo-400 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            <svg class="w-5 h-5 theme-icon-system text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
        </span>
    </button>
@endif
