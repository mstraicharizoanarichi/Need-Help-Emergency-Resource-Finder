@extends('layouts.app')

@section('title', 'Registered as First Responder – Need Help')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
            Welcome to the First Responder Network!
        </h1>

        <p class="mt-3 text-slate-600 dark:text-slate-400 text-sm sm:text-base">
            Thank you, <strong class="text-slate-900 dark:text-white">{{ $responder->name }}</strong>. 
            You are registered as a <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ str_replace('_', ' ', $responder->certification_level) }}</span> in <strong class="text-slate-900 dark:text-white">{{ $responder->area }}, {{ $responder->city }}</strong>.
        </p>

        <div class="mt-8 p-6 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg text-left space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Status</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    ON-DUTY / AVAILABLE
                </span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Registered Skills</span>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ((array) $responder->skills as $skill)
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Dispatch Protocol</span>
                <p class="text-xs text-slate-600 dark:text-slate-400">
                    When an emergency call (cardiac arrest, severe bleeding, or trauma) occurs within ~1–2 km of your location, 
                    our intelligent dispatch system will broadcast an instant alert to your mobile device.
                </p>
            </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}"
                class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-sm font-semibold transition">
                Return to Home
            </a>
            <a href="{{ route('emergency.report') }}"
                class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow transition">
                Report Emergency
            </a>
        </div>
    </div>
</div>
@endsection
