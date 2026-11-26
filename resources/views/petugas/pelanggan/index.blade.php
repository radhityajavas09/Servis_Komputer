<x-app-layout title="Data Pelanggan" breadcrumb="Data Pelanggan">
    <x-page-header
        eyebrow="PELAYANAN & PELANGGAN"
        title="Kelola Data Pelanggan"
        subtitle="Manajemen data kontak, alamat, serta penelusuran riwayat pelayanan servis pelanggan."
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" href="{{ route('petugas.pelanggan.create') }}">
                Tambah Pelanggan
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- KARTU STATISTIK RINGKASAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-stat-card
            label="Total Pelanggan"
            :value="$totalPelanggan"
            description="Pelanggan terdaftar dalam sistem"
            icon="user"
            color="primary"
        />

        <x-stat-card
            label="Pelanggan Aktif Servis"
            :value="$pelangganAktif"
            description="Perangkat sedang dalam penanganan"
            icon="clock"
            indicator="Proses Servis"
            color="amber"
        />

        <x-stat-card
            label="Total Servis Tercatat"
            :value="$totalServis"
            description="Total tiket servis masuk"
            icon="clipboard"
            color="blue"
        />
    </div>

    {{-- FILTER & PENCARIAN REAL-TIME --}}
    <div x-data="liveTableSearch('{{ route('petugas.pelanggan.index') }}', 'pelanggan-table-container', '{{ addslashes($search) }}')">
        <x-card class="p-4 mb-6">
            <form x-ref="filterForm" method="GET" action="{{ route('petugas.pelanggan.index') }}" @submit.prevent="applyFilter()" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="relative flex-1">
                    <x-input
                        name="q"
                        x-ref="searchInput"
                        x-model="searchQuery"
                        placeholder="Cari berdasarkan nama, nomor telepon, atau alamat pelanggan..."
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

                <div class="flex items-center gap-2">
                    <x-button type="submit" variant="secondary" size="md" icon="search">
                        Cari
                    </x-button>
                    @if ($search)
                        <x-button href="{{ route('petugas.pelanggan.index') }}" variant="ghost" size="md">
                            Reset
                        </x-button>
                    @endif
                </div>
            </form>
        </x-card>

        {{-- TABEL DATA PELANGGAN CONTAINER --}}
        <div id="pelanggan-table-container">
            @if ($pelanggan->count() > 0)
                <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-[#13251C]">
                    <thead class="bg-[#F6F8F7] text-xs font-bold text-[#576B61] uppercase tracking-wider border-b border-[#E3E8E5]">
                        <tr>
                            <th scope="col" class="py-3.5 px-4 sm:px-6">Pelanggan</th>
                            <th scope="col" class="py-3.5 px-4">Kontak / WhatsApp</th>
                            <th scope="col" class="py-3.5 px-4 hidden md:table-cell">Alamat</th>
                            <th scope="col" class="py-3.5 px-4 whitespace-nowrap">Riwayat Servis</th>
                            <th scope="col" class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F4F2]">
                        @foreach ($pelanggan as $item)
                            @php
                                $hasService = ($item->servis_count ?? 0) > 0;
                            @endphp
                            <tr class="hover:bg-[#F6F8F7] transition-colors">
                                {{-- Nama Pelanggan --}}
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#EDF7F2] text-[#15573F] font-bold text-xs flex items-center justify-center shrink-0 border border-[#B2DEC6]">
                                            {{ $item->inisial }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('petugas.pelanggan.show', $item) }}" class="font-bold text-[#13251C] hover:text-[#15573F] transition-colors truncate block">
                                                {{ $item->nama }}
                                            </a>
                                            <p class="text-[11px] text-[#576B61] truncate">
                                                Terdaftar: {{ $item->created_at?->translatedFormat('d M Y') ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kontak & WhatsApp Link --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-semibold text-[#13251C]">{{ $item->no_telepon }}</span>
                                        <a
                                            href="{{ $item->wa_link }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 transition-colors"
                                            title="Buka Chat WhatsApp"
                                        >
                                            <x-icon name="phone" class="w-3.5 h-3.5" />
                                        </a>
                                    </div>
                                </td>

                                {{-- Alamat --}}
                                <td class="py-3.5 px-4 text-xs text-[#576B61] max-w-xs truncate hidden md:table-cell" title="{{ $item->alamat }}">
                                    {{ $item->alamat }}
                                </td>

                                {{-- Total Riwayat Servis --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <a href="{{ route('petugas.pelanggan.show', $item) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F6F8F7] text-[#13251C] border border-[#E3E8E5] hover:border-[#15573F] hover:text-[#15573F] transition-colors">
                                        <x-icon name="clipboard" class="w-3.5 h-3.5 text-[#576B61]" />
                                        <span>{{ $item->servis_count }} servis</span>
                                    </a>
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Tombol Detail Riwayat --}}
                                        <x-button
                                            href="{{ route('petugas.pelanggan.show', $item) }}"
                                            variant="secondary"
                                            size="sm"
                                            icon="eye"
                                            title="Lihat Riwayat Servis Pelanggan"
                                        >
                                            <span class="hidden sm:inline">Riwayat</span>
                                        </x-button>

                                        {{-- Tombol Ubah --}}
                                        <x-button
                                            href="{{ route('petugas.pelanggan.edit', $item) }}"
                                            variant="secondary"
                                            size="sm"
                                            icon="pencil"
                                            title="Ubah Data Pelanggan"
                                        >
                                            <span class="hidden sm:inline">Ubah</span>
                                        </x-button>

                                        {{-- Tombol Hapus --}}
                                        @if ($hasService)
                                            <span title="Pelanggan tidak dapat dihapus karena memiliki riwayat servis">
                                                <x-button
                                                    variant="secondary"
                                                    size="sm"
                                                    icon="trash"
                                                    @click="$dispatch('open-modal-confirm', {
                                                        name: 'confirm-delete-pelanggan',
                                                        url: '{{ route('petugas.pelanggan.destroy', $item) }}',
                                                        message: 'Hapus pelanggan {{ addslashes($item->nama) }}? Pelanggan ini memiliki {{ $item->servis_count }} riwayat servis dan sistem akan menolak penghapusan untuk melindungi integritas data.'
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
                                                    name: 'confirm-delete-pelanggan',
                                                    url: '{{ route('petugas.pelanggan.destroy', $item) }}',
                                                    message: 'Hapus pelanggan {{ addslashes($item->nama) }}? Tindakan ini tidak dapat dibatalkan.'
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
            <x-pagination :paginator="$pelanggan" />
        </div>
    @else
        <x-empty-state
            icon="user"
            title="Tidak ada pelanggan ditemukan"
            description="Tidak ada pelanggan yang sesuai dengan kriteria pencarian nama, nomor telepon, atau alamat."
        >
            <x-button href="{{ route('petugas.pelanggan.create') }}" variant="primary" icon="plus" size="sm">
                Tambah Pelanggan Baru
            </x-button>
        </x-empty-state>
    @endif
    </div>
</div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <x-modal-confirm
        name="confirm-delete-pelanggan"
        title="Hapus Data Pelanggan"
        confirmText="Ya, Hapus Pelanggan"
        confirmVariant="danger"
    />
</x-app-layout>
