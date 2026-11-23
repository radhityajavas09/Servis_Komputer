<x-app-layout title="Pendaftaran Servis Baru" breadcrumb="Pendaftaran Servis">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-eyebrow>PENDAFTARAN PERANGKAT</x-eyebrow>
                <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1">Pendaftaran Servis Baru</h1>
                <p class="text-xs text-[#576B61] mt-0.5">Catat identitas pemilik dan keluhan kerusakan perangkat untuk memulai penanganan teknisi.</p>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6]">
                <x-icon name="clipboard" class="w-4 h-4 text-[#15573F]" />
                <span class="text-xs font-semibold">Estimasi No: <strong class="font-mono">{{ $nextNoServis }}</strong></span>
            </div>
        </div>

        @php
            $customersJson = $pelangganList->map(function ($p) {
                return [
                    'id' => $p->id_pelanggan,
                    'nama' => $p->nama,
                    'no_telepon' => $p->no_telepon,
                    'alamat' => $p->alamat,
                ];
            })->toJson();
        @endphp

        <form
            method="POST"
            action="{{ route('petugas.servis.store') }}"
            x-data="{
                customers: {{ $customersJson }},
                selectedId: '',
                nama: '{{ old('nama') }}',
                no_telepon: '{{ old('no_telepon') }}',
                alamat: '{{ old('alamat') }}',
                submitting: false,
                onCustomerSelect() {
                    const found = this.customers.find(c => String(c.id) === String(this.selectedId));
                    if (found) {
                        this.nama = found.nama;
                        this.no_telepon = found.no_telepon;
                        this.alamat = found.alamat;
                    }
                },
                resetCustomer() {
                    this.selectedId = '';
                    this.nama = '';
                    this.no_telepon = '';
                    this.alamat = '';
                }
            }"
            class="space-y-6"
        >
            @csrf

            {{-- SEKSI 1: IDENTITAS PELANGGAN --}}
            <x-card class="p-6">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#F0F4F2]">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#EDF7F2] text-[#15573F] flex items-center justify-center shrink-0">
                            <x-icon name="user" class="w-4 h-4" />
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-[#13251C]">1. Data Pemilik / Pelanggan</h2>
                            <p class="text-[11px] text-[#576B61]">Pilih pelanggan yang sudah terdaftar atau ketik data pelanggan baru.</p>
                        </div>
                    </div>

                    {{-- Tombol Pilih Pelanggan Terdaftar / Reset --}}
                    <div class="w-64">
                        <x-select
                            x-model="selectedId"
                            @change="onCustomerSelect()"
                            class="text-xs py-1.5"
                        >
                            <option value="">-- Pilih Pelanggan Terdaftar --</option>
                            @foreach ($pelangganList as $p)
                                <option value="{{ $p->id_pelanggan }}">
                                    {{ $p->nama }} ({{ $p->no_telepon }})
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Nama Pelanggan --}}
                    <x-field label="Nama Lengkap Pelanggan" name="nama" required :error="$errors->first('nama')">
                        <x-input
                            id="nama"
                            type="text"
                            name="nama"
                            x-model="nama"
                            placeholder="Contoh: Hendra Wijaya"
                            required
                            maxlength="100"
                            :error="$errors->has('nama')"
                        />
                    </x-field>

                    {{-- No Telepon --}}
                    <x-field label="Nomor Telepon / WhatsApp" name="no_telepon" required :error="$errors->first('no_telepon')" hint="Nomor unik untuk pencatatan dan konfirmasi.">
                        <x-input
                            id="no_telepon"
                            type="text"
                            name="no_telepon"
                            x-model="no_telepon"
                            placeholder="Contoh: 081234567890"
                            required
                            maxlength="20"
                            :error="$errors->has('no_telepon')"
                        />
                    </x-field>

                    {{-- Alamat --}}
                    <div class="sm:col-span-2">
                        <x-field label="Alamat Domisili" name="alamat" required :error="$errors->first('alamat')">
                            <x-textarea
                                id="alamat"
                                name="alamat"
                                x-model="alamat"
                                rows="2"
                                placeholder="Alamat tempat tinggal / kantor pelanggan..."
                                required
                                maxlength="500"
                                :error="$errors->has('alamat')"
                            ></x-textarea>
                        </x-field>
                    </div>
                </div>
            </x-card>

            {{-- SEKSI 2: DATA PERANGKAT & KELUHAN --}}
            <x-card class="p-6">
                <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-[#F0F4F2]">
                    <div class="w-8 h-8 rounded-lg bg-[#EDF7F2] text-[#15573F] flex items-center justify-center shrink-0">
                        <x-icon name="package" class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#13251C]">2. Informasi Perangkat & Kerusakan</h2>
                        <p class="text-[11px] text-[#576B61]">Rincian tipe barang yang dititipkan dan rincian masalah dari pelanggan.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Jenis Perangkat --}}
                    <x-field label="Jenis Perangkat" name="jenis_perangkat" required :error="$errors->first('jenis_perangkat')">
                        <x-select id="jenis_perangkat" name="jenis_perangkat" required :error="$errors->has('jenis_perangkat')">
                            <option value="Laptop" {{ old('jenis_perangkat') === 'Laptop' ? 'selected' : '' }}>Laptop / Notebook</option>
                            <option value="PC Desktop" {{ old('jenis_perangkat') === 'PC Desktop' ? 'selected' : '' }}>PC Desktop / CPU</option>
                            <option value="All-in-One PC" {{ old('jenis_perangkat') === 'All-in-One PC' ? 'selected' : '' }}>All-in-One PC (AIO)</option>
                            <option value="Monitor" {{ old('jenis_perangkat') === 'Monitor' ? 'selected' : '' }}>Monitor LCD/LED</option>
                            <option value="Printer" {{ old('jenis_perangkat') === 'Printer' ? 'selected' : '' }}>Printer</option>
                            <option value="Lainnya" {{ old('jenis_perangkat') === 'Lainnya' ? 'selected' : '' }}>Lainnya / Aksesoris</option>
                        </x-select>
                    </x-field>

                    {{-- Merk & Tipe --}}
                    <x-field label="Merk & Tipe Perangkat" name="merk_tipe" required :error="$errors->first('merk_tipe')" hint="Cantumkan merk dan model selengkap mungkin.">
                        <x-input
                            id="merk_tipe"
                            type="text"
                            name="merk_tipe"
                            value="{{ old('merk_tipe') }}"
                            placeholder="Contoh: ASUS VivoBook 14 A416MA"
                            required
                            maxlength="100"
                            :error="$errors->has('merk_tipe')"
                        />
                    </x-field>

                    {{-- Keluhan Kerusakan --}}
                    <div class="sm:col-span-2">
                        <x-field label="Keluhan Kerusakan / Masalah" name="keluhan" required :error="$errors->first('keluhan')" hint="Tuliskan gejala kerusakan, riwayat keluhan, atau pesan error yang muncul.">
                            <x-textarea
                                id="keluhan"
                                name="keluhan"
                                rows="3"
                                placeholder="Contoh: Laptop mati total setelah terkena tumpahan air, lampu indikator power tidak menyala sama sekali..."
                                required
                                maxlength="1000"
                                :error="$errors->has('keluhan')"
                            >{{ old('keluhan') }}</x-textarea>
                        </x-field>
                    </div>
                </div>
            </x-card>

            {{-- SEKSI 3: INFORMASI ADMINISTRASI PENERIMAAN --}}
            <div class="bg-[#EDF7F2] rounded-2xl border border-[#B2DEC6] p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#15573F] block">Informasi Penerimaan Tiket</span>
                    <div class="text-xs text-[#13251C] flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span>Tanggal Masuk: <strong>{{ now()->translatedFormat('d F Y') }}</strong></span>
                        <span>•</span>
                        <span>Petugas Penerima: <strong>{{ auth()->user()->nama }}</strong></span>
                        <span>•</span>
                        <span>Status Awal: <strong class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">Diterima</strong></span>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <x-button href="{{ route('petugas.servis.index') }}" variant="secondary" icon="arrow-left">
                        Kembali
                    </x-button>

                    <x-button type="submit" variant="primary" icon="printer" x-bind:disabled="submitting">
                        Simpan & Cetak Tanda Terima
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
