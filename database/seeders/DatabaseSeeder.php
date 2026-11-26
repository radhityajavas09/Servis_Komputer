<?php

namespace Database\Seeders;

use App\Enums\RolePengguna;
use App\Enums\StatusServis;
use App\Models\DetailSparepart;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tiga User Utama
        $admin = User::create([
            'nama' => 'Admin',
            'email' => 'admin@servis.test',
            'password' => Hash::make('adminSK'),
            'role' => RolePengguna::Admin,
        ]);

        $petugas = User::create([
            'nama' => 'Dewi Petugas',
            'email' => 'petugas@servis.test',
            'password' => Hash::make('petugasSK'),
            'role' => RolePengguna::Petugas,
        ]);

        $teknisi = User::create([
            'nama' => 'Budi Teknisi',
            'email' => 'teknisi@servis.test',
            'password' => Hash::make('teknisiSK'),
            'role' => RolePengguna::Teknisi,
        ]);

        // 2. Delapan Sparepart Realistis
        $spRam = Sparepart::create([
            'nama_sparepart' => 'RAM DDR4 8GB SODIMM',
            'harga' => 280000,
            'stok' => 10,
        ]);

        $spSsdSata = Sparepart::create([
            'nama_sparepart' => 'SSD 256GB SATA',
            'harga' => 320000,
            'stok' => 6,
        ]);

        $spSsdNvme = Sparepart::create([
            'nama_sparepart' => 'SSD NVMe 512GB',
            'harga' => 550000,
            'stok' => 5,
        ]);

        $spBaterai = Sparepart::create([
            'nama_sparepart' => 'Baterai laptop universal',
            'harga' => 420000,
            'stok' => 4,
        ]);

        $spCharger = Sparepart::create([
            'nama_sparepart' => 'Charger laptop 65W',
            'harga' => 180000,
            'stok' => 8,
        ]);

        $spKeyboard = Sparepart::create([
            'nama_sparepart' => 'Keyboard laptop',
            'harga' => 150000,
            'stok' => 2, // Stok menipis (<= 3)
        ]);

        $spPasta = Sparepart::create([
            'nama_sparepart' => 'Thermal paste',
            'harga' => 45000,
            'stok' => 15,
        ]);

        $spKipas = Sparepart::create([
            'nama_sparepart' => 'Kipas/fan laptop',
            'harga' => 95000,
            'stok' => 0, // Stok habis (= 0)
        ]);

        // 3. Enam Pelanggan
        $p1 = Pelanggan::create([
            'nama' => 'Nadia Putri',
            'no_telepon' => '081234567891',
            'alamat' => 'Jl. Melati No. 12, Purwokerto',
        ]);

        $p2 = Pelanggan::create([
            'nama' => 'Rizky Maulana',
            'no_telepon' => '081234567892',
            'alamat' => 'Jl. Mawar No. 5, Banyumas',
        ]);

        $p3 = Pelanggan::create([
            'nama' => 'Hendra Wijaya',
            'no_telepon' => '081234567893',
            'alamat' => 'Jl. Gatot Subroto No. 44, Sokaraja',
        ]);

        $p4 = Pelanggan::create([
            'nama' => 'Siti Rahmawati',
            'no_telepon' => '081234567894',
            'alamat' => 'Jl. Suparjo Rustam No. 18, Purwokerto',
        ]);

        $p5 = Pelanggan::create([
            'nama' => 'Dimas Pratama',
            'no_telepon' => '081234567895',
            'alamat' => 'Jl. Jend. Soedirman No. 88, Purwokerto',
        ]);

        $p6 = Pelanggan::create([
            'nama' => 'Anisa Fitriani',
            'no_telepon' => '081234567896',
            'alamat' => 'Jl. Patriot No. 21, Sokaraja',
        ]);

        // 4. TEPAT Tujuh Servis (1 diterima, 1 diperiksa, 2 diperbaiki, 2 selesai, 1 diambil)

        // Servis 1 (diperbaiki): ASUS VivoBook 14 · X1404ZA milik Nadia Putri, tgl masuk 21 Okt 2026
        $srv1 = Servis::create([
            'no_servis' => 'SRV-001',
            'id_pelanggan' => $p1->id_pelanggan,
            'id_petugas' => $petugas->id_user,
            'id_teknisi' => $teknisi->id_user,
            'jenis_perangkat' => 'Laptop',
            'merk_tipe' => 'ASUS VivoBook 14 · X1404ZA',
            'keluhan' => 'Layar berkedip dan loading Windows lambat sering macet',
            'diagnosis' => 'Harddisk internal sektor lambat (bad sector) dan debu fan menumpuk',
            'tindakan' => 'Penggantian ke SSD 256GB SATA dan pembersihan kipas internal',
            'status' => StatusServis::Diperbaiki,
            'tgl_masuk' => '2026-10-21',
            'tgl_selesai' => null,
            'tgl_diambil' => null,
            'biaya_jasa' => 100000,
            'total_biaya' => 420000, // 100000 + 320000
        ]);
        DetailSparepart::create([
            'id_servis' => $srv1->id_servis,
            'id_sparepart' => $spSsdSata->id_sparepart,
            'jumlah' => 1,
            'subtotal' => 320000,
        ]);

        // Servis 2 (diperiksa): Lenovo IdeaPad Slim 3 · 15IAH8 milik Rizky Maulana, tgl masuk 21 Okt 2026
        Servis::create([
            'no_servis' => 'SRV-002',
            'id_pelanggan' => $p2->id_pelanggan,
            'id_petugas' => $petugas->id_user,
            'id_teknisi' => $teknisi->id_user,
            'jenis_perangkat' => 'Laptop',
            'merk_tipe' => 'Lenovo IdeaPad Slim 3 · 15IAH8',
            'keluhan' => 'Mati total setelah terkena tumpahan air minum',
            'diagnosis' => 'Indikasi korosi dan short-circuit pada jalur power supply 19V',
            'tindakan' => null,
            'status' => StatusServis::Diperiksa,
            'tgl_masuk' => '2026-10-21',
            'tgl_selesai' => null,
            'tgl_diambil' => null,
            'biaya_jasa' => 0,
            'total_biaya' => 0,
        ]);

        // Servis 3 (diterima): Hendra Wijaya, tgl masuk 24 Okt 2026
        Servis::create([
            'no_servis' => 'SRV-003',
            'id_pelanggan' => $p3->id_pelanggan,
            'id_petugas' => $petugas->id_user,
            'id_teknisi' => null,
            'jenis_perangkat' => 'PC Desktop',
            'merk_tipe' => 'PC Rakitan · Ryzen 5 5600G',
            'keluhan' => 'Sering restart sendiri saat dipakai bermain game atau rendering',
            'diagnosis' => null,
            'tindakan' => null,
            'status' => StatusServis::Diterima,
            'tgl_masuk' => '2026-10-24',
            'tgl_selesai' => null,
            'tgl_diambil' => null,
            'biaya_jasa' => 0,
            'total_biaya' => 0,
        ]);

        // Servis 4 (diperbaiki): Siti Rahmawati, tgl masuk 18 Okt 2026
        $srv4 = Servis::create([
            'no_servis' => 'SRV-004',
            'id_pelanggan' => $p4->id_pelanggan,
            'id_petugas' => $petugas->id_user,
            'id_teknisi' => $teknisi->id_user,
            'jenis_perangkat' => 'Laptop',
            'merk_tipe' => 'Acer Aspire 5 · A514-54',
            'keluhan' => 'Laptop cepat panas dan mati mendadak saat membuka browser banyak tab',
            'diagnosis' => 'Thermal paste pada heatsink mengering total dan ventilasi tersumbat',
            'tindakan' => 'Repasting prosesor dengan thermal paste kualitas tinggi dan servis pendingin',
            'status' => StatusServis::Diperbaiki,
            'tgl_masuk' => '2026-10-18',
            'tgl_selesai' => null,
            'tgl_diambil' => null,
            'biaya_jasa' => 80000,
            'total_biaya' => 125000, // 80000 + 45000
        ]);
        DetailSparepart::create([
            'id_servis' => $srv4->id_servis,
            'id_sparepart' => $spPasta->id_sparepart,
            'jumlah' => 1,
            'subtotal' => 45000,
        ]);

        // Servis 5 (selesai): Dimas Pratama, tgl masuk 15 Okt 2026, selesai 17 Okt 2026
        $srv5 = Servis::create([
            'no_servis' => 'SRV-005',
            'id_pelanggan' => $p5->id_pelanggan,
            'id_petugas' => $petugas->id_user,
            'id_teknisi' => $teknisi->id_user,
            'jenis_perangkat' => 'Laptop',
            'merk_tipe' => 'HP Pavilion 14 · dv0065TX',
            'keluhan' => 'Upgrade performa laptop untuk tugas kantor dan penambahan memori',
            'diagnosis' => 'Penyimpanan masih memakai HDD 1TB dan RAM 4GB sudah maksimal terpakai',
            'tindakan' => 'Pemasangan SSD NVMe 512GB baru dan upgrade RAM DDR4 8GB SODIMM',
            'status' => StatusServis::Selesai,
            'tgl_masuk' => '2026-10-15',
            'tgl_selesai' => '2026-10-17',
            'tgl_diambil' => null,
            'biaya_jasa' => 150000,
            'total_biaya' => 980000, // 150000 + 550000 + 280000
        ]);
        DetailSparepart::create([
            'id_servis' => $srv5->id_servis,
            'id_sparepart' => $spSsdNvme->id_sparepart,
            'jumlah' => 1,
            'subtotal' => 550000,
        ]);
        DetailSparepart::create([
            'id_servis' => $srv5->id_servis,
            'id_sparepart' => $spRam->id_sparepart,
            'jumlah' => 1,
            'subtotal' => 280000,
        ]);

        // Servis 6 (selesai): Anisa Fitriani, tgl masuk bulan lalu (28 Sep 2026), selesai 2 Okt 2026
        $srv6 = Servis::create([
            'no_servis' => 'SRV-006',
            'id_pelanggan' => $p6->id_pelanggan,
            'id_petugas' => $petugas->id_user,
            'id_teknisi' => $teknisi->id_user,
            'jenis_perangkat' => 'Laptop',
            'merk_tipe' => 'Dell Inspiron 15 · 3511',
            'keluhan' => 'Baterai laptop cepat habis drop dari 100% langsung mati',
            'diagnosis' => 'Cell baterai lithium mengalami penurunan kapasitas drastis (health 12%)',
            'tindakan' => 'Penggantian baterai laptop universal baru dan kalibrasi charging',
            'status' => StatusServis::Selesai,
            'tgl_masuk' => '2026-09-28',
            'tgl_selesai' => '2026-10-02',
            'tgl_diambil' => null,
            'biaya_jasa' => 75000,
            'total_biaya' => 495000, // 75000 + 420000
        ]);
        DetailSparepart::create([
            'id_servis' => $srv6->id_servis,
            'id_sparepart' => $spBaterai->id_sparepart,
            'jumlah' => 1,
            'subtotal' => 420000,
        ]);

        // Servis 7 (diambil): Hendra Wijaya (riwayat kedua), tgl masuk 15 Sep 2026, selesai 17 Sep 2026, diambil 18 Sep 2026
        $srv7 = Servis::create([
            'no_servis' => 'SRV-007',
            'id_pelanggan' => $p3->id_pelanggan,
            'id_petugas' => $petugas->id_user,
            'id_teknisi' => $teknisi->id_user,
            'jenis_perangkat' => 'PC All-in-One',
            'merk_tipe' => 'Lenovo IdeaCentre A340 · 22IWL',
            'keluhan' => 'Adaptor charger putus digigit tikus dan tidak bisa menyala',
            'diagnosis' => 'Kabel adaptor putus tembus jalur arus tegangan',
            'tindakan' => 'Penggantian adaptor charger 65W baru original kompatibel',
            'status' => StatusServis::Diambil,
            'tgl_masuk' => '2026-09-15',
            'tgl_selesai' => '2026-09-17',
            'tgl_diambil' => '2026-09-18',
            'biaya_jasa' => 25000,
            'total_biaya' => 205000, // 25000 + 180000
        ]);
        DetailSparepart::create([
            'id_servis' => $srv7->id_servis,
            'id_sparepart' => $spCharger->id_sparepart,
            'jumlah' => 1,
            'subtotal' => 180000,
        ]);
    }
}
