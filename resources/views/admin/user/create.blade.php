<x-app-layout title="Tambah User" breadcrumb="Data User > Tambah">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-eyebrow>FORMULIR PENGGUNA</x-eyebrow>
                <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1">Tambah User Baru</h1>
                <p class="text-xs text-[#576B61] mt-0.5">Daftarkan akun pengguna baru dengan hak akses yang sesuai.</p>
            </div>

            <x-button href="{{ route('admin.user.index') }}" variant="secondary" size="sm" icon="arrow-left">
                Kembali
            </x-button>
        </div>

        <x-card>
            <form method="POST" action="{{ route('admin.user.store') }}" class="space-y-5" x-data="{ submitting: false, showPassword: false }">
                @csrf

                {{-- Nama Lengkap --}}
                <x-field label="Nama Lengkap" name="nama" required :error="$errors->first('nama')" hint="Nama lengkap pengguna untuk tampilan sistem.">
                    <x-input
                        id="nama"
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
                        placeholder="Contoh: Ahmad Teknisi"
                        required
                        autofocus
                        maxlength="100"
                        :error="$errors->has('nama')"
                    />
                </x-field>

                {{-- Alamat Email --}}
                <x-field label="Alamat Email" name="email" required :error="$errors->first('email')" hint="Email unik yang digunakan untuk login.">
                    <x-input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@servis.test"
                        required
                        :error="$errors->has('email')"
                    />
                </x-field>

                {{-- Peran Pengguna --}}
                <x-field label="Peran / Hak Akses" name="role" required :error="$errors->first('role')" hint="Menentukan halaman menu dan wewenang yang dapat diakses.">
                    <x-select id="role" name="role" required :error="$errors->has('role')">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih Peran Pengguna...</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Kelola User & Sparepart, Laporan)</option>
                        <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas Servis (Pendaftaran, Pelanggan, Penyerahan)</option>
                        <option value="teknisi" {{ old('role') === 'teknisi' ? 'selected' : '' }}>Teknisi (Diagnosis, Sparepart, Update Status)</option>
                    </x-select>
                </x-field>

                {{-- Bagian Password --}}
                <div class="pt-3 border-t border-[#F0F4F2] space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#13251C] uppercase tracking-wider">Kredensial Kata Sandi</span>
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="text-xs text-[#576B61] hover:text-[#13251C] flex items-center gap-1 cursor-pointer"
                        >
                            <x-icon name="eye" class="w-3.5 h-3.5" />
                            <span x-text="showPassword ? 'Sembunyikan' : 'Lihat Password'"></span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-field label="Password" name="password" required :error="$errors->first('password')" hint="Minimal 8 karakter.">
                            <x-input
                                id="password"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password"
                                placeholder="Minimal 8 karakter"
                                required
                                autocomplete="new-password"
                                :error="$errors->has('password')"
                            />
                        </x-field>

                        <x-field label="Konfirmasi Password" name="password_confirmation" required hint="Masukkan ulang password yang sama untuk konfirmasi.">
                            <x-input
                                id="password_confirmation"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password_confirmation"
                                placeholder="Ketik ulang password"
                                required
                                autocomplete="new-password"
                            />
                        </x-field>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-5 border-t border-[#F0F4F2] flex items-center justify-between gap-3">
                    <x-button href="{{ route('admin.user.index') }}" variant="secondary" icon="arrow-left">
                        Kembali
                    </x-button>
                    <x-button type="submit" variant="primary" icon="check-circle" x-bind:disabled="submitting">
                        Simpan User
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
