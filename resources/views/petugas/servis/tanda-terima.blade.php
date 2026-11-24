@php
    use App\Enums\RolePengguna;
    use App\Enums\StatusServis;
    use Carbon\Carbon;

    $docTitle = match ($servis->status) {
        StatusServis::Diterima => 'TANDA TERIMA SERVIS',
        StatusServis::Diperiksa => 'LEMBAR PEMERIKSAAN & ESTIMASI BIAYA',
        StatusServis::Diperbaiki => 'LEMBAR TINDAKAN PERBAIKAN SERVIS',
        StatusServis::Selesai => 'NOTA SERVIS (SELESAI - SIAP DIAMBIL)',
        StatusServis::Diambil => 'NOTA RESMI PELUNASAN & KARTU GARANSI',
        default => 'TANDA TERIMA SERVIS',
    };

    $defaultBack = auth()->check()
        ? (auth()->user()->role === RolePengguna::Admin ? route('admin.servis.show', $servis) : route('petugas.servis.index'))
        : route('lacak.show', $servis->no_servis);

    $backDestination = $backUrl ?? $defaultBack;
@endphp

<x-print-layout :title="$docTitle . ' - ' . $servis->no_servis" :back-url="$backDestination">
    <div class="space-y-5">
        {{-- KOP SURAT TANDA TERIMA / NOTA RESMI --}}
        <div class="border-b-2 border-[#15573F] pb-4 flex items-start justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6] flex items-center justify-center shrink-0">
                    <x-icon name="tool" class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-[#13251C] uppercase">
                        SERVIS KOMPUTER & LAPTOP
                    </h1>
                    <p class="text-xs text-[#576B61] mt-0.5">
                        Layanan Perbaikan Komputer, Laptop, Hardware & Software Profesional
                    </p>
                    <p class="text-[11px] text-[#84968C]">
                        Jl. Veteran No. 45, Banyumas • Telp/WhatsApp: 0812-3456-7890 • Buka: Senin - Sabtu (08.00 - 17.00)
                    </p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#84968C] block">Nomor Tiket / Dokumen</span>
                <span class="inline-block font-mono font-extrabold text-lg text-[#15573F] bg-[#EDF7F2] px-3 py-1 rounded-lg border border-[#B2DEC6] mt-1">
                    {{ $servis->no_servis }}
                </span>
            </div>
        </div>

        {{-- JUDUL DOKUMEN & STATUS SERVIS --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-[#F6F8F7] p-3 rounded-xl border border-[#E3E8E5]">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-[#13251C] uppercase tracking-wide">{{ $docTitle }}</span>
                <span class="text-xs text-[#576B61]">• Status:</span>
                <x-status-badge :status="$servis->status" />
            </div>

            <div class="flex flex-wrap items-center gap-3 text-xs text-[#576B61]">
                <span>Tgl Masuk: <strong class="text-[#13251C]">{{ $servis->tgl_masuk?->translatedFormat('d M Y') ?? now()->translatedFormat('d M Y') }}</strong></span>
                @if ($servis->tgl_selesai)
                    <span>• Selesai: <strong class="text-[#13251C]">{{ $servis->tgl_selesai->translatedFormat('d M Y') }}</strong></span>
                @endif
                @if ($servis->tgl_diambil)
                    <span>• Diambil: <strong class="text-[#13251C]">{{ $servis->tgl_diambil->translatedFormat('d M Y') }}</strong></span>
                @endif
            </div>
        </div>

        {{-- GRID DATA PELANGGAN & PERANGKAT --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- DATA PELANGGAN --}}
            <div class="bg-white rounded-xl border border-[#E3E8E5] p-4 space-y-2">
                <div class="flex items-center gap-2 pb-2 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                    <x-icon name="user" class="w-3.5 h-3.5" />
                    <span>Identitas Pelanggan</span>
                </div>
                <div class="text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-[#576B61]">Nama Pemilik:</span>
                        <strong class="text-[#13251C] text-right">{{ $servis->pelanggan->nama }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#576B61]">No. WhatsApp / HP:</span>
                        <span class="font-mono font-semibold text-[#13251C]">{{ $servis->pelanggan->no_telepon }}</span>
                    </div>
                    <div>
                        <span class="text-[#576B61] block">Alamat:</span>
                        <p class="text-[#13251C] mt-0.5 leading-relaxed">{{ $servis->pelanggan->alamat }}</p>
                    </div>
                </div>
            </div>

            {{-- DATA PERANGKAT & PENANGANAN --}}
            <div class="bg-white rounded-xl border border-[#E3E8E5] p-4 space-y-2">
                <div class="flex items-center gap-2 pb-2 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                    <x-icon name="package" class="w-3.5 h-3.5" />
                    <span>Rincian Perangkat & Tim</span>
                </div>
                <div class="text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-[#576B61]">Jenis Perangkat:</span>
                        <strong class="text-[#13251C]">{{ $servis->jenis_perangkat }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#576B61]">Merk & Tipe:</span>
                        <strong class="text-[#13251C] text-right">{{ $servis->merk_tipe }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#576B61]">Petugas Penerima:</span>
                        <span class="text-[#13251C] font-medium">{{ $servis->petugas?->nama ?? 'Petugas Front Desk' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#576B61]">Teknisi Pemeriksa:</span>
                        <span class="text-[#13251C] font-medium">{{ $servis->teknisi?->nama ?? 'Menunggu Antrean Teknisi' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIWAYAT KELUHAN & DIAGNOSIS TEKNISI --}}
        <div class="grid grid-cols-1 {{ ($servis->diagnosis || $servis->tindakan) ? 'sm:grid-cols-2' : '' }} gap-4">
            {{-- KELUHAN PELANGGAN --}}
            <div class="bg-white rounded-xl border border-[#E3E8E5] p-4 space-y-1.5">
                <span class="text-xs font-bold uppercase tracking-wider text-[#576B61] block">
                    Keluhan Masalah dari Pelanggan:
                </span>
                <div class="p-3 bg-[#F6F8F7] rounded-lg border border-[#E3E8E5] text-xs text-[#13251C] leading-relaxed whitespace-pre-line font-medium min-h-20">
                    {{ $servis->keluhan }}
                </div>
            </div>

            {{-- DIAGNOSIS & TINDAKAN TEKNISI (JIKA SUDAH DIPERIKSA) --}}
            @if ($servis->diagnosis || $servis->tindakan)
                <div class="bg-white rounded-xl border border-[#E3E8E5] p-4 space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#15573F] block">
                        Hasil Pemeriksaan & Diagnosis Teknisi:
                    </span>
                    <div class="text-xs space-y-2">
                        @if ($servis->diagnosis)
                            <div class="p-2.5 rounded-lg bg-[#EDF7F2] border border-[#B2DEC6]">
                                <span class="font-bold text-[#15573F] block mb-0.5">Temuan Diagnosis:</span>
                                <p class="text-[#13251C] leading-relaxed">{{ $servis->diagnosis }}</p>
                            </div>
                        @endif

                        @if ($servis->tindakan)
                            <div class="p-2.5 rounded-lg bg-[#F6F8F7] border border-[#E3E8E5]">
                                <span class="font-bold text-[#576B61] block mb-0.5">Tindakan Perbaikan:</span>
                                <p class="text-[#13251C] leading-relaxed">{{ $servis->tindakan }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- RINCIAN TAGIHAN BIAYA & STATUS PELUNASAN (JIKA ADA BIAYA ATAU STATUS BUKAN DITERIMA) --}}
        @if ($servis->total_biaya > 0 || $servis->detail->count() > 0 || in_array($servis->status, [StatusServis::Diperiksa, StatusServis::Diperbaiki, StatusServis::Selesai, StatusServis::Diambil]))
            <div class="bg-white rounded-xl border border-[#E3E8E5] p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-[#F0F4F2]">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#15573F]">
                        Rincian Tagihan Biaya Servis & Suku Cadang
                    </span>

                    {{-- BADGE / CAP STATUS PELUNASAN --}}
                    @if ($servis->status === StatusServis::Diambil)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-300 font-extrabold text-xs uppercase tracking-wider">
                            ✓ SUDAH LUNAS (Diserahkan: {{ $servis->tgl_diambil?->translatedFormat('d M Y') }})
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-300 font-bold text-xs uppercase tracking-wider">
                            ⏳ BELUM LUNAS (Dilunasi saat pengambilan)
                        </span>
                    @endif
                </div>

                <div class="text-xs space-y-2">
                    <div class="flex justify-between py-1 text-[#576B61] border-b border-[#F0F4F2]">
                        <span>Biaya Jasa Servis / Perbaikan:</span>
                        <strong class="text-[#13251C]">Rp {{ number_format($servis->biaya_jasa, 0, ',', '.') }}</strong>
                    </div>

                    @if ($servis->detail->count() > 0)
                        <div class="space-y-1.5 py-1">
                            <span class="text-[#576B61] font-semibold block">Penggantian Suku Cadang (Sparepart):</span>
                            @foreach ($servis->detail as $item)
                                <div class="flex justify-between pl-3 text-[#576B61]">
                                    <span>• {{ $item->sparepart->nama_sparepart }} ({{ $item->jumlah }}x @ Rp {{ number_format($item->subtotal / $item->jumlah, 0, ',', '.') }})</span>
                                    <span class="font-medium text-[#13251C]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-2 border-t-2 border-[#15573F] flex justify-between items-center bg-[#EDF7F2] p-3 rounded-lg">
                        <div>
                            <span class="font-bold text-sm text-[#13251C] block">TOTAL BIAYA AKHIR:</span>
                            <span class="text-[10px] text-[#576B61]">Termasuk jasa pengerjaan dan suku cadang</span>
                        </div>
                        <span class="text-xl font-extrabold text-[#15573F] font-mono">
                            Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        {{-- JAMINAN GARANSI TOKO (JIKA SELESAI ATAU SUDAH DIAMBIL) --}}
        @if (in_array($servis->status, [StatusServis::Selesai, StatusServis::Diambil]))
            @php
                $tglAcuan = $servis->tgl_diambil ?? $servis->tgl_selesai ?? now()->toDateString();
                $tglGaransiSampai = Carbon::parse($tglAcuan)->addDays(30);
            @endphp
            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-300 text-xs text-emerald-950 space-y-1">
                <div class="font-bold flex items-center gap-1.5 text-emerald-900">
                    <x-icon name="check-circle" class="w-4 h-4 text-emerald-700" />
                    <span>JAMINAN GARANSI TOKO RESMI 30 HARI</span>
                </div>
                <p class="text-[11px] leading-relaxed text-emerald-900 pl-5.5">
                    Perbaikan ini dilindungi garansi resmi toko hingga tanggal <strong>{{ $tglGaransiSampai->translatedFormat('d F Y') }}</strong>. Garansi mencakup kerusakan dan suku cadang yang sama sesuai nota pengerjaan ini. Segel garansi wajib utuh dan tidak rusak.
                </p>
            </div>
        @endif

        {{-- CATATAN & KETENTUAN PENGAMBILAN --}}
        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900 space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-amber-950">
                <x-icon name="alert-circle" class="w-3.5 h-3.5 text-amber-700" />
                <span>Ketentuan Pengambilan & Pelacakan Servis:</span>
            </div>
            <ol class="list-decimal list-inside space-y-0.5 text-amber-900 pl-1">
                <li>Bawa dan serahkan lembar tanda terima/nota ini (atau tunjukkan bukti digital) saat mengambil perangkat.</li>
                <li>Pelanggan dapat memantau status secara online kapan saja di: <strong class="font-mono text-amber-950 underline">{{ route('lacak.index') }}</strong> dengan kode tiket: <strong class="font-mono text-amber-950">{{ $servis->no_servis }}</strong>.</li>
                <li>Perangkat yang telah selesai diservis dan tidak diambil dalam waktu 30 hari berada di luar tanggung jawab pihak tempat servis.</li>
            </ol>
        </div>

        {{-- KOLOM TANDA TANGAN DUA PIHAK --}}
        <div class="pt-3 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <p class="text-[#576B61] mb-14">Tanda Tangan Pelanggan,</p>
                <p class="font-bold text-[#13251C] border-t border-[#CCD5D0] pt-1 inline-block min-w-44">
                    {{ $servis->pelanggan->nama }}
                </p>
            </div>

            <div>
                <p class="text-[#576B61] mb-14">
                    Petugas / Teknisi Penanggung Jawab,
                </p>
                <p class="font-bold text-[#13251C] border-t border-[#CCD5D0] pt-1 inline-block min-w-44">
                    {{ $servis->teknisi?->nama ?? ($servis->petugas?->nama ?? 'Petugas Servis') }}
                </p>
            </div>
        </div>
    </div>
</x-print-layout>
