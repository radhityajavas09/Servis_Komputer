@props([
    'disabled' => false,
    'error' => null,
])

@php
    $baseClasses = 'block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-[#13251C] transition-all duration-150 focus:outline-none focus:ring-2 disabled:bg-[#F6F8F7] disabled:text-[#84968C] disabled:cursor-not-allowed appearance-none pr-10 cursor-pointer';
    $borderClasses = $error
        ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20 text-red-900'
        : 'border-[#E3E8E5] hover:border-[#CCD5D0] focus:border-[#15573F] focus:ring-[#15573F]/20';
    $classes = "{$baseClasses} {$borderClasses}";
@endphp

<div class="relative w-full">
    <select {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </select>
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#576B61]">
        <x-icon name="chevron-down" class="w-4 h-4" />
    </div>
</div>
