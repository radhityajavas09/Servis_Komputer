<x-app-layout title="Data Sparepart" breadcrumb="Data Sparepart">
    <x-page-header
        eyebrow="INVENTARIS SUKU CADANG"
        title="Kelola Data Sparepart"
        subtitle="Manajemen persediaan suku cadang komputer, pembaruan harga satuan, dan pemantauan stok menipis."
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" href="{{ route('admin.sparepart.create') }}">
                Tambah Sparepart
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- KARTU STATISTIK RINGKASAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-stat-card
            label="Total Suku Cadang"
            :value="$totalItems"
            description="Jenis suku cadang terdaftar"
            icon="package"
            color="primary"
        />

        <x-stat-card
            label="Stok Menipis (< 5)"
            :value="$lowStockCount"
            description="Perlu segera dipesan ulang"
            icon="clock"
            indicator="Perhatian"
            color="amber"
        />

        <x-stat-card
            label="Stok Habis (0 Unit)"
            :value="$outOfStockCount"
            description="Tidak tersedia untuk servis"
            icon="x-circle"
            indicator="Kosong"
            color="amber"
        />
    </div>

    {{-- FILTER & PENCARIAN REAL-TIME --}}
    <div x-data="liveTableSearch('{{ route('admin.sparepart.index') }}', 'sparepart-table-container', '{{ addslashes($search) }}')">
        <x-card class="p-4 mb-6">
            <form x-ref="filterForm" method="GET" action="{{ route('admin.sparepart.index') }}" @submit.prevent="applyFilter()" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    {{-- Input Pencarian --}}
                    <div class="relative flex-1">
                        <x-input
                            name="q"
                            x-ref="searchInput"
                            x-model="searchQuery"
                            placeholder="Cari berdasarkan nama sparepart..."
                            class="pl-9 pr-9"
                            @input.debounce.300ms="applyFilter()"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#84968C]">
                            <x-icon name="search" class="w-4 h-4" />
                        </div>

                        {{-- Loading Spinner --}}
                        <div x-show="loading" x-cloak class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="animate-spin h-4 w-4 text-[#15573F]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>

                        {{-- Tombol Clear Cepat --}}
                        <button
                            type="button"
                            x-show="searchQuery && !loading"
                            x-cloak
                            @click="clearSearch()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#84968C] hover:text-[#13251C] transition-colors cursor-pointer"
                            title="Hapus kata kunci"
                        >
                            <x-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>

                    {{-- Filter Stok --}}
                    <div class="w-full sm:w-56">
                        <x-select name="stock" @change="applyFilter()">
                            <option value="">Semua Status Stok</option>
                            <option value="tersedia" {{ $stockFilter === 'tersedia' ? 'selected' : '' }}>Stok Normal (≥ 5 unit)</option>
                            <option value="menipis" {{ $stockFilter === 'menipis' ? 'selected' : '' }}>Stok Menipis (< 5 unit)</option>
                            <option value="habis" {{ $stockFilter === 'habis' ? 'selected' : '' }}>Stok Habis (0 unit)</option>
                        </x-select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-button type="submit" variant="secondary" size="md" icon="search">
                        Cari
                    </x-button>
                    @if ($search || $stockFilter)
                        <x-button href="{{ route('admin.sparepart.index') }}" variant="ghost" size="md">
                            Reset
                        </x-button>
                    @endif
                </div>
            </form>
        </x-card>

        {{-- TABEL DATA SPAREPART CONTAINER --}}
        <div id="sparepart-table-container">

    {{-- TABEL DATA SPAREPART --}}
    @if ($spareparts->count() > 0)
        <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[#13251C]">
                    <thead class="bg-[#F6F8F7] text-xs font-bold text-[#576B61] uppercase tracking-wider border-b border-[#E3E8E5]">
                        <tr>
                            <th scope="col" class="py-3.5 px-4 sm:px-6">Nama Suku Cadang</th>
                            <th scope="col" class="py-3.5 px-4">Harga Satuan</th>
                            <th scope="col" class="py-3.5 px-4">Status & Sisa Stok</th>
                            <th scope="col" class="py-3.5 px-4 hidden md:table-cell">Riwayat Pemakaian</th>
                            <th scope="col" class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F4F2]">
                        @foreach ($spareparts as $item)
                            @php
                                $isUsed = ($item->detail_servis_count ?? 0) > 0;
                            @endphp
                            <tr class="hover:bg-[#F6F8F7] transition-colors">
                                {{-- Nama Sparepart --}}
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center shrink-0 border border-[#B2DEC6]">
                                            <x-icon name="package" class="w-4 h-4" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-[#13251C] truncate">
                                                {{ $item->nama_sparepart }}
                                            </p>
                                            <p class="text-[11px] text-[#576B61] truncate">
                                                ID: #SPR-{{ str_pad((string)$item->id_sparepart, 3, '0', STR_PAD_LEFT) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Harga Satuan --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-semibold text-[#13251C]">{{ $item->harga_rupiah }}</span>
                                </td>

                                {{-- Status Stok --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($item->isStokHabis())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                            Habis (0 unit)
                                        </span>
                                    @elseif ($item->isStokMenipis())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Menipis ({{ $item->stok }} unit)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#15573F]"></span>
                                            {{ $item->stok }} unit
                                        </span>
                                    @endif
                                </td>

                                {{-- Riwayat Pemakaian --}}
                                <td class="py-3.5 px-4 text-xs text-[#576B61] whitespace-nowrap hidden md:table-cell">
                                    @if ($isUsed)
                                        <span class="font-medium text-[#13251C]">{{ $item->detail_servis_count }} kali</span> digunakan pada servis
                                    @else
                                        <span class="text-[#84968C]">Belum pernah digunakan</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Tombol Ubah --}}
                                        <x-button
                                            href="{{ route('admin.sparepart.edit', $item) }}"
                                            variant="secondary"
                                            size="sm"
                                            icon="pencil"
                                            title="Ubah Data Sparepart"
                                        >
                                            <span class="hidden sm:inline">Ubah</span>
                                        </x-button>

                                        {{-- Tombol Hapus --}}
                                        @if ($isUsed)
                                            <span title="Sparepart tidak dapat dihapus karena sudah digunakan pada data servis">
                                                <x-button
                                                    variant="secondary"
                                                    size="sm"
                                                    icon="trash"
                                                    @click="$dispatch('open-modal-confirm', {
                                                        name: 'confirm-delete-sparepart',
                                                        url: '{{ route('admin.sparepart.destroy', $item) }}',
                                                        message: 'Hapus sparepart {{ addslashes($item->nama_sparepart) }}? Sparepart ini telah digunakan dalam riwayat servis dan sistem akan menolak penghapusan untuk melindungi integritas data.'
                                                    })"
                                                    class="text-red-600 hover:text-red-700 hover:border-red-300"
                                                >
                                                    <span class="hidden sm:inline">Hapus</span>
                                                </x-button>
                                            </span>
                                        @else
                                            <x-button
                                                variant="secondary"
                                                size="sm"
                                                icon="trash"
                                                @click="$dispatch('open-modal-confirm', {
                                                    name: 'confirm-delete-sparepart',
                                                    url: '{{ route('admin.sparepart.destroy', $item) }}',
                                                    message: 'Hapus sparepart {{ addslashes($item->nama_sparepart) }}? Tindakan ini tidak dapat dibatalkan.'
                                                })"
                                                class="text-red-600 hover:text-red-700 hover:border-red-300"
                                            >
                                                <span class="hidden sm:inline">Hapus</span>
                                            </x-button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <x-pagination :paginator="$spareparts" />
        </div>
    @else
        <x-empty-state
            icon="package"
            title="Tidak ada sparepart ditemukan"
            description="Tidak ada suku cadang yang sesuai dengan kriteria pencarian atau status filter stok yang dipilih."
        >
            <x-button href="{{ route('admin.sparepart.create') }}" variant="primary" icon="plus" size="sm">
                Tambah Sparepart Baru
            </x-button>
        </x-empty-state>
    @endif
    </div>
</div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <x-modal-confirm
        name="confirm-delete-sparepart"
        title="Hapus Data Sparepart"
        confirmText="Ya, Hapus Sparepart"
        confirmVariant="danger"
    />
</x-app-layout>
