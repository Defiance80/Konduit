@extends('layouts.auth')
@section('title', 'Sign In')

@section('content')
<div class="relative flex min-h-screen w-full flex-col justify-center lg:flex-row dark:bg-gray-900">
    <!-- Form Side -->
    <div class="flex w-full flex-1 flex-col items-center justify-center px-6 py-12 lg:w-1/2">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="mb-10 flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-brand-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">Konduit</span>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-gray-900 dark:text-white mb-2">Welcome back</h1>
                <p class="text-gray-500 dark:text-gray-400">Sign in to your workspace</p>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-900/20 dark:text-error-400">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email address
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                           placeholder="you@agency.com"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                               placeholder="Enter your password"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                        <button type="button" @click="show = !show" class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                        Keep me signed in
                    </label>
                    <a href="#" class="text-sm text-brand-500 hover:text-brand-600">Forgot password?</a>
                </div>

                <button type="submit"
                        class="flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-600 focus:outline-none focus:ring-3 focus:ring-brand-500/20">
                    Sign In
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Agency Intelligence Platform &mdash; Powered by AI
            </p>
        </div>
    </div>

    <!-- Branding Side -->
    <div class="bg-brand-950 relative hidden h-screen w-full items-center lg:flex lg:w-1/2 dark:bg-white/5">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-brand-400 to-transparent"></div>
        </div>
        <div class="relative z-10 flex w-full flex-col items-center justify-center px-12 text-center">
            <div class="mb-8 flex size-20 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2 class="mb-4 text-4xl font-bold text-white">Agency Intelligence</h2>
            <p class="max-w-sm text-lg text-brand-200/70">
                Coordinate operations. Delight clients. Predict outcomes. All from one platform.
            </p>
            <div class="mt-12 grid grid-cols-3 gap-6 text-center">
                <div>
                    <div class="text-2xl font-bold text-white">AI</div>
                    <div class="text-xs text-brand-300/60 mt-1">Summaries</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white">∞</div>
                    <div class="text-xs text-brand-300/60 mt-1">Clients</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white">24/7</div>
                    <div class="text-xs text-brand-300/60 mt-1">Awareness</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
