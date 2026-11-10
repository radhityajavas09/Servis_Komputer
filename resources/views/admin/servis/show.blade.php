<x-app-layout title="Detail Servis {{ $servis->no_servis }}" breadcrumb="Dashboard > Detail Servis > {{ $servis->no_servis }}">
    {{-- HEADER HALAMAN --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <x-eyebrow>PENGAWASAN SERVIS (READ-ONLY)</x-eyebrow>
                <span class="text-[#84968C]">•</span>
                <x-status-badge :status="$servis->status" />
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[#13251C] font-mono">
                {{ $servis->no_servis }}
            </h1>
            <p class="text-xs text-[#576B61] mt-0.5">
                {{ $servis->merk_tipe }} • Pemilik: <strong>{{ $servis->pelanggan->nama }}</strong>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-button href="{{ route('admin.dashboard') }}" variant="secondary" size="sm" icon="chevron-right" class="rotate-180">
                Kembali ke Dashboard
            </x-button>

            <x-button href="{{ route('petugas.servis.tanda-terima', $servis) }}" variant="secondary" size="sm" icon="printer">
                Lihat Tanda Terima
            </x-button>
        </div>
    </div>

    @php
        $totalSparepart = $servis->detail->sum('subtotal');
    @endphp

    {{-- KONTEN UTAMA DUA KOLOM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI (2 KOLOM) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- DATA PELANGGAN & PERANGKAT --}}
            <x-card class="p-5">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                    <x-icon name="user" class="w-4 h-4" />
                    <span>Identitas Pemilik & Perangkat</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-[#576B61] block">Nama Pelanggan:</span>
                        <strong class="text-[#13251C] text-sm block mt-0.5">{{ $servis->pelanggan->nama }}</strong>
                        <span class="text-[#576B61] block mt-2">Nomor Telepon:</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="font-mono font-semibold text-[#13251C]">{{ $servis->pelanggan->no_telepon }}</span>
                            <a
                                href="{{ $servis->pelanggan->wa_link }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded hover:bg-emerald-100"
                            >
                                <x-icon name="phone" class="w-3 h-3" />
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        <span class="text-[#576B61] block">Jenis Perangkat:</span>
                        <strong class="text-[#13251C] text-sm block mt-0.5">{{ $servis->jenis_perangkat }}</strong>
                        <span class="text-[#576B61] block mt-2">Merk & Tipe:</span>
                        <strong class="text-[#13251C] text-sm block mt-0.5">{{ $servis->merk_tipe }}</strong>
                    </div>

                    <div class="sm:col-span-2 pt-2 border-t border-[#F0F4F2]">
                        <span class="text-[#576B61] block font-bold">Keluhan dari Pelanggan:</span>
                        <p class="text-[#13251C] mt-1 p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] leading-relaxed">
                            {{ $servis->keluhan }}
                        </p>
                    </div>
                </div>
            </x-card>

            {{-- CATATAN DIAGNOSIS & TINDAKAN --}}
            <x-card class="p-5">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                    <x-icon name="tool" class="w-4 h-4" />
                    <span>Catatan Diagnosis & Tindakan Teknisi</span>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-[#576B61] block font-bold">Hasil Diagnosis Pemeriksaan:</span>
                        <div class="mt-1 p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] text-[#13251C] leading-relaxed whitespace-pre-line">
                            {{ $servis->diagnosis ?: 'Belum ada catatan diagnosis pemeriksaan.' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-[#576B61] block font-bold">Tindakan Perbaikan yang Dilakukan:</span>
                        <div class="mt-1 p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] text-[#13251C] leading-relaxed whitespace-pre-line">
                            {{ $servis->tindakan ?: 'Belum ada catatan tindakan teknisi.' }}
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- PEMAKAIAN SUKU CADANG & BIAYA --}}
            <x-card class="p-5">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                    <x-icon name="wrench" class="w-4 h-4" />
                    <span>Rincian Suku Cadang & Biaya</span>
                </div>

                @if ($servis->detail->count() > 0)
                    <div class="border border-[#E3E8E5] rounded-xl overflow-hidden mb-4">
                        <table class="w-full text-left text-xs text-[#13251C]">
                            <thead class="bg-[#F6F8F7] font-bold text-[#576B61] border-b border-[#E3E8E5]">
                                <tr>
                                    <th class="py-2.5 px-3">Nama Sparepart</th>
                                    <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                    <th class="py-2.5 px-3 text-center">Jumlah</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E3E8E5]">
                                @foreach ($servis->detail as $item)
                                    <tr>
                                        <td class="py-2.5 px-3 font-medium">{{ $item->sparepart?->nama_sparepart ?? 'Item #' . $item->id_sparepart }}</td>
                                        <td class="py-2.5 px-3 text-right">Rp {{ number_format($item->sparepart?->harga ?? 0, 0, ',', '.') }}</td>
                                        <td class="py-2.5 px-3 text-center">{{ $item->jumlah }}</td>
                                        <td class="py-2.5 px-3 text-right font-semibold text-[#15573F]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-xs text-[#84968C] italic mb-4">Tidak ada suku cadang tambahan yang digunakan.</p>
                @endif

                <div class="pt-3 border-t border-[#F0F4F2] space-y-2 text-xs">
                    <div class="flex justify-between items-center text-[#576B61]">
                        <span>Biaya Jasa Teknisi:</span>
                        <span class="font-semibold text-[#13251C]">Rp {{ number_format($servis->biaya_jasa, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[#576B61]">
                        <span>Total Suku Cadang:</span>
                        <span class="font-semibold text-[#13251C]">Rp {{ number_format($totalSparepart, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-2 border-t border-[#E3E8E5] flex justify-between items-center text-sm">
                        <span class="font-bold text-[#13251C]">Total Biaya Akhir:</span>
                        <span class="text-lg font-extrabold text-[#15573F]">Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}</span>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- KOLOM KANAN: STATUS & PENANGGUNG JAWAB (1 KOLOM) --}}
        <div class="space-y-6">
            <x-card class="p-5 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#15573F] block pb-2 border-b border-[#F0F4F2]">
                    Status & Penanggung Jawab
                </span>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[#576B61] block">Status Saat Ini:</span>
                        <div class="mt-1">
                            <x-status-badge :status="$servis->status" />
                        </div>
                    </div>

                    <div>
                        <span class="text-[#576B61] block">Tanggal Masuk:</span>
                        <strong class="text-[#13251C]">
                            {{ $servis->tgl_masuk?->translatedFormat('d F Y') ?? '-' }}
                        </strong>
                    </div>

                    <div>
                        <span class="text-[#576B61] block">Tanggal Selesai:</span>
                        <strong class="text-[#13251C]">
                            {{ $servis->tgl_selesai?->translatedFormat('d F Y') ?? 'Belum Selesai' }}
                        </strong>
                    </div>

                    <div>
                        <span class="text-[#576B61] block">Tanggal Diambil:</span>
                        <strong class="text-[#13251C]">
                            {{ $servis->tgl_diambil?->translatedFormat('d F Y') ?? 'Belum Diambil' }}
                        </strong>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#F0F4F2] space-y-2 text-xs">
                    <div>
                        <span class="text-[#576B61] block">Petugas Penerima:</span>
                        <strong class="text-[#13251C]">{{ $servis->petugas?->nama ?? '-' }}</strong>
                    </div>

                    <div>
                        <span class="text-[#576B61] block">Teknisi Penanggung Jawab:</span>
                        <strong class="text-[#13251C]">
                            {{ $servis->teknisi?->nama ?? 'Belum Ditugaskan' }}
                        </strong>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
