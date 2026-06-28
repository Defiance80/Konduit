<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Request — {{ $tenant->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-gray-50 py-10 px-4" x-data="intake()">

<div class="w-full max-w-xl mx-auto">

    {{-- Brand header --}}
    <div class="text-center mb-8">
        @if($tenant->logo)
        <img src="{{ $tenant->logo }}" class="h-10 mx-auto mb-3" alt="{{ $tenant->name }}">
        @else
        <div class="inline-flex size-12 items-center justify-center rounded-2xl bg-brand-500 text-white font-bold text-xl mx-auto mb-3">
            {{ substr($tenant->name, 0, 1) }}
        </div>
        @endif
        <h1 class="text-xl font-semibold text-gray-900">Submit a Request</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $tenant->name }} · We typically respond within 1 business day</p>
    </div>

    {{-- Step indicator --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([['1','About You'],['2','What You Need'],['3','Details']] as [$n,$lbl])
        <div class="flex items-center gap-2 flex-1">
            <div class="flex items-center gap-1.5">
                <div class="size-6 rounded-full flex items-center justify-center text-xs font-semibold transition-all"
                    :class="step > {{ $n }} ? 'bg-brand-500 text-white' : (step == {{ $n }} ? 'bg-brand-500 text-white' : 'bg-gray-200 text-gray-500')">
                    <span x-show="step <= {{ $n }}">{{ $n }}</span>
                    <svg x-show="step > {{ $n }}" class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-xs hidden sm:block" :class="step >= {{ $n }} ? 'text-gray-700 font-medium' : 'text-gray-400'">{{ $lbl }}</span>
            </div>
            @if($n < 3)<div class="flex-1 h-px bg-gray-200 mx-1" :class="step > {{ $n }} ? 'bg-brand-300' : ''"></div>@endif
        </div>
        @endforeach
    </div>

    <form action="{{ route('intake.store', $tenant) }}" method="POST" @submit.prevent="handleSubmit">
        @csrf

        {{-- Step 1: About You --}}
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                <div class="pb-2 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">About You</h2>
                    <p class="text-sm text-gray-500 mt-0.5">We'll use this to match your request to your account.</p>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Your name <span class="text-error-500">*</span></label>
                        <input type="text" x-model="form.name" placeholder="Jane Smith"
                            class="h-10 w-full rounded-lg border border-gray-300 px-3.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 focus:outline-none">
                        <p x-show="errors.name" x-text="errors.name" class="text-xs text-error-500 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address <span class="text-error-500">*</span></label>
                        <input type="email" x-model="form.email" placeholder="jane@company.com"
                            class="h-10 w-full rounded-lg border border-gray-300 px-3.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 focus:outline-none">
                        <p x-show="errors.email" x-text="errors.email" class="text-xs text-error-500 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Company name</label>
                        <input type="text" x-model="form.company" placeholder="Acme Inc."
                            class="h-10 w-full rounded-lg border border-gray-300 px-3.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Primary contact person</label>
                        <input type="text" x-model="form.contact_person" placeholder="John Doe, CEO"
                            class="h-10 w-full rounded-lg border border-gray-300 px-3.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Business address <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" x-model="form.address" placeholder="123 Main St, City, State 00000"
                            class="h-10 w-full rounded-lg border border-gray-300 px-3.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 focus:outline-none">
                    </div>
                </div>
                <button type="button" @click="nextStep(1)"
                    class="w-full rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    Continue →
                </button>
            </div>
        </div>

        {{-- Step 2: What You Need --}}
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-5">
                <div class="pb-2 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">What You Need</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Select your request type and the services you're interested in.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type of request <span class="text-error-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach([
                            ['bug', '🐛', 'Something is broken'],
                            ['feature', '✨', 'New feature or idea'],
                            ['content', '📝', 'Content update'],
                            ['question', '💬', 'I have a question'],
                            ['billing', '💳', 'Billing or invoice'],
                            ['emergency', '🚨', 'Emergency'],
                        ] as [$val, $icon, $label])
                        <label class="flex items-center gap-2 cursor-pointer rounded-xl border px-3 py-2.5 text-sm transition-all"
                            :class="form.issue_type === '{{ $val }}' ? 'border-brand-500 bg-brand-50 text-brand-700 font-medium' : 'border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50'">
                            <input type="radio" name="issue_type" value="{{ $val }}" x-model="form.issue_type" class="sr-only">
                            <span>{{ $icon }}</span> {{ $label }}
                        </label>
                        @endforeach
                    </div>
                    <p x-show="errors.issue_type" x-text="errors.issue_type" class="text-xs text-error-500 mt-1.5"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Services you're interested in <span class="text-gray-400 font-normal">(select all that apply)</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach([
                            ['web_design','Web Design & Development'],
                            ['seo','SEO & Content'],
                            ['social_media','Social Media'],
                            ['branding','Branding & Identity'],
                            ['email_marketing','Email Marketing'],
                            ['ppc','PPC & Paid Ads'],
                            ['analytics','Analytics & Reporting'],
                            ['ecommerce','E-commerce'],
                            ['video','Video & Animation'],
                            ['app_dev','App Development'],
                            ['copywriting','Copywriting'],
                            ['photography','Photography & Creative'],
                        ] as [$val, $label])
                        <label class="flex items-center gap-2 cursor-pointer rounded-xl border px-3 py-2 text-sm transition-all"
                            :class="hasService('{{ $val }}') ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'"
                            @click.prevent="toggleService('{{ $val }}')">
                            <svg class="size-3.5 flex-shrink-0 transition-all" :class="hasService('{{ $val }}') ? 'text-brand-500' : 'text-gray-300'"
                                fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Estimated monthly budget <span class="text-gray-400 font-normal">(optional)</span></label>
                    <select x-model="form.retainer_range"
                        class="h-10 w-full rounded-lg border border-gray-300 px-3.5 text-sm text-gray-700 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 focus:outline-none bg-white">
                        <option value="">Not sure yet</option>
                        <option value="under_1k">Under $1,000/mo</option>
                        <option value="1k_2500">$1,000 – $2,500/mo</option>
                        <option value="2500_5k">$2,500 – $5,000/mo</option>
                        <option value="5k_10k">$5,000 – $10,000/mo</option>
                        <option value="over_10k">$10,000+/mo</option>
                    </select>
                </div>

                <div class="flex gap-2 pt-1">
                    <button type="button" @click="step=1"
                        class="flex-1 rounded-lg border border-gray-200 py-2.5 text-sm text-gray-600 hover:bg-gray-50">← Back</button>
                    <button type="button" @click="nextStep(2)"
                        class="flex-1 rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Continue →</button>
                </div>
            </div>
        </div>

        {{-- Step 3: Details --}}
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                <div class="pb-2 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Tell Us More</h2>
                    <p class="text-sm text-gray-500 mt-0.5">The more detail you provide, the faster we can help.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Describe your request <span class="text-error-500">*</span></label>
                    <textarea x-model="form.description" rows="5"
                        placeholder="What's happening? What would you like us to do? Include as much context as possible..."
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 focus:outline-none resize-none"></textarea>
                    <div class="flex items-center justify-between mt-1">
                        <p x-show="errors.description" x-text="errors.description" class="text-xs text-error-500"></p>
                        <p class="text-xs text-gray-400 ml-auto" x-text="(form.description?.length || 0) + ' chars'"></p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Project goals <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea x-model="form.project_goals" rows="3"
                        placeholder="What does success look like? What are you ultimately trying to achieve?"
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 focus:outline-none resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Affected URL <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="url" x-model="form.website_url" placeholder="https://yoursite.com/page"
                        class="h-10 w-full rounded-lg border border-gray-300 px-3.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 focus:outline-none">
                </div>

                {{-- All hidden fields --}}
                <input type="hidden" name="name"                 :value="form.name">
                <input type="hidden" name="email"                :value="form.email">
                <input type="hidden" name="company"              :value="form.company">
                <input type="hidden" name="contact_person"       :value="form.contact_person">
                <input type="hidden" name="address"              :value="form.address">
                <input type="hidden" name="issue_type"           :value="form.issue_type">
                <input type="hidden" name="retainer_range"       :value="form.retainer_range">
                <input type="hidden" name="description"          :value="form.description">
                <input type="hidden" name="project_goals"        :value="form.project_goals">
                <input type="hidden" name="website_url"          :value="form.website_url">
                <template x-for="(svc, idx) in form.services_interested" :key="idx">
                    <input type="hidden" name="services_interested[]" :value="svc">
                </template>

                <div class="flex gap-2 pt-1">
                    <button type="button" @click="step=2"
                        class="flex-1 rounded-lg border border-gray-200 py-2.5 text-sm text-gray-600 hover:bg-gray-50">← Back</button>
                    <button type="submit" :disabled="submitting"
                        class="flex-1 rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-60 transition-colors">
                        <span x-show="!submitting">Submit Request ✓</span>
                        <span x-show="submitting">Processing…</span>
                    </button>
                </div>
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
            name: '', email: '', company: '', contact_person: '', address: '',
            issue_type: '', services_interested: [], retainer_range: '',
            description: '', project_goals: '', website_url: '',
        },
        errors: {},
        toggleService(s) {
            const idx = this.form.services_interested.indexOf(s);
            if (idx === -1) this.form.services_interested.push(s);
            else this.form.services_interested.splice(idx, 1);
        },
        hasService(s) {
            return this.form.services_interested.includes(s);
        },
        nextStep(from) {
            this.errors = {};
            if (from === 1) {
                if (!this.form.name.trim()) { this.errors.name = 'Your name is required.'; return; }
                if (!this.form.email.trim() || !this.form.email.includes('@')) { this.errors.email = 'A valid email is required.'; return; }
            }
            if (from === 2) {
                if (!this.form.issue_type) { this.errors.issue_type = 'Please select a request type.'; return; }
            }
            this.step = from + 1;
        },
        handleSubmit(e) {
            this.errors = {};
            if (!this.form.description.trim() || this.form.description.length < 10) {
                this.errors.description = 'Please describe your request (min 10 characters).';
                return;
            }
            this.submitting = true;
            e.target.submit();
        }
    }
}
</script>
</body>
</html>
