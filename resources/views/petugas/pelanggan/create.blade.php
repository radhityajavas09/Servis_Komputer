<x-app-layout title="Tambah Pelanggan" breadcrumb="Data Pelanggan > Tambah">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-eyebrow>FORMULIR PELANGGAN</x-eyebrow>
                <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1">Tambah Pelanggan Baru</h1>
                <p class="text-xs text-[#576B61] mt-0.5">Daftarkan identitas dan kontak pelanggan servis baru.</p>
            </div>

            <x-button href="{{ route('petugas.pelanggan.index') }}" variant="secondary" size="sm" icon="arrow-left">
                Kembali
            </x-button>
        </div>

        <x-card>
            <form method="POST" action="{{ route('petugas.pelanggan.store') }}" class="space-y-5" x-data="{ submitting: false }">
                @csrf

                {{-- Nama Lengkap Pelanggan --}}
                <x-field label="Nama Lengkap" name="nama" required :error="$errors->first('nama')" hint="Nama lengkap pemilik perangkat.">
                    <x-input
                        id="nama"
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
                        placeholder="Contoh: Budi Prasetyo"
                        required
                        autofocus
                        maxlength="100"
                        :error="$errors->has('nama')"
                    />
                </x-field>

                {{-- Nomor Telepon / WhatsApp --}}
                <x-field label="Nomor Telepon / WhatsApp" name="no_telepon" required :error="$errors->first('no_telepon')" hint="Nomor aktif untuk konfirmasi progres servis dan tanda terima.">
                    <x-input
                        id="no_telepon"
                        type="text"
                        name="no_telepon"
                        value="{{ old('no_telepon') }}"
                        placeholder="Contoh: 081234567890"
                        required
                        maxlength="20"
                        :error="$errors->has('no_telepon')"
                    />
                </x-field>

                {{-- Alamat Domisili --}}
                <x-field label="Alamat Domisili Lengkap" name="alamat" required :error="$errors->first('alamat')" hint="Alamat tempat tinggal atau kantor pelanggan.">
                    <x-textarea
                        id="alamat"
                        name="alamat"
                        rows="3"
                        placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota..."
                        required
                        maxlength="500"
                        :error="$errors->has('alamat')"
                    >{{ old('alamat') }}</x-textarea>
                </x-field>

                {{-- Tombol Aksi --}}
                <div class="pt-5 border-t border-[#F0F4F2] flex items-center justify-between gap-3">
                    <x-button href="{{ route('petugas.pelanggan.index') }}" variant="secondary" icon="arrow-left">
                        Kembali
                    </x-button>
                    <x-button type="submit" variant="primary" icon="check-circle" x-bind:disabled="submitting">
                        Simpan Pelanggan
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
