@extends('layouts.app')

@section('title', 'Sign In – Need Help')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 py-10 sm:py-16 space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
        
        <div class="text-center space-y-1">
            <div class="w-12 h-12 rounded-2xl bg-red-600 text-white flex items-center justify-center font-black text-xl mx-auto mb-3 shadow-md shadow-red-600/20">
                NH
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Sign In to Need Help</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Access your emergency dashboard or manage listed facilities.</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4 text-xs sm:text-sm">
            @csrf

            <div>
                <label for="email" class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address</label>
                <input type="email" name="email" id="login-email" required value="{{ old('email') }}" placeholder="name@domain.com" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
            </div>

            <div>
                <label for="password" class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
                <input type="password" name="password" id="login-password" required placeholder="••••••••" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-red-600 focus:ring-red-500">
                    <span class="text-xs text-slate-600 dark:text-slate-400">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full h-12 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm shadow-md shadow-red-600/30 transition-transform hover:scale-[1.01]">
                Sign In
            </button>
        </form>

        {{-- Quick Demo Logins --}}
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center">Quick Demo Accounts (1-Click Fill)</span>
            <div class="grid grid-cols-3 gap-2 text-xs">
                <button type="button" onclick="fillDemo('admin@needhelp.bd', 'password123')" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-center truncate">
                    Admin
                </button>
                <button type="button" onclick="fillDemo('square.hospital@needhelp.bd', 'password123')" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-center truncate">
                    Provider
                </button>
                <button type="button" onclick="fillDemo('citizen@needhelp.bd', 'password123')" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-center truncate">
                    Citizen
                </button>
            </div>
        </div>

        <div class="text-center text-xs text-slate-500 pt-2">
            Don't have an account? <a href="{{ route('register') }}" class="font-bold text-red-600 hover:underline">Create Account</a>
        </div>

    </div>

</div>

@push('scripts')
<script>
function fillDemo(email, pass) {
    document.getElementById('login-email').value = email;
    document.getElementById('login-password').value = pass;
}
</script>
@endpush
@endsection
