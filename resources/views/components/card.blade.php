@props([
    'padding' => 'p-5 sm:p-6',
])

<div {{ $attributes->merge(['class' => "bg-white rounded-2xl border border-[#E3E8E5] shadow-xs {$padding}"]) }}>
    {{ $slot }}
</div>
