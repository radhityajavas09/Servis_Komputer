<x-app-layout title="Katalog Komponen UI (Lokal)" breadcrumb="Design System & Komponen">
    <!-- PAGE HEADER -->
    <x-page-header
        eyebrow="PANEL DESIGN SYSTEM"
        title="Katalog Komponen & Kerangka UI"
        subtitle="Halaman verifikasi seluruh komponen antarmuka, design token, dan interaktivitas Alpine.js."
    >
        <x-slot:actions>
            <x-button variant="secondary" icon="calendar" @click="$dispatch('show-toast', { type: 'success', message: 'Contoh flash message dari event Alpine!' })">
                Tes Toast Sukses
            </x-button>
            <x-button variant="primary" icon="plus" @click="$dispatch('open-modal-confirm', { name: 'demo-modal', url: '#', message: 'Ini adalah dialog konfirmasi Alpine.js untuk verifikasi modal.' })">
                Buka Modal Konfirmasi
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="space-y-10">
        <!-- SEKSI 1: STAT CARDS (GRID 2 KOLOM MOBILE, 4 KOLOM DESKTOP) -->
        <section>
            <div class="mb-4">
                <x-eyebrow>STATISTIK UTAMA</x-eyebrow>
                <h2 class="text-base font-bold text-[#13251C]">Kartu Statistik (Stat Cards)</h2>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card
                    label="Total Servis"
                    value="07"
                    icon="clipboard"
                    description="Semua pengajuan tercatat"
                    indicator="+12% bln lalu"
                    color="primary"
                />
                <x-stat-card
                    label="Sedang Dikerjakan"
                    value="03"
                    icon="wrench"
                    description="Pemeriksaan & perbaikan"
                    indicator="3 unit aktif"
                    color="blue"
                />
                <x-stat-card
                    label="Baru Diterima"
                    value="01"
                    icon="clock"
                    description="Menunggu teknisi"
                    indicator="Perlu ditinjau"
                    color="amber"
                />
                <x-stat-card
                    label="Servis Selesai"
                    value="02"
                    icon="check-circle"
                    description="Siap diambil pelanggan"
                    indicator="2 bulan ini"
                    color="green"
                />
            </div>
        </section>

        <!-- SEKSI 2: BADGE (STATUS & ROLE) -->
        <section>
            <div class="mb-4">
                <x-eyebrow>INDIKATOR STATUS & PERAN</x-eyebrow>
                <h2 class="text-base font-bold text-[#13251C]">Badge Variasi</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Status Badges -->
                <x-card>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#576B61] mb-3">Status Servis (x-status-badge)</h3>
                    <div class="flex flex-wrap gap-2.5 items-center">
                        <x-status-badge status="diterima" />
                        <x-status-badge status="diperiksa" />
                        <x-status-badge status="diperbaiki" />
                        <x-status-badge status="selesai" />
                        <x-status-badge status="diambil" />
                    </div>
                </x-card>

                <!-- Role Badges -->
                <x-card>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#576B61] mb-3">Peran Pengguna (x-role-badge)</h3>
                    <div class="flex flex-wrap gap-2.5 items-center">
                        <x-role-badge role="admin" />
                        <x-role-badge role="petugas" />
                        <x-role-badge role="teknisi" />
                    </div>
                </x-card>
            </div>
        </section>

        <!-- SEKSI 3: TOMBOL (BUTTON VARIAN & UKURAN) -->
        <section>
            <div class="mb-4">
                <x-eyebrow>KONTROL AKSI</x-eyebrow>
                <h2 class="text-base font-bold text-[#13251C]">Tombol (Button Components)</h2>
            </div>

            <x-card class="space-y-6">
                <!-- Varian -->
                <div>
                    <h4 class="text-xs font-bold text-[#576B61] mb-3">Varian Tombol</h4>
                    <div class="flex flex-wrap gap-3 items-center">
                        <x-button variant="primary" icon="plus">Primary Button</x-button>
                        <x-button variant="secondary" icon="download">Secondary Button</x-button>
                        <x-button variant="danger" icon="trash">Danger Button</x-button>
                        <x-button variant="ghost" icon="eye">Ghost Button</x-button>
                        <x-button variant="primary" :loading="true">Loading State</x-button>
                    </div>
                </div>

                <!-- Ukuran -->
                <div class="pt-4 border-t border-[#F0F4F2]">
                    <h4 class="text-xs font-bold text-[#576B61] mb-3">Ukuran Tombol</h4>
                    <div class="flex flex-wrap gap-3 items-center">
                        <x-button size="sm" icon="pencil">Ukuran Small</x-button>
                        <x-button size="md" icon="pencil">Ukuran Medium</x-button>
                        <x-button size="lg" icon="pencil">Ukuran Large</x-button>
                    </div>
                </div>
            </x-card>
        </section>

        <!-- SEKSI 4: FORM INPUT & FIELD -->
        <section>
            <div class="mb-4">
                <x-eyebrow>FORMULIR & ISIAN</x-eyebrow>
                <h2 class="text-base font-bold text-[#13251C]">Form Controls</h2>
            </div>

            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-field label="Nama Pelanggan" name="demo_nama" required hint="Masukkan nama lengkap pelanggan">
                        <x-input id="demo_nama" placeholder="Contoh: Nadia Putri" value="Nadia Putri" />
                    </x-field>

                    <x-field label="Nomor Telepon (Error State)" name="demo_telp" required error="Nomor telepon wajib diisi dan minimal 8 digit.">
                        <x-input id="demo_telp" placeholder="08xxxxxxxxxx" :error="true" value="081" />
                    </x-field>

                    <x-field label="Jenis Perangkat" name="demo_jenis" required>
                        <x-select id="demo_jenis">
                            <option value="Laptop">Laptop</option>
                            <option value="PC Desktop">PC Desktop</option>
                            <option value="All-in-One">All-in-One</option>
                        </x-select>
                    </x-field>

                    <x-field label="Status Servis" name="demo_status">
                        <x-select id="demo_status">
                            <option value="diterima">Diterima</option>
                            <option value="diperiksa">Dalam pemeriksaan</option>
                            <option value="diperbaiki">Dalam perbaikan</option>
                            <option value="selesai">Selesai</option>
                        </x-select>
                    </x-field>

                    <div class="md:col-span-2">
                        <x-field label="Keluhan Kerusakan" name="demo_keluhan" hint="Deskripsikan gejala kerusakan sedetail mungkin.">
                            <x-textarea id="demo_keluhan" placeholder="Jelaskan kendala perangkat...">Layar berkedip saat digerakkan dan baterai drop cepat.</x-textarea>
                        </x-field>
                    </div>
                </div>
            </x-card>
        </section>

        <!-- SEKSI 5: TABEL RESPONSIF -->
        <section>
            <div class="mb-4">
                <x-eyebrow>DATA & TABEL</x-eyebrow>
                <h2 class="text-base font-bold text-[#13251C]">Tabel Responsif</h2>
            </div>

            <x-table>
                <thead class="bg-[#F6F8F7] text-xs font-bold text-[#576B61] uppercase tracking-wider border-b border-[#E3E8E5]">
                    <tr>
                        <th class="py-3.5 px-4">No. Servis</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Perangkat</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Biaya Total</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0F4F2]">
                    <tr class="hover:bg-[#F6F8F7] transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#15573F]">SRV-001</td>
                        <td class="py-3.5 px-4 font-medium">Nadia Putri</td>
                        <td class="py-3.5 px-4 text-[#576B61]">ASUS VivoBook 14 &bull; X1404ZA</td>
                        <td class="py-3.5 px-4"><x-status-badge status="diperbaiki" /></td>
                        <td class="py-3.5 px-4 text-right font-semibold">Rp 420.000</td>
                        <td class="py-3.5 px-4 text-center">
                            <x-button size="sm" variant="secondary" icon="eye">Detail</x-button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F6F8F7] transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#15573F]">SRV-002</td>
                        <td class="py-3.5 px-4 font-medium">Rizky Maulana</td>
                        <td class="py-3.5 px-4 text-[#576B61]">Lenovo IdeaPad Slim 3 &bull; 15IAH8</td>
                        <td class="py-3.5 px-4"><x-status-badge status="diperiksa" /></td>
                        <td class="py-3.5 px-4 text-right font-semibold">Rp 0</td>
                        <td class="py-3.5 px-4 text-center">
                            <x-button size="sm" variant="secondary" icon="eye">Detail</x-button>
                        </td>
                    </tr>
                </tbody>
            </x-table>
        </section>

        <!-- SEKSI 6: EMPTY STATE -->
        <section>
            <div class="mb-4">
                <x-eyebrow>KEADAAN KOSONG</x-eyebrow>
                <h2 class="text-base font-bold text-[#13251C]">Empty State</h2>
            </div>

            <x-empty-state
                icon="box"
                title="Belum ada riwayat servis"
                description="Pelanggan ini belum memiliki riwayat servis perangkat sebelumnya di sistem."
            >
                <x-button variant="primary" icon="plus" size="sm">
                    Daftarkan Servis Baru
                </x-button>
            </x-empty-state>
        </section>
    </div>

    <!-- MODAL CONFIRMATION DEMO -->
    <x-modal-confirm name="demo-modal" title="Konfirmasi Hapus Data">
        Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan dan data akan hilang dari sistem.
    </x-modal-confirm>
</x-app-layout>
