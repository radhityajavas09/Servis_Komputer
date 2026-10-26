<?php

use App\Enums\StatusServis;
use App\Models\DetailSparepart;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Tahap 11: Teknisi - Daftar Servis & Diagnosis Tests
|--------------------------------------------------------------------------
*/

test('guests are redirected to login when accessing teknisi service workbench', function () {
    $servis = Servis::factory()->create();

    $this->get(route('teknisi.servis.index'))->assertRedirect('/login');
    $this->get(route('teknisi.servis.show', $servis))->assertRedirect('/login');
    $this->put(route('teknisi.servis.update-diagnosis', $servis), [])->assertRedirect('/login');
    $this->post(route('teknisi.servis.update-status', $servis), [])->assertRedirect('/login');
});

test('petugas cannot access teknisi service routes', function () {
    $petugas = User::factory()->petugas()->create();
    $servis = Servis::factory()->create();

    $this->actingAs($petugas)->get(route('teknisi.servis.index'))->assertStatus(403);
    $this->actingAs($petugas)->get(route('teknisi.servis.show', $servis))->assertStatus(403);
    $this->actingAs($petugas)->put(route('teknisi.servis.update-diagnosis', $servis), [
        'biaya_jasa' => 50000,
    ])->assertStatus(403);
    $this->actingAs($petugas)->post(route('teknisi.servis.update-status', $servis))->assertStatus(403);
});

test('teknisi can view service list with stats, search, and status filter', function () {
    $teknisi = User::factory()->teknisi()->create();
    $custA = Pelanggan::factory()->create(['nama' => 'Joko Santoso', 'no_telepon' => '081233334444']);
    $custB = Pelanggan::factory()->create(['nama' => 'Rina Wijaya', 'no_telepon' => '081255556666']);

    $srv1 = Servis::factory()->create([
        'no_servis' => 'SRV-201',
        'id_pelanggan' => $custA->id_pelanggan,
        'merk_tipe' => 'Lenovo ThinkPad X1',
        'status' => StatusServis::Diterima,
    ]);

    $srv2 = Servis::factory()->create([
        'no_servis' => 'SRV-202',
        'id_pelanggan' => $custB->id_pelanggan,
        'merk_tipe' => 'Dell XPS 13',
        'status' => StatusServis::Diperbaiki,
    ]);

    $response = $this->actingAs($teknisi)->get(route('teknisi.servis.index'));
    $response->assertStatus(200);
    $response->assertSee('SRV-201');
    $response->assertSee('SRV-202');
    $response->assertSee('Lenovo ThinkPad X1');
    $response->assertSee('Dell XPS 13');
    $response->assertSee('Joko Santoso');
    $response->assertSee('Rina Wijaya');

    // Search query
    $searchRes = $this->actingAs($teknisi)->get(route('teknisi.servis.index', ['q' => 'ThinkPad']));
    $searchRes->assertStatus(200);
    $searchItems = $searchRes->viewData('servisList')->pluck('no_servis')->all();
    expect($searchItems)->toContain('SRV-201')
        ->and($searchItems)->not->toContain('SRV-202');

    // Filter status
    $filterRes = $this->actingAs($teknisi)->get(route('teknisi.servis.index', ['status' => 'diperbaiki']));
    $filterRes->assertStatus(200);
    $filterItems = $filterRes->viewData('servisList')->pluck('no_servis')->all();
    expect($filterItems)->toContain('SRV-202')
        ->and($filterItems)->not->toContain('SRV-201');
});

test('teknisi can view service detail and diagnosis workbench page', function () {
    $teknisi = User::factory()->teknisi()->create();
    $sparepart = Sparepart::factory()->create(['nama_sparepart' => 'RAM DDR4 8GB', 'harga' => 350000, 'stok' => 10]);
    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-203',
        'status' => StatusServis::Diperiksa,
    ]);

    $response = $this->actingAs($teknisi)->get(route('teknisi.servis.show', $servis));
    $response->assertStatus(200);
    $response->assertSee('SRV-203');
    $response->assertSee('RAM DDR4 8GB');
    $response->assertSee('Catatan Pemeriksaan');
    $response->assertSee('Mulai Perbaikan (Diperbaiki)');
});

test('teknisi can save diagnosis, actions, service fee, and dynamic spareparts with stock deduction', function () {
    $teknisi = User::factory()->teknisi()->create();
    $sparepartA = Sparepart::factory()->create(['nama_sparepart' => 'Thermal Paste Arctic MX-4', 'harga' => 50000, 'stok' => 5]);
    $sparepartB = Sparepart::factory()->create(['nama_sparepart' => 'Fan Cooler Laptop', 'harga' => 120000, 'stok' => 4]);

    $servis = Servis::factory()->create([
        'no_servis' => 'SRV-204',
        'id_teknisi' => null,
        'status' => StatusServis::Diperiksa,
        'biaya_jasa' => 0,
        'total_biaya' => 0,
    ]);

    $payload = [
        'diagnosis' => 'Pasta prosesor mengering dan kipas bearing aus sehingga overheat.',
        'tindakan' => 'Penggantian pasta pendingin dan unit kipas baru.',
        'biaya_jasa' => 80000,
        'spareparts' => [
            [
                'id_sparepart' => $sparepartA->id_sparepart,
                'jumlah' => 1,
            ],
            [
                'id_sparepart' => $sparepartB->id_sparepart,
                'jumlah' => 1,
            ],
        ],
    ];

    $response = $this->actingAs($teknisi)->put(route('teknisi.servis.update-diagnosis', $servis), $payload);
    $response->assertRedirect(route('teknisi.servis.show', $servis));
    $response->assertSessionHas('success');

    // Verify service model
    $servis->refresh();
    expect($servis->id_teknisi)->toBe($teknisi->id_user)
        ->and($servis->diagnosis)->toBe($payload['diagnosis'])
        ->and($servis->tindakan)->toBe($payload['tindakan'])
        ->and($servis->biaya_jasa)->toBe(80000)
        ->and($servis->total_biaya)->toBe(80000 + 50000 + 120000); // 250.000

    // Verify stock deduction
    expect($sparepartA->fresh()->stok)->toBe(4) // 5 - 1
        ->and($sparepartB->fresh()->stok)->toBe(3); // 4 - 1

    // Verify detail_sparepart records
    expect(DetailSparepart::where('id_servis', $servis->id_servis)->count())->toBe(2);
});

test('saving diagnosis again restores previously used stock before deducting new usage', function () {
    $teknisi = User::factory()->teknisi()->create();
    $sparepart = Sparepart::factory()->create(['nama_sparepart' => 'SSD NVMe 512GB', 'harga' => 500000, 'stok' => 5]);

    $servis = Servis::factory()->create([
        'status' => StatusServis::Diperiksa,
    ]);

    // Save initial sparepart (qty: 2)
    $this->actingAs($teknisi)->put(route('teknisi.servis.update-diagnosis', $servis), [
        'diagnosis' => 'SSD bermasalah',
        'tindakan' => 'Ganti SSD 2 unit',
        'biaya_jasa' => 100000,
        'spareparts' => [
            [
                'id_sparepart' => $sparepart->id_sparepart,
                'jumlah' => 2,
            ],
        ],
    ]);

    expect($sparepart->fresh()->stok)->toBe(3); // 5 - 2

    // Re-save with only 1 unit
    $this->actingAs($teknisi)->put(route('teknisi.servis.update-diagnosis', $servis), [
        'diagnosis' => 'Cukup ganti SSD 1 unit saja',
        'tindakan' => 'Ganti SSD 1 unit',
        'biaya_jasa' => 100000,
        'spareparts' => [
            [
                'id_sparepart' => $sparepart->id_sparepart,
                'jumlah' => 1,
            ],
        ],
    ]);

    // Stock should be restored to 5 then decremented to 4
    expect($sparepart->fresh()->stok)->toBe(4)
        ->and($servis->fresh()->total_biaya)->toBe(600000); // 100k + 500k
});

test('saving diagnosis fails and rolls back when sparepart stock is insufficient', function () {
    $teknisi = User::factory()->teknisi()->create();
    $sparepart = Sparepart::factory()->create([
        'nama_sparepart' => 'Keyboard Mekanikal Laptop',
        'harga' => 400000,
        'stok' => 2,
    ]);

    $servis = Servis::factory()->create([
        'status' => StatusServis::Diperiksa,
        'biaya_jasa' => 0,
        'total_biaya' => 0,
    ]);

    // Request 5 units when only 2 available
    $response = $this->actingAs($teknisi)->put(route('teknisi.servis.update-diagnosis', $servis), [
        'diagnosis' => 'Keyboard rusak',
        'tindakan' => 'Ganti keyboard',
        'biaya_jasa' => 50000,
        'spareparts' => [
            [
                'id_sparepart' => $sparepart->id_sparepart,
                'jumlah' => 5,
            ],
        ],
    ]);

    $response->assertSessionHas('error', "Stok {$sparepart->nama_sparepart} tidak cukup (tersisa 2)");

    // No changes to stock or database
    expect($sparepart->fresh()->stok)->toBe(2)
        ->and(DetailSparepart::where('id_servis', $servis->id_servis)->count())->toBe(0)
        ->and($servis->fresh()->total_biaya)->toBe(0);
});

test('service status advances sequentially from diterima to diperiksa, diperbaiki, and selesai', function () {
    $teknisi = User::factory()->teknisi()->create();
    $servis = Servis::factory()->create([
        'status' => StatusServis::Diterima,
        'diagnosis' => 'Motherboard short',
        'tindakan' => 'Penggantian MOSFET daya',
    ]);

    // 1. Diterima -> Diperiksa
    $this->actingAs($teknisi)->post(route('teknisi.servis.update-status', $servis))
        ->assertRedirect(route('teknisi.servis.show', $servis));
    expect($servis->fresh()->status)->toBe(StatusServis::Diperiksa);

    // 2. Diperiksa -> Diperbaiki
    $this->actingAs($teknisi)->post(route('teknisi.servis.update-status', $servis))
        ->assertRedirect(route('teknisi.servis.show', $servis));
    expect($servis->fresh()->status)->toBe(StatusServis::Diperbaiki);

    // 3. Diperbaiki -> Selesai
    $this->actingAs($teknisi)->post(route('teknisi.servis.update-status', $servis))
        ->assertRedirect(route('teknisi.servis.show', $servis));
    $fresh = $servis->fresh();
    expect($fresh->status)->toBe(StatusServis::Selesai)
        ->and($fresh->tgl_selesai->format('Y-m-d'))->toBe(now()->format('Y-m-d'));
});

test('cannot advance to selesai if diagnosis or tindakan is empty', function () {
    $teknisi = User::factory()->teknisi()->create();
    $servis = Servis::factory()->create([
        'status' => StatusServis::Diperbaiki,
        'diagnosis' => null,
        'tindakan' => null,
    ]);

    $response = $this->actingAs($teknisi)->post(route('teknisi.servis.update-status', $servis));
    $response->assertSessionHas('error', 'Diagnosis dan tindakan perbaikan wajib diisi sebelum menyelesaikan status servis.');

    expect($servis->fresh()->status)->toBe(StatusServis::Diperbaiki);
});

test('teknisi cannot advance status beyond selesai to diambil', function () {
    $teknisi = User::factory()->teknisi()->create();
    $servis = Servis::factory()->create([
        'status' => StatusServis::Selesai,
        'diagnosis' => 'Done',
        'tindakan' => 'Done',
    ]);

    $response = $this->actingAs($teknisi)->post(route('teknisi.servis.update-status', $servis));
    $response->assertSessionHas('error');
    expect($servis->fresh()->status)->toBe(StatusServis::Selesai);
});

test('service with status selesai or diambil is locked and cannot be edited', function () {
    $teknisi = User::factory()->teknisi()->create();
    $servis = Servis::factory()->create([
        'status' => StatusServis::Selesai,
        'biaya_jasa' => 100000,
    ]);

    $response = $this->actingAs($teknisi)->put(route('teknisi.servis.update-diagnosis', $servis), [
        'diagnosis' => 'Ubah diagnosis setelah selesai',
        'tindakan' => 'Ubah tindakan',
        'biaya_jasa' => 200000,
    ]);

    $response->assertSessionHas('error', 'Servis yang telah selesai atau diambil terkunci dan tidak dapat diubah lagi.');
    expect($servis->fresh()->biaya_jasa)->toBe(100000);

    // Workbench page renders read-only locked badge
    $viewRes = $this->actingAs($teknisi)->get(route('teknisi.servis.show', $servis));
    $viewRes->assertStatus(200);
    $viewRes->assertSee('Terkunci');
    $viewRes->assertSee('Read-Only');
});
