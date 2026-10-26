<x-app-layout title="Ubah Pelanggan" breadcrumb="Data Pelanggan > Ubah">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-eyebrow>FORMULIR PELANGGAN</x-eyebrow>
                <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1">Ubah Data Pelanggan</h1>
                <p class="text-xs text-[#576B61] mt-0.5">Perbarui kontak atau alamat pelanggan {{ $pelanggan->nama }}.</p>
            </div>

            <x-button href="{{ route('petugas.pelanggan.index') }}" variant="secondary" size="sm" icon="chevron-right" class="rotate-180">
                Kembali
            </x-button>
        </div>

        <x-card>
            <form method="POST" action="{{ route('petugas.pelanggan.update', $pelanggan) }}" class="space-y-5" x-data="{ submitting: false }">
                @csrf
                @method('PUT')

                {{-- Nama Lengkap Pelanggan --}}
                <x-field label="Nama Lengkap" name="nama" required :error="$errors->first('nama')" hint="Nama lengkap pemilik perangkat.">
                    <x-input
                        id="nama"
                        type="text"
                        name="nama"
                        value="{{ old('nama', $pelanggan->nama) }}"
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
                        value="{{ old('no_telepon', $pelanggan->no_telepon) }}"
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
                        required
                        maxlength="500"
                        :error="$errors->has('alamat')"
                    >{{ old('alamat', $pelanggan->alamat) }}</x-textarea>
                </x-field>

                {{-- Tombol Aksi --}}
                <div class="pt-5 border-t border-[#F0F4F2] flex items-center justify-end gap-3">
                    <x-button href="{{ route('petugas.pelanggan.index') }}" variant="secondary">
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
