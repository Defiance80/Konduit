@extends('layouts.app')
@section('title', 'New Curriculum')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <div>
        <a href="{{ route('agency.training.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-brand-500 transition-colors mb-4">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Training Academy
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Curriculum</h1>
        <p class="text-sm text-gray-400 mt-1">A curriculum is a top-level subject area that groups related courses together — e.g. "Marketing Strategy" or "Agency Operations".</p>
    </div>

    <form method="POST" action="{{ route('agency.training.curricula.store') }}" class="space-y-5">
        @csrf

        @if($errors->any())
        <div class="rounded-xl border border-error-200 dark:border-error-800 bg-error-50 dark:bg-error-500/10 p-4">
            <ul class="space-y-1">
                @foreach($errors->all() as $e)
                <li class="text-sm text-error-600 dark:text-error-400">{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Curriculum Title <span class="text-error-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. Marketing Strategy"
                       class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                <textarea name="description" rows="3" placeholder="What will learners gain from this curriculum?"
                          class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Accent Colour <span class="text-error-500">*</span></label>
                <div class="flex gap-3">
                    @foreach(['brand' => 'Brand Blue', 'blue-light' => 'Sky Blue', 'success' => 'Emerald', 'warning' => 'Amber', 'error' => 'Red'] as $val => $label)
                    @php
                        $swatch = ['brand' => 'bg-brand-400', 'blue-light' => 'bg-sky-400', 'success' => 'bg-emerald-400', 'warning' => 'bg-amber-400', 'error' => 'bg-red-400'][$val];
                    @endphp
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="color" value="{{ $val }}" {{ old('color', 'brand') === $val ? 'checked' : '' }}
                               class="text-brand-500 focus:ring-brand-500">
                        <div class="flex items-center gap-1.5">
                            <div class="size-3.5 rounded-full {{ $swatch }}"></div>
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $label }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('agency.training.index') }}"
               class="rounded-lg border border-gray-200 dark:border-gray-700 px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                Create Curriculum
            </button>
        </div>
    </form>
</div>
@endsection
