<x-app-layout title="Daftar Servis Masuk" breadcrumb="Daftar Servis Masuk">
    <x-page-header
        eyebrow="WORKBENCH TEKNISI"
        title="Daftar Servis & Pengerjaan"
        subtitle="Pantau antrean servis masuk, lakukan diagnosis mendalam, pasang sparepart, dan perbarui progres servis hingga selesai."
    />

    {{-- KARTU STATISTIK RINGKASAN --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            label="Total Servis"
            :value="$totalServis"
            description="Semua perangkat terdaftar"
            icon="clipboard"
            color="primary"
        />

        <x-stat-card
            label="Menunggu Antrean"
            :value="$antrean"
            description="Status 'Diterima'"
            icon="clock"
            indicator="Antrean"
            color="amber"
        />

        <x-stat-card
            label="Sedang Dikerjakan"
            :value="$sedangDikerjakan"
            description="Pemeriksaan & perbaikan"
            icon="wrench"
            indicator="Dikerjakan"
            color="primary"
        />

        <x-stat-card
            label="Selesai Diservis"
            :value="$selesai"
            description="Menunggu diambil pemilik"
            icon="check-circle"
            color="green"
        />
    </div>

    {{-- FILTER & PENCARIAN REAL-TIME --}}
    <div x-data="liveTableSearch('{{ route('teknisi.servis.index') }}', 'teknisi-servis-table-container', '{{ addslashes($search) }}')">
        <x-card class="p-4 mb-6">
            <form x-ref="filterForm" method="GET" action="{{ route('teknisi.servis.index') }}" @submit.prevent="applyFilter()" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    {{-- Input Pencarian --}}
                    <div class="relative flex-1">
                        <x-input
                            name="q"
                            x-ref="searchInput"
                            x-model="searchQuery"
                            placeholder="Cari no. tiket, nama pelanggan, atau merk perangkat..."
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

                    {{-- Filter Status --}}
                    <div class="w-full sm:w-56">
                        <x-select name="status" @change="applyFilter()">
                            <option value="">Semua Status</option>
                            <option value="diterima" {{ $statusFilter === 'diterima' ? 'selected' : '' }}>Diterima (Antrean)</option>
                            <option value="diperiksa" {{ $statusFilter === 'diperiksa' ? 'selected' : '' }}>Dalam Pemeriksaan</option>
                            <option value="diperbaiki" {{ $statusFilter === 'diperbaiki' ? 'selected' : '' }}>Dalam Perbaikan</option>
                            <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="diambil" {{ $statusFilter === 'diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                        </x-select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-button type="submit" variant="secondary" size="md" icon="search">
                        Cari
                    </x-button>
                    @if ($search || $statusFilter)
                        <x-button href="{{ route('teknisi.servis.index') }}" variant="ghost" size="md">
                            Reset
                        </x-button>
                    @endif
                </div>
            </form>
        </x-card>

        {{-- TABEL DAFTAR SERVIS CONTAINER --}}
        <div id="teknisi-servis-table-container">
            @if ($servisList->count() > 0)
                <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-[#13251C]">
                            <thead class="bg-[#F6F8F7] text-xs font-bold text-[#576B61] uppercase tracking-wider border-b border-[#E3E8E5]">
                                <tr>
                                    <th scope="col" class="py-3.5 px-4">No. Servis</th>
                                    <th scope="col" class="py-3.5 px-4">Pelanggan</th>
                                    <th scope="col" class="py-3.5 px-4">Perangkat & Keluhan</th>
                                    <th scope="col" class="py-3.5 px-4">Status</th>
                                    <th scope="col" class="py-3.5 px-4">Teknisi</th>
                                    <th scope="col" class="py-3.5 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E3E8E5]">
                                @foreach ($servisList as $item)
                                    <tr class="hover:bg-[#F9FBFA] transition-colors">
                                        {{-- No Servis & Tgl --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-bold text-[#1F6B4B]">
                                                {{ $item->no_servis }}
                                            </div>
                                            <div class="text-xs text-[#84968C] mt-0.5">
                                                {{ $item->tgl_masuk ? $item->tgl_masuk->format('d M Y') : '-' }}
                                            </div>
                                        </td>

                                        {{-- Pelanggan --}}
                                        <td class="py-3.5 px-4">
                                            <div class="font-medium text-[#13251C]">
                                                {{ $item->pelanggan?->nama ?? '-' }}
                                            </div>
                                            <div class="text-xs text-[#84968C]">
                                                {{ $item->pelanggan?->no_telepon ?? '-' }}
                                            </div>
                                        </td>

                                        {{-- Perangkat & Keluhan --}}
                                        <td class="py-3.5 px-4 max-w-xs">
                                            <div class="font-medium text-[#13251C]">
                                                {{ $item->merk_tipe }}
                                            </div>
                                            <div class="text-xs text-[#576B61] line-clamp-1 mt-0.5">
                                                {{ $item->keluhan }}
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <x-status-badge :status="$item->status" />
                                        </td>

                                        {{-- Teknisi Penanggung Jawab --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if ($item->teknisi)
                                                <div class="inline-flex items-center gap-1.5 text-xs text-[#13251C] font-medium bg-[#E8F3EE] px-2.5 py-1 rounded-md">
                                                    <x-icon name="user" class="w-3.5 h-3.5 text-[#1F6B4B]" />
                                                    <span>{{ $item->teknisi->nama }}</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-[#84968C] italic">Belum diambil</span>
                                            @endif
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                            <a
                                                href="{{ route('teknisi.servis.show', $item) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#E8F3EE] text-[#1F6B4B] hover:bg-[#1F6B4B] hover:text-white transition-all shadow-2xs"
                                            >
                                                <x-icon name="wrench" class="w-3.5 h-3.5" />
                                                <span>Periksa / Kerjakan</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    @if ($servisList->hasPages())
                        <div class="px-4 py-3 bg-[#F6F8F7] border-t border-[#E3E8E5]">
                            {{ $servisList->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-2xl border border-[#E3E8E5] p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[#E8F3EE] flex items-center justify-center text-[#1F6B4B]">
                        <x-icon name="wrench" class="w-8 h-8" />
                    </div>
                    <h3 class="text-base font-bold text-[#13251C]">Tidak Ada Data Servis</h3>
                    <p class="text-sm text-[#576B61] mt-1 max-w-sm mx-auto">
                        @if ($search || $statusFilter)
                            Tidak ditemukan data servis yang cocok dengan kriteria pencarian Anda.
                        @else
                            Saat ini belum ada antrean atau pengerjaan servis masuk.
                        @endif
                    </p>
                    @if ($search || $statusFilter)
                        <div class="mt-4">
                            <x-button href="{{ route('teknisi.servis.index') }}" variant="secondary" size="sm">
                                Bersihkan Filter
                            </x-button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
