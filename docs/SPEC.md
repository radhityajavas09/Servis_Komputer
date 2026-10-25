# 1. Ringkasan
Aplikasi web untuk mengelola pelayanan servis komputer dan laptop di sebuah tempat servis: mencatat pelanggan, perangkat yang diservis, keluhan, hasil pemeriksaan (diagnosis), pemakaian sparepart, perkembangan status pengerjaan, sampai penyerahan perangkat ke pelanggan. Output: data servis terorganisir, status pengerjaan yang jelas, riwayat servis pelanggan, tanda terima yang bisa dicetak, dan laporan untuk Administrator.

# 2. Stack (wajib)
- PHP 8.2+, Laravel 12 (tanpa starter kit; autentikasi dibuat sendiri agar sesuai skenario), Blade.
- Lingkungan lokal: Laragon di Windows (sudah berisi Apache/Nginx, MySQL, PHP 8.2+, Composer, Node.js, dan HeidiSQL/phpMyAdmin). Proyek berada di `C:\laragon\www\servis-komputer` dan diakses lewat virtual host otomatis Laragon `http://servis-komputer.test`.
- Database: MySQL bawaan Laragon. Nama `servis_komputer`, user `root`, tanpa password (lokal).
- Tailwind CSS 4 + Vite (bawaan Laravel 12), Alpine.js untuk interaksi kecil, Chart.js untuk grafik (di-bundle lewat npm, bukan CDN).
- Ikon: komponen Blade `<x-icon name="...">` berisi SVG gaya Lucide (stroke 1.75). Jangan pakai emoji sebagai ikon.
- Pengembang: Windows 11, Antigravity IDE. Gunakan perintah yang jalan di PowerShell/terminal Laragon. Jalankan aplikasi lewat `http://servis-komputer.test` (Laragon) bersama `npm run dev`; `php artisan serve` hanya cadangan.
- Locale `id`, timezone `Asia/Jakarta`, mata uang Rupiah ("Rp 150.000"), tanggal "21 Okt 2026".
- Seluruh teks antarmuka berbahasa Indonesia.

# 3. Peran pengguna
| Peran (nilai enum) | Label | Hak |
|---|---|---|
| `admin` | Administrator | Kelola user, kelola sparepart, lihat laporan + grafik, ubah profil |
| `petugas` | Petugas Servis | Kelola pelanggan, daftarkan servis, cetak tanda terima, serahkan perangkat, ubah profil |
| `teknisi` | Teknisi | Lihat daftar servis masuk, diagnosis & perbaikan, input sparepart, update status, ubah profil |
Setelah login, tiap pengguna diarahkan ke dashboard dengan menu sesuai perannya. Akses ke halaman peran lain harus ditolak (403 dengan halaman rapi, bukan error mentah).

# 4. Database (ikuti Class Diagram, nama kolom persis)
Tabel `users` (model User, primary key `id_user`):
`id_user` PK, `nama` varchar, `email` varchar UNIQUE, `password` varchar (hash), `role` enum(admin, petugas, teknisi), `remember_token`, timestamps.

Tabel `pelanggan` (PK `id_pelanggan`): `nama`, `no_telepon`, `alamat` text, timestamps.

Tabel `servis` (PK `id_servis`):
`no_servis` varchar UNIQUE (format `SRV-001`, naik otomatis, lebih dari 999 jadi `SRV-1000`), `id_pelanggan` FK, `id_petugas` FK ke users, `id_teknisi` FK ke users NULLABLE, `jenis_perangkat` varchar, `merk_tipe` varchar, `keluhan` text, `diagnosis` text NULL, `tindakan` text NULL, `status` enum(diterima, diperiksa, diperbaiki, selesai, diambil) default `diterima`, `tgl_masuk` date, `tgl_selesai` date NULL, `tgl_diambil` date NULL (TAMBAHAN di luar class diagram, dibutuhkan skenario Penyerahan), `biaya_jasa` int default 0, `total_biaya` int default 0, timestamps.

Tabel `sparepart` (PK `id_sparepart`): `nama_sparepart`, `harga` int, `stok` int, timestamps.

Tabel `detail_sparepart` (PK `id_detail`): `id_servis` FK, `id_sparepart` FK, `jumlah` int, `subtotal` int.

Relasi: User 1..* Servis (sebagai petugas dan sebagai teknisi); Pelanggan 1..* Servis; Servis 1..* DetailSparepart; Sparepart 1..* DetailSparepart.
Foreign key memakai RESTRICT (bukan cascade). Data yang masih dipakai tidak boleh dihapus; tampilkan pesan error yang ramah.
Jangan memakai tabel bawaan Laravel yang tidak perlu: SESSION_DRIVER=file, CACHE_STORE=file, QUEUE_CONNECTION=sync.

# 5. Aturan bisnis penting
- No servis dibuat otomatis di dalam transaksi database (hindari duplikat). Status awal `diterima`, `tgl_masuk` = hari ini.
- Alur status WAJIB berurutan: diterima -> diperiksa -> diperbaiki -> selesai -> diambil.
  - Teknisi hanya boleh maju satu langkah ke status berikutnya (diterima->diperiksa, diperiksa->diperbaiki, diperbaiki->selesai). Tidak boleh loncat, tidak boleh mundur.
  - `diambil` HANYA lewat proses Penyerahan Perangkat oleh Petugas, dan hanya jika status sudah `selesai`.
  - Saat menjadi `selesai`: isi `tgl_selesai` = hari ini. Syarat menjadi `selesai`: diagnosis dan tindakan sudah terisi.
  - Saat `diambil`: isi `tgl_diambil` = hari ini.
  - Servis `selesai`/`diambil` terkunci: form diagnosis tidak bisa diedit lagi (tampil read-only).
- Diagnosis & Perbaikan (Teknisi): input diagnosis, tindakan, biaya jasa, dan sparepart (boleh banyak baris). Saat simpan, dalam satu transaksi: validasi, cek stok (dengan lockForUpdate), kurangi stok, simpan `detail_sparepart` (subtotal = harga x jumlah), hitung `total_biaya = biaya_jasa + sum(subtotal)`. Jika diagnosis disimpan ulang, kembalikan dulu stok dari detail lama sebelum menerapkan yang baru. Stok tidak cukup = error "Stok {nama} tidak cukup (tersisa N)" dan tidak ada perubahan apa pun yang tersimpan.
- `id_teknisi` diisi dengan teknisi yang pertama kali menyimpan diagnosis/status pada servis itu (kalau masih NULL).
- Penyerahan Perangkat: tampilkan detail + total biaya, wajib centang "Pembayaran sudah diterima / lunas", lalu status jadi `diambil`.
- Pendaftaran servis: kalau `no_telepon` sudah ada di tabel pelanggan, pakai pelanggan yang sama (jangan buat duplikat). Pelanggan baru dibuat kalau belum ada.
- Hapus data selalu lewat dialog konfirmasi. Admin tidak boleh menghapus akun sendiri dan tidak boleh menghapus/menurunkan Administrator terakhir.
- Pesan berhasil memakai toast/flash, contoh: "Data berhasil disimpan", "Data berhasil dihapus", "Perangkat berhasil diserahkan", "Diagnosis berhasil disimpan", "Status berhasil diperbarui".

# 6. Halaman dan menu (Petasitus)
Halaman Login -> Dashboard (sesuai peran)
- Administrator: Dashboard, Data User, Data Sparepart, Laporan Servis, Ubah Profil, Logout
- Petugas Servis: Dashboard, Data Pelanggan, Pendaftaran Servis, Data Servis (berisi aksi Detail, Cetak Tanda Terima, Serahkan), Ubah Profil, Logout
- Teknisi: Dashboard, Daftar Servis Masuk, Detail & Diagnosis (di dalamnya: input sparepart + update status), Ubah Profil, Logout
Tambahan kecil: halaman Detail Servis read-only (dengan riwayat singkat, diagnosis, sparepart, total biaya) yang bisa dibuka Administrator dari Dashboard/Laporan agar tidak ada tautan mati.
Route key servis memakai `no_servis` (contoh `/teknisi/servis/SRV-002`).

# 7. Desain antarmuka (acuan: screenshot `docs/design/dashboard-admin.png`)
Screenshot adalah acuan GAYA VISUAL dan komponen. Wireframe di dokumen rancangan adalah acuan SUSUNAN FORM/TABEL. Kalau warna beda, ikuti screenshot (hijau), bukan wireframe (biru tua).
- Gaya: bersih, modern, banyak ruang putih, kartu putih berborder tipis, sudut membulat (radius 12px), bayangan sangat halus.
- Warna (estimasi dari screenshot, sesuaikan setelah melihat gambarnya): primer hijau tua `#1F6B4B` (hover `#185a3e`), teks utama `#13251C`, teks sekunder `#5B6B63`, latar halaman `#F6F8F7`, kartu `#FFFFFF`, border `#E3E8E5`. Definisikan semuanya sebagai CSS variable / token tema Tailwind, jangan hardcode berulang.
- Font: sans geometris modern (Plus Jakarta Sans dari Google Fonts, fallback system-ui). Angka statistik tebal dan besar.
- Pola komponen yang ada di screenshot (wajib dipakai konsisten di semua halaman):
  - Topbar: tombol hamburger (mobile), breadcrumb "Workspace > {Nama Halaman}", ikon kalender, ikon lonceng dengan titik merah, avatar bulat berisi inisial nama (mis. "AD") berlatar hijau muda.
  - Eyebrow: teks kecil kapital berspasi lebar dengan titik hijau di depan (mis. "PANEL ADMINISTRATOR", "AKTIVITAS TERBARU").
  - Judul halaman besar tebal, subjudul abu-abu.
  - Tombol: primer (hijau penuh + ikon), sekunder (putih bergaris + ikon).
  - Kartu statistik: label kecil di kiri atas, ikon dalam kotak pastel di kanan atas, angka besar dua digit berpadding nol ("07"), deskripsi kecil di bawah, indikator kecil di kanan bawah. Grid 2 kolom di mobile, 4 kolom di desktop.
  - Daftar aktivitas: ikon perangkat dalam kotak pastel, judul tebal, sub "Nama pelanggan · 21 Okt 2026", pil status di kanan, panah chevron.
- Pil status (badge dengan titik kecil di depan). Nilai enum -> label tampilan -> warna:
  - diterima -> "Diterima" -> kuning/amber
  - diperiksa -> "Dalam pemeriksaan" -> biru
  - diperbaiki -> "Dalam perbaikan" -> ungu
  - selesai -> "Selesai" -> hijau
  - diambil -> "Diambil" -> abu-abu
  Buat SATU komponen `<x-status-badge :status="...">` dan pakai di mana pun.
- Responsif: mobile-first. Sidebar tetap di desktop (>=1024px), drawer dari hamburger di mobile. Tabel di mobile berubah menjadi daftar kartu atau scroll horizontal yang rapi. Target sentuh minimal 44px.
- Setiap halaman wajib punya: empty state (ikon + teks + aksi), loading/disabled state saat submit, pesan error validasi di bawah field dalam bahasa Indonesia, dan fokus keyboard yang terlihat.

# 8. Dokumen cetak: Tanda Terima Servis
Halaman khusus (tanpa sidebar/topbar) siap cetak: nama tempat servis, "TANDA TERIMA SERVIS", no servis, tgl masuk, data pelanggan (nama, telepon, alamat), perangkat (jenis, merk/tipe), keluhan, nama petugas, kolom tanda tangan pelanggan dan petugas, catatan "Bawa tanda terima ini saat mengambil perangkat". Tombol Cetak (`window.print()`, bisa juga Simpan sebagai PDF lewat dialog cetak) dan Kembali; tombol disembunyikan saat cetak (`@media print`). Ukuran A5/A4 rapi.

# 9. Aturan Kerja (untuk semua tahap)
1. Kerjakan HANYA tahap yang diminta. Jangan mengerjakan fitur tahap berikutnya.
2. Sebelum menulis kode, baca `docs/SPEC.md` dan lihat kode yang sudah ada. Konsisten dengan struktur yang sudah dibuat.
3. Struktur Laravel: Controller tipis, validasi lewat FormRequest, logika bisnis berat (nomor servis, stok, transisi status) di class Service/Action yang bisa dites, route dikelompokkan per peran dengan middleware `auth` dan `role:`.
4. Nama route, view, dan folder rapi per peran: `admin.*`, `petugas.*`, `teknisi.*`.
5. Semua query yang menampilkan relasi memakai eager loading (hindari N+1). Tabel panjang diberi pagination (10 per halaman) dan pencarian bila relevan.
6. Keamanan: CSRF, password di-hash, mass assignment dijaga ($fillable), otorisasi per peran di sisi server (jangan hanya menyembunyikan menu), regenerate session saat login, throttle login.
7. Jangan ada tombol/tautan mati dan jangan ada data palsu hardcode di view; semua data dari database.
8. Setelah selesai, JALANKAN sendiri: migrasi/seeder yang relevan, `php artisan test` kalau ada tes, dan `npm run build` untuk memastikan tidak error. Bukti dilaporkan, bukan sekadar klaim "sudah jalan".
9. Akhiri SETIAP tahap dengan format "STOP & LAPOR": (a) apa yang dibuat, (b) file penting yang berubah, (c) cara saya mencobanya secara manual (langkah klik), (d) hal yang kamu putuskan sendiri atau ragu, (e) hasil pengujian. Lalu berhenti dan tunggu perintah tahap berikutnya.
10. Kalau menemukan konflik/ambiguitas di spesifikasi, tanyakan di laporan; jangan diam-diam mengubah spesifikasi.
