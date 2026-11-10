<x-app-layout title="Dashboard Petugas Servis" breadcrumb="Dashboard">
    {{-- HEADER DASHBOARD --}}
    <x-page-header
        eyebrow="PANEL PETUGAS SERVIS"
        :title="\App\Support\TimeGreeting::greeting() . ', ' . auth()->user()->nama"
        subtitle="Kelola penerimaan servis, pendaftaran pelanggan baru, dan penyerahan perangkat yang telah selesai."
    >
        <x-slot:actions>
            <x-button variant="secondary" icon="user" href="{{ route('petugas.pelanggan.index') }}">
                Data Pelanggan
            </x-button>
            <x-button variant="primary" icon="plus" href="{{ route('petugas.servis.tambah') }}">
                Pendaftaran Baru
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- 4 KARTU STATISTIK PETUGAS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            label="Servis Masuk Hari Ini"
            :value="str_pad($servisHariIni, 2, '0', STR_PAD_LEFT)"
            description="Pendaftaran hari ini"
            icon="clock"
            color="primary"
        />

        <x-stat-card
            label="Menunggu Antrean"
            :value="str_pad($menungguAntrean, 2, '0', STR_PAD_LEFT)"
            description="Status 'Diterima'"
            icon="clipboard"
            indicator="Antrean"
            color="amber"
        />

        <x-stat-card
            label="Siap Diserahkan"
            :value="str_pad($siapDiserahkan, 2, '0', STR_PAD_LEFT)"
            description="Selesai diservis teknisi"
            icon="check-circle"
            indicator="Siap Serah"
            color="green"
        />

        <x-stat-card
            label="Sudah Diambil"
            :value="str_pad($sudahDiambil, 2, '0', STR_PAD_LEFT)"
            description="Telah diserahkan ke pemilik"
            icon="user"
            color="blue"
        />
    </div>

    {{-- KONTEN UTAMA DUA KOLOM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI: PERANGKAT SIAP DISERAHKAN (2 KOLOM) --}}
        <div class="lg:col-span-2 space-y-4">
            <x-card class="p-5">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#F0F4F2]">
                    <div>
                        <x-eyebrow>SIAP DISERAHKAN</x-eyebrow>
                        <h2 class="text-base font-bold text-[#13251C] mt-0.5">Antrean Penyerahan ke Pelanggan</h2>
                    </div>
                    <span class="text-xs text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-md font-semibold border border-emerald-200">
                        {{ $antreanSerah->count() }} Menunggu Diambil
                    </span>
                </div>

                @if ($antreanSerah->count() > 0)
                    <div class="divide-y divide-[#F0F4F2]">
                        @foreach ($antreanSerah as $srv)
                            <div class="py-3.5 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#F9FBFA] -mx-5 px-5 transition-colors">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
                                        <x-icon name="check-circle" class="w-5 h-5" />
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-[#15573F] font-mono">{{ $srv->no_servis }}</span>
                                            <span class="text-xs font-semibold text-[#13251C] truncate">{{ $srv->merk_tipe }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-0.5 text-[11px] text-[#576B61]">
                                            <span class="font-medium text-[#13251C]">{{ $srv->pelanggan?->nama }}</span>
                                            <span>&bull;</span>
                                            <span class="font-mono">{{ $srv->pelanggan?->no_telepon }}</span>
                                            <a
                                                href="{{ $srv->pelanggan?->wa_link }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-emerald-700 hover:underline inline-flex items-center gap-0.5 font-semibold"
                                            >
                                                WA
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-0 border-[#F0F4F2]">
                                    <div class="text-left sm:text-right">
                                        <span class="text-[10px] text-[#84968C] block uppercase font-semibold">Total Tagihan</span>
                                        <span class="text-xs font-bold text-[#15573F]">
                                            Rp {{ number_format($srv->total_biaya, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <x-button
                                        href="{{ route('petugas.servis.serahkan', $srv) }}"
                                        variant="primary"
                                        size="sm"
                                        icon="check-circle"
                                    >
                                        Serahkan
                                    </x-button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center">
                        <x-icon name="check-circle" class="w-8 h-8 mx-auto text-[#84968C] mb-2" />
                        <h4 class="text-xs font-bold text-[#13251C]">Tidak Ada Antrean Penyerahan</h4>
                        <p class="text-xs text-[#576B61] mt-0.5">Semua servis selesai telah diserahkan atau masih dalam pengerjaan teknisi.</p>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- KOLOM KANAN: REGISTRASI TERBARU & PELANGGAN BARU (1 KOLOM) --}}
        <div class="space-y-6">
            {{-- SERVIS TERDAFTAR TERBARU --}}
            <x-card class="p-5">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F0F4F2]">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#15573F]">
                        Pendaftaran Servis Terbaru
                    </span>
                    <a href="{{ route('petugas.servis.index') }}" class="text-xs text-[#1F6B4B] font-semibold hover:underline">
                        Lihat Semua
                    </a>
                </div>

                @if ($servisTerbaru->count() > 0)
                    <div class="divide-y divide-[#F0F4F2] text-xs">
                        @foreach ($servisTerbaru as $srv)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-[#15573F] font-mono">{{ $srv->no_servis }}</span>
                                        <span class="text-[#13251C] font-medium truncate">{{ $srv->merk_tipe }}</span>
                                    </div>
                                    <p class="text-[11px] text-[#576B61] truncate mt-0.5">
                                        {{ $srv->pelanggan?->nama }} &bull; {{ $srv->tgl_masuk?->translatedFormat('d M') }}
                                    </p>
                                </div>
                                <div class="shrink-0">
                                    <x-status-badge :status="$srv->status" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-[#84968C] italic">Belum ada pendaftaran servis tercatat.</p>
                @endif
            </x-card>

            {{-- PELANGGAN TERDAFTAR TERBARU --}}
            <x-card class="p-5">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F0F4F2]">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#15573F]">
                        Pelanggan Terbaru
                    </span>
                    <a href="{{ route('petugas.pelanggan.index') }}" class="text-xs text-[#1F6B4B] font-semibold hover:underline">
                        Lihat Semua
                    </a>
                </div>

                @if ($pelangganTerbaru->count() > 0)
                    <div class="divide-y divide-[#F0F4F2] text-xs">
                        @foreach ($pelangganTerbaru as $cust)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-semibold text-[#13251C] truncate">{{ $cust->nama }}</p>
                                    <p class="text-[11px] text-[#576B61] font-mono mt-0.5">{{ $cust->no_telepon }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-[#F6F8F7] text-[#576B61] border border-[#E3E8E5] shrink-0">
                                    {{ $cust->servis_count }} Servis
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-[#84968C] italic">Belum ada pelanggan terdaftar.</p>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
