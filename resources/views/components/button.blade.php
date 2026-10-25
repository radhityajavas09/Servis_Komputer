@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'href' => null,
    'type' => 'button',
    'loading' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer select-none';

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs gap-1.5 min-h-[36px]',
        'lg' => 'px-5 py-3 text-base gap-2.5 min-h-[48px]',
        default => 'px-4 py-2.5 text-sm gap-2 min-h-[42px]',
    };

    $variantClasses = match ($variant) {
        'secondary' => 'bg-white text-[#13251C] border border-[#E3E8E5] hover:bg-[#F6F8F7] hover:border-[#CCD5D0] focus:ring-[#15573F]/20 shadow-2xs',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 shadow-2xs',
        'ghost' => 'bg-transparent text-[#576B61] hover:text-[#13251C] hover:bg-[#EFF3F0] focus:ring-[#15573F]/20',
        default => 'bg-[#15573F] text-white hover:bg-[#104430] active:bg-[#0C3223] focus:ring-[#15573F] shadow-xs',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-icon :name="$icon" class="{{ $size === 'sm' ? 'w-4 h-4' : 'w-4.5 h-4.5' }}" />
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($loading)
            <svg class="animate-spin -ml-0.5 mr-2 h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        @elseif ($icon)
            <x-icon :name="$icon" class="{{ $size === 'sm' ? 'w-4 h-4' : 'w-4.5 h-4.5' }}" />
        @endif
        {{ $slot }}
    </button>
@endif
