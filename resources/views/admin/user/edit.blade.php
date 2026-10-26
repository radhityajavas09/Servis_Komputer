<x-app-layout title="Ubah User" breadcrumb="Data User > Ubah">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-eyebrow>FORMULIR PENGGUNA</x-eyebrow>
                <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1">Ubah Data User</h1>
                <p class="text-xs text-[#576B61] mt-0.5">Perbarui profil dan wewenang pengguna {{ $user->nama }}.</p>
            </div>

            <x-button href="{{ route('admin.user.index') }}" variant="secondary" size="sm" icon="chevron-right" class="rotate-180">
                Kembali
            </x-button>
        </div>

        <x-card>
            <form method="POST" action="{{ route('admin.user.update', $user) }}" class="space-y-5" x-data="{ submitting: false, showPassword: false }">
                @csrf
                @method('PUT')

                {{-- Nama Lengkap --}}
                <x-field label="Nama Lengkap" name="nama" required :error="$errors->first('nama')" hint="Nama lengkap pengguna untuk tampilan sistem.">
                    <x-input
                        id="nama"
                        type="text"
                        name="nama"
                        value="{{ old('nama', $user->nama) }}"
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
                        value="{{ old('email', $user->email) }}"
                        required
                        :error="$errors->has('email')"
                    />
                </x-field>

                {{-- Peran Pengguna --}}
                @php
                    $currentRole = old('role', $user->role instanceof \App\Enums\RolePengguna ? $user->role->value : (string)$user->role);
                @endphp
                <x-field label="Peran / Hak Akses" name="role" required :error="$errors->first('role')" hint="Menentukan halaman menu dan wewenang yang dapat diakses.">
                    <x-select id="role" name="role" required :error="$errors->has('role')">
                        <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>Administrator (Kelola User & Sparepart, Laporan)</option>
                        <option value="petugas" {{ $currentRole === 'petugas' ? 'selected' : '' }}>Petugas Servis (Pendaftaran, Pelanggan, Penyerahan)</option>
                        <option value="teknisi" {{ $currentRole === 'teknisi' ? 'selected' : '' }}>Teknisi (Diagnosis, Sparepart, Update Status)</option>
                    </x-select>
                </x-field>

                {{-- Bagian Password (Opsional) --}}
                <div class="pt-3 border-t border-[#F0F4F2] space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-[#13251C] uppercase tracking-wider block">Ganti Password (Opsional)</span>
                            <span class="text-[11px] text-[#576B61] block">Kosongkan jika Anda tidak ingin mengubah password akun ini.</span>
                        </div>
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
                        <x-field label="Password Baru" name="password" :error="$errors->first('password')" hint="Minimal 8 karakter jika diisi.">
                            <x-input
                                id="password"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password"
                                placeholder="Kosongkan jika tidak diubah"
                                autocomplete="new-password"
                                :error="$errors->has('password')"
                            />
                        </x-field>

                        <x-field label="Konfirmasi Password Baru" name="password_confirmation" hint="Ulangi password baru jika diisi.">
                            <x-input
                                id="password_confirmation"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password_confirmation"
                                placeholder="Ketik ulang password baru"
                                autocomplete="new-password"
                            />
                        </x-field>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-5 border-t border-[#F0F4F2] flex items-center justify-end gap-3">
                    <x-button href="{{ route('admin.user.index') }}" variant="secondary">
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
