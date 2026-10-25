@props([
    'icon' => 'box',
    'title' => 'Belum ada data',
    'description' => 'Data yang Anda cari saat ini belum tersedia dalam sistem.',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-8 sm:p-12 text-center bg-white rounded-2xl border border-dashed border-[#CCD5D0]']) }}>
    <div class="w-14 h-14 rounded-2xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center mb-4 border border-[#B2DEC6]">
        <x-icon :name="$icon" class="w-7 h-7" />
    </div>

    <h3 class="text-base font-bold text-[#13251C] mb-1.5">{{ $title }}</h3>
    <p class="text-xs sm:text-sm text-[#576B61] max-w-sm mb-6 leading-relaxed">{{ $description }}</p>

    @if ($slot->isNotEmpty())
        <div class="flex items-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
