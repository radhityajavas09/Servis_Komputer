<x-app-layout title="Detail Servis {{ $servis->no_servis }}" breadcrumb="Data Servis > {{ $servis->no_servis }}">
    {{-- HEADER HALAMAN --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <x-eyebrow>DETAIL PENGERJAAN</x-eyebrow>
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
            <x-button href="{{ route('petugas.servis.index') }}" variant="secondary" size="sm" icon="chevron-right" class="rotate-180">
                Kembali
            </x-button>

            <x-button href="{{ route('petugas.servis.tanda-terima', $servis) }}" variant="secondary" size="sm" icon="printer">
                Cetak Tanda Terima
            </x-button>

            @if ($servis->status === \App\Enums\StatusServis::Selesai)
                <x-button href="{{ route('petugas.servis.serahkan', $servis) }}" variant="primary" size="sm" icon="check-circle">
                    Serahkan Perangkat
                </x-button>
            @endif
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI (LEBIH LEBAR: 2 KOLOM) --}}
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
                        <span class="text-[#576B61] block">Alamat Domisili Pelanggan:</span>
                        <p class="text-[#13251C] mt-0.5 leading-relaxed">{{ $servis->pelanggan->alamat }}</p>
                    </div>

                    <div class="sm:col-span-2 pt-2 border-t border-[#F0F4F2]">
                        <span class="text-[#576B61] block font-bold">Keluhan Masalah dari Pelanggan:</span>
                        <p class="text-[#13251C] mt-1 p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] leading-relaxed">
                            {{ $servis->keluhan }}
                        </p>
                    </div>
                </div>
            </x-card>

            {{-- HASIL DIAGNOSIS & TINDAKAN TEKNISI --}}
            <x-card class="p-5">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                    <x-icon name="tool" class="w-4 h-4" />
                    <span>Catatan Pemeriksaan & Tindakan Teknisi</span>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-[#576B61] block font-bold">Hasil Diagnosis Kerusakan:</span>
                        @if ($servis->diagnosis)
                            <div class="mt-1 p-3 bg-white rounded-xl border border-[#E3E8E5] text-[#13251C] leading-relaxed whitespace-pre-line font-medium">
                                {{ $servis->diagnosis }}
                            </div>
                        @else
                            <p class="text-[#84968C] italic mt-1 p-3 bg-[#F6F8F7] rounded-xl border border-dashed border-[#E3E8E5]">
                                Belum ada catatan diagnosis yang diinput oleh teknisi.
                            </p>
                        @endif
                    </div>

                    <div>
                        <span class="text-[#576B61] block font-bold">Tindakan Perbaikan / Solusi:</span>
                        @if ($servis->tindakan)
                            <div class="mt-1 p-3 bg-white rounded-xl border border-[#E3E8E5] text-[#13251C] leading-relaxed whitespace-pre-line font-medium">
                                {{ $servis->tindakan }}
                            </div>
                        @else
                            <p class="text-[#84968C] italic mt-1 p-3 bg-[#F6F8F7] rounded-xl border border-dashed border-[#E3E8E5]">
                                Belum ada catatan tindakan perbaikan yang diinput oleh teknisi.
                            </p>
                        @endif
                    </div>
                </div>
            </x-card>

            {{-- PEMAKAIAN SPAREPART & TOTAL BIAYA --}}
            <x-card class="p-5">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                    <x-icon name="package" class="w-4 h-4" />
                    <span>Rincian Biaya Servis & Penggantian Suku Cadang</span>
                </div>

                @if ($servis->detail->count() > 0)
                    <div class="overflow-x-auto mb-4 border border-[#E3E8E5] rounded-xl">
                        <table class="w-full text-left text-xs text-[#13251C]">
                            <thead class="bg-[#F6F8F7] font-bold text-[#576B61] border-b border-[#E3E8E5]">
                                <tr>
                                    <th class="py-2.5 px-3">Nama Suku Cadang</th>
                                    <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                    <th class="py-2.5 px-3 text-center">Jumlah</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F0F4F2]">
                                @foreach ($servis->detail as $item)
                                    <tr>
                                        <td class="py-2.5 px-3 font-medium">{{ $item->sparepart->nama_sparepart }}</td>
                                        <td class="py-2.5 px-3 text-right">Rp {{ number_format($item->sparepart->harga, 0, ',', '.') }}</td>
                                        <td class="py-2.5 px-3 text-center">{{ $item->jumlah }}</td>
                                        <td class="py-2.5 px-3 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-xs text-[#576B61] italic mb-4 p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5]">
                        Tidak ada penggantian sparepart pada servis ini.
                    </p>
                @endif

                {{-- REKAPITULASI BIAYA --}}
                <div class="bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] p-4 space-y-2 text-xs">
                    <div class="flex justify-between items-center text-[#576B61]">
                        <span>Biaya Jasa Pengerjaan Teknisi:</span>
                        <span class="font-semibold text-[#13251C]">
                            Rp {{ number_format($servis->biaya_jasa, 0, ',', '.') }}
                        </span>
                    </div>

                    @php
                        $totalSparepart = $servis->detail->sum('subtotal');
                    @endphp
                    <div class="flex justify-between items-center text-[#576B61]">
                        <span>Subtotal Suku Cadang ({{ $servis->detail->count() }} item):</span>
                        <span class="font-semibold text-[#13251C]">
                            Rp {{ number_format($totalSparepart, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="pt-2 border-t border-[#E3E8E5] flex justify-between items-center">
                        <span class="font-bold text-sm text-[#13251C]">Total Biaya Akhir:</span>
                        <span class="text-lg font-extrabold text-[#15573F]">
                            Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- KOLOM KANAN (SIDEBAR INFO: 1 KOLOM) --}}
        <div class="space-y-6">
            {{-- STATUS & TIMELINE --}}
            <x-card class="p-5 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#15573F] block pb-2 border-b border-[#F0F4F2]">
                    Informasi Status & Jadwal
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
                        <span class="text-[#576B61] block">Tanggal Selesai Diservis:</span>
                        <strong class="text-[#13251C]">
                            {{ $servis->tgl_selesai?->translatedFormat('d F Y') ?? 'Belum Selesai' }}
                        </strong>
                    </div>

                    <div>
                        <span class="text-[#576B61] block">Tanggal Diambil Pelanggan:</span>
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

            {{-- KOTAK AKSI PENYERAHAN --}}
            @if ($servis->status === \App\Enums\StatusServis::Selesai)
                <div class="bg-[#EDF7F2] rounded-2xl border border-[#B2DEC6] p-5 space-y-3">
                    <div class="flex items-center gap-2 text-[#15573F] font-bold text-xs uppercase tracking-wider">
                        <x-icon name="check-circle" class="w-4 h-4" />
                        <span>Siap Diserahkan</span>
                    </div>
                    <p class="text-xs text-[#13251C] leading-relaxed">
                        Pengerjaan servis perangkat telah dinyatakan selesai oleh teknisi. Pelanggan dapat mengambil perangkat dengan melunasi total biaya sebesar:
                    </p>
                    <div class="p-3 bg-white rounded-xl border border-[#B2DEC6] text-center">
                        <span class="text-xs text-[#576B61] block">Tagihan Pelunasan:</span>
                        <span class="text-xl font-extrabold text-[#15573F] block mt-0.5">
                            Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}
                        </span>
                    </div>
                    <x-button href="{{ route('petugas.servis.serahkan', $servis) }}" variant="primary" class="w-full justify-center" icon="check-circle">
                        Proses Penyerahan Perangkat
                    </x-button>
                </div>
            @elseif ($servis->status === \App\Enums\StatusServis::Diambil)
                <div class="bg-blue-50 rounded-2xl border border-blue-200 p-5 space-y-2">
                    <div class="flex items-center gap-2 text-blue-800 font-bold text-xs uppercase tracking-wider">
                        <x-icon name="check-circle" class="w-4 h-4 text-blue-600" />
                        <span>Perangkat Telah Diambil</span>
                    </div>
                    <p class="text-xs text-blue-900 leading-relaxed">
                        Perangkat telah diserahkan kembali kepada pelanggan pada <strong>{{ $servis->tgl_diambil?->translatedFormat('d F Y') }}</strong> dan pembayaran telah lunas.
                    </p>
                </div>
            @else
                <div class="bg-[#F6F8F7] rounded-2xl border border-[#E3E8E5] p-5 space-y-2">
                    <span class="text-xs font-bold text-[#576B61] block">Status Penyerahan</span>
                    <p class="text-xs text-[#84968C] leading-relaxed">
                        Perangkat masih dalam proses penanganan oleh teknisi. Tombol penyerahan akan aktif otomatis setelah status berubah menjadi <strong>Selesai</strong>.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
