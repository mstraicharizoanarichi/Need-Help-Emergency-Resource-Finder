@props(['resource'])

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group relative">
    
    {{-- Card Header --}}
    <div>
        <div class="flex items-start justify-between gap-3 mb-3">
            {{-- Category Pill --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider {{ $resource->category?->getIconColorClasses() }} border">
                <x-category-icon :icon="$resource->category?->icon" class="w-3.5 h-3.5" />
                {{ $resource->category?->name }}
            </span>

            {{-- Distance & Verification Badges --}}
            <div class="flex items-center gap-1.5">
                @if (isset($resource->distance))
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-sm">
                        <svg class="w-3 h-3 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd"/></svg>
                        {{ $resource->formatted_distance }}
                    </span>
                @endif

                @if ($resource->isVerified())
                    <x-status-badge type="verified" />
                @else
                    <x-status-badge type="pending" />
                @endif
            </div>
        </div>

        {{-- Resource Title --}}
        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1.5 line-clamp-1">
            <a href="{{ route('resources.show', $resource->slug) }}">
                {{ $resource->name }}
            </a>
        </h3>

        {{-- Address & Location --}}
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mb-3 line-clamp-1">
            <svg class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd"/>
            </svg>
            {{ $resource->address }}{{ $resource->area ? ', ' . $resource->area : '' }}, {{ $resource->city }}
        </p>

        {{-- Special Highlights (ICU, Ambulance, Oxygen, 24/7) --}}
        <div class="flex flex-wrap items-center gap-1.5 mb-4">
            @if ($resource->is_24_7)
                <x-status-badge type="24_7" />
            @endif

            @if ($resource->details?->icu_available)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-800">
                    ICU: {{ $resource->details->icu_beds_count ? $resource->details->icu_beds_count . ' Beds' : 'Available' }}
                </span>
            @endif

            @if ($resource->details?->oxygen_available)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                    Oxygen Available
                </span>
            @endif

            @if ($resource->details?->ambulance_available)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300 border border-orange-200 dark:border-orange-800">
                    Ambulance On-site
                </span>
            @endif

            @if ($resource->details?->paramedic_onboard)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    Paramedic Staff
                </span>
            @endif
        </div>
    </div>

    {{-- Emergency Action Buttons --}}
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 space-y-2">
        <div class="grid grid-cols-2 gap-2">
            {{-- CALL NOW (Primary Emergency Action) --}}
            <a href="tel:{{ $resource->getCleanPhone() }}" class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl font-bold text-xs sm:text-sm bg-red-600 hover:bg-red-700 text-white shadow-sm transition-all hover:shadow hover:scale-[1.02]">
                <svg class="w-4 h-4 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <span>CALL NOW</span>
            </a>

            {{-- GET DIRECTIONS (Live Map Navigation) --}}
            <a href="{{ $resource->getDirectionsUrl() }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl font-semibold text-xs sm:text-sm bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 transition-colors">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                </svg>
                <span>Directions</span>
            </a>
        </div>

        {{-- More details link --}}
        <div class="flex items-center justify-between pt-1 text-xs text-slate-500 dark:text-slate-400">
            <span class="font-medium text-slate-700 dark:text-slate-300">
                📞 {{ $resource->emergency_phone ?: $resource->phone }}
            </span>
            <a href="{{ route('resources.show', $resource->slug) }}" class="font-semibold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 flex items-center gap-0.5">
                Full Details &rarr;
            </a>
        </div>
    </div>

</div>
