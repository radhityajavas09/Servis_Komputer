<x-app-layout title="Ubah Sparepart" breadcrumb="Data Sparepart > Ubah">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-eyebrow>FORMULIR SUKU CADANG</x-eyebrow>
                <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1">Ubah Data Sparepart</h1>
                <p class="text-xs text-[#576B61] mt-0.5">Perbarui informasi harga atau ketersediaan stok {{ $sparepart->nama_sparepart }}.</p>
            </div>

            <x-button href="{{ route('admin.sparepart.index') }}" variant="secondary" size="sm" icon="chevron-right" class="rotate-180">
                Kembali
            </x-button>
        </div>

        <x-card>
            <form method="POST" action="{{ route('admin.sparepart.update', $sparepart) }}" class="space-y-5" x-data="{ submitting: false }">
                @csrf
                @method('PUT')

                {{-- Nama Sparepart --}}
                <x-field label="Nama Suku Cadang" name="nama_sparepart" required :error="$errors->first('nama_sparepart')" hint="Nama lengkap dan spesifikasi sparepart yang jelas.">
                    <x-input
                        id="nama_sparepart"
                        type="text"
                        name="nama_sparepart"
                        value="{{ old('nama_sparepart', $sparepart->nama_sparepart) }}"
                        required
                        autofocus
                        maxlength="100"
                        :error="$errors->has('nama_sparepart')"
                    />
                </x-field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Harga Satuan --}}
                    <x-field label="Harga Satuan (Rp)" name="harga" required :error="$errors->first('harga')" hint="Nominal harga dalam Rupiah (tanpa titik).">
                        <x-input
                            id="harga"
                            type="number"
                            name="harga"
                            value="{{ old('harga', $sparepart->harga) }}"
                            required
                            min="0"
                            step="500"
                            :error="$errors->has('harga')"
                        />
                    </x-field>

                    {{-- Stok Saat Ini --}}
                    <x-field label="Jumlah Stok Tersedia" name="stok" required :error="$errors->first('stok')" hint="Banyaknya unit fisik yang tersedia di inventaris.">
                        <x-input
                            id="stok"
                            type="number"
                            name="stok"
                            value="{{ old('stok', $sparepart->stok) }}"
                            required
                            min="0"
                            :error="$errors->has('stok')"
                        />
                    </x-field>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-5 border-t border-[#F0F4F2] flex items-center justify-end gap-3">
                    <x-button href="{{ route('admin.sparepart.index') }}" variant="secondary">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" icon="check-circle" x-bind:disabled="submitting">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
