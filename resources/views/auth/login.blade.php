<x-guest-layout title="Masuk ke Akun - Servis Komputer">
    <div class="mb-6">
        <h2 class="text-xl font-bold tracking-tight text-[#13251C]">Selamat Datang Kembali</h2>
        <p class="text-xs text-[#576B61] mt-1">Silakan masukkan email dan password untuk masuk ke sistem pelayanan servis.</p>
    </div>

    {{-- Error Banner di atas form bila login gagal --}}
    @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-start gap-3">
            <x-icon name="alert" class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
            <div class="text-xs">
                <p class="font-bold">Gagal Masuk</p>
                <p class="mt-0.5 text-red-700 leading-relaxed">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ submitting: false, showPassword: false }" @submit="submitting = true">
        @csrf

        {{-- Email --}}
        <x-field label="Alamat Email" name="email" required>
            <x-input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="nama@servis.test"
                required
                autofocus
                autocomplete="username"
                :error="$errors->has('email')"
            />
        </x-field>

        {{-- Password dengan toggle show/hide --}}
        <x-field label="Password" name="password" required>
            <div class="relative">
                <x-input
                    id="password"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    name="password"
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                    :error="$errors->has('password')"
                    class="pr-10"
                />
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#84968C] hover:text-[#13251C] transition-colors cursor-pointer"
                    tabindex="-1"
                    title="Tampilkan / Sembunyikan Password"
                >
                    <x-icon name="eye" class="w-4.5 h-4.5" />
                </button>
            </div>
        </x-field>

        {{-- Ingat Saya --}}
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded-md border-[#CCD5D0] text-[#15573F] shadow-2xs focus:ring-[#15573F]/20 cursor-pointer w-4 h-4"
                >
                <span class="text-xs text-[#576B61]">Ingat saya di perangkat ini</span>
            </label>
        </div>

        {{-- Tombol Masuk --}}
        <div class="pt-2">
            <x-button
                type="submit"
                variant="primary"
                size="md"
                class="w-full"
                x-bind:disabled="submitting"
            >
                <span x-show="!submitting" class="flex items-center gap-2">
                    <span>Masuk ke Sistem</span>
                    <x-icon name="arrow-up-right" class="w-4 h-4" />
                </span>
                <span x-show="submitting" x-cloak class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memverifikasi...</span>
                </span>
            </x-button>
        </div>
    </form>

    {{-- Akses Pelanggan: Cek / Lacak Servis --}}
    <div class="mt-6 pt-5 border-t border-[#F0F4F2] text-center">
        <p class="text-xs text-[#576B61] mb-2.5">Pelanggan ingin cek status servis perangkat Anda?</p>
        <a href="{{ route('lacak.index') }}" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-xs font-semibold bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6] hover:bg-[#deede4] transition-colors shadow-2xs">
            <x-icon name="search" class="w-4 h-4 text-[#15573F]" />
            <span>Lacak Status Servis via Kode Tiket</span>
            <x-icon name="arrow-up-right" class="w-3.5 h-3.5 text-[#15573F]" />
        </a>
    </div>

    {{-- Info Akun Contoh Demo --}}
    <div class="mt-6 pt-5 border-t border-[#F0F4F2] text-xs text-[#576B61] space-y-1.5">
        <p class="font-bold text-[#13251C] text-[11px] uppercase tracking-wider">Akun Demo Default:</p>
        <p class="flex items-center justify-between">
            <span>Admin: <strong class="text-[#13251C]">admin@servis.test</strong></span>
            <span class="font-mono text-[#84968C]">password</span>
        </p>
        <p class="flex items-center justify-between">
            <span>Petugas: <strong class="text-[#13251C]">petugas@servis.test</strong></span>
            <span class="font-mono text-[#84968C]">password</span>
        </p>
        <p class="flex items-center justify-between">
            <span>Teknisi: <strong class="text-[#13251C]">teknisi@servis.test</strong></span>
            <span class="font-mono text-[#84968C]">password</span>
        </p>
    </div>
</x-guest-layout>
