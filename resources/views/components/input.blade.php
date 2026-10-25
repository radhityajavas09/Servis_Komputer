@props([
    'disabled' => false,
    'error' => null,
])

@php
    $baseClasses = 'block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-[#13251C] placeholder-[#84968C] transition-all duration-150 focus:outline-none focus:ring-2 disabled:bg-[#F6F8F7] disabled:text-[#84968C] disabled:cursor-not-allowed';
    $borderClasses = $error
        ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20 text-red-900'
        : 'border-[#E3E8E5] hover:border-[#CCD5D0] focus:border-[#15573F] focus:ring-[#15573F]/20';
    $classes = "{$baseClasses} {$borderClasses}";
@endphp

<input {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
