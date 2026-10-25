@props([
    'label',
    'value',
    'icon',
    'description',
    'indicator' => null,
    'color' => 'primary',
])

@php
    $formattedValue = is_numeric($value) && (int)$value < 10 && (int)$value >= 0 ? sprintf('%02d', $value) : $value;

    $colorStyles = match ($color) {
        'blue' => [
            'iconBg' => 'bg-blue-50 text-blue-700 border border-blue-100',
            'indicator' => 'text-blue-700 bg-blue-50',
        ],
        'amber' => [
            'iconBg' => 'bg-amber-50 text-amber-700 border border-amber-100',
            'indicator' => 'text-amber-700 bg-amber-50',
        ],
        'purple' => [
            'iconBg' => 'bg-purple-50 text-purple-700 border border-purple-100',
            'indicator' => 'text-purple-700 bg-purple-50',
        ],
        'green' => [
            'iconBg' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
            'indicator' => 'text-emerald-700 bg-emerald-50',
        ],
        default => [
            'iconBg' => 'bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6]',
            'indicator' => 'text-[#15573F] bg-[#EDF7F2]',
        ],
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-[#E3E8E5] p-5 shadow-xs flex flex-col justify-between hover:border-[#CCD5D0] transition-colors']) }}>
    <div class="flex items-start justify-between gap-2 mb-3">
        <span class="text-xs font-semibold text-[#576B61] tracking-tight">{{ $label }}</span>
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $colorStyles['iconBg'] }}">
            <x-icon :name="$icon" class="w-4.5 h-4.5" />
        </div>
    </div>

    <div>
        <div class="text-3xl font-extrabold tracking-tight text-[#13251C] mb-2 font-mono tabular-nums">
            {{ $formattedValue }}
        </div>
        <div class="flex items-center justify-between text-xs gap-2 pt-2 border-t border-[#F0F4F2]">
            <span class="text-[#576B61] truncate">{{ $description }}</span>
            @if ($indicator)
                <span class="shrink-0 font-medium px-2 py-0.5 rounded-full {{ $colorStyles['indicator'] }} text-[11px]">
                    {{ $indicator }}
                </span>
            @endif
        </div>
    </div>
</div>
