<?php

use App\Enums\StatusServis;
use App\Models\DetailSparepart;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Portal Pelacakan Servis Pelanggan (Public Customer Portal) Tests
|--------------------------------------------------------------------------
*/

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
    $petugas = User::factory()->petugas()->create();
    $teknisi = User::factory()->teknisi()->create(['nama' => 'Budi Teknisi']);
    $pelanggan = Pelanggan::factory()->create([
        'nama' => 'Budi Santoso',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Merdeka No. 10',
    ]);

    Servis::factory()->create([
        'no_servis' => 'SRV-001',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'id_teknisi' => $teknisi->id_user,
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
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create();

    Servis::factory()->create([
        'no_servis' => 'SRV-001',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
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
    $petugas = User::factory()->petugas()->create();
    $teknisi = User::factory()->teknisi()->create();
    $pelanggan = Pelanggan::factory()->create();

    $sparepart = Sparepart::factory()->create([
        'nama_sparepart' => 'SSD Kingston 512GB',
        'harga' => 450000,
        'stok' => 5,
    ]);

    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-005',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'id_teknisi' => $teknisi->id_user,
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
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create();

    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-010',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
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
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create();

    Servis::factory()->create([
        'no_servis' => 'SRV-011',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'status' => StatusServis::Selesai,
        'keluhan' => 'Ganti pasta thermal',
    ]);

    $response = $this->post('/lacak/SRV-011/keluhan', [
        'catatan_tambahan' => 'Mau nambah ganti kipas',
    ]);

    $response->assertRedirect('/lacak/SRV-011');
    $response->assertSessionHas('error');
});

test('pelanggan dapat membuka halaman cetak nota tanpa perlu login', function () {
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create();

    Servis::factory()->create([
        'no_servis' => 'SRV-020',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'status' => StatusServis::Selesai,
        'keluhan' => 'Keyboard rusak',
        'diagnosis' => 'Short circuit di switch huruf A',
        'tindakan' => 'Ganti keyboard baru',
        'biaya_jasa' => 100000,
        'total_biaya' => 250000,
    ]);

    $response = $this->get('/lacak/SRV-020/cetak');

    $response->assertStatus(200);
    $response->assertSee('SRV-020');
    $response->assertSee('Keyboard rusak');
    $response->assertSee('Short circuit di switch huruf A');
    $response->assertSee('Rp 250.000');
    $response->assertSee('JAMINAN GARANSI TOKO RESMI 30 HARI');
});

test('admin dapat mencetak salinan tanda terima atau nota dari detail servis', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create();

    Servis::factory()->create([
        'no_servis' => 'SRV-021',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'status' => StatusServis::Diambil,
        'biaya_jasa' => 100000,
        'total_biaya' => 100000,
    ]);

    $response = $this->actingAs($admin)->get('/admin/servis/SRV-021/cetak');

    $response->assertStatus(200);
    $response->assertSee('SRV-021');
    $response->assertSee('SUDAH LUNAS');
});
