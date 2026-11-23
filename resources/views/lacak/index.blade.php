<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $servis ? 'Dashboard Servis ' . $servis->no_servis : 'Portal Pelayanan & Lacak Servis Komputer' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-[#13251C] antialiased flex flex-col justify-between">

    {{-- TOPBAR KHUSUS PELANGGAN & AKSES STAF --}}
    <header class="bg-white border-b border-[#E3E8E5] sticky top-0 z-30 shadow-2xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('lacak.index') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-[#15573F] text-white font-bold flex items-center justify-center text-base shadow-xs group-hover:bg-[#104430] transition-colors">
                    SK
                </div>
                <div>
                    <span class="font-bold text-sm sm:text-base text-[#13251C] tracking-tight block leading-tight">Servis Komputer</span>
                    <span class="text-[11px] text-[#576B61] hidden sm:block">Portal Pelacakan & Informasi Pelanggan</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6] hover:bg-[#dff0e7] transition-colors">
                        <x-icon name="user" class="w-3.5 h-3.5" />
                        <span>Kembali ke Panel {{ ucfirst(auth()->user()->role->value) }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-[#576B61] hover:text-[#13251C] hover:bg-[#F6F8F7] border border-transparent hover:border-[#E3E8E5] transition-all">
                        <span>Login Petugas / Staf</span>
                        <x-icon name="arrow-up-right" class="w-3.5 h-3.5" />
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- KONTEN UTAMA --}}
    <main class="flex-1 py-8 px-4 sm:px-6">
        <div class="max-w-6xl mx-auto space-y-6">

            {{-- HERO SECTION / KOTAK LACAK TIKET (SELALU MENGUTAMAKAN PELANGGAN) --}}
            <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs p-6 sm:p-8">
                <div class="max-w-2xl mx-auto text-center mb-6">
                    <x-eyebrow>PORTAL PELAYANAN PELANGGAN</x-eyebrow>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#13251C] mt-2">
                        Cek Status Servis Perangkat
                    </h1>
                    <p class="text-xs sm:text-sm text-[#576B61] mt-1.5 leading-relaxed">
                        Masukkan kode nomor tiket servis Anda untuk memantau perkembangan perbaikan, catatan teknisi, rincian biaya, dan garansi secara transparan.
                    </p>
                </div>

                {{-- Alert Error --}}
                @if ($error)
                    <div class="max-w-xl mx-auto mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-start gap-3">
                        <x-icon name="alert" class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                        <div class="text-xs">
                            <p class="font-bold">Nomor Tiket Tidak Ditemukan</p>
                            <p class="mt-0.5 text-red-700 leading-relaxed">{{ $error }}</p>
                        </div>
                    </div>
                @endif

                {{-- Form Pencarian Cukup Nomor Tiket Saja (Tanpa No WhatsApp) --}}
                <form method="GET" action="{{ route('lacak.index') }}" class="max-w-xl mx-auto" x-data="{ submitting: false }" @submit="submitting = true">
                    <label for="no_servis" class="block text-xs font-bold text-[#13251C] mb-1.5 text-left">
                        Nomor Tiket Servis / Kode Akses
                    </label>
                    <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
                        <div class="relative flex-1">
                            <input
                                type="text"
                                id="no_servis"
                                name="no_servis"
                                value="{{ $searchQuery ?? '' }}"
                                placeholder="Masukkan No. Tiket (Contoh: SRV-001 atau ketik 1)"
                                required
                                autofocus
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-[#CCD5D0] bg-[#F6F8F7] focus:bg-white text-sm font-mono font-bold uppercase text-[#13251C] shadow-2xs focus:ring-2 focus:ring-[#15573F]/20 focus:border-[#15573F] transition-all"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#84968C]">
                                <x-icon name="search" class="w-5 h-5" />
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold bg-[#15573F] text-white hover:bg-[#104430] transition-colors shadow-xs cursor-pointer shrink-0 disabled:opacity-50"
                            x-bind:disabled="submitting"
                        >
                            <span>Lacak Status</span>
                            <x-icon name="chevron-right" class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="mt-3 flex items-center justify-center gap-4 text-[11px] text-[#576B61]">
                        <span>✓ Kode tertera pada nota fisik atau chat WhatsApp</span>
                        <span>•</span>
                        <span>✓ Bebas akses tanpa password</span>
                    </div>
                </form>
            </div>

            {{-- MINI DASHBOARD PELANGGAN (JIKA TIKET DITEMUKAN) --}}
            @if ($servis)
                @php
                    $statusOrder = [
                        'diterima' => 1,
                        'diperiksa' => 2,
                        'diperbaiki' => 3,
                        'selesai' => 4,
                        'diambil' => 5,
                    ];
                    $currentOrder = $statusOrder[$servis->status->value] ?? 1;
                @endphp

                {{-- Alert Sukses Update Keluhan --}}
                @if (session('success'))
                    <div class="p-4 rounded-2xl bg-[#EDF7F2] border border-[#B2DEC6] text-[#15573F] flex items-center gap-3 shadow-xs">
                        <x-icon name="check-circle" class="w-5 h-5 text-[#15573F] shrink-0" />
                        <p class="text-xs font-semibold">{{ session('success') }}</p>
                    </div>
                @endif

                {{-- Alert Error Update Keluhan --}}
                @if (session('error'))
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 flex items-center gap-3 shadow-xs">
                        <x-icon name="alert" class="w-5 h-5 text-red-600 shrink-0" />
                        <p class="text-xs font-semibold">{{ session('error') }}</p>
                    </div>
                @endif

                {{-- 1. HEADER MINI DASHBOARD --}}
                <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs p-6 sm:p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-[#F0F4F2]">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-xs font-bold text-[#15573F] bg-[#EDF7F2] px-2.5 py-0.5 rounded-full border border-[#B2DEC6] uppercase tracking-wider">
                                    TIKET SERVIS AKTIF
                                </span>
                                <span class="text-[#84968C]">•</span>
                                <span class="text-xs text-[#576B61]">Terdaftar: {{ \Carbon\Carbon::parse($servis->tgl_masuk)->translatedFormat('d M Y') }}</span>
                            </div>
                            <h2 class="text-3xl font-extrabold text-[#13251C] font-mono tracking-tight flex items-center gap-3">
                                <span>{{ $servis->no_servis }}</span>
                                <x-status-badge :status="$servis->status" />
                            </h2>
                            <p class="text-sm font-semibold text-[#576B61] mt-1">
                                {{ $servis->jenis_perangkat }} &bull; {{ $servis->merk_tipe }} &bull; Pemilik: <strong class="text-[#13251C]">{{ $maskedNama }}</strong> ({{ $maskedTelepon }})
                            </p>
                        </div>

                        {{-- Tombol Cepat Aksi Pelanggan --}}
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($waLink)
                                <a
                                    href="{{ $waLink }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-[#25D366] text-white hover:bg-[#1ebc59] transition-colors shadow-xs"
                                >
                                    <span>💬 Konfirmasi via WhatsApp</span>
                                </a>
                            @endif

                            <a
                                href="{{ route('petugas.servis.tanda-terima', $servis) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-white border border-[#CCD5D0] text-[#13251C] hover:bg-[#F6F8F7] transition-colors"
                            >
                                <x-icon name="printer" class="w-4 h-4 text-[#576B61]" />
                                <span>Cetak Nota</span>
                            </a>
                        </div>
                    </div>

                    {{-- 4 KARTU METRIK RINGKASAN (MINI DASHBOARD STATS) --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 pt-6">
                        <div class="p-4 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5]">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#576B61] block mb-1">Status Perbaikan</span>
                            <span class="text-base font-extrabold text-[#13251C] block capitalize">{{ $servis->status->label() }}</span>
                            <span class="text-[10px] text-[#576B61] mt-0.5 block">Tahap {{ $currentOrder }} dari 5</span>
                        </div>

                        <div class="p-4 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5]">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#576B61] block mb-1">Perangkat</span>
                            <span class="text-base font-extrabold text-[#13251C] truncate block">{{ $servis->merk_tipe }}</span>
                            <span class="text-[10px] text-[#576B61] mt-0.5 block">{{ $servis->jenis_perangkat }}</span>
                        </div>

                        <div class="p-4 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5]">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#576B61] block mb-1">Total Biaya</span>
                            <span class="text-base font-extrabold text-[#15573F] font-mono block">Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}</span>
                            <span class="text-[10px] {{ $servis->status === \App\Enums\StatusServis::Diambil ? 'text-emerald-700 font-bold' : 'text-amber-700 font-semibold' }} mt-0.5 block">
                                {{ $servis->status === \App\Enums\StatusServis::Diambil ? '✓ Sudah Lunas' : 'Belum Lunas' }}
                            </span>
                        </div>

                        <div class="p-4 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5]">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#576B61] block mb-1">Garansi Toko</span>
                            @if ($garansiInfo && $garansiInfo['aktif'])
                                <span class="text-base font-extrabold text-[#15573F] block">Aktif ({{ $garansiInfo['sisa_hari'] }} Hari)</span>
                                <span class="text-[10px] text-[#576B61] mt-0.5 block">Hingga {{ $garansiInfo['berlaku_sampai'] }}</span>
                            @else
                                <span class="text-base font-extrabold text-[#576B61] block">30 Hari</span>
                                <span class="text-[10px] text-[#576B61] mt-0.5 block">Setelah pengerjaan tuntas</span>
                            @endif
                        </div>
                    </div>

                    {{-- VISUAL STEPPER STATUS (5 TAHAPAN) --}}
                    <div class="pt-6 border-t border-[#F0F4F2] mt-6">
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 text-center">
                            {{-- 1. Diterima --}}
                            <div class="p-3 rounded-xl border {{ $currentOrder >= 1 ? 'bg-[#EDF7F2] border-[#B2DEC6] text-[#15573F]' : 'bg-[#F6F8F7] border-[#E3E8E5] text-[#84968C]' }}">
                                <div class="w-6 h-6 mx-auto mb-1 rounded-full flex items-center justify-center text-xs font-bold {{ $currentOrder >= 1 ? 'bg-[#15573F] text-white' : 'bg-[#E3E8E5] text-[#576B61]' }}">
                                    @if($currentOrder > 1) ✓ @else 1 @endif
                                </div>
                                <p class="text-xs font-bold">Diterima</p>
                                <p class="text-[10px] opacity-75">Unit Masuk Toko</p>
                            </div>

                            {{-- 2. Diperiksa --}}
                            <div class="p-3 rounded-xl border {{ $currentOrder >= 2 ? 'bg-[#EDF7F2] border-[#B2DEC6] text-[#15573F]' : 'bg-[#F6F8F7] border-[#E3E8E5] text-[#84968C]' }}">
                                <div class="w-6 h-6 mx-auto mb-1 rounded-full flex items-center justify-center text-xs font-bold {{ $currentOrder >= 2 ? 'bg-[#15573F] text-white' : 'bg-[#E3E8E5] text-[#576B61]' }}">
                                    @if($currentOrder > 2) ✓ @else 2 @endif
                                </div>
                                <p class="text-xs font-bold">Diperiksa</p>
                                <p class="text-[10px] opacity-75">Pengecekan Kerusakan</p>
                            </div>

                            {{-- 3. Diperbaiki --}}
                            <div class="p-3 rounded-xl border {{ $currentOrder >= 3 ? 'bg-[#EDF7F2] border-[#B2DEC6] text-[#15573F]' : 'bg-[#F6F8F7] border-[#E3E8E5] text-[#84968C]' }}">
                                <div class="w-6 h-6 mx-auto mb-1 rounded-full flex items-center justify-center text-xs font-bold {{ $currentOrder >= 3 ? 'bg-[#15573F] text-white' : 'bg-[#E3E8E5] text-[#576B61]' }}">
                                    @if($currentOrder > 3) ✓ @else 3 @endif
                                </div>
                                <p class="text-xs font-bold">Diperbaiki</p>
                                <p class="text-[10px] opacity-75">Tindakan & Sparepart</p>
                            </div>

                            {{-- 4. Selesai --}}
                            <div class="p-3 rounded-xl border {{ $currentOrder >= 4 ? 'bg-[#EDF7F2] border-[#B2DEC6] text-[#15573F]' : 'bg-[#F6F8F7] border-[#E3E8E5] text-[#84968C]' }}">
                                <div class="w-6 h-6 mx-auto mb-1 rounded-full flex items-center justify-center text-xs font-bold {{ $currentOrder >= 4 ? 'bg-[#15573F] text-white' : 'bg-[#E3E8E5] text-[#576B61]' }}">
                                    @if($currentOrder > 4) ✓ @else 4 @endif
                                </div>
                                <p class="text-xs font-bold">Selesai</p>
                                <p class="text-[10px] opacity-75">Siap Diambil</p>
                            </div>

                            {{-- 5. Diambil --}}
                            <div class="p-3 rounded-xl border col-span-2 sm:col-span-1 {{ $currentOrder >= 5 ? 'bg-[#EDF7F2] border-[#B2DEC6] text-[#15573F]' : 'bg-[#F6F8F7] border-[#E3E8E5] text-[#84968C]' }}">
                                <div class="w-6 h-6 mx-auto mb-1 rounded-full flex items-center justify-center text-xs font-bold {{ $currentOrder >= 5 ? 'bg-[#15573F] text-white' : 'bg-[#E3E8E5] text-[#576B61]' }}">
                                    @if($currentOrder >= 5) ✓ @else 5 @endif
                                </div>
                                <p class="text-xs font-bold">Diambil</p>
                                <p class="text-[10px] opacity-75">Lunas & Diserahkan</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. KONTEN DUA KOLOM DASHBOARD PELANGGAN --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- KOLOM KIRI (7/12): DIAGNOSIS & BIAYA SPAREPART --}}
                    <div class="lg:col-span-7 space-y-6">

                        {{-- Catatan Diagnosis & Tindakan Teknisi --}}
                        <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs p-6">
                            <div class="flex items-center gap-2 mb-3 text-[#15573F]">
                                <x-icon name="wrench" class="w-4.5 h-4.5" />
                                <h3 class="text-sm font-bold uppercase tracking-wider text-[#13251C]">Hasil Pemeriksaan Teknisi</h3>
                            </div>

                            @if ($servis->diagnosis || $servis->tindakan)
                                <div class="space-y-3 text-xs">
                                    <div class="p-3.5 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5]">
                                        <span class="font-bold text-[#13251C] block mb-1">Diagnosis Kerusakan:</span>
                                        <p class="text-[#576B61] leading-relaxed whitespace-pre-line">{{ $servis->diagnosis }}</p>
                                    </div>
                                    @if ($servis->tindakan)
                                        <div class="p-3.5 rounded-xl bg-[#EDF7F2] border border-[#B2DEC6]">
                                            <span class="font-bold text-[#15573F] block mb-1">Tindakan Reparasi:</span>
                                            <p class="text-[#13251C] leading-relaxed whitespace-pre-line">{{ $servis->tindakan }}</p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                                    <p class="font-bold">Sedang Dalam Pemeriksaan Fisik</p>
                                    <p class="mt-0.5 text-amber-700 leading-relaxed">
                                        Teknisi kami sedang memeriksa komponen perangkat Anda di meja servis. Catatan diagnosis dan tindakan akan otomatis muncul di sini begitu diperbarui.
                                    </p>
                                </div>
                            @endif
                            <p class="text-[11px] text-[#576B61] mt-3">
                                Teknisi Penanggung Jawab: <strong>{{ $servis->teknisi->nama ?? 'Antrean Teknisi' }}</strong>
                            </p>
                        </div>

                        {{-- Rincian Biaya & Suku Cadang --}}
                        <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs p-6">
                            <div class="flex items-center justify-between pb-3 border-b border-[#F0F4F2] mb-3">
                                <div class="flex items-center gap-2 text-[#15573F]">
                                    <x-icon name="package" class="w-4.5 h-4.5" />
                                    <h3 class="text-sm font-bold text-[#13251C]">Rincian Biaya & Suku Cadang</h3>
                                </div>
                                <span class="text-xs font-bold text-[#15573F] font-mono">
                                    Total: Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}
                                </span>
                            </div>

                            @if ($servis->detail->count() > 0)
                                <div class="overflow-x-auto mb-3">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-[#F6F8F7] text-[#576B61] uppercase tracking-wider border-b border-[#E3E8E5]">
                                            <tr>
                                                <th class="py-2 px-3 font-bold">Suku Cadang</th>
                                                <th class="py-2 px-2 font-bold text-center">Jml</th>
                                                <th class="py-2 px-3 font-bold text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#F0F4F2]">
                                            @foreach ($servis->detail as $item)
                                                <tr>
                                                    <td class="py-2 px-3 font-medium text-[#13251C]">
                                                        {{ $item->sparepart->nama_sparepart ?? 'Suku Cadang' }}
                                                    </td>
                                                    <td class="py-2 px-2 text-center text-[#576B61]">
                                                        {{ $item->jumlah }}
                                                    </td>
                                                    <td class="py-2 px-3 text-right font-semibold text-[#13251C]">
                                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-xs text-[#84968C] italic mb-3">
                                    Tidak ada pergantian suku cadang tambahan pada perbaikan ini.
                                </p>
                            @endif

                            <div class="pt-3 border-t border-[#F0F4F2] space-y-1.5 text-xs">
                                <div class="flex justify-between text-[#576B61]">
                                    <span>Biaya Jasa Servis:</span>
                                    <span class="font-semibold text-[#13251C]">Rp {{ number_format($servis->biaya_jasa, 0, ',', '.') }}</span>
                                </div>
                                @if ($servis->detail->count() > 0)
                                    <div class="flex justify-between text-[#576B61]">
                                        <span>Total Suku Cadang:</span>
                                        <span class="font-semibold text-[#13251C]">Rp {{ number_format($servis->detail->sum('subtotal'), 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between pt-2 border-t border-[#E3E8E5] text-sm">
                                    <span class="font-bold text-[#13251C]">Total Biaya Akhir:</span>
                                    <span class="font-extrabold text-[#15573F] font-mono">Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN (5/12): KELUHAN & FORM TAMBAH CATATAN PELANGGAN --}}
                    <div class="lg:col-span-5 space-y-6">

                        {{-- KARTU KELUHAN & FORM EDIT/TAMBAH CATATAN PELANGGAN --}}
                        <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs p-6">
                            <div class="flex items-center gap-2 mb-3 text-[#15573F]">
                                <x-icon name="clipboard" class="w-4.5 h-4.5" />
                                <h3 class="text-sm font-bold uppercase tracking-wider text-[#13251C]">Keluhan & Catatan Anda</h3>
                            </div>

                            {{-- Keluhan yang sudah tercatat --}}
                            <div class="p-3.5 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5] text-xs text-[#13251C] leading-relaxed whitespace-pre-line mb-4 font-medium">
                                {{ $servis->keluhan ?: 'Tidak ada catatan keluhan awal.' }}
                            </div>

                            {{-- Form Tambah Catatan / Edit Keluhan Mandiri oleh Pelanggan --}}
                            @if (! in_array($servis->status, [\App\Enums\StatusServis::Selesai, \App\Enums\StatusServis::Diambil]))
                                <div class="pt-4 border-t border-[#F0F4F2]">
                                    <p class="text-xs font-bold text-[#13251C] mb-1">Tambah Catatan / Keluhan Tambahan:</p>
                                    <p class="text-[11px] text-[#576B61] mb-2.5">
                                        Ingin mengingatkan teknisi atau ada masalah lain yang baru diingat? Ketik di sini dan sistem akan langsung menyampaikannya ke meja kerja teknisi.
                                    </p>

                                    <form method="POST" action="{{ route('lacak.keluhan.update', $servis->no_servis) }}" class="space-y-3" x-data="{ sending: false }" @submit="sending = true">
                                        @csrf
                                        <textarea
                                            name="catatan_tambahan"
                                            rows="3"
                                            required
                                            placeholder="Contoh: Tolong jangan lupa file penting di folder Dokumen jangan dihapus ya mas..."
                                            class="w-full p-3 rounded-xl border border-[#CCD5D0] text-xs text-[#13251C] focus:ring-2 focus:ring-[#15573F]/20 focus:border-[#15573F] transition-all"
                                        ></textarea>

                                        <button
                                            type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-[#15573F] text-white hover:bg-[#104430] transition-colors shadow-2xs cursor-pointer disabled:opacity-50"
                                            x-bind:disabled="sending"
                                        >
                                            <x-icon name="check-circle" class="w-4 h-4" />
                                            <span>Simpan Catatan Tambahan</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="p-3 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5] text-[11px] text-[#576B61] text-center">
                                    🔒 Pengerjaan telah berstatus selesai/diambil, catatan pengerjaan telah dikunci permanen sebagai arsip riwayat.
                                </div>
                            @endif
                        </div>

                        {{-- KARTU BANTUAN LANGSUNG WHATSAPP --}}
                        <div class="bg-gradient-to-br from-white to-[#EDF7F2] rounded-2xl border border-[#B2DEC6] p-6 shadow-xs text-center space-y-3">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-[#25D366] text-white flex items-center justify-center text-xl shadow-xs">
                                💬
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-[#13251C]">Butuh Konfirmasi Langsung?</h4>
                                <p class="text-xs text-[#576B61] mt-0.5">
                                    Tim kami siap merespons chat Anda jika ada pertanyaan mengenai estimasi biaya atau ketersediaan suku cadang.
                                </p>
                            </div>
                            @if ($waLink)
                                <a
                                    href="{{ $waLink }}"
                                    target="_blank"
                                    class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-xs font-semibold bg-[#25D366] text-white hover:bg-[#1ebc59] transition-colors shadow-xs"
                                >
                                    <span>Hubungi Admin Toko via WhatsApp</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </main>

    {{-- FOOTER SEDERHANA --}}
    <footer class="text-center text-xs text-[#576B61] py-6 border-t border-[#E3E8E5] bg-white mt-8">
        &copy; {{ date('Y') }} Servis Komputer &bull; Sistem Informasi Manajemen & Pelayanan Servis
    </footer>

</body>
</html>
