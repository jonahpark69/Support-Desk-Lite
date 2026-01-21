@props([
    'type',
    'value',
])

@php
    $mapping = (array) config("ticket.$type", []);
    $entry = $mapping[$value] ?? null;
    $label = is_array($entry) ? ($entry['label'] ?? $value) : $value;
    $baseClasses = 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset';
    $colorClasses = is_array($entry)
        ? ($entry['classes'] ?? 'bg-slate-100 text-slate-700 ring-slate-200')
        : 'bg-slate-100 text-slate-700 ring-slate-200';
@endphp

@if ($value !== null && $value !== '')
    <span {{ $attributes->merge(['class' => $baseClasses . ' ' . $colorClasses]) }}>
        {{ $label }}
    </span>
@endif
