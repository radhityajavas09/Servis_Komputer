<x-app-layout title="Data User" breadcrumb="Data User">
    <x-page-header
        eyebrow="DATA PENGGUNA"
        title="Kelola Data Pengguna"
        subtitle="Manajemen akun Administrator, Petugas Servis, dan Teknisi yang memiliki hak akses sistem."
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" href="{{ route('admin.user.create') }}">
                Tambah User
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- FILTER & PENCARIAN --}}
    <x-card class="p-4 mb-6">
        <form method="GET" action="{{ route('admin.user.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Input Pencarian --}}
                <div class="relative flex-1">
                    <x-input
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari berdasarkan nama atau email..."
                        class="pl-9"
                        @input.debounce.400ms="$el.form.submit()"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#84968C]">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                </div>

                {{-- Filter Peran --}}
                <div class="w-full sm:w-48">
                    <x-select name="role" @change="$el.form.submit()">
                        <option value="">Semua Peran</option>
                        <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="petugas" {{ $selectedRole === 'petugas' ? 'selected' : '' }}>Petugas Servis</option>
                        <option value="teknisi" {{ $selectedRole === 'teknisi' ? 'selected' : '' }}>Teknisi</option>
                    </x-select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <x-button type="submit" variant="secondary" size="md" icon="search">
                    Cari
                </x-button>
                @if ($search || $selectedRole)
                    <x-button href="{{ route('admin.user.index') }}" variant="ghost" size="md">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    {{-- TABEL DATA USER --}}
    @if ($users->count() > 0)
        <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[#13251C]">
                    <thead class="bg-[#F6F8F7] text-xs font-bold text-[#576B61] uppercase tracking-wider border-b border-[#E3E8E5]">
                        <tr>
                            <th scope="col" class="py-3.5 px-4 sm:px-6">Pengguna</th>
                            <th scope="col" class="py-3.5 px-4">Peran</th>
                            <th scope="col" class="py-3.5 px-4 hidden md:table-cell">Tanggal Dibuat</th>
                            <th scope="col" class="py-3.5 px-4 hidden lg:table-cell">Aktivitas Servis</th>
                            <th scope="col" class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F4F2]">
                        @foreach ($users as $user)
                            @php
                                $isSelf = $user->id_user === auth()->id();
                                $hasServiceHistory = ($user->servis_sebagai_petugas_count ?? 0) > 0 || ($user->servis_sebagai_teknisi_count ?? 0) > 0;
                            @endphp
                            <tr class="hover:bg-[#F6F8F7] transition-colors">
                                {{-- Nama & Email --}}
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#EDF7F2] text-[#15573F] font-bold text-xs flex items-center justify-center shrink-0 border border-[#B2DEC6]">
                                            {{ $user->inisial }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-[#13251C] truncate flex items-center gap-1.5">
                                                <span>{{ $user->nama }}</span>
                                                @if ($isSelf)
                                                    <span class="text-[10px] font-semibold px-2 py-0.2 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                        Anda
                                                    </span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-[#576B61] truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Peran --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <x-role-badge :role="$user->role" />
                                </td>

                                {{-- Tanggal Dibuat --}}
                                <td class="py-3.5 px-4 text-xs text-[#576B61] whitespace-nowrap hidden md:table-cell">
                                    {{ $user->created_at?->translatedFormat('d M Y') ?? '-' }}
                                </td>

                                {{-- Aktivitas Servis --}}
                                <td class="py-3.5 px-4 text-xs text-[#576B61] hidden lg:table-cell">
                                    @if ($user->isPetugas())
                                        <span>{{ $user->servis_sebagai_petugas_count }} servis didaftarkan</span>
                                    @elseif ($user->isTeknisi())
                                        <span>{{ $user->servis_sebagai_teknisi_count }} servis ditangani</span>
                                    @else
                                        <span class="text-[#84968C]">&mdash;</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Tombol Ubah --}}
                                        <x-button
                                            href="{{ route('admin.user.edit', $user) }}"
                                            variant="secondary"
                                            size="sm"
                                            icon="pencil"
                                            title="Ubah Data User"
                                        >
                                            <span class="hidden sm:inline">Ubah</span>
                                        </x-button>

                                        {{-- Tombol Hapus --}}
                                        @if ($isSelf)
                                            <span title="Tidak dapat menghapus akun Anda sendiri">
                                                <x-button
                                                    variant="secondary"
                                                    size="sm"
                                                    icon="trash"
                                                    disabled
                                                    class="opacity-40 cursor-not-allowed text-[#84968C]"
                                                >
                                                    <span class="hidden sm:inline">Hapus</span>
                                                </x-button>
                                            </span>
                                        @elseif ($hasServiceHistory)
                                            <span title="User tidak dapat dihapus karena sudah tercatat pada data servis">
                                                <x-button
                                                    variant="secondary"
                                                    size="sm"
                                                    icon="trash"
                                                    @click="$dispatch('open-modal-confirm', {
                                                        name: 'confirm-delete-user',
                                                        url: '{{ route('admin.user.destroy', $user) }}',
                                                        message: 'Hapus user {{ addslashes($user->nama) }}? User ini telah tercatat pada data servis dan sistem akan menolak penghapusan untuk melindungi integritas data.'
                                                    })"
                                                    class="text-red-600 hover:text-red-700 hover:border-red-300"
                                                >
                                                    <span class="hidden sm:inline">Hapus</span>
                                                </x-button>
                                            </span>
                                        @else
                                            <x-button
                                                variant="secondary"
                                                size="sm"
                                                icon="trash"
                                                @click="$dispatch('open-modal-confirm', {
                                                    name: 'confirm-delete-user',
                                                    url: '{{ route('admin.user.destroy', $user) }}',
                                                    message: 'Hapus user {{ addslashes($user->nama) }}? Tindakan ini tidak dapat dibatalkan.'
                                                })"
                                                class="text-red-600 hover:text-red-700 hover:border-red-300"
                                            >
                                                <span class="hidden sm:inline">Hapus</span>
                                            </x-button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <x-pagination :paginator="$users" />
        </div>
    @else
        <x-empty-state
            icon="user"
            title="Tidak ada data user ditemukan"
            description="Tidak ada pengguna yang sesuai dengan kriteria pencarian atau filter peran yang dipilih."
        >
            <x-button href="{{ route('admin.user.create') }}" variant="primary" icon="plus" size="sm">
                Tambah User Baru
            </x-button>
        </x-empty-state>
    @endif

    {{-- MODAL KONFIRMASI HAPUS --}}
    <x-modal-confirm
        name="confirm-delete-user"
        title="Hapus Data Pengguna"
        confirmText="Ya, Hapus User"
        confirmVariant="danger"
    />
</x-app-layout>
