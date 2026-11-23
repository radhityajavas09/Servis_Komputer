<x-app-layout title="Workbench Pengerjaan {{ $servis->no_servis }}" breadcrumb="Daftar Servis > {{ $servis->no_servis }}">
    {{-- HEADER HALAMAN --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <x-eyebrow>WORKBENCH TEKNISI</x-eyebrow>
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
            <x-button href="{{ route('teknisi.servis.index') }}" variant="secondary" size="sm" icon="arrow-left">
                Kembali ke Daftar
            </x-button>
        </div>
    </div>

    @php
        $isLocked = in_array($servis->status->value, ['selesai', 'diambil'], true);
        $totalSparepart = $servis->detail->sum('subtotal');
    @endphp

    {{-- STEPPER ALUR PENGERJAAN SERVIS --}}
    <x-card class="p-5 mb-6">
        <div class="flex items-center justify-between gap-2 pb-3 mb-4 border-b border-[#F0F4F2]">
            <span class="text-xs font-bold uppercase tracking-wider text-[#15573F]">
                Alur Pengerjaan & Perkembangan Status
            </span>
            <span class="text-xs font-medium text-[#576B61]">
                Status Saat Ini: <strong class="text-[#13251C]">{{ $servis->status->label() }}</strong>
            </span>
        </div>

        @php
            $steps = [
                ['key' => 'diterima', 'label' => '1. Diterima', 'desc' => 'Antrean awal'],
                ['key' => 'diperiksa', 'label' => '2. Diperiksa', 'desc' => 'Analisis & diagnosis'],
                ['key' => 'diperbaiki', 'label' => '3. Diperbaiki', 'desc' => 'Tindakan & suku cadang'],
                ['key' => 'selesai', 'label' => '4. Selesai', 'desc' => 'Siap diserahkan'],
            ];

            $order = [
                'diterima' => 1,
                'diperiksa' => 2,
                'diperbaiki' => 3,
                'selesai' => 4,
                'diambil' => 5,
            ];

            $currentStepNum = $order[$servis->status->value] ?? 1;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ($steps as $index => $step)
                @php
                    $stepNum = $index + 1;
                    $isPassed = $currentStepNum > $stepNum;
                    $isCurrent = $currentStepNum === $stepNum;
                @endphp
                <div class="p-3 rounded-xl border {{ $isCurrent ? 'bg-[#E8F3EE] border-[#1F6B4B] text-[#13251C]' : ($isPassed ? 'bg-[#F6F8F7] border-[#CCD5D0] text-[#576B61]' : 'bg-white border-[#E3E8E5] text-[#84968C]') }}">
                    <div class="flex items-center gap-2 mb-1">
                        @if ($isPassed)
                            <div class="w-5 h-5 rounded-full bg-[#1F6B4B] text-white flex items-center justify-center">
                                <x-icon name="check" class="w-3.5 h-3.5" />
                            </div>
                        @elseif ($isCurrent)
                            <div class="w-5 h-5 rounded-full bg-[#1F6B4B] text-white flex items-center justify-center text-xs font-bold font-mono">
                                {{ $stepNum }}
                            </div>
                        @else
                            <div class="w-5 h-5 rounded-full bg-[#E3E8E5] text-[#84968C] flex items-center justify-center text-xs font-bold font-mono">
                                {{ $stepNum }}
                            </div>
                        @endif
                        <span class="text-xs font-bold {{ $isCurrent ? 'text-[#1F6B4B]' : ($isPassed ? 'text-[#13251C]' : 'text-[#84968C]') }}">
                            {{ $step['label'] }}
                        </span>
                    </div>
                    <p class="text-[11px] {{ $isCurrent ? 'text-[#15573F] font-medium' : 'text-[#84968C]' }} pl-7">
                        {{ $step['desc'] }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- AKSI MEMALUKAN STATUS BERIKUTNYA --}}
        <div class="mt-5 pt-4 border-t border-[#F0F4F2] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            @if ($servis->status === \App\Enums\StatusServis::Diterima)
                <div class="text-xs text-[#576B61]">
                    Perangkat berada dalam antrean masuk. Klik tombol untuk memulai tahapan pemeriksaan teknisi.
                </div>
                <form method="POST" action="{{ route('teknisi.servis.update-status', $servis) }}">
                    @csrf
                    <x-button type="submit" variant="primary" size="md" icon="wrench">
                        Mulai Pemeriksaan (Diperiksa)
                    </x-button>
                </form>
            @elseif ($servis->status === \App\Enums\StatusServis::Diperiksa)
                <div class="text-xs text-[#576B61]">
                    Pemeriksaan kerusakan sedang berjalan. Simpan diagnosis lalu lanjutkan ke tahapan perbaikan.
                </div>
                <form method="POST" action="{{ route('teknisi.servis.update-status', $servis) }}">
                    @csrf
                    <x-button type="submit" variant="primary" size="md" icon="wrench">
                        Mulai Perbaikan (Diperbaiki)
                    </x-button>
                </form>
            @elseif ($servis->status === \App\Enums\StatusServis::Diperbaiki)
                <div class="text-xs text-[#576B61]">
                    Pastikan diagnosis dan rincian tindakan sudah tersimpan lengkap sebelum menyelesaikan servis.
                </div>
                <form method="POST" action="{{ route('teknisi.servis.update-status', $servis) }}">
                    @csrf
                    <x-button type="submit" variant="primary" size="md" icon="check-circle">
                        Tandai Selesai Diservis
                    </x-button>
                </form>
            @elseif ($servis->status === \App\Enums\StatusServis::Selesai)
                <div class="flex items-center gap-2 text-xs font-medium text-emerald-800 bg-emerald-50 px-3.5 py-2.5 rounded-xl border border-emerald-200 w-full">
                    <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                    <span>Pengerjaan selesai pada <strong>{{ $servis->tgl_selesai?->translatedFormat('d F Y') }}</strong>. Data terkunci dan perangkat siap diserahkan oleh Petugas.</span>
                </div>
            @elseif ($servis->status === \App\Enums\StatusServis::Diambil)
                <div class="flex items-center gap-2 text-xs font-medium text-blue-800 bg-blue-50 px-3.5 py-2.5 rounded-xl border border-blue-200 w-full">
                    <x-icon name="check-circle" class="w-4 h-4 text-blue-600 shrink-0" />
                    <span>Perangkat telah diambil oleh pelanggan pada <strong>{{ $servis->tgl_diambil?->translatedFormat('d F Y') }}</strong>. Seluruh proses selesai.</span>
                </div>
            @endif
        </div>
    </x-card>

    {{-- KONTEN UTAMA DUA KOLOM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI (LEBAR: 2 KOLOM) --}}
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
                        <span class="text-[#576B61] block font-bold">Keluhan Kerusakan dari Pelanggan:</span>
                        <p class="text-[#13251C] mt-1 p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] leading-relaxed">
                            {{ $servis->keluhan }}
                        </p>
                    </div>
                </div>
            </x-card>

            @if ($isLocked)
                {{-- MODE READ-ONLY (TERKUNCI KARENA SUDAH SELESAI / DIAMBIL) --}}
                <x-card class="p-5">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#F0F4F2]">
                        <div class="flex items-center gap-2 text-[#15573F] font-bold text-xs uppercase tracking-wider">
                            <x-icon name="tool" class="w-4 h-4" />
                            <span>Hasil Diagnosis & Tindakan Teknisi (Read-Only)</span>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#84968C] bg-[#F6F8F7] px-2.5 py-1 rounded-md border border-[#E3E8E5]">
                            <x-icon name="lock" class="w-3 h-3" />
                            Terkunci
                        </span>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="text-[#576B61] block font-bold">Hasil Diagnosis Kerusakan:</span>
                            <div class="mt-1 p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] text-[#13251C] leading-relaxed whitespace-pre-line">
                                {{ $servis->diagnosis ?: 'Belum ada catatan diagnosis.' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-[#576B61] block font-bold">Tindakan Perbaikan yang Dilakukan:</span>
                            <div class="mt-1 p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] text-[#13251C] leading-relaxed whitespace-pre-line">
                                {{ $servis->tindakan ?: 'Belum ada catatan tindakan perbaikan.' }}
                            </div>
                        </div>
                    </div>
                </x-card>

                {{-- RINCIAN BIAYA & SPAREPART READ-ONLY --}}
                <x-card class="p-5">
                    <div class="flex items-center gap-2 pb-3 mb-4 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                        <x-icon name="wrench" class="w-4 h-4" />
                        <span>Suku Cadang yang Digunakan</span>
                    </div>

                    @if ($servis->detail->count() > 0)
                        <div class="border border-[#E3E8E5] rounded-xl overflow-hidden mb-4">
                            <table class="w-full text-left text-xs text-[#13251C]">
                                <thead class="bg-[#F6F8F7] font-bold text-[#576B61] border-b border-[#E3E8E5]">
                                    <tr>
                                        <th class="py-2.5 px-3">Nama Sparepart</th>
                                        <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                        <th class="py-2.5 px-3 text-center">Jumlah</th>
                                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E3E8E5]">
                                    @foreach ($servis->detail as $item)
                                        <tr>
                                            <td class="py-2.5 px-3 font-medium">{{ $item->sparepart?->nama_sparepart ?? 'Item #' . $item->id_sparepart }}</td>
                                            <td class="py-2.5 px-3 text-right">Rp {{ number_format($item->sparepart?->harga ?? 0, 0, ',', '.') }}</td>
                                            <td class="py-2.5 px-3 text-center">{{ $item->jumlah }}</td>
                                            <td class="py-2.5 px-3 text-right font-semibold text-[#15573F]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-xs text-[#84968C] italic mb-4">Tidak ada suku cadang/sparepart yang digunakan.</p>
                    @endif

                    <div class="pt-3 border-t border-[#F0F4F2] space-y-2 text-xs">
                        <div class="flex justify-between items-center text-[#576B61]">
                            <span>Biaya Jasa Teknisi:</span>
                            <span class="font-semibold text-[#13251C]">Rp {{ number_format($servis->biaya_jasa, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[#576B61]">
                            <span>Total Suku Cadang:</span>
                            <span class="font-semibold text-[#13251C]">Rp {{ number_format($totalSparepart, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-2 border-t border-[#E3E8E5] flex justify-between items-center text-sm">
                            <span class="font-bold text-[#13251C]">Total Biaya:</span>
                            <span class="text-lg font-extrabold text-[#15573F]">Rp {{ number_format($servis->total_biaya, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </x-card>
            @else
                @php
                    $partsJson = $availableSpareparts->map(fn($p) => [
                        'id_sparepart' => $p->id_sparepart,
                        'nama_sparepart' => $p->nama_sparepart,
                        'harga' => (int) $p->harga,
                        'stok' => (int) $p->stok,
                    ])->toJson();

                    $initialDetailsJson = $servis->detail->map(fn($d) => [
                        'id_sparepart' => $d->id_sparepart,
                        'jumlah' => (int) $d->jumlah,
                    ])->toJson();
                @endphp

                {{-- MODE EDIT / INPUT DIAGNOSIS & SPAREPART (ALPINE.JS FORM) --}}
                <div
                    x-data="{
                        availableParts: {{ $partsJson }},
                        rows: {{ $initialDetailsJson }},
                        biayaJasa: {{ (int) old('biaya_jasa', $servis->biaya_jasa) }},
                        addRow() {
                            this.rows.push({ id_sparepart: '', jumlah: 1 });
                        },
                        removeRow(index) {
                            this.rows.splice(index, 1);
                        },
                        calculateRowSubtotal(row) {
                            if (!row.id_sparepart) return 0;
                            const part = this.availableParts.find(p => p.id_sparepart == row.id_sparepart);
                            const qty = parseInt(row.jumlah, 10) || 0;
                            return part ? (part.harga * qty) : 0;
                        },
                        totalSparepart() {
                            return this.rows.reduce((sum, row) => sum + this.calculateRowSubtotal(row), 0);
                        },
                        grandTotal() {
                            const jasa = parseInt(this.biayaJasa, 10) || 0;
                            return this.totalSparepart() + jasa;
                        },
                        formatNumber(val) {
                            const num = parseInt(val, 10) || 0;
                            return new Intl.NumberFormat('id-ID').format(num);
                        }
                    }"
                    class="space-y-6"
                >
                    <form method="POST" action="{{ route('teknisi.servis.update-diagnosis', $servis) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- CARD CATATAN DIAGNOSIS & TINDAKAN --}}
                        <x-card class="p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-[#F0F4F2] text-[#15573F] font-bold text-xs uppercase tracking-wider">
                                <x-icon name="tool" class="w-4 h-4" />
                                <span>Form Catatan Pemeriksaan & Tindakan</span>
                            </div>

                            <x-field
                                label="Hasil Pemeriksaan & Diagnosis Kerusakan"
                                name="diagnosis"
                                :error="$errors->first('diagnosis')"
                                hint="Deskripsikan komponen yang bermasalah atau hasil pengujian perangkat."
                            >
                                <x-textarea
                                    name="diagnosis"
                                    id="diagnosis"
                                    rows="3"
                                    placeholder="Contoh: IC Power short circuit, kipas pendingin macet, pasta prosesor kering..."
                                    :error="$errors->has('diagnosis')"
                                >{{ old('diagnosis', $servis->diagnosis) }}</x-textarea>
                            </x-field>

                            <x-field
                                label="Tindakan Perbaikan yang Dilakukan"
                                name="tindakan"
                                :error="$errors->first('tindakan')"
                                hint="Langkah teknis atau perbaikan yang diambil untuk mengatasi kerusakan."
                            >
                                <x-textarea
                                    name="tindakan"
                                    id="tindakan"
                                    rows="3"
                                    placeholder="Contoh: Reballing IC Power, pembersihan heatsink, penggantian thermal paste, dan running test 3 jam..."
                                    :error="$errors->has('tindakan')"
                                >{{ old('tindakan', $servis->tindakan) }}</x-textarea>
                            </x-field>
                        </x-card>

                        {{-- CARD PEMAKAIAN SPAREPART DINAMIS --}}
                        <x-card class="p-5">
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#F0F4F2]">
                                <div class="flex items-center gap-2 text-[#15573F] font-bold text-xs uppercase tracking-wider">
                                    <x-icon name="wrench" class="w-4 h-4" />
                                    <span>Pemakaian Sparepart / Suku Cadang</span>
                                </div>
                                <button
                                    type="button"
                                    @click="addRow()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#E8F3EE] text-[#1F6B4B] hover:bg-[#1F6B4B] hover:text-white transition-all shadow-2xs"
                                >
                                    <x-icon name="plus" class="w-3.5 h-3.5" />
                                    <span>Tambah Sparepart</span>
                                </button>
                            </div>

                            {{-- LIST BARIS SPAREPART --}}
                            <div class="space-y-3">
                                <template x-for="(row, index) in rows" :key="index">
                                    <div class="p-3 bg-[#F9FBFA] rounded-xl border border-[#E3E8E5] flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                        {{-- Pilih Sparepart --}}
                                        <div class="flex-1">
                                            <label class="block text-[11px] font-semibold text-[#576B61] mb-1 sm:hidden">Pilih Suku Cadang</label>
                                            <select
                                                :name="'spareparts[' + index + '][id_sparepart]'"
                                                x-model.number="row.id_sparepart"
                                                required
                                                class="block w-full rounded-xl border border-[#E3E8E5] bg-white px-3 py-2 text-xs text-[#13251C] focus:border-[#15573F] focus:outline-none focus:ring-2 focus:ring-[#15573F]/20"
                                            >
                                                <option value="">-- Pilih Sparepart --</option>
                                                <template x-for="part in availableParts" :key="part.id_sparepart">
                                                    <option
                                                        :value="part.id_sparepart"
                                                        :selected="part.id_sparepart == row.id_sparepart"
                                                        x-text="part.nama_sparepart + ' (Rp ' + formatNumber(part.harga) + ' | Stok: ' + part.stok + ')'"
                                                    ></option>
                                                </template>
                                            </select>
                                        </div>

                                        {{-- Jumlah Qty --}}
                                        <div class="w-full sm:w-28">
                                            <label class="block text-[11px] font-semibold text-[#576B61] mb-1 sm:hidden">Jumlah (Qty)</label>
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    :name="'spareparts[' + index + '][jumlah]'"
                                                    x-model.number="row.jumlah"
                                                    min="1"
                                                    required
                                                    class="block w-full rounded-xl border border-[#E3E8E5] bg-white px-3 py-2 text-xs text-center text-[#13251C] font-semibold focus:border-[#15573F] focus:outline-none focus:ring-2 focus:ring-[#15573F]/20"
                                                    placeholder="Qty"
                                                />
                                            </div>
                                        </div>

                                        {{-- Subtotal --}}
                                        <div class="w-full sm:w-36 text-right sm:text-right">
                                            <span class="text-[11px] text-[#576B61] block sm:hidden">Subtotal:</span>
                                            <div class="text-xs font-bold text-[#15573F] mt-0.5 sm:mt-0 font-mono">
                                                Rp <span x-text="formatNumber(calculateRowSubtotal(row))"></span>
                                            </div>
                                        </div>

                                        {{-- Tombol Hapus Baris --}}
                                        <div class="text-right sm:text-center">
                                            <button
                                                type="button"
                                                @click="removeRow(index)"
                                                class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Hapus Baris"
                                            >
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- JIKA KOSONG --}}
                                <div x-show="rows.length === 0" class="p-6 text-center bg-[#F6F8F7] rounded-xl border border-dashed border-[#CCD5D0]">
                                    <x-icon name="wrench" class="w-6 h-6 mx-auto text-[#84968C] mb-1.5" />
                                    <p class="text-xs text-[#576B61]">Tidak ada pemakaian sparepart tambahan.</p>
                                    <button
                                        type="button"
                                        @click="addRow()"
                                        class="mt-2 text-xs text-[#1F6B4B] font-bold hover:underline"
                                    >
                                        + Tambah baris sparepart baru
                                    </button>
                                </div>
                            </div>

                            {{-- BIAYA JASA & LIVE SUMMARY --}}
                            <div class="mt-6 pt-5 border-t border-[#F0F4F2] space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                                    <div>
                                        <x-field
                                            label="Biaya Jasa Teknisi (Rp)"
                                            name="biaya_jasa"
                                            :error="$errors->first('biaya_jasa')"
                                            hint="Isi 0 jika tidak ada biaya jasa."
                                            required
                                        >
                                            <x-input
                                                type="number"
                                                name="biaya_jasa"
                                                id="biaya_jasa"
                                                x-model.number="biayaJasa"
                                                min="0"
                                                step="1000"
                                                required
                                                :error="$errors->has('biaya_jasa')"
                                            />
                                        </x-field>
                                    </div>

                                    {{-- KOTAK KALKULASI TOTAL BIAYA --}}
                                    <div class="bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] p-3.5 space-y-1.5 text-xs">
                                        <div class="flex justify-between text-[#576B61]">
                                            <span>Subtotal Sparepart:</span>
                                            <span class="font-semibold text-[#13251C]">
                                                Rp <span x-text="formatNumber(totalSparepart())"></span>
                                            </span>
                                        </div>
                                        <div class="flex justify-between text-[#576B61]">
                                            <span>Biaya Jasa Teknisi:</span>
                                            <span class="font-semibold text-[#13251C]">
                                                Rp <span x-text="formatNumber(biayaJasa)"></span>
                                            </span>
                                        </div>
                                        <div class="pt-2 border-t border-[#E3E8E5] flex justify-between items-center text-sm">
                                            <span class="font-bold text-[#13251C]">Estimasi Total Biaya:</span>
                                            <span class="text-base font-extrabold text-[#15573F]">
                                                Rp <span x-text="formatNumber(grandTotal())"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-card>

                        {{-- TOMBOL SUBMIT FORM --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button type="submit" variant="primary" size="lg" icon="check">
                                Simpan Diagnosis & Rincian Servis
                            </x-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        {{-- KOLOM KANAN (SIDEBAR: 1 KOLOM) --}}
        <div class="space-y-6">
            {{-- STATUS & JADWAL PENGERJAAN --}}
            <x-card class="p-5 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#15573F] block pb-2 border-b border-[#F0F4F2]">
                    Jadwal & Penanggung Jawab
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
                        <span class="text-[#576B61] block">Tanggal Selesai:</span>
                        <strong class="text-[#13251C]">
                            {{ $servis->tgl_selesai?->translatedFormat('d F Y') ?? 'Dalam proses pengerjaan' }}
                        </strong>
                    </div>

                    <div>
                        <span class="text-[#576B61] block">Tanggal Diambil:</span>
                        <strong class="text-[#13251C]">
                            {{ $servis->tgl_diambil?->translatedFormat('d F Y') ?? 'Belum diambil' }}
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

            {{-- PANDUAN KERJA TEKNISI --}}
            <x-card class="p-5 bg-[#F9FBFA] border-[#E3E8E5] space-y-3 text-xs">
                <span class="font-bold text-[#15573F] uppercase tracking-wider block">
                    Alur Kerja Teknisi
                </span>
                <ol class="list-decimal list-inside space-y-1.5 text-[#576B61] leading-relaxed">
                    <li>Pindahkan status ke <strong>Diperiksa</strong> saat mulai cek fisik & software.</li>
                    <li>Catat hasil analisis kerusakan pada kolom diagnosis.</li>
                    <li>Lanjutkan status ke <strong>Diperbaiki</strong> saat melakukan reparasi & pasang sparepart.</li>
                    <li>Masukkan sparepart yang dipakai & biaya jasa. Stok akan otomatis terpotong saat disimpan.</li>
                    <li>Tekan <strong>Tandai Selesai</strong> jika pengujian perangkat telah normal.</li>
                </ol>
            </x-card>
        </div>
    </div>
</x-app-layout>

