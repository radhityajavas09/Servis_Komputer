<x-app-layout title="Penyerahan Perangkat - {{ $servis->no_servis }}" breadcrumb="Data Servis > {{ $servis->no_servis }} > Penyerahan">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-eyebrow>PENYERAHAN & PELUNASAN</x-eyebrow>
                <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1">Penyerahan Perangkat Servis</h1>
                <p class="text-xs text-[#576B61] mt-0.5">Konfirmasi pelunasan biaya servis dan penyerahan perangkat {{ $servis->no_servis }} kepada pelanggan.</p>
            </div>

            <x-button href="{{ route('petugas.servis.detail', $servis) }}" variant="secondary" size="sm" icon="arrow-left">
                Kembali
            </x-button>
        </div>

        <form method="POST" action="{{ route('petugas.servis.proses-serahkan', $servis) }}" class="space-y-6" x-data="{ lunas: false, submitting: false }">
            @csrf

            {{-- RINGKASAN DATA SERVIS --}}
            <x-card class="p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#F0F4F2]">
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-extrabold text-sm text-[#15573F] bg-[#EDF7F2] px-3 py-1 rounded-lg border border-[#B2DEC6]">
                            {{ $servis->no_servis }}
                        </span>
                        <span class="text-xs text-[#576B61]">• Status:</span>
                        <x-status-badge :status="$servis->status" />
                    </div>

                    <span class="text-xs text-[#576B61]">
                        Masuk: {{ $servis->tgl_masuk?->translatedFormat('d M Y') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-[#576B61] block">Pemilik Perangkat:</span>
                        <strong class="text-[#13251C] text-sm block mt-0.5">{{ $servis->pelanggan->nama }}</strong>
                        <span class="font-mono text-[#576B61] block mt-0.5">{{ $servis->pelanggan->no_telepon }}</span>
                    </div>

                    <div>
                        <span class="text-[#576B61] block">Perangkat yang Diservis:</span>
                        <strong class="text-[#13251C] text-sm block mt-0.5">{{ $servis->merk_tipe }}</strong>
                        <span class="text-[#576B61] block mt-0.5">{{ $servis->jenis_perangkat }}</span>
                    </div>

                    <div class="sm:col-span-2 pt-2 border-t border-[#F0F4F2]">
                        <span class="text-[#576B61] block">Hasil Pengerjaan Teknisi:</span>
                        <p class="text-[#13251C] mt-1 leading-relaxed">
                            {{ $servis->tindakan ?: ($servis->diagnosis ?: 'Pengerjaan servis telah diselesaikan oleh teknisi.') }}
                        </p>
                    </div>
                </div>
            </x-card>

            {{-- RINCIAN TAGIHAN PEMBAYARAN --}}
            <x-card class="p-6">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#15573F] pb-3 mb-4 border-b border-[#F0F4F2]">
                    Rincian Tagihan Biaya Servis
                </h2>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center text-[#576B61]">
                        <span>Biaya Jasa Servis:</span>
                        <span class="font-semibold text-[#13251C]">
                            Rp {{ number_format($servis->biaya_jasa, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($servis->detail->count() > 0)
                        <div class="pt-2 border-t border-[#F0F4F2]">
                            <span class="text-[#576B61] block mb-2 font-medium">Penggantian Suku Cadang:</span>
                            <div class="space-y-1.5 pl-2 border-l-2 border-[#B2DEC6]">
                                @foreach ($servis->detail as $item)
                                    <div class="flex justify-between text-[#576B61]">
                                        <span>{{ $item->sparepart->nama_sparepart }} ({{ $item->jumlah }}x)</span>
                                        <span class="font-medium text-[#13251C]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pt-3 border-t-2 border-[#15573F] flex justify-between items-center bg-[#EDF7F2] p-4 rounded-xl mt-3">
                        <div>
                            <span class="font-bold text-sm text-[#13251C] block">TOTAL BIAYA PELUNASAN:</span>
                            <span class="text-[11px] text-[#576B61]">Wajib dilunasi pelanggan saat pengambilan</span>
                        </div>
                        <span class="text-2xl font-extrabold text-[#15573F]">
                            Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </x-card>

            {{-- KONFIRMASI CHECKBOX PELUNASAN --}}
            <div class="bg-amber-50 rounded-2xl border-2 border-amber-300 p-5 space-y-4">
                <div class="flex items-start gap-3">
                    <input
                        id="pembayaran_lunas"
                        name="pembayaran_lunas"
                        type="checkbox"
                        value="1"
                        x-model="lunas"
                        required
                        class="mt-1 w-5 h-5 rounded text-[#15573F] border-[#CCD5D0] focus:ring-[#15573F] cursor-pointer"
                    >
                    <label for="pembayaran_lunas" class="text-xs text-amber-950 leading-relaxed cursor-pointer select-none">
                        <strong class="text-amber-900 block font-bold text-sm mb-0.5">
                            Konfirmasi Pembayaran Lunas & Penyerahan Fisik
                        </strong>
                        Saya mengonfirmasi bahwa pembayaran total tagihan sebesar
                        <strong>Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}</strong>
                        telah <strong>DITERIMA & LUNAS</strong> dari pelanggan, dan perangkat telah diserahkan kembali dalam kondisi baik.
                    </label>
                </div>

                @if ($errors->has('pembayaran_lunas'))
                    <p class="text-xs text-red-600 font-semibold pl-8">
                        {{ $errors->first('pembayaran_lunas') }}
                    </p>
                @endif
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex items-center justify-between gap-3 pt-2">
                <x-button href="{{ route('petugas.servis.detail', $servis) }}" variant="secondary" icon="arrow-left">
                    Kembali
                </x-button>

                <x-button
                    type="submit"
                    variant="primary"
                    icon="check-circle"
                    x-bind:disabled="!lunas || submitting"
                    class="disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Konfirmasi & Serahkan Perangkat
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>
