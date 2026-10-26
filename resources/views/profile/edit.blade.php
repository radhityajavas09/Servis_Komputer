<x-app-layout title="Ubah Profil" breadcrumb="Ubah Profil">
    <x-page-header
        eyebrow="PENGATURAN AKUN"
        title="Ubah Profil Pengguna"
        subtitle="Perbarui data pribadi dan informasi keamanan akun Anda."
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-start">
        {{-- KOLOM UTAMA: FORM PROFIL --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <form method="POST" action="{{ route('profil.update') }}" class="space-y-6" x-data="{ submitting: false, showPassword: false }">
                    @csrf
                    @method('PUT')

                    {{-- Bagian 1: Data Dasar --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-[#F0F4F2]">
                            <x-icon name="user" class="w-4.5 h-4.5 text-[#15573F]" />
                            <h2 class="text-sm font-bold text-[#13251C]">Informasi Pribadi</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-field label="Nama Lengkap" name="nama" required :error="$errors->first('nama')" hint="Maksimal 100 karakter.">
                                <x-input
                                    id="nama"
                                    type="text"
                                    name="nama"
                                    value="{{ old('nama', $user->nama) }}"
                                    required
                                    maxlength="100"
                                    :error="$errors->has('nama')"
                                />
                            </x-field>

                            <x-field label="Alamat Email" name="email" required :error="$errors->first('email')" hint="Digunakan untuk masuk ke sistem.">
                                <x-input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    required
                                    :error="$errors->has('email')"
                                />
                            </x-field>
                        </div>
                    </div>

                    {{-- Bagian 2: Ganti Password --}}
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#F0F4F2]">
                            <div class="flex items-center gap-2">
                                <x-icon name="wrench" class="w-4.5 h-4.5 text-[#15573F]" />
                                <h2 class="text-sm font-bold text-[#13251C]">Ganti Password</h2>
                            </div>
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="text-xs text-[#576B61] hover:text-[#13251C] flex items-center gap-1 cursor-pointer"
                            >
                                <x-icon name="eye" class="w-3.5 h-3.5" />
                                <span x-text="showPassword ? 'Sembunyikan' : 'Lihat Input'"></span>
                            </button>
                        </div>

                        <div class="p-3.5 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5] text-xs text-[#576B61] mb-4">
                            Kosongkan ketiga field di bawah ini jika Anda <strong>tidak ingin</strong> mengubah password saat ini.
                        </div>

                        <div class="space-y-4">
                            <x-field label="Password Saat Ini" name="current_password" :error="$errors->first('current_password')" hint="Wajib diisi jika Anda ingin mengganti password baru.">
                                <x-input
                                    id="current_password"
                                    x-bind:type="showPassword ? 'text' : 'password'"
                                    name="current_password"
                                    placeholder="Masukkan password yang aktif saat ini"
                                    autocomplete="current-password"
                                    :error="$errors->has('current_password')"
                                />
                            </x-field>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-field label="Password Baru" name="password" :error="$errors->first('password')" hint="Minimal 8 karakter.">
                                    <x-input
                                        id="password"
                                        x-bind:type="showPassword ? 'text' : 'password'"
                                        name="password"
                                        placeholder="Minimal 8 karakter"
                                        autocomplete="new-password"
                                        :error="$errors->has('password')"
                                    />
                                </x-field>

                                <x-field label="Konfirmasi Password Baru" name="password_confirmation" hint="Ulangi password baru yang sama.">
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
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-4 border-t border-[#F0F4F2] flex items-center justify-end gap-3">
                        <x-button
                            type="submit"
                            variant="primary"
                            icon="check-circle"
                            x-bind:disabled="submitting"
                        >
                            Simpan Perubahan
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- KOLOM SAMPING: KARTU INFORMASI AKUN --}}
        <div class="space-y-6">
            <x-card class="text-center">
                {{-- Avatar inisial --}}
                <div class="w-16 h-16 rounded-full bg-[#EDF7F2] text-[#15573F] text-xl font-bold flex items-center justify-center mx-auto mb-3 border-2 border-[#B2DEC6] shadow-xs">
                    {{ $user->inisial }}
                </div>

                <h3 class="text-base font-bold text-[#13251C] truncate">{{ $user->nama }}</h3>
                <p class="text-xs text-[#576B61] truncate mt-0.5">{{ $user->email }}</p>

                <div class="mt-3">
                    <x-role-badge :role="$user->role" />
                </div>

                <div class="mt-6 pt-5 border-t border-[#F0F4F2] text-left space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[#576B61]">Bergabung Sejak</span>
                        <span class="font-semibold text-[#13251C]">{{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[#576B61]">Status Akun</span>
                        <span class="inline-flex items-center gap-1.5 text-emerald-700 font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Aktif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[#576B61]">Tingkat Akses</span>
                        <span class="font-semibold text-[#13251C]">{{ $user->role_label }}</span>
                    </div>
                </div>

                <div class="mt-5 p-3 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5] text-[11px] text-[#576B61] text-left">
                    <p class="font-semibold text-[#13251C] mb-1 flex items-center gap-1.5">
                        <x-icon name="alert" class="w-3.5 h-3.5 text-[#15573F]" />
                        <span>Catatan Keamanan</span>
                    </p>
                    Hak akses peran dikelola oleh Administrator dan tidak dapat diubah melalui halaman ini demi menjaga integritas sistem.
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
