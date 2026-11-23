<x-app-layout title="Data Servis" breadcrumb="Data Servis">
    <x-page-header
        eyebrow="PELAYANAN & DATA SERVIS"
        title="Data Servis Perangkat"
        subtitle="Kelola seluruh tiket servis, pantau perkembangan pengerjaan teknisi, dan serahkan perangkat yang telah selesai."
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" href="{{ route('petugas.servis.tambah') }}">
                Pendaftaran Baru
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- KARTU STATISTIK RINGKASAN --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            label="Total Servis Masuk"
            :value="$totalServis"
            description="Seluruh pengerjaan terdaftar"
            icon="clipboard"
            color="primary"
        />

        <x-stat-card
            label="Dalam Proses"
            :value="$dalamProses"
            description="Sedang diperiksa / diperbaiki"
            icon="clock"
            indicator="Proses"
            color="amber"
        />

        <x-stat-card
            label="Siap Diambil"
            :value="$siapDiambil"
            description="Selesai diservis teknisi"
            icon="check-circle"
            indicator="Siap Serah"
            color="green"
        />

        <x-stat-card
            label="Sudah Diambil"
            :value="$sudahDiambil"
            description="Telah diserahkan ke pemilik"
            icon="user"
            color="blue"
        />
    </div>

    {{-- FILTER & PENCARIAN --}}
    <x-card class="p-4 mb-6">
        <form method="GET" action="{{ route('petugas.servis.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Input Pencarian --}}
                <div class="relative flex-1">
                    <x-input
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari no. tiket, nama pelanggan, atau merk perangkat..."
                        class="pl-9"
                        @input.debounce.400ms="$el.form.submit()"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#84968C]">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                </div>

                {{-- Filter Status --}}
                <div class="w-full sm:w-56">
                    <x-select name="status" @change="$el.form.submit()">
                        <option value="">Semua Status Servis</option>
                        <option value="diterima" {{ $statusFilter === 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="diperiksa" {{ $statusFilter === 'diperiksa' ? 'selected' : '' }}>Dalam Pemeriksaan</option>
                        <option value="diperbaiki" {{ $statusFilter === 'diperbaiki' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai (Siap Diambil)</option>
                        <option value="diambil" {{ $statusFilter === 'diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                    </x-select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <x-button type="submit" variant="secondary" size="md" icon="search">
                    Cari
                </x-button>
                @if ($search || $statusFilter)
                    <x-button href="{{ route('petugas.servis.index') }}" variant="ghost" size="md">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    {{-- TABEL DATA SERVIS --}}
    @if ($servisList->count() > 0)
        <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[#13251C]">
                    <thead class="bg-[#F6F8F7] text-xs font-bold text-[#576B61] uppercase tracking-wider border-b border-[#E3E8E5]">
                        <tr>
                            <th scope="col" class="py-3.5 px-4 sm:px-6">No. Servis</th>
                            <th scope="col" class="py-3.5 px-4">Pelanggan</th>
                            <th scope="col" class="py-3.5 px-4">Perangkat</th>
                            <th scope="col" class="py-3.5 px-4">Status</th>
                            <th scope="col" class="py-3.5 px-4 hidden md:table-cell">Tgl. Masuk</th>
                            <th scope="col" class="py-3.5 px-4 hidden lg:table-cell">Total Biaya</th>
                            <th scope="col" class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F4F2]">
                        @foreach ($servisList as $item)
                            <tr class="hover:bg-[#F6F8F7] transition-colors">
                                {{-- Nomor Servis --}}
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <a
                                        href="{{ route('petugas.servis.detail', $item) }}"
                                        class="font-mono font-bold text-xs px-2.5 py-1 rounded-md bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6] hover:bg-[#15573F] hover:text-white transition-colors inline-block"
                                    >
                                        {{ $item->no_servis }}
                                    </a>
                                </td>

                                {{-- Pelanggan --}}
                                <td class="py-3.5 px-4">
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#13251C] text-xs truncate">
                                            {{ $item->pelanggan->nama }}
                                        </p>
                                        <p class="text-[11px] font-mono text-[#576B61] truncate">
                                            {{ $item->pelanggan->no_telepon }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Perangkat --}}
                                <td class="py-3.5 px-4">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-[#13251C] text-xs truncate">
                                            {{ $item->merk_tipe }}
                                        </p>
                                        <p class="text-[11px] text-[#576B61] truncate">
                                            {{ $item->jenis_perangkat }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Status Servis --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <x-status-badge :status="$item->status" />
                                </td>

                                {{-- Tanggal Masuk --}}
                                <td class="py-3.5 px-4 text-xs text-[#576B61] whitespace-nowrap hidden md:table-cell">
                                    {{ $item->tgl_masuk?->translatedFormat('d M Y') ?? '-' }}
                                </td>

                                {{-- Total Biaya --}}
                                <td class="py-3.5 px-4 whitespace-nowrap hidden lg:table-cell">
                                    <span class="font-bold text-xs text-[#13251C]">
                                        Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Tombol Detail --}}
                                        <x-button
                                            href="{{ route('petugas.servis.detail', $item) }}"
                                            variant="secondary"
                                            size="sm"
                                            icon="eye"
                                            title="Lihat Detail Pengerjaan"
                                        >
                                            <span class="hidden sm:inline">Detail</span>
                                        </x-button>

                                        {{-- Tombol Cetak Tanda Terima --}}
                                        <x-button
                                            href="{{ route('petugas.servis.tanda-terima', $item) }}"
                                            variant="secondary"
                                            size="sm"
                                            icon="printer"
                                            title="Cetak Ulang Tanda Terima"
                                        />

                                        {{-- Tombol Serahkan Perangkat --}}
                                        @if ($item->status === \App\Enums\StatusServis::Selesai)
                                            <x-button
                                                href="{{ route('petugas.servis.serahkan', $item) }}"
                                                variant="primary"
                                                size="sm"
                                                icon="check-circle"
                                                title="Serahkan Perangkat ke Pelanggan"
                                            >
                                                <span>Serahkan</span>
                                            </x-button>
                                        @elseif ($item->status === \App\Enums\StatusServis::Diambil)
                                            <span class="text-[11px] font-semibold text-[#576B61] px-2 py-1 rounded bg-[#F0F4F2]" title="Perangkat telah diserahkan pada {{ $item->tgl_diambil?->translatedFormat('d M Y') }}">
                                                Diambil
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <x-pagination :paginator="$servisList" />
        </div>
    @else
        <x-empty-state
            icon="clipboard"
            title="Tidak ada tiket servis ditemukan"
            description="Tidak ada data pengerjaan yang cocok dengan kriteria pencarian atau filter status yang dipilih."
        >
            <x-button href="{{ route('petugas.servis.tambah') }}" variant="primary" icon="plus" size="sm">
                Daftarkan Servis Baru
            </x-button>
        </x-empty-state>
    @endif
</x-app-layout>
