<x-app-layout title="Laporan Servis & Analitik" breadcrumb="Laporan Servis">
    {{-- HEADER HALAMAN --}}
    <x-page-header
        eyebrow="ANALITIK & REKAPITULASI"
        title="Laporan Servis & Grafik Performa"
        subtitle="Analisis tren pengerjaan perangkat, pendapatan servis lunas, distribusi status pengerjaan, dan cetak rekapitulasi resmi."
    >
        <x-slot:actions>
            <x-button
                href="{{ route('admin.laporan.cetak', request()->query()) }}"
                target="_blank"
                variant="primary"
                icon="printer"
            >
                Cetak Dokumen Laporan
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- KARTU STATISTIK RINGKASAN --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            label="Total Servis Terfilter"
            :value="str_pad($summary['total_servis'], 2, '0', STR_PAD_LEFT)"
            description="Tiket dalam periode & kriteria"
            icon="clipboard"
            color="primary"
        />

        <x-stat-card
            label="Total Pendapatan Lunas"
            :value="'Rp ' . number_format($summary['total_pendapatan'], 0, ',', '.')"
            description="Dari servis yang sudah diambil"
            icon="credit-card"
            color="green"
        />

        <x-stat-card
            label="Pendapatan Jasa Teknisi"
            :value="'Rp ' . number_format($summary['total_jasa'], 0, ',', '.')"
            description="Murni jasa perbaikan bengkel"
            icon="wrench"
            color="blue"
        />

        <x-stat-card
            label="Servis Telah Selesai"
            :value="str_pad($summary['selesai_count'], 2, '0', STR_PAD_LEFT)"
            description="Status 'Selesai' & 'Diambil'"
            icon="check-circle"
            indicator="Tuntas"
            color="green"
        />
    </div>

    {{-- FORM FILTER LAPORAN --}}
    <x-card class="p-5 mb-6">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                {{-- Tanggal Awal --}}
                <div>
                    <label for="tgl_awal" class="block text-xs font-semibold text-[#13251C] mb-1">Tanggal Awal</label>
                    <x-input
                        type="date"
                        name="tgl_awal"
                        id="tgl_awal"
                        value="{{ $filters['tgl_awal'] ?? '' }}"
                    />
                </div>

                {{-- Tanggal Akhir --}}
                <div>
                    <label for="tgl_akhir" class="block text-xs font-semibold text-[#13251C] mb-1">Tanggal Akhir</label>
                    <x-input
                        type="date"
                        name="tgl_akhir"
                        id="tgl_akhir"
                        value="{{ $filters['tgl_akhir'] ?? '' }}"
                    />
                </div>

                {{-- Filter Status --}}
                <div>
                    <label for="status" class="block text-xs font-semibold text-[#13251C] mb-1">Status Servis</label>
                    <x-select name="status" id="status">
                        <option value="">Semua Status</option>
                        <option value="diterima" {{ ($filters['status'] ?? '') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="diperiksa" {{ ($filters['status'] ?? '') === 'diperiksa' ? 'selected' : '' }}>Dalam Pemeriksaan</option>
                        <option value="diperbaiki" {{ ($filters['status'] ?? '') === 'diperbaiki' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        <option value="selesai" {{ ($filters['status'] ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="diambil" {{ ($filters['status'] ?? '') === 'diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                    </x-select>
                </div>

                {{-- Filter Teknisi --}}
                <div>
                    <label for="id_teknisi" class="block text-xs font-semibold text-[#13251C] mb-1">Teknisi Penanggung Jawab</label>
                    <x-select name="id_teknisi" id="id_teknisi">
                        <option value="">Semua Teknisi</option>
                        @foreach ($teknisiList as $tk)
                            <option value="{{ $tk->id_user }}" {{ (string)($filters['id_teknisi'] ?? '') === (string)$tk->id_user ? 'selected' : '' }}>
                                {{ $tk->nama }}
                            </option>
                        @endforeach
                    </x-select>
                </div>

                {{-- Pencarian Kata Kunci --}}
                <div>
                    <label for="q" class="block text-xs font-semibold text-[#13251C] mb-1">Kata Kunci</label>
                    <div class="relative">
                        <x-input
                            type="text"
                            name="q"
                            id="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="No. tiket, nama, merk..."
                            class="pl-9"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#84968C]">
                            <x-icon name="search" class="w-4 h-4" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-[#F0F4F2]">
                <span class="text-xs text-[#576B61]">
                    Menampilkan hasil filter rentang tanggal, status, dan penanggung jawab servis.
                </span>
                <div class="flex items-center gap-2">
                    @if (array_filter($filters))
                        <x-button href="{{ route('admin.laporan.index') }}" variant="ghost" size="md">
                            Reset Filter
                        </x-button>
                    @endif
                    <x-button type="submit" variant="secondary" size="md" icon="search">
                        Terapkan Filter
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>

    {{-- SEKSI GRAFIK INTERAKTIF (CHART.JS VIA NPM) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- GRAFIK 1: TREN BULANAN SERVIS (LEBAR: 2 KOLOM) --}}
        <div class="lg:col-span-2">
            <x-card class="p-5 h-full flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#F0F4F2]">
                        <div>
                            <x-eyebrow>TREN BULANAN</x-eyebrow>
                            <h3 class="text-base font-bold text-[#13251C] mt-0.5">Volume Servis Masuk vs Selesai (6 Bulan)</h3>
                        </div>
                        <span class="text-xs text-[#84968C] font-mono">Bulan Berjalan</span>
                    </div>
                    <div class="relative w-full h-72">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-6 mt-4 pt-3 border-t border-[#F0F4F2] text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#15573F]"></span>
                        <span class="text-[#576B61]">Servis Masuk</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#10B981]"></span>
                        <span class="text-[#576B61]">Servis Selesai & Diambil</span>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- GRAFIK 2: DISTRIBUSI STATUS SERVIS (1 KOLOM) --}}
        <div>
            <x-card class="p-5 h-full flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#F0F4F2]">
                        <div>
                            <x-eyebrow>KOMPOSISI STATUS</x-eyebrow>
                            <h3 class="text-base font-bold text-[#13251C] mt-0.5">Proporsi Status Servis</h3>
                        </div>
                    </div>
                    <div class="relative w-full h-64 flex items-center justify-center">
                        <canvas id="statusDistributionChart"></canvas>
                    </div>
                </div>
                <div class="pt-3 border-t border-[#F0F4F2] text-[11px] text-[#576B61] text-center">
                    Data sebaran status berdasarkan filter aktif saat ini.
                </div>
            </x-card>
        </div>
    </div>

    {{-- GRAFIK 3: TOP 5 SUKU CADANG TERBANYAK DIGUNAKAN --}}
    @if (!empty($topSpareparts['labels']) && count($topSpareparts['labels']) > 0)
        <x-card class="p-5 mb-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#F0F4F2]">
                <div>
                    <x-eyebrow>INVENTARIS SUKU CADANG</x-eyebrow>
                    <h3 class="text-base font-bold text-[#13251C] mt-0.5">5 Suku Cadang Paling Sering Digunakan</h3>
                </div>
                <a href="{{ route('admin.sparepart.index') }}" class="text-xs text-[#1F6B4B] font-semibold hover:underline">
                    Kelola Stok &rarr;
                </a>
            </div>
            <div class="relative w-full h-56">
                <canvas id="topSparepartsChart"></canvas>
            </div>
        </x-card>
    @endif

    {{-- TABEL DATA RINCIAN LAPORAN --}}
    <x-card class="p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 mb-4 border-b border-[#F0F4F2]">
            <div>
                <x-eyebrow>DATA TABULAR</x-eyebrow>
                <h3 class="text-base font-bold text-[#13251C] mt-0.5">Rincian Riwayat Servis Perangkat</h3>
            </div>
            <div class="text-xs text-[#576B61]">
                Menampilkan <strong>{{ $servisList->firstItem() ?? 0 }}-{{ $servisList->lastItem() ?? 0 }}</strong> dari total <strong>{{ $servisList->total() }}</strong> tiket
            </div>
        </div>

        @if ($servisList->count() > 0)
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full text-left text-xs text-[#13251C]">
                    <thead class="bg-[#F6F8F7] font-bold text-[#576B61] uppercase tracking-wider border-b border-[#E3E8E5]">
                        <tr>
                            <th scope="col" class="py-3 px-3">No. Servis</th>
                            <th scope="col" class="py-3 px-3">Tanggal</th>
                            <th scope="col" class="py-3 px-3">Pelanggan</th>
                            <th scope="col" class="py-3 px-3">Perangkat</th>
                            <th scope="col" class="py-3 px-3">Teknisi</th>
                            <th scope="col" class="py-3 px-3">Status</th>
                            <th scope="col" class="py-3 px-3 text-right">Biaya Jasa</th>
                            <th scope="col" class="py-3 px-3 text-right">Sparepart</th>
                            <th scope="col" class="py-3 px-3 text-right">Total Biaya</th>
                            <th scope="col" class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3E8E5]">
                        @foreach ($servisList as $item)
                            @php
                                $subtotalPart = $item->detail->sum('subtotal');
                            @endphp
                            <tr class="hover:bg-[#F9FBFA] transition-colors">
                                <td class="py-3 px-3 font-bold font-mono text-[#15573F] whitespace-nowrap">
                                    {{ $item->no_servis }}
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap text-[#576B61]">
                                    {{ $item->tgl_masuk ? $item->tgl_masuk->format('d M Y') : '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-[#13251C]">{{ $item->pelanggan?->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-[#84968C]">{{ $item->pelanggan?->no_telepon ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3 max-w-xs">
                                    <div class="font-medium text-[#13251C] truncate">{{ $item->merk_tipe }}</div>
                                    <div class="text-[11px] text-[#576B61] line-clamp-1 mt-0.5">{{ $item->keluhan }}</div>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap text-[#576B61]">
                                    {{ $item->teknisi?->nama ?? '-' }}
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <x-status-badge :status="$item->status" />
                                </td>
                                <td class="py-3 px-3 text-right whitespace-nowrap font-mono">
                                    Rp {{ number_format($item->biaya_jasa, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right whitespace-nowrap font-mono text-[#576B61]">
                                    Rp {{ number_format($subtotalPart, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right whitespace-nowrap font-bold font-mono text-[#15573F]">
                                    Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <a
                                        href="{{ route('admin.servis.show', $item) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-[#E8F3EE] text-[#1F6B4B] hover:bg-[#1F6B4B] hover:text-white transition-all"
                                        title="Buka Detail Servis"
                                    >
                                        <x-icon name="clipboard" class="w-3.5 h-3.5" />
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($servisList->hasPages())
                <div class="pt-4 border-t border-[#E3E8E5]">
                    {{ $servisList->links() }}
                </div>
            @endif
        @else
            <div class="py-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center mx-auto mb-3">
                    <x-icon name="clipboard" class="w-6 h-6" />
                </div>
                <h4 class="text-sm font-bold text-[#13251C]">Tidak Ada Data Servis</h4>
                <p class="text-xs text-[#576B61] mt-1 max-w-sm mx-auto">
                    Tidak ditemukan data servis yang cocok dengan kriteria filter yang Anda tentukan.
                </p>
                <div class="mt-4">
                    <x-button href="{{ route('admin.laporan.index') }}" variant="secondary" size="sm">
                        Reset Filter
                    </x-button>
                </div>
            </div>
        @endif
    </x-card>

    {{-- SCRIPT INISIALISASI CHART.JS DARI BUNDLE NPM --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js belum dimuat.');
                return;
            }

            // 1. Grafik Tren Bulanan (Line Chart)
            const trendCtx = document.getElementById('monthlyTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($monthlyTrend['labels']),
                        datasets: [
                            {
                                label: 'Servis Masuk',
                                data: @json($monthlyTrend['masuk']),
                                borderColor: '#15573F',
                                backgroundColor: 'rgba(21, 87, 63, 0.1)',
                                tension: 0.35,
                                fill: true,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#15573F',
                                pointRadius: 4,
                            },
                            {
                                label: 'Servis Selesai & Diambil',
                                data: @json($monthlyTrend['selesai']),
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.35,
                                fill: true,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#10B981',
                                pointRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, font: { family: 'inherit', size: 11 } },
                                grid: { color: '#F0F4F2' }
                            },
                            x: {
                                ticks: { font: { family: 'inherit', size: 11 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Grafik Distribusi Status (Doughnut Chart)
            const statusCtx = document.getElementById('statusDistributionChart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusDistribution['labels']),
                        datasets: [{
                            data: @json($statusDistribution['data']),
                            backgroundColor: [
                                '#F59E0B', // Diterima (Amber)
                                '#3B82F6', // Diperiksa (Blue)
                                '#8B5CF6', // Diperbaiki (Purple)
                                '#10B981', // Selesai (Emerald)
                                '#6B7280'  // Diambil (Gray)
                            ],
                            borderWidth: 2,
                            borderColor: '#FFFFFF'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 12,
                                    font: { family: 'inherit', size: 11 }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

            // 3. Grafik Top 5 Suku Cadang (Bar Chart)
            const sparepartsCtx = document.getElementById('topSparepartsChart');
            if (sparepartsCtx) {
                new Chart(sparepartsCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($topSpareparts['labels']),
                        datasets: [{
                            label: 'Jumlah Unit Digunakan',
                            data: @json($topSpareparts['data']),
                            backgroundColor: '#15573F',
                            borderRadius: 6,
                            maxBarThickness: 36
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, font: { family: 'inherit', size: 11 } },
                                grid: { color: '#F0F4F2' }
                            },
                            x: {
                                ticks: { font: { family: 'inherit', size: 11 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
