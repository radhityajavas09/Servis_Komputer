<?php

use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Tahap 8: Petugas - Data Pelanggan Tests
|--------------------------------------------------------------------------
*/

test('guests are redirected to login when accessing customer management', function () {
    $pelanggan = Pelanggan::factory()->create();

    $this->get(route('petugas.pelanggan.index'))->assertRedirect('/login');
    $this->get(route('petugas.pelanggan.create'))->assertRedirect('/login');
    $this->post(route('petugas.pelanggan.store'), [])->assertRedirect('/login');
    $this->get(route('petugas.pelanggan.show', $pelanggan))->assertRedirect('/login');
    $this->get(route('petugas.pelanggan.edit', $pelanggan))->assertRedirect('/login');
    $this->put(route('petugas.pelanggan.update', $pelanggan), [])->assertRedirect('/login');
    $this->delete(route('petugas.pelanggan.destroy', $pelanggan))->assertRedirect('/login');
});

test('teknisi receives 403 forbidden when accessing customer management', function () {
    $teknisi = User::factory()->teknisi()->create();
    $pelanggan = Pelanggan::factory()->create();

    $this->actingAs($teknisi)->get(route('petugas.pelanggan.index'))->assertStatus(403);
    $this->actingAs($teknisi)->get(route('petugas.pelanggan.create'))->assertStatus(403);
    $this->actingAs($teknisi)->post(route('petugas.pelanggan.store'), [])->assertStatus(403);
    $this->actingAs($teknisi)->get(route('petugas.pelanggan.show', $pelanggan))->assertStatus(403);
    $this->actingAs($teknisi)->get(route('petugas.pelanggan.edit', $pelanggan))->assertStatus(403);
    $this->actingAs($teknisi)->put(route('petugas.pelanggan.update', $pelanggan), [])->assertStatus(403);
    $this->actingAs($teknisi)->delete(route('petugas.pelanggan.destroy', $pelanggan))->assertStatus(403);
});

test('petugas can view customer list with eager-loaded service counts and search filter', function () {
    $petugas = User::factory()->petugas()->create();

    $customer1 = Pelanggan::factory()->create([
        'nama' => 'Joko Susanto',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Pahlawan No. 12, Purwokerto',
    ]);

    $customer2 = Pelanggan::factory()->create([
        'nama' => 'Dewi Anggraeni',
        'no_telepon' => '085712345678',
        'alamat' => 'Jl. Sudirman No. 45, Banyumas',
    ]);

    // View customer index
    $response = $this->actingAs($petugas)->get(route('petugas.pelanggan.index'));
    $response->assertStatus(200);
    $response->assertSee('Joko Susanto');
    $response->assertSee('Dewi Anggraeni');
    $response->assertSee('081234567890');

    // Search by name
    $searchName = $this->actingAs($petugas)->get(route('petugas.pelanggan.index', ['q' => 'Joko']));
    $searchName->assertStatus(200);
    $searchName->assertSee('Joko Susanto');
    $searchName->assertDontSee('Dewi Anggraeni');

    // Search by phone
    $searchPhone = $this->actingAs($petugas)->get(route('petugas.pelanggan.index', ['q' => '085712345678']));
    $searchPhone->assertStatus(200);
    $searchPhone->assertSee('Dewi Anggraeni');
    $searchPhone->assertDontSee('Joko Susanto');
});

test('petugas can see customer detail page with service history', function () {
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create(['nama' => 'Bambang Triatmojo']);

    // Create a service record for this customer
    $servis = Servis::factory()->create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'merk_tipe' => 'Lenovo ThinkPad X280',
        'keluhan' => 'Layar bergaris vertikal',
    ]);

    $response = $this->actingAs($petugas)->get(route('petugas.pelanggan.show', $pelanggan));
    $response->assertStatus(200);
    $response->assertSee('Bambang Triatmojo');
    $response->assertSee('Lenovo ThinkPad X280');
    $response->assertSee('Layar bergaris vertikal');
    $response->assertSee($servis->no_servis);
});

test('petugas can see create form and store a new customer', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)->get(route('petugas.pelanggan.create'))->assertStatus(200);

    $response = $this->actingAs($petugas)->post(route('petugas.pelanggan.store'), [
        'nama' => 'Citra Lestari',
        'no_telepon' => '081399887766',
        'alamat' => 'Perum Griya Satria Blok B No. 7, Purwokerto',
    ]);

    $response->assertRedirect(route('petugas.pelanggan.index'));
    $response->assertSessionHas('success', 'Data berhasil disimpan');

    $this->assertDatabaseHas('pelanggan', [
        'nama' => 'Citra Lestari',
        'no_telepon' => '081399887766',
    ]);
});

test('storing customer validates required fields and unique phone number', function () {
    $petugas = User::factory()->petugas()->create();
    Pelanggan::factory()->create(['no_telepon' => '081299998888']);

    $response = $this->actingAs($petugas)->post(route('petugas.pelanggan.store'), [
        'nama' => '',
        'no_telepon' => '081299998888', // Duplicate
        'alamat' => '',
    ]);

    $response->assertSessionHasErrors(['nama', 'no_telepon', 'alamat']);
});

test('petugas can see edit form and update customer details', function () {
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create([
        'nama' => 'Nama Awal',
        'no_telepon' => '082111222333',
        'alamat' => 'Alamat Awal',
    ]);

    $this->actingAs($petugas)->get(route('petugas.pelanggan.edit', $pelanggan))->assertStatus(200);

    $response = $this->actingAs($petugas)->put(route('petugas.pelanggan.update', $pelanggan), [
        'nama' => 'Nama Baru Lengkap',
        'no_telepon' => '082111222444',
        'alamat' => 'Alamat Baru Lengkap',
    ]);

    $response->assertRedirect(route('petugas.pelanggan.index'));
    $response->assertSessionHas('success', 'Data berhasil disimpan');

    $pelanggan->refresh();
    expect($pelanggan->nama)->toBe('Nama Baru Lengkap')
        ->and($pelanggan->no_telepon)->toBe('082111222444')
        ->and($pelanggan->alamat)->toBe('Alamat Baru Lengkap');
});

test('updating customer allows keeping own phone number but rejects duplicate of other customer', function () {
    $petugas = User::factory()->petugas()->create();
    $custA = Pelanggan::factory()->create(['no_telepon' => '081122334455']);
    $custB = Pelanggan::factory()->create(['no_telepon' => '087788990011']);

    // Keeping own phone is permitted
    $responseSame = $this->actingAs($petugas)->put(route('petugas.pelanggan.update', $custA), [
        'nama' => 'Cust A Updated',
        'no_telepon' => '081122334455',
        'alamat' => 'Alamat Tetap',
    ]);
    $responseSame->assertSessionHasNoErrors();

    // Taking other customer's phone is rejected
    $responseDuplicate = $this->actingAs($petugas)->put(route('petugas.pelanggan.update', $custA), [
        'nama' => 'Cust A Updated',
        'no_telepon' => '087788990011',
        'alamat' => 'Alamat Tetap',
    ]);
    $responseDuplicate->assertSessionHasErrors(['no_telepon']);
});

test('cannot delete customer who has service records', function () {
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create(['nama' => 'Pelanggan Berservis']);

    Servis::factory()->create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
    ]);

    $response = $this->actingAs($petugas)->delete(route('petugas.pelanggan.destroy', $pelanggan));

    $response->assertSessionHas('error', 'Pelanggan tidak dapat dihapus karena memiliki riwayat servis.');
    $this->assertDatabaseHas('pelanggan', ['id_pelanggan' => $pelanggan->id_pelanggan]);
});

test('petugas can delete customer who has no service records', function () {
    $petugas = User::factory()->petugas()->create();
    $pelanggan = Pelanggan::factory()->create(['nama' => 'Pelanggan Tanpa Servis']);

    $response = $this->actingAs($petugas)->delete(route('petugas.pelanggan.destroy', $pelanggan));

    $response->assertRedirect(route('petugas.pelanggan.index'));
    $response->assertSessionHas('success', 'Data berhasil dihapus');
    $this->assertDatabaseMissing('pelanggan', ['id_pelanggan' => $pelanggan->id_pelanggan]);
});
