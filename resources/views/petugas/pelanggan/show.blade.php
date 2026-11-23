<x-app-layout title="Riwayat Servis Pelanggan" breadcrumb="Data Pelanggan > Riwayat">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-eyebrow>PROFIL & RIWAYAT PELANGGAN</x-eyebrow>
            <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1">{{ $pelanggan->nama }}</h1>
            <p class="text-xs text-[#576B61] mt-0.5">Informasi profil pelanggan serta daftar seluruh perangkat yang pernah diservis.</p>
        </div>

        <div class="flex items-center gap-2">
            <x-button href="{{ route('petugas.pelanggan.index') }}" variant="secondary" size="sm" icon="arrow-left">
                Kembali
            </x-button>
            <x-button href="{{ route('petugas.pelanggan.edit', $pelanggan) }}" variant="secondary" size="sm" icon="pencil">
                Ubah Profil
            </x-button>
        </div>
    </div>

    {{-- KARTU INFORMASI PELANGGAN --}}
    <x-card class="p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Kolom 1: Profil --}}
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-[#EDF7F2] text-[#15573F] font-bold text-lg flex items-center justify-center shrink-0 border border-[#B2DEC6]">
                    {{ $pelanggan->inisial }}
                </div>
                <div class="min-w-0">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#84968C] block">Nama Pelanggan</span>
                    <h2 class="text-base font-bold text-[#13251C] truncate">{{ $pelanggan->nama }}</h2>
                    <span class="text-xs text-[#576B61] block mt-0.5">
                        ID: #CUST-{{ str_pad((string)$pelanggan->id_pelanggan, 4, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="text-xs text-[#84968C] block mt-0.5">
                        Terdaftar: {{ $pelanggan->created_at?->translatedFormat('d F Y') ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- Kolom 2: Kontak --}}
            <div class="space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#84968C] block">Kontak & Telepon</span>
                <div class="flex items-center gap-2 pt-1">
                    <span class="font-mono text-sm font-semibold text-[#13251C]">{{ $pelanggan->no_telepon }}</span>
                    <a
                        href="{{ $pelanggan->wa_link }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors"
                        title="Chat WhatsApp Pelanggan"
                    >
                        <x-icon name="phone" class="w-3.5 h-3.5" />
                        <span>WhatsApp</span>
                    </a>
                </div>
                <span class="text-[11px] text-[#576B61] block pt-1">
                    Hubungi nomor ini untuk konfirmasi status pengerjaan perangkat.
                </span>
            </div>

            {{-- Kolom 3: Alamat --}}
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#84968C] block">Alamat Domisili</span>
                <p class="text-xs text-[#13251C] leading-relaxed mt-1">
                    {{ $pelanggan->alamat }}
                </p>
            </div>
        </div>
    </x-card>

    {{-- DAFTAR RIWAYAT SERVIS --}}
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-[#13251C]">Riwayat Servis Perangkat</h2>
            <p class="text-xs text-[#576B61]">Daftar tiket servis yang pernah didaftarkan oleh {{ $pelanggan->nama }}.</p>
        </div>
        <span class="text-xs font-semibold text-[#15573F] bg-[#EDF7F2] px-3 py-1 rounded-full border border-[#B2DEC6]">
            Total {{ $pelanggan->servis->count() }} Servis
        </span>
    </div>

    @if ($pelanggan->servis->count() > 0)
        <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[#13251C]">
                    <thead class="bg-[#F6F8F7] text-xs font-bold text-[#576B61] uppercase tracking-wider border-b border-[#E3E8E5]">
                        <tr>
                            <th scope="col" class="py-3.5 px-4 sm:px-6">No. Servis</th>
                            <th scope="col" class="py-3.5 px-4">Perangkat</th>
                            <th scope="col" class="py-3.5 px-4 hidden md:table-cell">Keluhan Utama</th>
                            <th scope="col" class="py-3.5 px-4">Status</th>
                            <th scope="col" class="py-3.5 px-4 hidden lg:table-cell">Tgl. Masuk</th>
                            <th scope="col" class="py-3.5 px-4 text-right sm:px-6">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F4F2]">
                        @foreach ($pelanggan->servis as $servis)
                            <tr class="hover:bg-[#F6F8F7] transition-colors">
                                {{-- Nomor Servis --}}
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-md bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6]">
                                        {{ $servis->no_servis }}
                                    </span>
                                </td>

                                {{-- Perangkat --}}
                                <td class="py-3.5 px-4">
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#13251C] text-xs">
                                            {{ $servis->merk_tipe }}
                                        </p>
                                        <p class="text-[11px] text-[#576B61]">
                                            {{ $servis->jenis_perangkat }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Keluhan --}}
                                <td class="py-3.5 px-4 text-xs text-[#576B61] max-w-xs truncate hidden md:table-cell" title="{{ $servis->keluhan }}">
                                    {{ $servis->keluhan }}
                                </td>

                                {{-- Status Servis --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <x-status-badge :status="$servis->status" />
                                </td>

                                {{-- Tanggal Masuk --}}
                                <td class="py-3.5 px-4 text-xs text-[#576B61] whitespace-nowrap hidden lg:table-cell">
                                    {{ $servis->tgl_masuk?->translatedFormat('d M Y') ?? '-' }}
                                </td>

                                {{-- Total Biaya --}}
                                <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <span class="font-bold text-xs text-[#13251C]">
                                        Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <x-empty-state
            icon="clipboard"
            title="Belum ada riwayat servis"
            description="Pelanggan ini belum pernah mendaftarkan servis komputer atau laptop."
        />
    @endif
</x-app-layout>
