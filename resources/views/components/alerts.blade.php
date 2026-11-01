@if (session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-950/50 dark:border-emerald-800 dark:text-emerald-300 flex items-start justify-between gap-3 shadow-sm transition-all" role="alert">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-white p-1 rounded-md">
            <span class="sr-only">Dismiss</span>
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 dark:bg-red-950/50 dark:border-red-800 dark:text-red-300 flex items-start justify-between gap-3 shadow-sm" role="alert">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-white p-1 rounded-md">
            <span class="sr-only">Dismiss</span>
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 dark:bg-amber-950/50 dark:border-amber-800 dark:text-amber-300 shadow-sm" role="alert">
        <div class="flex items-center gap-2 mb-2 font-semibold text-sm">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
            <span>Please correct the following errors:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1 text-amber-800 dark:text-amber-200 ml-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
