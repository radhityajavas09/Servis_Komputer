<x-print-layout :title="'Tanda Terima Servis - ' . $servis->no_servis" :back-url="route('petugas.pelanggan.index')">
    <div class="space-y-6">
        {{-- KOP SURAT TANDA TERIMA --}}
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
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#84968C] block">Dokumen Resmi</span>
                <span class="inline-block font-mono font-extrabold text-lg text-[#15573F] bg-[#EDF7F2] px-3 py-1 rounded-lg border border-[#B2DEC6] mt-1">
                    {{ $servis->no_servis }}
                </span>
            </div>
        </div>

        {{-- JUDUL DOKUMEN & TANGGAL --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-[#F6F8F7] p-3 rounded-xl border border-[#E3E8E5]">
            <div>
                <span class="text-xs font-bold text-[#13251C] uppercase tracking-wide">TANDA TERIMA SERVIS</span>
                <span class="text-xs text-[#576B61] ml-2">• Status Penerimaan: <strong>Diterima</strong></span>
            </div>
            <div class="text-xs text-[#576B61]">
                Tanggal Masuk: <strong class="text-[#13251C]">{{ $servis->tgl_masuk?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</strong>
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
                        <span class="text-[#576B61]">Nama:</span>
                        <strong class="text-[#13251C] text-right">{{ $servis->pelanggan->nama }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#576B61]">Telepon / WA:</span>
                        <span class="font-mono font-semibold text-[#13251C]">{{ $servis->pelanggan->no_telepon }}</span>
                    </div>
                    <div>
                        <span class="text-[#576B61] block">Alamat Domisili:</span>
                        <p class="text-[#13251C] mt-0.5 leading-relaxed">{{ $servis->pelanggan->alamat }}</p>
                    </div>
                </div>
            </div>

            {{-- DATA PERANGKAT --}}
            <div class="bg-white rounded-xl border border-[#E3E8E5] p-4 space-y-2">
                <div class="flex items-center gap-2 pb-2 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                    <x-icon name="package" class="w-3.5 h-3.5" />
                    <span>Rincian Perangkat</span>
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
                        <span class="text-[#13251C] font-medium">{{ $servis->petugas?->nama ?? 'Petugas Servis' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KELUHAN KERUSAKAN --}}
        <div class="bg-white rounded-xl border border-[#E3E8E5] p-4 space-y-1.5">
            <span class="text-xs font-bold uppercase tracking-wider text-[#576B61] block">
                Keluhan Kerusakan / Masalah yang Dilaporkan:
            </span>
            <div class="p-3 bg-[#F6F8F7] rounded-lg border border-[#E3E8E5] text-xs text-[#13251C] leading-relaxed whitespace-pre-line font-medium">
                {{ $servis->keluhan }}
            </div>
        </div>

        {{-- CATATAN & KETENTUAN PENGAMBILAN --}}
        <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900 space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-amber-950">
                <x-icon name="alert-circle" class="w-3.5 h-3.5 text-amber-700" />
                <span>PENTING — Syarat & Ketentuan Pengambilan Perangkat:</span>
            </div>
            <ol class="list-decimal list-inside space-y-0.5 text-amber-900 pl-1">
                <li><strong>Wajib membawa dan menyerahkan lembar tanda terima ini</strong> saat mengambil perangkat servis.</li>
                <li>Pantau status perbaikan dan rincian biaya secara online di: <strong class="font-mono text-amber-950 underline">{{ route('lacak.index') }}</strong> dengan nomor tiket <strong class="font-mono text-amber-950">{{ $servis->no_servis }}</strong>.</li>
                <li>Pemeriksaan kerusakan dan estimasi biaya perbaikan/sparepart akan dikonfirmasikan oleh teknisi kepada pelanggan.</li>
                <li>Perangkat yang telah selesai diservis dan tidak diambil dalam waktu 30 hari sejak pemberitahuan selesai berada di luar tanggung jawab pihak tempat servis.</li>
            </ol>
        </div>

        {{-- KOLOM TANDA TANGAN --}}
        <div class="pt-4 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <p class="text-[#576B61] mb-16">Tanda Tangan Pelanggan,</p>
                <p class="font-bold text-[#13251C] border-t border-[#CCD5D0] pt-1.5 inline-block min-w-44">
                    {{ $servis->pelanggan->nama }}
                </p>
            </div>

            <div>
                <p class="text-[#576B61] mb-16">Petugas Penerima,</p>
                <p class="font-bold text-[#13251C] border-t border-[#CCD5D0] pt-1.5 inline-block min-w-44">
                    {{ $servis->petugas?->nama ?? 'Petugas Servis' }}
                </p>
            </div>
        </div>
    </div>
</x-print-layout>
