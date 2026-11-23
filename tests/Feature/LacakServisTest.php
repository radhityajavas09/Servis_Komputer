<?php

use App\Enums\RolePengguna;
use App\Enums\StatusServis;
use App\Models\DetailSparepart;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;

beforeEach(function () {
    $this->petugas = User::factory()->create([
        'nama' => 'Petugas Front Desk',
        'role' => RolePengguna::Petugas,
    ]);

    $this->teknisi = User::factory()->create([
        'nama' => 'Budi Teknisi',
        'role' => RolePengguna::Teknisi,
    ]);

    $this->pelanggan = Pelanggan::factory()->create([
        'nama' => 'Budi Santoso',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Merdeka No. 10',
    ]);
});

test('halaman lacak servis publik dapat dibuka oleh guest', function () {
    $response = $this->get('/lacak');

    $response->assertStatus(200);
    $response->assertSee('Cek Status Servis Perangkat');
    $response->assertSee('Nomor Tiket Servis / Kode Akses');
    $response->assertSee('Lacak Status');
});

test('pencarian tiket yang tidak ada menampilkan pesan ramah', function () {
    $response = $this->get('/lacak?no_servis=SRV-999');

    $response->assertStatus(200);
    $response->assertSee('Pencarian Tidak Ditemukan');
    $response->assertSee('SRV-999');
});

test('pelanggan dapat melacak status servis yang ada dengan nomor tiket', function () {
    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-001',
        'id_pelanggan' => $this->pelanggan->id_pelanggan,
        'id_petugas' => $this->petugas->id_user,
        'id_teknisi' => $this->teknisi->id_user,
        'jenis_perangkat' => 'Laptop',
        'merk_tipe' => 'Asus Vivobook 14',
        'keluhan' => 'Layar blank hitam bergaris',
        'diagnosis' => 'Kabel fleksibel kendor',
        'tindakan' => 'Pemasangan ulang kabel fleksibel',
        'status' => StatusServis::Diperbaiki,
        'biaya_jasa' => 150000,
        'total_biaya' => 150000,
    ]);

    $response = $this->get('/lacak?no_servis=SRV-001');

    $response->assertStatus(200);
    $response->assertSee('SRV-001');
    $response->assertSee('Asus Vivobook 14');
    $response->assertSee('Layar blank hitam bergaris');
    $response->assertSee('Kabel fleksibel kendor');
    $response->assertSee('Pemasangan ulang kabel fleksibel');
    $response->assertSee('Rp 150.000');
    $response->assertSee('Konfirmasi via WhatsApp');
});

test('pencarian dengan input angka seperti 1 otomatis diformat ke SRV-001', function () {
    Servis::factory()->create([
        'no_servis' => 'SRV-001',
        'id_pelanggan' => $this->pelanggan->id_pelanggan,
        'id_petugas' => $this->petugas->id_user,
        'jenis_perangkat' => 'Laptop',
        'merk_tipe' => 'Lenovo IdeaPad',
        'status' => StatusServis::Diterima,
    ]);

    $response = $this->get('/lacak?no_servis=1');

    $response->assertStatus(200);
    $response->assertSee('SRV-001');
    $response->assertSee('Lenovo IdeaPad');
});

test('tiket berstatus selesai menampilkan garansi toko dan rincian suku cadang', function () {
    $sparepart = Sparepart::factory()->create([
        'nama_sparepart' => 'SSD Kingston 512GB',
        'harga' => 450000,
        'stok' => 5,
    ]);

    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-005',
        'id_pelanggan' => $this->pelanggan->id_pelanggan,
        'id_petugas' => $this->petugas->id_user,
        'id_teknisi' => $this->teknisi->id_user,
        'status' => StatusServis::Selesai,
        'biaya_jasa' => 100000,
        'total_biaya' => 550000,
        'tgl_selesai' => now()->toDateString(),
    ]);

    DetailSparepart::create([
        'id_servis' => $servis->id_servis,
        'id_sparepart' => $sparepart->id_sparepart,
        'jumlah' => 1,
        'subtotal' => 450000,
    ]);

    $response = $this->get('/lacak/SRV-005');

    $response->assertStatus(200);
    $response->assertSee('SSD Kingston 512GB');
    $response->assertSee('Rp 550.000');
    $response->assertSee('Garansi Toko');
});

test('pelanggan dapat menambahkan catatan keluhan tambahan pada tiket servis aktif', function () {
    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-010',
        'id_pelanggan' => $this->pelanggan->id_pelanggan,
        'id_petugas' => $this->petugas->id_user,
        'status' => StatusServis::Diperiksa,
        'keluhan' => 'Layar berkedip',
    ]);

    $response = $this->post('/lacak/SRV-010/keluhan', [
        'catatan_tambahan' => 'Tolong data di folder Desktop diselamatkan ya mas.',
    ]);

    $response->assertRedirect('/lacak/SRV-010');
    $response->assertSessionHas('success');

    $servis->refresh();
    expect($servis->keluhan)->toContain('Layar berkedip')
        ->and($servis->keluhan)->toContain('Tolong data di folder Desktop diselamatkan ya mas.');
});

test('pelanggan tidak dapat menambah catatan jika tiket sudah berstatus selesai', function () {
    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-011',
        'id_pelanggan' => $this->pelanggan->id_pelanggan,
        'id_petugas' => $this->petugas->id_user,
        'status' => StatusServis::Selesai,
        'keluhan' => 'Ganti pasta thermal',
    ]);

    $response = $this->post('/lacak/SRV-011/keluhan', [
        'catatan_tambahan' => 'Mau nambah ganti kipas',
    ]);

    $response->assertRedirect('/lacak/SRV-011');
    $response->assertSessionHas('error');
});
