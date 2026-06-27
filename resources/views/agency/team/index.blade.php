@extends('layouts.app')
@section('title', 'Team')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Team</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your agency team members and client contacts.</p>
        </div>
        <button
            onclick="document.getElementById('invite-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
        >
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Invite Member
        </button>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 p-4 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="rounded-lg border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-900/20 dark:text-error-400">
        {{ session('error') }}
    </div>
    @endif

    {{-- Agency Team Members --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Agency Team</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $members->count() }} {{ Str::plural('member', $members->count()) }}</p>
            </div>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($members as $member)
            <div class="flex items-center gap-4 px-6 py-4">
                <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="size-10 rounded-full object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $member->name }}</p>
                        @if($member->id === auth()->id())
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">You</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $member->email }}</p>
                    @if($member->job_title)
                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $member->job_title }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @foreach($member->roles as $role)
                    @php
                        $roleColors = [
                            'agency_admin'  => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
                            'agency_member' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                        ];
                        $colorClass = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300';
                    @endphp
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colorClass }}">
                        {{ Str::title(str_replace('_', ' ', $role->name)) }}
                    </span>
                    @endforeach

                    @if($member->id !== auth()->id())
                    <form method="POST" action="{{ route('agency.team.destroy', $member) }}" onsubmit="return confirm('Remove {{ $member->name }} from your team?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-error-500 dark:hover:text-error-400 transition-colors">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center">
                <svg class="mx-auto size-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">No team members yet.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Client Contacts --}}
    @if($clientContacts->count() > 0)
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Client Contacts</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $clientContacts->count() }} portal {{ Str::plural('user', $clientContacts->count()) }}</p>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($clientContacts as $contact)
            <div class="flex items-center gap-4 px-6 py-4">
                <img src="{{ $contact->avatar_url }}" alt="{{ $contact->name }}" class="size-10 rounded-full object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $contact->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $contact->email }}</p>
                    @if($contact->client)
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $contact->client->name }}</p>
                    @endif
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-teal-50 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400">
                    Client Contact
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Invite Modal --}}
<div id="invite-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);">
    <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Invite Team Member</h3>
            <button onclick="document.getElementById('invite-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('agency.team.invite') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Job Title</label>
                <input type="text" name="job_title" value="{{ old('job_title') }}" placeholder="e.g. SEO Specialist"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Role</label>
                <select name="role" required class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="agency_member">Agency Member</option>
                    <option value="agency_admin">Agency Admin</option>
                </select>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">They'll be added with a temporary password of <code class="font-mono">password</code>. Ask them to change it on first login.</p>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('invite-modal').classList.add('hidden')"
                    class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="flex-1 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    Add Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
