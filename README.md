# Aplikasi Servis Komputer & Laptop

Sistem informasi manajemen pelayanan servis komputer dan laptop berbasis web yang modern, cepat, dan aman. Aplikasi ini mencakup pencatatan pelanggan, pendaftaran perangkat masuk, tanda terima cetak, hasil pemeriksaan kerusakan (diagnosis), pemakaian suku cadang dinamis dengan manajemen stok real-time, transisi status pengerjaan bertahap, penyerahan perangkat lunas, hingga dashboard analitik dan laporan bergrafik interaktif untuk Administrator.

---

## Daftar Isi
- [Fitur Utama Berdasarkan Peran](#fitur-utama-berdasarkan-peran)
- [Teknologi & Arsitektur](#teknologi--arsitektur)
- [Desain & Tema Visual (Nordic Pine)](#desain--tema-visual-nordic-pine)
- [Alur Status Servis](#alur-status-servis)
- [Panduan Instalasi Lokal](#panduan-instalasi-lokal)
- [Akun Demo Bawaan](#akun-demo-bawaan)
- [Pengujian Otomatis (Automated Tests)](#pengujian-otomatis-automated-tests)
- [Struktur Direktori](#struktur-direktori)
- [Lisensi](#lisensi)

---

## Fitur Utama Berdasarkan Peran

### 1. Administrator (`admin`)
- **Dashboard Eksekutif & Analitik**:
  - 4 kartu metrik utama (*Total Servis*, *Baru Diterima*, *Dalam Pengerjaan*, *Selesai & Diambil*).
  - Indikator finansial real-time (*Total Penerimaan Lunas / Omzet*, *Pelanggan Terdaftar*, *Peringatan Stok Suku Cadang Menipis*).
  - Feed aktivitas servis terkini dengan eager loading dan tautan langsung ke detail pengawasan.
  - Widget rekapitulasi tim aktif (Admin, Petugas, Teknisi).
- **Manajemen Pengguna (User Management)**:
  - CRUD Akun pengguna dengan validasi email unik, enkripsi password, dan penetapan peran.
  - Aturan keamanan: Larangan menghapus akun sendiri dan larangan menghapus/menurunkan Administrator terakhir.
- **Manajemen Suku Cadang (Sparepart Inventory)**:
  - CRUD Katalog sparepart (nama, harga jual, kuantitas stok).
  - Indikator visual stok real-time: Normal (Hijau), Menipis $\le 3$ (Kuning), Habis $= 0$ (Merah).
  - Proteksi integritas relasi database (`RESTRICT` foreign key dengan pesan error ramah).
- **Laporan Servis & Analitik Grafik (Chart.js via NPM)**:
  - Filter multi-kriteria: rentang tanggal (*tgl_awal* s/d *tgl_akhir*), status pengerjaan, teknisi penanggung jawab, dan pencarian kata kunci.
  - Grafik 1: *Line Chart* tren volume servis masuk vs selesai (6 bulan terakhir).
  - Grafik 2: *Doughnut Chart* proporsi distribusi status servis aktif.
  - Grafik 3: *Bar Chart* 5 suku cadang paling banyak digunakan.
  - **Cetak Dokumen Resmi (Siap Cetak A4/A5 / Simpan PDF)**: Kop surat resmi bengkel, nomor dokumen, tabel rincian komprehensif, akumulasi total biaya jasa & suku cadang, dan kolom pengesahan tanda tangan Administrator.
- **Pengawasan Detail Servis (Read-Only)**: Akses audit tiket servis tanpa risiko salah edit.

### 2. Petugas Servis (`petugas`)
- **Dashboard Pelayanan**: Antrean penyerahan perangkat siap diambil, registrasi tiket terbaru, dan metrik operasional harian.
- **Manajemen Pelanggan**:
  - Pencatatan identitas pelanggan (nama, nomor telepon, alamat).
  - Tautan langsung WhatsApp (`https://wa.me/...`) untuk komunikasi cepat.
  - Riwayat lengkap servis per pelanggan.
- **Pendaftaran Servis Baru**:
  - Pencarian nomor telepon otomatis (*Customer Auto-Lookup* via Alpine.js) untuk mencegah duplikasi data pelanggan.
  - Pembuatan nomor servis terstruktur (`SRV-001`, `SRV-002`, `SRV-1000`) dengan database transaction and lock (`lockForUpdate()`) anti race-condition.
  - Cetak **Tanda Terima Servis Resmi** siap cetak: nomor tiket, data perangkat, keluhan, instruksi pengambilan, dan kolom tanda tangan dua pihak (Petugas & Pelanggan).
- **Data Servis & Penyerahan Perangkat**:
  - Daftar seluruh tiket servis dengan filter status dan pencarian.
  - Penyerahan perangkat tuntas: Validasi wajib berstatus *Selesai*, verifikasi centang pelunasan pembayaran, pencatatan otomatis `tgl_diambil` hari ini, dan update status menjadi *Diambil*.

### 3. Teknisi Perbaikan (`teknisi`)
- **Dashboard Workbench**: Monitor pengerjaan aktif khusus milik teknisi yang sedang login, antrean tiket baru, dan riwayat perbaikan tuntas.
- **Daftar Servis Masuk**: Pencarian dan filter antrean servis bengkel.
- **Workbench Pengerjaan, Diagnosis & Suku Cadang**:
  - **Stepper Progres Visual**: 4 tahapan berurutan (*Diterima* $\rightarrow$ *Diperiksa* $\rightarrow$ *Diperbaiki* $\rightarrow$ *Selesai*).
  - **Transisi Status Aman**: Hanya boleh maju satu langkah bertahap.
  - **Form Diagnosis & Tindakan**: Catatan teknis hasil pemeriksaan komponen dan tindakan reparasi.
  - **Input Pemakaian Suku Cadang Dinamis (Alpine.js)**: Tambah/hapus baris sparepart dinamis, kalkulasi subtotal dan grand total biaya secara real-time di layar.
  - **Manajemen Stok Transaksional (`PengerjaanServisService`)**:
    - Pengecekan stok dengan `lockForUpdate()`.
    - Pengembalian stok lama secara otomatis sebelum menerapkan pemakaian baru saat teknisi mengedit ulang rincian.
    - Rollback total jika stok tidak mencukupi dengan pesan ramah: *"Stok {nama} tidak cukup (tersisa N)"*.
    - Syarat status *Selesai*: Diagnosis dan tindakan wajib terisi, `tgl_selesai` terisi otomatis hari ini.
    - Penguncian data otomatis: Servis yang telah *Selesai* atau *Diambil* terkunci penuh (*Read-Only*).

---

## Teknologi & Arsitektur

- **Backend**:
  - PHP 8.3+
  - Laravel 12 (tanpa starter kit eksternal, autentikasi kustom berbasis sesi aman)
  - Arsitektur berlapis: Service/Action Classes (`PendaftaranServisService`, `PengerjaanServisService`, `PenyerahanServisService`, `NomorServisGenerator`), FormRequest Validation, Enums (`RolePengguna`, `StatusServis`), dan Thin Controllers.
- **Frontend**:
  - Tailwind CSS 4 (Vite)
  - Alpine.js 3 (Interaktivitas reaktif, live repeater, dan customer autocomplete)
  - Chart.js 4 (Bundled via NPM, bebas ketergantungan CDN eksternal)
  - Lucide SVG Icons (`<x-icon name="..." />`)
  - Tipografi: Plus Jakarta Sans (Google Fonts)
- **Database & Testing**:
  - MySQL 8.4 (Lingkungan lokal Laragon) / SQLite In-Memory (Automated Testing)
  - Pest PHP (108 feature & unit tests)
  - Laravel Pint (Standar format PSR-12 / Laravel Code Style)

---

## Desain & Tema Visual (Nordic Pine)

Antarmuka dibangun dengan panduan desain modern, bersih, dan profesional:
- **Primer (Forest Green)**: `#15573F` (Hover: `#104430`, Active: `#0C3223`)
- **Aksen Hijau Muda**: `#EDF7F2` / `#E8F3EE` (Border: `#B2DEC6`)
- **Latar Halaman**: `#F6F8F7`
- **Kartu & Kontainer**: `#FFFFFF` dengan radius membulat `rounded-2xl` dan border halus `#E3E8E5`
- **Teks Utama**: `#13251C` | **Teks Sekunder**: `#576B61` | **Muted**: `#84968C`
- **Pil Status (Badge)**:
  - `diterima` : Kuning / Amber
  - `diperiksa` : Biru
  - `diperbaiki` : Ungu
  - `selesai` : Hijau Emerald
  - `diambil` : Abu-abu Slate

---

## Alur Status Servis

Alur pengerjaan tiket servis wajib berjalan sekuensial dan terjaga di sisi server:

```
 [ Petugas ]               [ Teknisi ]                   [ Teknisi ]               [ Teknisi ]                 [ Petugas ]
  Diterima     ───►    Dalam Pemeriksaan   ───►       Dalam Perbaikan   ───►         Selesai       ───►          Diambil
(Pendaftaran)          (Mulai Periksa)              (Reparasi + Part)          (Diagnosis Wajib)          (Serah Terima & Lunas)
```

1. **Diterima**: Tiket didaftarkan oleh Petugas Servis. `tgl_masuk` tercatat hari ini.
2. **Diperiksa**: Teknisi mengambil tiket dan melakukan pengecekan fisik/software.
3. **Diperbaiki**: Teknisi mengisi catatan diagnosis, tindakan perbaikan, biaya jasa, dan pemakaian sparepart. Stok sparepart langsung terpotong.
4. **Selesai**: Perangkat berhasil diperbaiki dan diuji normal. `tgl_selesai` tercatat hari ini. Formulir pengerjaan terkunci (*Read-Only*).
5. **Diambil**: Pelanggan melunasi total biaya dan menerima perangkat dari Petugas. `tgl_diambil` tercatat hari ini.

---

## Panduan Instalasi Lokal

### 1. Prasyarat Sistem
- Windows 10/11 dengan [Laragon](https://laragon.org/) (Apache/Nginx, MySQL 8.0+, PHP 8.2+)
- Composer 2.x
- Node.js 20+ & NPM

### 2. Kloning & Pengaturan Dependensi
Pindahkan atau clone repositori ke dalam folder `C:\laragon\www\servis-komputer`:

```bash
cd C:\laragon\www\serviskomputer
composer install
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file konfigurasi contoh dan buat application key:

```bash
cp .env.example .env
php artisan key:generate
```

Pastikan konfigurasi database di file `.env` mengarah ke MySQL lokal Anda:

```env
APP_NAME="Servis Komputer"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://servis-komputer.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=servis_komputer
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### 4. Migrasi & Seeder Database
Jalankan migrasi tabel beserta data demo realistis:

```bash
php artisan migrate:fresh --seed
```

### 5. Kompilasi Aset Frontend
Jalankan Vite untuk mode produksi atau development:

```bash
# Untuk kompilasi aset produksi:
npm run build

# Atau untuk live-reloading saat pengembangan:
npm run dev
```

Aplikasi siap diakses melalui virtual host otomatis Laragon di browser:
👉 **`http://servis-komputer.test`** (atau `http://localhost:8000` jika menggunakan `php artisan serve`).

---

## Akun Demo Bawaan

Semua akun demo di bawah ini telah di-generate secara otomatis oleh seeder dengan password default: **`password`**

| Peran | Nama Pengguna | Alamat Email | Password | Hak Akses Utama |
|---|---|---|---|---|
| **Administrator** | Admin | `admin@servis.test` | `password` | Kelola user, kelola sparepart, laporan analitik & grafik, ubah profil |
| **Petugas Servis** | Dewi Petugas | `petugas@servis.test` | `password` | Kelola pelanggan, pendaftaran servis, cetak tanda terima, serahkan perangkat |
| **Teknisi** | Budi Teknisi | `teknisi@servis.test` | `password` | Daftar servis masuk, workbench diagnosis & perbaikan, pasang sparepart, update status |

---

## Pengujian Otomatis (Automated Tests)

Aplikasi dilengkapi dengan rangkaian pengujian fitur dan unit komprehensif menggunakan **Pest PHP**. Pengujian mencakup:
- Integritas relasi database dan model
- Autentikasi, regenerasi sesi, dan rate limiting login
- CRUD User & aturan proteksi administrator
- CRUD Sparepart & indikator batas stok
- CRUD Pelanggan & riwayat servis
- Transaksi database pendaftaran servis & nomor tiket otomatis
- Logika bisnis pengerjaan, mutasi stok `lockForUpdate()`, dan transisi status sekuensial
- Otorisasi penyerahan perangkat berstatus selesai & pelunasan
- Hak akses dashboard tiap peran & pengawasan detail read-only
- Laporan analitik, filtering, dan dokumen cetak resmi
- Halaman error kustom (403, 404, 500) dan audit responsif

Jalankan pengujian kapan saja dengan perintah:

```bash
php artisan test --compact
```

Hasil pengujian: **108 passed, 696 assertions, 0 failures**.

---

## Struktur Direktori

```text
serviskomputer/
├── app/
│   ├── Enums/                 # Enum RolePengguna & StatusServis
│   ├── Http/
│   │   ├── Controllers/       # Controller per peran (Admin, Petugas, Teknisi, Auth, Profil)
│   │   ├── Middleware/        # Middleware autentikasi & RoleMiddleware
│   │   └── Requests/          # FormRequest validation dengan pesan Bahasa Indonesia
│   ├── Models/                # Eloquent models (User, Pelanggan, Servis, Sparepart, DetailSparepart)
│   ├── Services/              # Logika bisnis (Pendaftaran, Pengerjaan, Penyerahan, NomorServisGenerator)
│   └── Support/               # Helper menu, salam waktu, dan format mata uang
├── database/
│   ├── factories/             # Model factories untuk testing & seeding
│   ├── migrations/            # Skema tabel database sesuai Class Diagram
│   └── seeders/               # DatabaseSeeder realistis (akun, pelanggan, sparepart, servis)
├── resources/
│   ├── css/                   # Tailwind CSS 4 theme tokens & custom utilities
│   ├── js/                    # Bootstrap Alpine.js & Chart.js via NPM
│   └── views/
│       ├── admin/             # View Administrator (Dashboard, User, Sparepart, Laporan, Cetak)
│       ├── auth/              # Form Login aman
│       ├── components/        # Blade components (stat-card, button, input, status-badge, modal, dll)
│       ├── errors/            # Halaman error kustom bertema Nordic Pine (403, 404, 419, 500)
│       ├── layouts/           # Layout utama (Sidebar, Topbar, Toast, Stack Scripts)
│       ├── petugas/           # View Petugas (Dashboard, Pelanggan, Pendaftaran, Tanda Terima, Penyerahan)
│       ├── profile/           # Form Ubah Profil & Ubah Password
│       └── teknisi/           # View Teknisi (Dashboard, Workbench Diagnosis & Suku Cadang)
├── routes/
│   └── web.php                # Rute web terisolasi per peran dengan middleware auth & role
└── tests/
    └── Feature/               # 108 automated feature tests berbasis Pest PHP
```

---

## Lisensi

Proyek ini dikembangkan di bawah lisensi terbuka [MIT License](LICENSE).
Semua komponen antarmuka, logika bisnis, dan pengujian dirancang khusus mengikuti dokumen spesifikasi teknis `docs/SPEC.md`.
