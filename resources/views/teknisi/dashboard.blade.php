<x-app-layout title="Dashboard Teknisi" breadcrumb="Dashboard">
    {{-- HEADER DASHBOARD --}}
    <x-page-header
        eyebrow="PANEL TEKNISI"
        :title="\App\Support\TimeGreeting::greeting() . ', ' . auth()->user()->nama"
        subtitle="Pantau antrean masuk bengkel, lanjutkan servis aktif Anda, dan catat perbaikan perangkat pelanggan."
    >
        <x-slot:actions>
            <x-button variant="primary" icon="wrench" href="{{ route('teknisi.servis.index') }}">
                Daftar Servis Masuk
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- 4 KARTU STATISTIK TEKNISI --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            label="Antrean Masuk"
            :value="str_pad($antreanMasuk, 2, '0', STR_PAD_LEFT)"
            description="Status 'Diterima' di bengkel"
            icon="clock"
            indicator="Antrean"
            color="amber"
        />

        <x-stat-card
            label="Sedang Saya Kerjakan"
            :value="str_pad($sedangSayaKerjakan, 2, '0', STR_PAD_LEFT)"
            description="Pemeriksaan & perbaikan aktif"
            icon="wrench"
            indicator="Aktif"
            color="primary"
        />

        <x-stat-card
            label="Selesai oleh Saya"
            :value="str_pad($selesaiOlehSaya, 2, '0', STR_PAD_LEFT)"
            description="Total pengerjaan tuntas Anda"
            icon="check-circle"
            color="green"
        />

        <x-stat-card
            label="Total Selesai Bengkel"
            :value="str_pad($totalSelesaiBengkel, 2, '0', STR_PAD_LEFT)"
            description="Akumulasi seluruh teknisi"
            icon="clipboard"
            color="blue"
        />
    </div>

    {{-- KONTEN UTAMA DUA KOLOM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI: PENGERJAAN AKTIF SAYA (2 KOLOM) --}}
        <div class="lg:col-span-2 space-y-4">
            <x-card class="p-5">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#F0F4F2]">
                    <div>
                        <x-eyebrow>PENGERJAAN AKTIF SAYA</x-eyebrow>
                        <h2 class="text-base font-bold text-[#13251C] mt-0.5">Servis Sedang Anda Tangani</h2>
                    </div>
                    <span class="text-xs text-[#15573F] bg-[#EDF7F2] px-2.5 py-1 rounded-md font-semibold border border-[#B2DEC6]">
                        {{ $pekerjaanAktif->count() }} Sedang Diproses
                    </span>
                </div>

                @if ($pekerjaanAktif->count() > 0)
                    <div class="divide-y divide-[#F0F4F2]">
                        @foreach ($pekerjaanAktif as $srv)
                            <div class="py-3.5 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#F9FBFA] -mx-5 px-5 transition-colors">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center shrink-0 border border-[#B2DEC6]">
                                        <x-icon name="tool" class="w-5 h-5" />
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-[#15573F] font-mono">{{ $srv->no_servis }}</span>
                                            <span class="text-xs font-semibold text-[#13251C] truncate">{{ $srv->merk_tipe }}</span>
                                        </div>
                                        <p class="text-[11px] text-[#576B61] truncate mt-0.5">
                                            Pemilik: <strong>{{ $srv->pelanggan?->nama }}</strong> &bull; Keluhan: {{ $srv->keluhan }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-0 border-[#F0F4F2]">
                                    <x-status-badge :status="$srv->status" />
                                    <x-button
                                        href="{{ route('teknisi.servis.show', $srv) }}"
                                        variant="primary"
                                        size="sm"
                                        icon="wrench"
                                    >
                                        Buka Pengerjaan
                                    </x-button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center">
                        <x-icon name="wrench" class="w-8 h-8 mx-auto text-[#84968C] mb-2" />
                        <h4 class="text-xs font-bold text-[#13251C]">Tidak Ada Pekerjaan Aktif</h4>
                        <p class="text-xs text-[#576B61] mt-0.5 mb-4">Anda belum memiliki tiket servis yang sedang dalam proses pemeriksaan atau perbaikan.</p>
                        <x-button href="{{ route('teknisi.servis.index') }}" variant="secondary" size="sm" icon="plus">
                            Ambil Servis dari Antrean
                        </x-button>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- KOLOM KANAN: ANTREAN MENUNGGU & RIWAYAT SELESAI (1 KOLOM) --}}
        <div class="space-y-6">
            {{-- ANTREAN MASUK BARU --}}
            <x-card class="p-5">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F0F4F2]">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#15573F]">
                        Antrean Servis Baru
                    </span>
                    <a href="{{ route('teknisi.servis.index', ['status' => 'diterima']) }}" class="text-xs text-[#1F6B4B] font-semibold hover:underline">
                        Lihat Antrean
                    </a>
                </div>

                @if ($antreanMenunggu->count() > 0)
                    <div class="divide-y divide-[#F0F4F2] text-xs">
                        @foreach ($antreanMenunggu as $srv)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-[#15573F] font-mono">{{ $srv->no_servis }}</span>
                                        <span class="text-[#13251C] font-medium truncate">{{ $srv->merk_tipe }}</span>
                                    </div>
                                    <p class="text-[11px] text-[#576B61] truncate mt-0.5">
                                        {{ $srv->pelanggan?->nama }} &bull; Masuk: {{ $srv->tgl_masuk?->translatedFormat('d M') }}
                                    </p>
                                </div>
                                <a
                                    href="{{ route('teknisi.servis.show', $srv) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-[#E8F3EE] text-[#1F6B4B] hover:bg-[#1F6B4B] hover:text-white transition-all shrink-0"
                                >
                                    Periksa
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-[#84968C] italic">Tidak ada antrean servis baru.</p>
                @endif
            </x-card>

            {{-- RIWAYAT TUNTAS SAYA --}}
            <x-card class="p-5">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F0F4F2]">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#15573F]">
                        Selesai Terakhir oleh Saya
                    </span>
                </div>

                @if ($riwayatSelesai->count() > 0)
                    <div class="divide-y divide-[#F0F4F2] text-xs">
                        @foreach ($riwayatSelesai as $srv)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-[#15573F] font-mono">{{ $srv->no_servis }}</span>
                                        <span class="text-[#13251C] font-medium truncate">{{ $srv->merk_tipe }}</span>
                                    </div>
                                    <p class="text-[11px] text-[#576B61] truncate mt-0.5">
                                        {{ $srv->pelanggan?->nama }} &bull; Selesai: {{ $srv->tgl_selesai?->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <div class="shrink-0">
                                    <x-status-badge :status="$srv->status" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-[#84968C] italic">Belum ada servis yang diselesaikan.</p>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
