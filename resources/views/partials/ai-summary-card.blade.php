{{--
    Reusable AI Summary Card
    Props:
      $summary       — AiSummary model or null
      $generateRoute — named route string for the generate POST action
      $generateParam — route parameter value (the model)
      $label         — "Project" or "Client"
--}}
<div class="rounded-xl border border-brand-200/60 bg-gradient-to-br from-brand-50/40 to-white dark:from-brand-500/5 dark:to-gray-900 dark:border-brand-800/40">
    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-brand-100 dark:border-brand-800/30">
        <div class="flex items-center gap-2">
            <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <h3 class="text-sm font-semibold text-brand-700 dark:text-brand-300">AI Intelligence</h3>
            @if($summary)
            <span class="text-xs text-brand-400 dark:text-brand-500">· updated {{ $summary->updated_at->diffForHumans() }}</span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @if($summary)
            {{-- Confidence indicator --}}
            @php $conf = (float) $summary->confidence; @endphp
            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                <div class="h-1.5 w-14 rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-1.5 rounded-full {{ $conf >= 0.8 ? 'bg-success-400' : ($conf >= 0.5 ? 'bg-warning-400' : 'bg-error-400') }}"
                        style="width: {{ round($conf * 100) }}%"></div>
                </div>
                <span>{{ round($conf * 100) }}%</span>
            </div>
            {{-- Toggle client visibility --}}
            <form action="{{ route('agency.ai-summaries.toggle-visible', $summary) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit"
                    class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs font-medium transition-colors
                    {{ $summary->visible_to_client
                        ? 'bg-success-50 text-success-700 hover:bg-success-100 dark:bg-success-500/10 dark:text-success-400'
                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}"
                    title="{{ $summary->visible_to_client ? 'Click to hide from client' : 'Click to share with client' }}">
                    {{ $summary->visible_to_client ? 'Visible to client' : 'Hidden from client' }}
                </button>
            </form>
            @endif
            {{-- Generate / Regenerate button --}}
            <form action="{{ route($generateRoute, $generateParam) }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors
                    {{ $summary ? 'border border-brand-200 text-brand-600 hover:bg-brand-50 dark:border-brand-800 dark:text-brand-400 dark:hover:bg-brand-500/10' : 'bg-brand-500 text-white hover:bg-brand-600' }}">
                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ $summary ? 'Regenerate' : 'Generate AI Summary' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Body --}}
    @if($summary)
    <div class="px-5 py-4 space-y-4">
        {{-- Main content --}}
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $summary->content }}</p>

        {{-- Three-panel breakdown --}}
        @if($summary->what_happened || $summary->why || $summary->what_next)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach([
                ['label' => 'What happened',  'value' => $summary->what_happened, 'color' => 'blue'],
                ['label' => 'Why it matters', 'value' => $summary->why,           'color' => 'brand'],
                ['label' => 'What\'s next',   'value' => $summary->what_next,     'color' => 'success'],
            ] as $panel)
            @if($panel['value'])
            <div class="rounded-lg bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 px-3 py-3">
                <p class="text-xs font-semibold text-{{ $panel['color'] }}-600 dark:text-{{ $panel['color'] }}-400 mb-1 uppercase tracking-wide">{{ $panel['label'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{{ $panel['value'] }}</p>
            </div>
            @endif
            @endforeach
        </div>
        @endif

        {{-- Client-facing version (collapsed) --}}
        @if($summary->client_content)
        <div x-data="{ open: false }">
            <button @click="open=!open"
                class="flex items-center gap-1 text-xs text-gray-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                <svg class="size-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                Client-facing version
            </button>
            <div x-show="open" x-cloak class="mt-2 rounded-lg border border-dashed border-brand-200 dark:border-brand-800/60 bg-brand-50/30 dark:bg-brand-500/5 px-4 py-3">
                <p class="text-xs font-medium text-brand-600 dark:text-brand-400 mb-1">What your client sees:</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 italic leading-relaxed">{{ $summary->client_content }}</p>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="px-5 py-6 text-center">
        <p class="text-sm text-gray-400 dark:text-gray-500">No AI summary yet for this {{ strtolower($label) }}.</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Click "Generate AI Summary" to create one.</p>
    </div>
    @endif
</div>
