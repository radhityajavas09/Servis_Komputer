@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8']) }}>
    <div>
        @if ($eyebrow)
            <div class="mb-1.5">
                <x-eyebrow>{{ $eyebrow }}</x-eyebrow>
            </div>
        @endif
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#13251C]">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-[#576B61] mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            {{ $actions }}
        </div>
    @endisset
</div>
