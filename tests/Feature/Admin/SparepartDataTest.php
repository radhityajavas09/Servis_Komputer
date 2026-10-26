<?php

use App\Models\DetailSparepart;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Tahap 7: Admin - Data Sparepart Tests
|--------------------------------------------------------------------------
*/

test('guests are redirected to login when accessing sparepart management', function () {
    $sparepart = Sparepart::factory()->create();

    $this->get(route('admin.sparepart.index'))->assertRedirect('/login');
    $this->get(route('admin.sparepart.create'))->assertRedirect('/login');
    $this->post(route('admin.sparepart.store'), [])->assertRedirect('/login');
    $this->get(route('admin.sparepart.edit', $sparepart))->assertRedirect('/login');
    $this->put(route('admin.sparepart.update', $sparepart), [])->assertRedirect('/login');
    $this->delete(route('admin.sparepart.destroy', $sparepart))->assertRedirect('/login');
});

test('non-admin users receive 403 forbidden when accessing sparepart management', function () {
    $petugas = User::factory()->petugas()->create();
    $teknisi = User::factory()->teknisi()->create();
    $sparepart = Sparepart::factory()->create();

    // Petugas trying to access admin sparepart routes
    $this->actingAs($petugas)->get(route('admin.sparepart.index'))->assertStatus(403);
    $this->actingAs($petugas)->get(route('admin.sparepart.create'))->assertStatus(403);
    $this->actingAs($petugas)->post(route('admin.sparepart.store'), [])->assertStatus(403);
    $this->actingAs($petugas)->get(route('admin.sparepart.edit', $sparepart))->assertStatus(403);
    $this->actingAs($petugas)->put(route('admin.sparepart.update', $sparepart), [])->assertStatus(403);
    $this->actingAs($petugas)->delete(route('admin.sparepart.destroy', $sparepart))->assertStatus(403);

    // Teknisi trying to access admin sparepart routes
    $this->actingAs($teknisi)->get(route('admin.sparepart.index'))->assertStatus(403);
    $this->actingAs($teknisi)->get(route('admin.sparepart.create'))->assertStatus(403);
    $this->actingAs($teknisi)->post(route('admin.sparepart.store'), [])->assertStatus(403);
    $this->actingAs($teknisi)->get(route('admin.sparepart.edit', $sparepart))->assertStatus(403);
    $this->actingAs($teknisi)->put(route('admin.sparepart.update', $sparepart), [])->assertStatus(403);
    $this->actingAs($teknisi)->delete(route('admin.sparepart.destroy', $sparepart))->assertStatus(403);
});

test('admin can view sparepart list with search and stock status filters', function () {
    $admin = User::factory()->admin()->create();

    $partNormal = Sparepart::factory()->create([
        'nama_sparepart' => 'RAM DDR4 8GB Corsair',
        'harga' => 350000,
        'stok' => 10,
    ]);

    $partLow = Sparepart::factory()->create([
        'nama_sparepart' => 'Thermal Paste Arctic MX-4',
        'harga' => 95000,
        'stok' => 2,
    ]);

    $partEmpty = Sparepart::factory()->create([
        'nama_sparepart' => 'Baterai Asus A456U',
        'harga' => 280000,
        'stok' => 0,
    ]);

    // View index
    $response = $this->actingAs($admin)->get(route('admin.sparepart.index'));
    $response->assertStatus(200);
    $response->assertSee('RAM DDR4 8GB Corsair');
    $response->assertSee('Thermal Paste Arctic MX-4');
    $response->assertSee('Baterai Asus A456U');
    $response->assertSee('Rp 350.000');
    $response->assertSee('Menipis (2 unit)');
    $response->assertSee('Habis (0 unit)');

    // Search by name
    $searchResponse = $this->actingAs($admin)->get(route('admin.sparepart.index', ['q' => 'Corsair']));
    $searchResponse->assertStatus(200);
    $searchResponse->assertSee('RAM DDR4 8GB Corsair');
    $searchResponse->assertDontSee('Thermal Paste Arctic MX-4');

    // Filter by stock menipis
    $filterLow = $this->actingAs($admin)->get(route('admin.sparepart.index', ['stock' => 'menipis']));
    $filterLow->assertStatus(200);
    $filterLow->assertSee('Thermal Paste Arctic MX-4');
    $filterLow->assertDontSee('RAM DDR4 8GB Corsair');
    $filterLow->assertDontSee('Baterai Asus A456U');

    // Filter by stock habis
    $filterEmpty = $this->actingAs($admin)->get(route('admin.sparepart.index', ['stock' => 'habis']));
    $filterEmpty->assertStatus(200);
    $filterEmpty->assertSee('Baterai Asus A456U');
    $filterEmpty->assertDontSee('RAM DDR4 8GB Corsair');

    // Filter by stock tersedia
    $filterAvailable = $this->actingAs($admin)->get(route('admin.sparepart.index', ['stock' => 'tersedia']));
    $filterAvailable->assertStatus(200);
    $filterAvailable->assertSee('RAM DDR4 8GB Corsair');
    $filterAvailable->assertDontSee('Thermal Paste Arctic MX-4');
});

test('admin can see create form and store a new sparepart', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.sparepart.create'))->assertStatus(200);

    $response = $this->actingAs($admin)->post(route('admin.sparepart.store'), [
        'nama_sparepart' => 'SSD NVMe 1TB Samsung 980',
        'harga' => 1250000,
        'stok' => 8,
    ]);

    $response->assertRedirect(route('admin.sparepart.index'));
    $response->assertSessionHas('success', 'Data berhasil disimpan');

    $this->assertDatabaseHas('sparepart', [
        'nama_sparepart' => 'SSD NVMe 1TB Samsung 980',
        'harga' => 1250000,
        'stok' => 8,
    ]);
});

test('storing sparepart validates required fields and negative numbers', function () {
    $admin = User::factory()->admin()->create();
    Sparepart::factory()->create(['nama_sparepart' => 'Keyboard Logitech K120']);

    $response = $this->actingAs($admin)->post(route('admin.sparepart.store'), [
        'nama_sparepart' => 'Keyboard Logitech K120', // Duplicate
        'harga' => -50000,                            // Negative
        'stok' => -3,                                 // Negative
    ]);

    $response->assertSessionHasErrors(['nama_sparepart', 'harga', 'stok']);
});

test('admin can see edit form and update sparepart details', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->create([
        'nama_sparepart' => 'SSD Sata 120GB',
        'harga' => 175000,
        'stok' => 5,
    ]);

    $this->actingAs($admin)->get(route('admin.sparepart.edit', $sparepart))->assertStatus(200);

    $response = $this->actingAs($admin)->put(route('admin.sparepart.update', $sparepart), [
        'nama_sparepart' => 'SSD Sata 120GB V-Gen',
        'harga' => 180000,
        'stok' => 9,
    ]);

    $response->assertRedirect(route('admin.sparepart.index'));
    $response->assertSessionHas('success', 'Data berhasil disimpan');

    $sparepart->refresh();
    expect($sparepart->nama_sparepart)->toBe('SSD Sata 120GB V-Gen')
        ->and($sparepart->harga)->toBe(180000)
        ->and($sparepart->stok)->toBe(9);
});

test('updating sparepart allows keeping same name but rejects duplicate of other sparepart', function () {
    $admin = User::factory()->admin()->create();
    $partA = Sparepart::factory()->create(['nama_sparepart' => 'Charger Lenovo Type-C 65W']);
    $partB = Sparepart::factory()->create(['nama_sparepart' => 'Charger Asus 19V 3.42A']);

    // Keeping own name is valid
    $responseSame = $this->actingAs($admin)->put(route('admin.sparepart.update', $partA), [
        'nama_sparepart' => 'Charger Lenovo Type-C 65W',
        'harga' => 250000,
        'stok' => 4,
    ]);
    $responseSame->assertSessionHasNoErrors();

    // Duplicate name of another sparepart is rejected
    $responseDuplicate = $this->actingAs($admin)->put(route('admin.sparepart.update', $partA), [
        'nama_sparepart' => 'Charger Asus 19V 3.42A',
        'harga' => 250000,
        'stok' => 4,
    ]);
    $responseDuplicate->assertSessionHasErrors(['nama_sparepart']);
});

test('cannot delete sparepart that has been used in service records', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create();
    $sparepart = Sparepart::factory()->create(['nama_sparepart' => 'RAM DDR3 4GB']);

    $servis = Servis::factory()->create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
    ]);

    // Attach to detail_sparepart
    DetailSparepart::create([
        'id_servis' => $servis->id_servis,
        'id_sparepart' => $sparepart->id_sparepart,
        'jumlah' => 1,
        'subtotal' => $sparepart->harga,
    ]);

    // Attempt to delete
    $response = $this->actingAs($admin)->delete(route('admin.sparepart.destroy', $sparepart));

    $response->assertSessionHas('error', 'Sparepart tidak dapat dihapus karena sudah digunakan pada data servis.');
    $this->assertDatabaseHas('sparepart', ['id_sparepart' => $sparepart->id_sparepart]);
});

test('admin can delete sparepart that has never been used in service records', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->create(['nama_sparepart' => 'Sparepart Baru Belum Terpakai']);

    $response = $this->actingAs($admin)->delete(route('admin.sparepart.destroy', $sparepart));

    $response->assertRedirect(route('admin.sparepart.index'));
    $response->assertSessionHas('success', 'Data berhasil dihapus');
    $this->assertDatabaseMissing('sparepart', ['id_sparepart' => $sparepart->id_sparepart]);
});
