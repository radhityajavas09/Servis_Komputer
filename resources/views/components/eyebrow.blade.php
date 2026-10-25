@props([
    'dotColor' => 'bg-[#15573F]',
])

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-[11px] font-bold tracking-widest uppercase text-[#15573F] select-none']) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} shrink-0"></span>
    <span>{{ $slot }}</span>
</div>
