@props([
    'paginator',
])

@if ($paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'flex items-center justify-between px-4 py-3 bg-white border-t border-[#E3E8E5] text-xs text-[#576B61]']) }}>
        <div class="hidden sm:block">
            Menampilkan <span class="font-bold text-[#13251C]">{{ $paginator->firstItem() }}</span> sampai <span class="font-bold text-[#13251C]">{{ $paginator->lastItem() }}</span> dari <span class="font-bold text-[#13251C]">{{ $paginator->total() }}</span> data
        </div>

        <div class="flex items-center gap-1.5 ml-auto">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded-xl border border-[#E3E8E5] bg-[#F6F8F7] text-[#84968C] cursor-not-allowed">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-[#E3E8E5] bg-white text-[#13251C] hover:bg-[#F6F8F7] transition-colors font-medium">
                    Sebelumnya
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-[#E3E8E5] bg-white text-[#13251C] hover:bg-[#F6F8F7] transition-colors font-medium">
                    Berikutnya
                </a>
            @else
                <span class="px-3 py-1.5 rounded-xl border border-[#E3E8E5] bg-[#F6F8F7] text-[#84968C] cursor-not-allowed">
                    Berikutnya
                </span>
            @endif
        </div>
    </div>
@endif
