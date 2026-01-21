@props([
    'rounded' => 'rounded-md',
])

<div {{ $attributes->merge(['class' => 'animate-pulse bg-slate-200 ' . $rounded]) }} aria-hidden="true"></div>
