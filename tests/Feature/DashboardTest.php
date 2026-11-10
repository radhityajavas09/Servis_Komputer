<?php

use App\Enums\StatusServis;
use App\Models\DetailSparepart;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Tahap 12: Dashboard Tiap Peran & Admin Detail Servis Tests
|--------------------------------------------------------------------------
*/

test('guests are redirected to login when accessing any dashboard or admin service detail', function () {
    $servis = Servis::factory()->create();

    $this->get(route('admin.dashboard'))->assertRedirect('/login');
    $this->get(route('petugas.dashboard'))->assertRedirect('/login');
    $this->get(route('teknisi.dashboard'))->assertRedirect('/login');
    $this->get(route('admin.servis.show', $servis))->assertRedirect('/login');
});

test('users cannot access dashboards of other roles', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();
    $teknisi = User::factory()->teknisi()->create();
    $servis = Servis::factory()->create();

    // Admin access checks
    $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
    $this->actingAs($admin)->get(route('admin.servis.show', $servis))->assertStatus(200);
    $this->actingAs($admin)->get(route('petugas.dashboard'))->assertStatus(403);
    $this->actingAs($admin)->get(route('teknisi.dashboard'))->assertStatus(403);

    // Petugas access checks
    $this->actingAs($petugas)->get(route('petugas.dashboard'))->assertStatus(200);
    $this->actingAs($petugas)->get(route('admin.dashboard'))->assertStatus(403);
    $this->actingAs($petugas)->get(route('admin.servis.show', $servis))->assertStatus(403);
    $this->actingAs($petugas)->get(route('teknisi.dashboard'))->assertStatus(403);

    // Teknisi access checks
    $this->actingAs($teknisi)->get(route('teknisi.dashboard'))->assertStatus(200);
    $this->actingAs($teknisi)->get(route('admin.dashboard'))->assertStatus(403);
    $this->actingAs($teknisi)->get(route('admin.servis.show', $servis))->assertStatus(403);
    $this->actingAs($teknisi)->get(route('petugas.dashboard'))->assertStatus(403);
});

test('admin dashboard renders accurate metrics, activities, and inventory warnings', function () {
    $admin = User::factory()->admin()->create(['nama' => 'Bambang Admin']);
    $teknisi = User::factory()->teknisi()->create(['nama' => 'Doni Teknisi']);
    $cust = Pelanggan::factory()->create(['nama' => 'Siti Pelanggan']);

    // Low stock sparepart
    $sparepartKritis = Sparepart::factory()->create([
        'nama_sparepart' => 'Pasta Thermal MX-4',
        'stok' => 2,
        'harga' => 45000,
    ]);

    // Services in various states
    $srv1 = Servis::factory()->create([
        'no_servis' => 'SRV-010',
        'id_pelanggan' => $cust->id_pelanggan,
        'id_teknisi' => $teknisi->id_user,
        'merk_tipe' => 'Asus ROG Zephyrus',
        'status' => StatusServis::Diterima,
    ]);

    $srv2 = Servis::factory()->create([
        'no_servis' => 'SRV-011',
        'id_pelanggan' => $cust->id_pelanggan,
        'id_teknisi' => $teknisi->id_user,
        'merk_tipe' => 'MacBook Pro M1',
        'status' => StatusServis::Diambil,
        'total_biaya' => 750000,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('PANEL ADMINISTRATOR');
    $response->assertSee('Bambang Admin');
    $response->assertSee('SRV-010');
    $response->assertSee('SRV-011');
    $response->assertSee('Asus ROG Zephyrus');
    $response->assertSee('MacBook Pro M1');
    $response->assertSee('Pasta Thermal MX-4');
    $response->assertSee('Rp 750.000');
});

test('admin can view read-only service detail page', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create(['nama' => 'Petugas Citra']);
    $teknisi = User::factory()->teknisi()->create(['nama' => 'Teknisi Eko']);
    $pelanggan = Pelanggan::factory()->create(['nama' => 'Rudi Hartono', 'no_telepon' => '081234567890']);
    $part = Sparepart::factory()->create(['nama_sparepart' => 'Baterai Laptop Dell', 'harga' => 450000]);

    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-777',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'id_teknisi' => $teknisi->id_user,
        'merk_tipe' => 'Dell Latitude 7490',
        'keluhan' => 'Baterai kembung dan cepat drop',
        'diagnosis' => 'Cell baterai telah aus dan membengkak',
        'tindakan' => 'Penggantian baterai OEM baru dan kalibrasi pengisian',
        'status' => StatusServis::Selesai,
        'biaya_jasa' => 100000,
        'total_biaya' => 550000,
    ]);

    DetailSparepart::create([
        'id_servis' => $servis->id_servis,
        'id_sparepart' => $part->id_sparepart,
        'jumlah' => 1,
        'subtotal' => 450000,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.servis.show', $servis));

    $response->assertStatus(200);
    $response->assertSee('PENGAWASAN SERVIS (READ-ONLY)');
    $response->assertSee('SRV-777');
    $response->assertSee('Rudi Hartono');
    $response->assertSee('081234567890');
    $response->assertSee('Dell Latitude 7490');
    $response->assertSee('Baterai Laptop Dell');
    $response->assertSee('Petugas Citra');
    $response->assertSee('Teknisi Eko');
    $response->assertSee('Rp 550.000');
});

test('petugas dashboard renders handover queue, daily intake metrics, and recent registrations', function () {
    $petugas = User::factory()->petugas()->create(['nama' => 'Winda Petugas']);
    $cust = Pelanggan::factory()->create(['nama' => 'Fajar Santoso']);

    // Finished service waiting for handover
    $srvSiap = Servis::factory()->create([
        'no_servis' => 'SRV-333',
        'id_pelanggan' => $cust->id_pelanggan,
        'merk_tipe' => 'Lenovo Legion 5',
        'status' => StatusServis::Selesai,
        'total_biaya' => 600000,
        'tgl_selesai' => now(),
    ]);

    $response = $this->actingAs($petugas)->get(route('petugas.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('PANEL PETUGAS SERVIS');
    $response->assertSee('Winda Petugas');
    $response->assertSee('SIAP DISERAHKAN');
    $response->assertSee('SRV-333');
    $response->assertSee('Lenovo Legion 5');
    $response->assertSee('Fajar Santoso');
    $response->assertSee('Rp 600.000');
    $response->assertSee(route('petugas.servis.serahkan', $srvSiap));
});

test('teknisi dashboard renders active workbench jobs, queue, and completion history', function () {
    $teknisi = User::factory()->teknisi()->create(['nama' => 'Gunawan Teknisi']);
    $otherTeknisi = User::factory()->teknisi()->create(['nama' => 'Teknisi Lain']);
    $cust = Pelanggan::factory()->create(['nama' => 'Maya Indah']);

    // Job assigned to this technician
    $myJob = Servis::factory()->create([
        'no_servis' => 'SRV-555',
        'id_pelanggan' => $cust->id_pelanggan,
        'id_teknisi' => $teknisi->id_user,
        'merk_tipe' => 'Acer Predator Helios',
        'keluhan' => 'Layar biru saat main game',
        'status' => StatusServis::Diperbaiki,
    ]);

    // Job assigned to another technician
    $otherJob = Servis::factory()->create([
        'no_servis' => 'SRV-556',
        'id_pelanggan' => $cust->id_pelanggan,
        'id_teknisi' => $otherTeknisi->id_user,
        'merk_tipe' => 'Asus Vivobook',
        'status' => StatusServis::Diperiksa,
    ]);

    // Unassigned incoming queue
    $queueJob = Servis::factory()->create([
        'no_servis' => 'SRV-557',
        'id_pelanggan' => $cust->id_pelanggan,
        'id_teknisi' => null,
        'merk_tipe' => 'HP Envy x360',
        'status' => StatusServis::Diterima,
    ]);

    $response = $this->actingAs($teknisi)->get(route('teknisi.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('PANEL TEKNISI');
    $response->assertSee('Gunawan Teknisi');
    $response->assertSee('PENGERJAAN AKTIF SAYA');
    $response->assertSee('SRV-555');
    $response->assertSee('Acer Predator Helios');
    $response->assertSee('Antrean Servis Baru');
    $response->assertSee('SRV-557');
    $response->assertSee('HP Envy x360');

    // Active jobs card should specifically show jobs for this technician
    $pekerjaanAktif = $response->viewData('pekerjaanAktif');
    expect($pekerjaanAktif->pluck('no_servis'))->toContain('SRV-555')
        ->and($pekerjaanAktif->pluck('no_servis'))->not->toContain('SRV-556');
});
