@props([
    'title',
    'message' => null,
])

<div {{ $attributes->merge(['class' => 'mx-auto flex w-full max-w-2xl flex-col items-center rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-10']) }}>
    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
        @isset($icon)
            {{ $icon }}
        @else
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h7M5 6.5A1.5 1.5 0 0 1 6.5 5h11A1.5 1.5 0 0 1 19 6.5v11A1.5 1.5 0 0 1 17.5 19h-11A1.5 1.5 0 0 1 5 17.5v-11z"/>
            </svg>
        @endisset
    </div>

    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $title }}</h3>

    @if ($message)
        <p class="mt-2 text-sm text-slate-500">{{ $message }}</p>
    @endif

    @isset($actions)
        <div class="mt-5 flex flex-wrap justify-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
