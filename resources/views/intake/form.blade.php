<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Request — {{ $tenant->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-gray-50 flex items-center justify-center p-4" x-data="intake()">

<div class="w-full max-w-lg">
    {{-- Brand header --}}
    <div class="text-center mb-8">
        @if($tenant->logo)
        <img src="{{ $tenant->logo }}" class="h-10 mx-auto mb-3" alt="{{ $tenant->name }}">
        @else
        <div class="inline-flex size-10 items-center justify-center rounded-xl bg-brand-500 text-white font-bold text-lg mx-auto mb-3">
            {{ substr($tenant->name, 0, 1) }}
        </div>
        @endif
        <h1 class="text-xl font-semibold text-gray-900">Submit a Request</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $tenant->name }} · We typically respond within 1 business day</p>
    </div>

    {{-- Progress bar --}}
    <div class="flex gap-1 mb-6">
        @foreach([1,2,3] as $s)
        <div class="flex-1 h-1.5 rounded-full transition-all"
            :class="step >= {{ $s }} ? 'bg-brand-500' : 'bg-gray-200'"></div>
        @endforeach
    </div>

    <form action="{{ route('intake.store', $tenant) }}" method="POST">
        @csrf

        {{-- Step 1: Contact --}}
        <div x-show="step === 1" class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
            <div>
                <h2 class="font-semibold text-gray-900 text-lg">Who are you?</h2>
                <p class="text-sm text-gray-500 mt-0.5">We'll use this to match your request to your account.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Your name *</label>
                <input type="text" x-model="form.name" placeholder="Jane Smith"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                <p x-show="errors.name" x-text="errors.name" class="text-xs text-error-500 mt-1"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address *</label>
                <input type="email" x-model="form.email" placeholder="jane@company.com"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                <p x-show="errors.email" x-text="errors.email" class="text-xs text-error-500 mt-1"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Company / website</label>
                <input type="text" x-model="form.company" placeholder="Acme Inc."
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
            </div>
            <button type="button" @click="nextStep(1)"
                class="w-full rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                Continue →
            </button>
        </div>

        {{-- Step 2: Issue --}}
        <div x-show="step === 2" class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
            <div>
                <h2 class="font-semibold text-gray-900 text-lg">What's going on?</h2>
                <p class="text-sm text-gray-500 mt-0.5">Be as specific as possible — it helps us prioritize correctly.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Type of request *</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        ['bug', 'Something is broken'],
                        ['feature', 'I want something new'],
                        ['content', 'Content needs updating'],
                        ['question', 'I have a question'],
                        ['billing', 'Billing or invoice'],
                        ['emergency', '🚨 Emergency'],
                    ] as [$val, $label])
                    <label class="flex items-center gap-2 cursor-pointer rounded-lg border px-3 py-2.5 text-sm transition-colors"
                        :class="form.issue_type === '{{ $val }}' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="issue_type" value="{{ $val }}" x-model="form.issue_type" class="sr-only">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Affected URL</label>
                <input type="url" x-model="form.website_url" placeholder="https://example.com/page"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
            </div>
            <div class="flex gap-2">
                <button type="button" @click="step=1" class="flex-1 rounded-lg border border-gray-200 py-2.5 text-sm text-gray-600">← Back</button>
                <button type="button" @click="nextStep(2)" class="flex-1 rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Continue →</button>
            </div>
        </div>

        {{-- Step 3: Description --}}
        <div x-show="step === 3" class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
            <div>
                <h2 class="font-semibold text-gray-900 text-lg">Tell us more</h2>
                <p class="text-sm text-gray-500 mt-0.5">Include what you expected to happen and what actually happened.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description *</label>
                <textarea x-model="form.description" rows="6" placeholder="Describe the issue or request in as much detail as you can..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500"></textarea>
                <p class="text-xs text-gray-400 mt-1" x-text="(form.description?.length || 0) + ' characters (min 10)'"></p>
                <p x-show="errors.description" x-text="errors.description" class="text-xs text-error-500 mt-1"></p>
            </div>

            {{-- Hidden fields --}}
            <input type="hidden" name="name"        :value="form.name">
            <input type="hidden" name="email"       :value="form.email">
            <input type="hidden" name="company"     :value="form.company">
            <input type="hidden" name="website_url" :value="form.website_url">
            <input type="hidden" name="issue_type"  :value="form.issue_type">
            <input type="hidden" name="description" :value="form.description">

            <div class="flex gap-2">
                <button type="button" @click="step=2" class="flex-1 rounded-lg border border-gray-200 py-2.5 text-sm text-gray-600">← Back</button>
                <button type="submit" @click="submitting=true" :disabled="submitting"
                    class="flex-1 rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-60">
                    <span x-show="!submitting">Submit Request</span>
                    <span x-show="submitting">Processing…</span>
                </button>
            </div>
        </div>

    </form>

    <p class="text-center text-xs text-gray-400 mt-6">Powered by <span class="font-medium text-gray-500">Konduit</span></p>
</div>

<script>
function intake() {
    return {
        step: 1,
        submitting: false,
        form: {
            name: '', email: '', company: '', website_url: '',
            issue_type: 'bug', description: ''
        },
        errors: {},
        nextStep(from) {
            this.errors = {};
            if (from === 1) {
                if (!this.form.name.trim()) { this.errors.name = 'Name is required.'; return; }
                if (!this.form.email.trim() || !this.form.email.includes('@')) { this.errors.email = 'Valid email required.'; return; }
            }
            if (from === 2) {
                if (!this.form.issue_type) return;
            }
            this.step = from + 1;
        }
    }
}
</script>
</body>
</html>
