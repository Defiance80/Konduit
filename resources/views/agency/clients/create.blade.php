@extends('layouts.app')
@section('title', 'New Client')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('agency.clients.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Add Client</h1>
    </div>

    <form method="POST" action="{{ route('agency.clients.store') }}" class="space-y-5">
        @csrf

        {{-- Company Info --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Company Information</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name <span class="text-error-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Acme Corp">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Company Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="hello@client.com">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="+1 (555) 000-0000">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                    <input type="url" name="website" value="{{ old('website') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="https://client.com">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Industry</label>
                    <input type="text" name="industry" value="{{ old('industry') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="E-commerce, SaaS, Healthcare…">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Business Address</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="123 Main St, City, State 00000">
                </div>
            </div>
        </div>

        {{-- Primary Contact --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Primary Contact Person</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Name</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Jane Smith, Marketing Director">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Email</label>
                    <input type="email" name="contact_person_email" value="{{ old('contact_person_email') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="jane@client.com">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Phone</label>
                    <input type="text" name="contact_person_phone" value="{{ old('contact_person_phone') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="+1 (555) 000-0000">
                </div>
            </div>
        </div>

        {{-- Services Interested --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Services Interested In</h2>
            @php
            $serviceList = [
                'web_design'=>'Web Design & Development','seo'=>'SEO & Content Marketing',
                'social_media'=>'Social Media Management','branding'=>'Branding & Identity',
                'email_marketing'=>'Email Marketing','ppc'=>'PPC & Paid Advertising',
                'analytics'=>'Analytics & Reporting','ecommerce'=>'E-commerce Solutions',
                'video'=>'Video & Animation','app_dev'=>'App Development',
                'copywriting'=>'Copywriting','photography'=>'Photography & Creative',
            ];
            $selected = old('services_interested', []);
            @endphp
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                @foreach($serviceList as $val => $label)
                <label class="flex items-center gap-2 cursor-pointer rounded-xl border px-3 py-2.5 text-sm transition-all hover:border-brand-300 hover:bg-brand-50/50 dark:hover:border-brand-700 dark:hover:bg-brand-500/5">
                    <input type="checkbox" name="services_interested[]" value="{{ $val }}" {{ in_array($val, $selected) ? 'checked' : '' }}
                        class="size-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    <span class="text-gray-700 dark:text-gray-300">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Notes --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Internal Notes</label>
            <textarea name="notes" rows="3"
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Internal notes about this client (not visible to client)…">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('agency.clients.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">Cancel</a>
            <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Create Client</button>
        </div>
    </form>
</div>
@endsection
