<x-app-layout title="Dashboard Administrator" breadcrumb="Dashboard">
    {{-- HEADER DASHBOARD --}}
    <x-page-header
        eyebrow="PANEL ADMINISTRATOR"
        :title="\App\Support\TimeGreeting::greeting() . ', ' . auth()->user()->nama"
        subtitle="Berikut ringkasan performa operasional servis, antrean perangkat, dan ketersediaan suku cadang bengkel Anda."
    >
        <x-slot:actions>
            <x-button variant="secondary" icon="package" href="{{ route('admin.sparepart.index') }}">
                Kelola Sparepart
            </x-button>
            <x-button variant="primary" icon="user" href="{{ route('admin.user.index') }}">
                Kelola User
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- 4 KARTU STATISTIK UTAMA --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            label="Total Servis"
            :value="str_pad($totalServis, 2, '0', STR_PAD_LEFT)"
            description="Seluruh tiket terdaftar"
            icon="clipboard"
            color="primary"
        />

        <x-stat-card
            label="Baru Diterima"
            :value="str_pad($baruDiterima, 2, '0', STR_PAD_LEFT)"
            description="Menunggu pemeriksaan teknisi"
            icon="clock"
            indicator="Antrean"
            color="amber"
        />

        <x-stat-card
            label="Dalam Pengerjaan"
            :value="str_pad($dalamPengerjaan, 2, '0', STR_PAD_LEFT)"
            description="Pemeriksaan & perbaikan"
            icon="wrench"
            indicator="Proses"
            color="primary"
        />

        <x-stat-card
            label="Selesai & Diambil"
            :value="str_pad($selesaiDanDiambil, 2, '0', STR_PAD_LEFT)"
            description="Telah selesai ditangani"
            icon="check-circle"
            color="green"
        />
    </div>

    {{-- HIGHLIGHT SEKUNDER (OMZET, PELANGGAN, PERINGATAN STOK) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-white rounded-2xl border border-[#E3E8E5] shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center shrink-0">
                <x-icon name="credit-card" class="w-5 h-5" />
            </div>
            <div>
                <span class="text-[11px] font-semibold text-[#576B61] uppercase tracking-wider block">Total Penerimaan Lunas</span>
                <span class="text-base font-extrabold text-[#13251C] mt-0.5 block">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-[#E3E8E5] shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                <x-icon name="user" class="w-5 h-5" />
            </div>
            <div>
                <span class="text-[11px] font-semibold text-[#576B61] uppercase tracking-wider block">Pelanggan Terdaftar</span>
                <span class="text-base font-extrabold text-[#13251C] mt-0.5 block">
                    {{ $totalPelanggan }} Pelanggan
                </span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-[#E3E8E5] shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl {{ $stokKritisCount > 0 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }} flex items-center justify-center shrink-0">
                <x-icon name="package" class="w-5 h-5" />
            </div>
            <div>
                <span class="text-[11px] font-semibold text-[#576B61] uppercase tracking-wider block">Suku Cadang Menipis (&le; 5)</span>
                <span class="text-base font-extrabold {{ $stokKritisCount > 0 ? 'text-amber-800' : 'text-[#13251C]' }} mt-0.5 block">
                    {{ $stokKritisCount }} Item Kritis
                </span>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA DUA KOLOM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI: AKTIVITAS TERBARU (2 KOLOM) --}}
        <div class="lg:col-span-2 space-y-4">
            <x-card class="p-5">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#F0F4F2]">
                    <div>
                        <x-eyebrow>AKTIVITAS TERBARU</x-eyebrow>
                        <h2 class="text-base font-bold text-[#13251C] mt-0.5">Perkembangan Servis Terkini</h2>
                    </div>
                    <span class="text-xs text-[#84968C] font-mono">Real-Time Data</span>
                </div>

                @if ($aktivitasTerbaru->count() > 0)
                    <div class="divide-y divide-[#F0F4F2]">
                        @foreach ($aktivitasTerbaru as $srv)
                            <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 hover:bg-[#F9FBFA] -mx-5 px-5 transition-colors">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    {{-- Ikon Perangkat --}}
                                    <div class="w-10 h-10 rounded-xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center shrink-0 border border-[#B2DEC6]">
                                        <x-icon name="laptop" class="w-5 h-5" />
                                    </div>

                                    {{-- Detail Perangkat & Pelanggan --}}
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-[#15573F] font-mono">{{ $srv->no_servis }}</span>
                                            <span class="text-xs font-semibold text-[#13251C] truncate">{{ $srv->merk_tipe }}</span>
                                        </div>
                                        <p class="text-[11px] text-[#576B61] truncate mt-0.5">
                                            {{ $srv->pelanggan?->nama ?? '-' }} &bull; {{ $srv->tgl_masuk?->translatedFormat('d M Y') ?? '-' }}
                                            @if ($srv->teknisi)
                                                &bull; PJ: <span class="font-medium text-[#13251C]">{{ $srv->teknisi->nama }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <x-status-badge :status="$srv->status" />
                                    <a
                                        href="{{ route('admin.servis.show', $srv) }}"
                                        class="p-1.5 rounded-lg text-[#84968C] hover:text-[#15573F] hover:bg-[#E8F3EE] transition-colors"
                                        title="Buka Detail Servis"
                                    >
                                        <x-icon name="chevron-right" class="w-4 h-4" />
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center">
                        <x-icon name="clipboard" class="w-8 h-8 mx-auto text-[#84968C] mb-2" />
                        <p class="text-xs text-[#576B61]">Belum ada data tiket servis tercatat dalam sistem.</p>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- KOLOM KANAN: STOK KRITIS & TIM PENGGUNA (1 KOLOM) --}}
        <div class="space-y-6">
            {{-- KARTU MONITORING STOK SPAREPART --}}
            <x-card class="p-5">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F0F4F2]">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#15573F]">
                        Monitoring Suku Cadang
                    </span>
                    <a href="{{ route('admin.sparepart.index') }}" class="text-xs text-[#1F6B4B] font-semibold hover:underline">
                        Lihat Semua
                    </a>
                </div>

                @if ($sparepartKritis->count() > 0)
                    <div class="divide-y divide-[#F0F4F2] text-xs">
                        @foreach ($sparepartKritis as $part)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-semibold text-[#13251C] truncate">{{ $part->nama_sparepart }}</p>
                                    <p class="text-[11px] text-[#576B61] mt-0.5">Rp {{ number_format($part->harga, 0, ',', '.') }}</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold font-mono {{ $part->stok <= 5 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    Stok: {{ $part->stok }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-[#84968C] italic">Katalog sparepart masih kosong.</p>
                @endif
            </x-card>

            {{-- KARTU STRUKTUR TIM PENGGUNA --}}
            <x-card class="p-5">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F0F4F2]">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#15573F]">
                        Tim & Pengguna Aktif
                    </span>
                    <a href="{{ route('admin.user.index') }}" class="text-xs text-[#1F6B4B] font-semibold hover:underline">
                        Kelola Akun
                    </a>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#F6F8F7]">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-[#EDF7F2] text-[#15573F] flex items-center justify-center font-bold text-[10px]">
                                AD
                            </div>
                            <span class="font-medium text-[#13251C]">Administrator</span>
                        </div>
                        <strong class="text-sm font-bold text-[#15573F]">{{ $timCounts['admin'] }} User</strong>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#F6F8F7]">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center font-bold text-[10px]">
                                PT
                            </div>
                            <span class="font-medium text-[#13251C]">Petugas Servis</span>
                        </div>
                        <strong class="text-sm font-bold text-[#15573F]">{{ $timCounts['petugas'] }} User</strong>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#F6F8F7]">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-[10px]">
                                TK
                            </div>
                            <span class="font-medium text-[#13251C]">Teknisi Perbaikan</span>
                        </div>
                        <strong class="text-sm font-bold text-[#15573F]">{{ $timCounts['teknisi'] }} User</strong>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
