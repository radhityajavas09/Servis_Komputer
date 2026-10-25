@props([
    'status',
])

@php
    use App\Enums\StatusServis;

    $statusEnum = $status instanceof StatusServis ? $status : StatusServis::tryFrom((string)$status);
    $statusValue = $statusEnum?->value ?? (string)$status;
    $label = $statusEnum?->label() ?? ucfirst($statusValue);

    $styles = match ($statusValue) {
        'diperiksa' => [
            'bg' => 'bg-blue-50 text-blue-800 border-blue-200',
            'dot' => 'bg-blue-600',
        ],
        'diperbaiki' => [
            'bg' => 'bg-purple-50 text-purple-800 border-purple-200',
            'dot' => 'bg-purple-600',
        ],
        'selesai' => [
            'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'dot' => 'bg-emerald-600',
        ],
        'diambil' => [
            'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
            'dot' => 'bg-slate-500',
        ],
        default => [
            'bg' => 'bg-amber-50 text-amber-800 border-amber-200',
            'dot' => 'bg-amber-500',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {$styles['bg']} tracking-tight select-none shrink-0"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $styles['dot'] }}"></span>
    <span>{{ $label }}</span>
</span>
