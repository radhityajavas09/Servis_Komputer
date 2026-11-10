<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Rekapitulasi Servis &bull; {{ config('app.name', 'Servis Komputer') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: #000000 !important;
                font-size: 11pt;
            }
            .print-page {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body class="bg-[#F6F8F7] text-[#13251C] antialiased min-h-screen py-8">

    {{-- TOMBOL KONTROL ATAS (HILANG SAAT CETAK) --}}
    <div class="no-print max-w-5xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a
            href="{{ route('admin.laporan.index', request()->query()) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-[#E3E8E5] text-[#576B61] hover:text-[#13251C] hover:bg-[#F0F4F2] shadow-2xs transition-all"
        >
            <x-icon name="chevron-right" class="w-4 h-4 rotate-180" />
            <span>Kembali ke Panel Laporan</span>
        </a>

        <div class="flex items-center gap-3">
            <span class="text-xs text-[#84968C] hidden sm:inline">Gunakan layout Landscape / Portrait sesuai kebutuhan</span>
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-[#15573F] text-white hover:bg-[#104430] shadow-xs cursor-pointer transition-all"
            >
                <x-icon name="printer" class="w-4 h-4" />
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    {{-- HALAMAN UTAMA DOKUMEN CETAK --}}
    <div class="print-page max-w-5xl mx-auto bg-white border border-[#E3E8E5] rounded-2xl shadow-sm p-8 sm:p-12">
        {{-- KOP SURAT RESMI --}}
        <div class="border-b-2 border-[#13251C] pb-4 mb-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-[#15573F] text-white flex items-center justify-center font-extrabold text-xl tracking-wider">
                        SK
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-[#13251C] uppercase font-serif">
                            BENGKEL SERVIS KOMPUTER & LAPTOP
                        </h1>
                        <p class="text-xs text-[#576B61] mt-0.5">
                            Pusat Reparasi Hardware, Penggantian Komponen, Perawatan & Software Komputer
                        </p>
                        <p class="text-[11px] text-[#84968C]">
                            Jl. Raya Sokaraja No. 88, Banyumas &bull; Telp: (0281) 688990 &bull; Email: info@serviskomputer.com
                        </p>
                    </div>
                </div>

                <div class="text-right text-[11px] text-[#576B61]">
                    <span class="font-bold block text-[#13251C]">DOKUMEN RESMI</span>
                    <span>No. Dok: LAP/{{ now()->format('Ym/d') }}</span>
                    <span class="block">Tgl Cetak: {{ $tanggalCetak }}</span>
                </div>
            </div>
        </div>

        {{-- JUDUL DOKUMEN --}}
        <div class="text-center mb-6">
            <h2 class="text-base font-bold text-[#13251C] uppercase tracking-wide">
                LAPORAN REKAPITULASI PELAYANAN SERVIS & PENDAPATAN
            </h2>
            <div class="inline-flex items-center gap-2 mt-1 text-xs text-[#576B61] bg-[#F6F8F7] px-3 py-1 rounded-full border border-[#E3E8E5]">
                <span>Periode:</span>
                <strong class="text-[#13251C]">
                    @if (!empty($filters['tgl_awal']) && !empty($filters['tgl_akhir']))
                        {{ \Carbon\Carbon::parse($filters['tgl_awal'])->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($filters['tgl_akhir'])->translatedFormat('d M Y') }}
                    @elseif (!empty($filters['tgl_awal']))
                        Mulai {{ \Carbon\Carbon::parse($filters['tgl_awal'])->translatedFormat('d M Y') }}
                    @elseif (!empty($filters['tgl_akhir']))
                        Sampai {{ \Carbon\Carbon::parse($filters['tgl_akhir'])->translatedFormat('d M Y') }}
                    @else
                        Seluruh Periode Operasional
                    @endif
                </strong>
                @if (!empty($filters['status']))
                    <span>&bull; Status: <strong>{{ ucfirst($filters['status']) }}</strong></span>
                @endif
            </div>
        </div>

        {{-- RINGKASAN METRIK DALAM DOKUMEN --}}
        <div class="grid grid-cols-4 gap-3 mb-6 text-xs">
            <div class="p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5]">
                <span class="text-[#576B61] block text-[10px] uppercase font-bold">Total Tiket Servis</span>
                <span class="text-base font-extrabold text-[#13251C] mt-0.5 block">
                    {{ $summary['total_servis'] }} Perangkat
                </span>
            </div>

            <div class="p-3 bg-[#EDF7F2] rounded-xl border border-[#B2DEC6]">
                <span class="text-[#15573F] block text-[10px] uppercase font-bold">Total Pendapatan Lunas</span>
                <span class="text-base font-extrabold text-[#15573F] mt-0.5 block">
                    Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}
                </span>
            </div>

            <div class="p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5]">
                <span class="text-[#576B61] block text-[10px] uppercase font-bold">Pendapatan Jasa</span>
                <span class="text-base font-extrabold text-[#13251C] mt-0.5 block">
                    Rp {{ number_format($summary['total_jasa'], 0, ',', '.') }}
                </span>
            </div>

            <div class="p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5]">
                <span class="text-[#576B61] block text-[10px] uppercase font-bold">Servis Tuntas</span>
                <span class="text-base font-extrabold text-[#13251C] mt-0.5 block">
                    {{ $summary['selesai_count'] }} Unit ({{ $summary['proses_count'] }} Proses)
                </span>
            </div>
        </div>

        {{-- TABEL DATA SELURUHNYA --}}
        <div class="border border-[#E3E8E5] rounded-xl overflow-hidden mb-8">
            <table class="w-full text-left text-xs text-[#13251C]">
                <thead class="bg-[#F6F8F7] text-[#576B61] uppercase tracking-wider font-bold border-b border-[#E3E8E5]">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">No</th>
                        <th class="py-2.5 px-3">No. Servis</th>
                        <th class="py-2.5 px-3">Tgl Masuk</th>
                        <th class="py-2.5 px-3">Pelanggan</th>
                        <th class="py-2.5 px-3">Perangkat</th>
                        <th class="py-2.5 px-3">Teknisi</th>
                        <th class="py-2.5 px-3 text-center">Status</th>
                        <th class="py-2.5 px-3 text-right">Jasa</th>
                        <th class="py-2.5 px-3 text-right">Sparepart</th>
                        <th class="py-2.5 px-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E3E8E5]">
                    @forelse ($servisList as $index => $row)
                        @php
                            $subpart = $row->detail->sum('subtotal');
                        @endphp
                        <tr>
                            <td class="py-2 px-3 text-center text-[#84968C]">{{ $index + 1 }}</td>
                            <td class="py-2 px-3 font-mono font-bold text-[#15573F]">{{ $row->no_servis }}</td>
                            <td class="py-2 px-3 whitespace-nowrap text-[#576B61]">
                                {{ $row->tgl_masuk ? $row->tgl_masuk->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-2 px-3">
                                <div class="font-semibold">{{ $row->pelanggan?->nama ?? '-' }}</div>
                                <div class="text-[10px] text-[#84968C]">{{ $row->pelanggan?->no_telepon ?? '-' }}</div>
                            </td>
                            <td class="py-2 px-3">
                                <div>{{ $row->merk_tipe }}</div>
                            </td>
                            <td class="py-2 px-3 text-[#576B61]">
                                {{ $row->teknisi?->nama ?? '-' }}
                            </td>
                            <td class="py-2 px-3 text-center whitespace-nowrap">
                                <x-status-badge :status="$row->status" class="!text-[10px] !py-0.5 !px-2" />
                            </td>
                            <td class="py-2 px-3 text-right font-mono">
                                Rp {{ number_format($row->biaya_jasa, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 text-right font-mono text-[#576B61]">
                                Rp {{ number_format($subpart, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-[#15573F]">
                                Rp {{ number_format($row->total_biaya, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-6 text-center text-[#84968C] italic">
                                Tidak ada data servis yang memenuhi kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($servisList->count() > 0)
                    @php
                        $accumulatedJasa = $servisList->sum('biaya_jasa');
                        $accumulatedTotal = $servisList->sum('total_biaya');
                        $accumulatedPart = $accumulatedTotal - $accumulatedJasa;
                    @endphp
                    <tfoot class="bg-[#F6F8F7] border-t-2 border-[#13251C] font-bold text-xs">
                        <tr>
                            <td colspan="7" class="py-2.5 px-3 text-right uppercase">Akumulasi Total:</td>
                            <td class="py-2.5 px-3 text-right font-mono">Rp {{ number_format($accumulatedJasa, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono text-[#576B61]">Rp {{ number_format($accumulatedPart, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono text-[#15573F]">Rp {{ number_format($accumulatedTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- KOLOM PENGESAHAN / TANDA TANGAN --}}
        <div class="flex justify-between items-end pt-4 text-xs">
            <div class="text-[11px] text-[#84968C] max-w-sm leading-relaxed">
                <p><strong>Catatan:</strong></p>
                <p>Dokumen ini merupakan rekapitulasi data resmi dari sistem komputerisasi servis. Seluruh data pendapatan di atas sah dan tercatat pada basis data pusat bengkel.</p>
            </div>

            <div class="text-center w-56">
                <p class="text-[#576B61] mb-1">Banyumas, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-[#13251C] mb-16">Mengetahui,<br>Administrator Bengkel</p>
                <p class="font-bold text-[#13251C] underline uppercase">{{ auth()->user()->nama }}</p>
                <p class="text-[11px] text-[#84968C]">ID Petugas: #{{ auth()->user()->id_user }}</p>
            </div>
        </div>
    </div>

</body>
</html>
