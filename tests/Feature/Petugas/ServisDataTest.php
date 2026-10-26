<?php

use App\Enums\StatusServis;
use App\Models\DetailSparepart;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Tahap 10: Petugas - Data Servis & Penyerahan Perangkat Tests
|--------------------------------------------------------------------------
*/

test('guests are redirected to login when accessing service management', function () {
    $servis = Servis::factory()->create();

    $this->get(route('petugas.servis.index'))->assertRedirect('/login');
    $this->get(route('petugas.servis.detail', $servis))->assertRedirect('/login');
    $this->get(route('petugas.servis.serahkan', $servis))->assertRedirect('/login');
    $this->post(route('petugas.servis.proses-serahkan', $servis), [])->assertRedirect('/login');
});

test('teknisi cannot access device handover', function () {
    $teknisi = User::factory()->teknisi()->create();
    $servis = Servis::factory()->create(['status' => StatusServis::Selesai]);

    $this->actingAs($teknisi)->get(route('petugas.servis.serahkan', $servis))->assertStatus(403);
    $this->actingAs($teknisi)->post(route('petugas.servis.proses-serahkan', $servis), [
        'pembayaran_lunas' => '1',
    ])->assertStatus(403);
});

test('petugas can view service list with status filter and search', function () {
    $petugas = User::factory()->petugas()->create();
    $custA = Pelanggan::factory()->create(['nama' => 'Ahmad Pelanggan']);
    $custB = Pelanggan::factory()->create(['nama' => 'Budi Pelanggan']);

    $srv1 = Servis::factory()->create([
        'no_servis' => 'SRV-101',
        'id_pelanggan' => $custA->id_pelanggan,
        'merk_tipe' => 'HP Pavilion 14',
        'status' => StatusServis::Diterima,
    ]);

    $srv2 = Servis::factory()->create([
        'no_servis' => 'SRV-102',
        'id_pelanggan' => $custB->id_pelanggan,
        'merk_tipe' => 'Acer Swift 3',
        'status' => StatusServis::Selesai,
    ]);

    // View index
    $response = $this->actingAs($petugas)->get(route('petugas.servis.index'));
    $response->assertStatus(200);
    $response->assertSee('SRV-101');
    $response->assertSee('SRV-102');
    $response->assertSee('HP Pavilion 14');
    $response->assertSee('Acer Swift 3');

    // Search by service number
    $searchSrv = $this->actingAs($petugas)->get(route('petugas.servis.index', ['q' => 'SRV-101']));
    $searchSrv->assertStatus(200);
    $searchNumbers = $searchSrv->viewData('servisList')->pluck('no_servis')->all();
    expect($searchNumbers)->toContain('SRV-101')
        ->and($searchNumbers)->not->toContain('SRV-102');

    // Filter by status selesai
    $filterSelesai = $this->actingAs($petugas)->get(route('petugas.servis.index', ['status' => 'selesai']));
    $filterSelesai->assertStatus(200);
    $filterNumbers = $filterSelesai->viewData('servisList')->pluck('no_servis')->all();
    expect($filterNumbers)->toContain('SRV-102')
        ->and($filterNumbers)->not->toContain('SRV-101');
});

test('petugas can view comprehensive service detail page', function () {
    $petugas = User::factory()->petugas()->create(['nama' => 'Petugas Satu']);
    $teknisi = User::factory()->teknisi()->create(['nama' => 'Teknisi Handal']);
    $pelanggan = Pelanggan::factory()->create(['nama' => 'Pelanggan Uji']);
    $sparepart = Sparepart::factory()->create(['nama_sparepart' => 'SSD NVMe 256GB', 'harga' => 300000]);

    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-888',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'id_teknisi' => $teknisi->id_user,
        'jenis_perangkat' => 'Laptop',
        'merk_tipe' => 'Asus ZenBook Duo',
        'keluhan' => 'Layar kedua berkedip dan SSD lambat',
        'diagnosis' => 'SSD mengalami bad block dan flex cable layar kotor',
        'tindakan' => 'Pembersihan fleksibel dan penggantian SSD NVMe',
        'status' => StatusServis::Selesai,
        'biaya_jasa' => 150000,
        'total_biaya' => 450000,
    ]);

    DetailSparepart::create([
        'id_servis' => $servis->id_servis,
        'id_sparepart' => $sparepart->id_sparepart,
        'jumlah' => 1,
        'subtotal' => 300000,
    ]);

    $response = $this->actingAs($petugas)->get(route('petugas.servis.detail', $servis));

    $response->assertStatus(200);
    $response->assertSee('SRV-888');
    $response->assertSee('Pelanggan Uji');
    $response->assertSee('Asus ZenBook Duo');
    $response->assertSee('Layar kedua berkedip');
    $response->assertSee('SSD mengalami bad block');
    $response->assertSee('SSD NVMe 256GB');
    $response->assertSee('Rp 150.000');
    $response->assertSee('Rp 450.000');
    $response->assertSee('Teknisi Handal');
    $response->assertSee('Serahkan Perangkat');
});

test('petugas cannot open handover page if service status is not selesai', function () {
    $petugas = User::factory()->petugas()->create();
    $servis = Servis::factory()->create(['status' => StatusServis::Diterima]);

    $response = $this->actingAs($petugas)->get(route('petugas.servis.serahkan', $servis));

    $response->assertRedirect(route('petugas.servis.detail', $servis));
    $response->assertSessionHas('error', 'Perangkat hanya dapat diserahkan jika pengerjaan sudah berstatus selesai.');
});

test('petugas can open handover page for selesai status', function () {
    $petugas = User::factory()->petugas()->create();
    $servis = Servis::factory()->create([
        'status' => StatusServis::Selesai,
        'total_biaya' => 250000,
    ]);

    $response = $this->actingAs($petugas)->get(route('petugas.servis.serahkan', $servis));

    $response->assertStatus(200);
    $response->assertSee('Penyerahan Perangkat Servis');
    $response->assertSee($servis->no_servis);
    $response->assertSee('Rp 250.000');
    $response->assertSee('Serahkan Perangkat');
});

test('handover requires payment confirmation checkbox', function () {
    $petugas = User::factory()->petugas()->create();
    $servis = Servis::factory()->create(['status' => StatusServis::Selesai]);

    $response = $this->actingAs($petugas)->post(route('petugas.servis.proses-serahkan', $servis), [
        'pembayaran_lunas' => '0',
    ]);

    $response->assertSessionHasErrors(['pembayaran_lunas']);
    expect($servis->fresh()->status)->toBe(StatusServis::Selesai);
});

test('petugas successfully handovers device to customer with payment confirmed', function () {
    $petugas = User::factory()->petugas()->create();
    $servis = Servis::factory()->create([
        'status' => StatusServis::Selesai,
        'tgl_diambil' => null,
    ]);

    $response = $this->actingAs($petugas)->post(route('petugas.servis.proses-serahkan', $servis), [
        'pembayaran_lunas' => '1',
    ]);

    $response->assertRedirect(route('petugas.servis.detail', $servis));
    $response->assertSessionHas('success', 'Perangkat berhasil diserahkan kepada pelanggan.');

    $servis->refresh();
    expect($servis->status)->toBe(StatusServis::Diambil)
        ->and($servis->tgl_diambil->format('Y-m-d'))->toBe(now()->format('Y-m-d'));
});
