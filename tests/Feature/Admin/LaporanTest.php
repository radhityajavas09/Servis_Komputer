<?php

use App\Enums\StatusServis;
use App\Models\DetailSparepart;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Tahap 13: Admin - Laporan Servis dan Grafik Tests
|--------------------------------------------------------------------------
*/

test('guests are redirected to login when accessing reports', function () {
    $this->get(route('admin.laporan.index'))->assertRedirect('/login');
    $this->get(route('admin.laporan.cetak'))->assertRedirect('/login');
});

test('non-admin users cannot access reports', function () {
    $petugas = User::factory()->petugas()->create();
    $teknisi = User::factory()->teknisi()->create();

    $this->actingAs($petugas)->get(route('admin.laporan.index'))->assertStatus(403);
    $this->actingAs($petugas)->get(route('admin.laporan.cetak'))->assertStatus(403);

    $this->actingAs($teknisi)->get(route('admin.laporan.index'))->assertStatus(403);
    $this->actingAs($teknisi)->get(route('admin.laporan.cetak'))->assertStatus(403);
});

test('admin can view report index with aggregated summaries and chart data', function () {
    $admin = User::factory()->admin()->create();
    $teknisi = User::factory()->teknisi()->create(['nama' => 'Teknisi Laporan']);
    $pelanggan = Pelanggan::factory()->create(['nama' => 'Pelanggan Laporan']);
    $part = Sparepart::factory()->create(['nama_sparepart' => 'RAM DDR4 16GB', 'harga' => 600000]);

    // Service 1: Taken (paid)
    $srv1 = Servis::factory()->create([
        'no_servis' => 'SRV-901',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_teknisi' => $teknisi->id_user,
        'merk_tipe' => 'Lenovo ThinkPad T480',
        'status' => StatusServis::Diambil,
        'biaya_jasa' => 150000,
        'total_biaya' => 750000,
        'tgl_masuk' => now()->toDateString(),
        'tgl_selesai' => now()->toDateString(),
        'tgl_diambil' => now()->toDateString(),
    ]);

    DetailSparepart::create([
        'id_servis' => $srv1->id_servis,
        'id_sparepart' => $part->id_sparepart,
        'jumlah' => 1,
        'subtotal' => 600000,
    ]);

    // Service 2: In progress
    $srv2 = Servis::factory()->create([
        'no_servis' => 'SRV-902',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_teknisi' => $teknisi->id_user,
        'merk_tipe' => 'Asus TUF Gaming',
        'status' => StatusServis::Diperbaiki,
        'biaya_jasa' => 50000,
        'total_biaya' => 50000,
        'tgl_masuk' => now()->toDateString(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.laporan.index'));

    $response->assertStatus(200);
    $response->assertSee('ANALITIK & REKAPITULASI');
    $response->assertSee('Laporan Servis & Grafik Performa');
    $response->assertSee('SRV-901');
    $response->assertSee('SRV-902');
    $response->assertSee('Lenovo ThinkPad T480');
    $response->assertSee('Asus TUF Gaming');
    $response->assertSee('Rp 750.000');
    $response->assertSee('Teknisi Laporan');

    // Assert summary and chart structures passed to view
    $summary = $response->viewData('summary');
    expect($summary['total_servis'])->toBe(2)
        ->and($summary['total_pendapatan'])->toBe(750000)
        ->and($summary['total_jasa'])->toBe(150000)
        ->and($summary['selesai_count'])->toBe(1)
        ->and($summary['proses_count'])->toBe(1);

    $statusDist = $response->viewData('statusDistribution');
    expect($statusDist['labels'])->toContain('Diterima', 'Dalam Pemeriksaan', 'Dalam Perbaikan', 'Selesai', 'Sudah Diambil');

    $monthlyTrend = $response->viewData('monthlyTrend');
    expect($monthlyTrend['labels'])->toHaveCount(6)
        ->and($monthlyTrend['masuk'])->toHaveCount(6);
});

test('admin can filter reports by date range, status, technician, and search keyword', function () {
    $admin = User::factory()->admin()->create();
    $tekA = User::factory()->teknisi()->create(['nama' => 'Teknisi Alfa']);
    $tekB = User::factory()->teknisi()->create(['nama' => 'Teknisi Beta']);
    $custA = Pelanggan::factory()->create(['nama' => 'Haris']);
    $custB = Pelanggan::factory()->create(['nama' => 'Dewi']);

    // Past service
    $pastSrv = Servis::factory()->create([
        'no_servis' => 'SRV-PAST',
        'id_pelanggan' => $custA->id_pelanggan,
        'id_teknisi' => $tekA->id_user,
        'merk_tipe' => 'Acer Aspire 3',
        'status' => StatusServis::Diambil,
        'tgl_masuk' => now()->subMonths(2)->toDateString(),
    ]);

    // Current service
    $currentSrv = Servis::factory()->create([
        'no_servis' => 'SRV-NOW',
        'id_pelanggan' => $custB->id_pelanggan,
        'id_teknisi' => $tekB->id_user,
        'merk_tipe' => 'HP Victus 16',
        'status' => StatusServis::Selesai,
        'tgl_masuk' => now()->toDateString(),
    ]);

    // Filter by status 'selesai'
    $resStatus = $this->actingAs($admin)->get(route('admin.laporan.index', ['status' => 'selesai']));
    $resStatus->assertStatus(200);
    $itemsStatus = $resStatus->viewData('servisList')->pluck('no_servis')->all();
    expect($itemsStatus)->toContain('SRV-NOW')
        ->and($itemsStatus)->not->toContain('SRV-PAST');

    // Filter by technician tekA
    $resTek = $this->actingAs($admin)->get(route('admin.laporan.index', ['id_teknisi' => $tekA->id_user]));
    $resTek->assertStatus(200);
    $itemsTek = $resTek->viewData('servisList')->pluck('no_servis')->all();
    expect($itemsTek)->toContain('SRV-PAST')
        ->and($itemsTek)->not->toContain('SRV-NOW');

    // Filter by search query 'Victus'
    $resQuery = $this->actingAs($admin)->get(route('admin.laporan.index', ['q' => 'Victus']));
    $resQuery->assertStatus(200);
    $itemsQuery = $resQuery->viewData('servisList')->pluck('no_servis')->all();
    expect($itemsQuery)->toContain('SRV-NOW')
        ->and($itemsQuery)->not->toContain('SRV-PAST');
});

test('admin can access printable report with all filtered rows and accumulated totals', function () {
    $admin = User::factory()->admin()->create(['nama' => 'Admin Kepala']);
    $pelanggan = Pelanggan::factory()->create(['nama' => 'Bambang Sudarsono']);

    $srv = Servis::factory()->create([
        'no_servis' => 'SRV-PRINT-01',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'merk_tipe' => 'Dell Inspiron 15',
        'status' => StatusServis::Diambil,
        'biaya_jasa' => 100000,
        'total_biaya' => 250000,
        'tgl_masuk' => now()->toDateString(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.laporan.cetak'));

    $response->assertStatus(200);
    $response->assertSee('BENGKEL SERVIS KOMPUTER');
    $response->assertSee('LAPORAN REKAPITULASI PELAYANAN SERVIS');
    $response->assertSee('SRV-PRINT-01');
    $response->assertSee('Bambang Sudarsono');
    $response->assertSee('Dell Inspiron 15');
    $response->assertSee('Rp 250.000');
    $response->assertSee('Admin Kepala');
    $response->assertSee('Akumulasi Total:');
});
