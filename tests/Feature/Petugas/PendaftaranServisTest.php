<?php

use App\Enums\StatusServis;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Tahap 9: Petugas - Pendaftaran Servis & Tanda Terima Tests
|--------------------------------------------------------------------------
*/

test('guests are redirected to login when accessing service registration', function () {
    $servis = Servis::factory()->create();

    $this->get(route('petugas.servis.tambah'))->assertRedirect('/login');
    $this->post(route('petugas.servis.store'), [])->assertRedirect('/login');
    $this->get(route('petugas.servis.tanda-terima', $servis))->assertRedirect('/login');
});

test('teknisi receives 403 when accessing service registration', function () {
    $teknisi = User::factory()->teknisi()->create();
    $servis = Servis::factory()->create();

    $this->actingAs($teknisi)->get(route('petugas.servis.tambah'))->assertStatus(403);
    $this->actingAs($teknisi)->post(route('petugas.servis.store'), [])->assertStatus(403);
});

test('petugas can view service registration form with customer list and next number preview', function () {
    $petugas = User::factory()->petugas()->create();
    Pelanggan::factory()->create(['nama' => 'Pelanggan Langganan', 'no_telepon' => '081299998888']);

    $response = $this->actingAs($petugas)->get(route('petugas.servis.tambah'));

    $response->assertStatus(200);
    $response->assertSee('Pendaftaran Servis Baru');
    $response->assertSee('Pelanggan Langganan');
    $response->assertSee('081299998888');
    $response->assertSee('Estimasi No:');
});

test('petugas can register new service with a new customer', function () {
    $petugas = User::factory()->petugas()->create();

    $payload = [
        'nama' => 'Rian Hidayat',
        'no_telepon' => '087711223344',
        'alamat' => 'Jl. Overste Isdiman No. 20, Purwokerto',
        'jenis_perangkat' => 'Laptop',
        'merk_tipe' => 'ASUS ROG Strix G15',
        'keluhan' => 'Kipas pendingin berbunyi keras dan overheat saat bermain game',
    ];

    $response = $this->actingAs($petugas)->post(route('petugas.servis.store'), $payload);

    // Assert customer was automatically created
    $this->assertDatabaseHas('pelanggan', [
        'nama' => 'Rian Hidayat',
        'no_telepon' => '087711223344',
    ]);

    $customer = Pelanggan::where('no_telepon', '087711223344')->first();
    expect($customer)->not->toBeNull();

    // Assert service was created with correct fields
    $this->assertDatabaseHas('servis', [
        'id_pelanggan' => $customer->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'id_teknisi' => null,
        'jenis_perangkat' => 'Laptop',
        'merk_tipe' => 'ASUS ROG Strix G15',
        'status' => StatusServis::Diterima->value,
        'biaya_jasa' => 0,
        'total_biaya' => 0,
    ]);

    $createdService = Servis::where('id_pelanggan', $customer->id_pelanggan)->first();
    expect($createdService)->not->toBeNull()
        ->and($createdService->tgl_masuk->format('Y-m-d'))->toBe(now()->format('Y-m-d'));

    // Assert redirected to receipt page
    $response->assertRedirect(route('petugas.servis.tanda-terima', $createdService));
    $response->assertSessionHas('success');
});

test('petugas registering service with existing customer phone number does not duplicate customer', function () {
    $petugas = User::factory()->petugas()->create();

    $existingCustomer = Pelanggan::factory()->create([
        'nama' => 'Pelanggan Setia Lama',
        'no_telepon' => '085811223344',
        'alamat' => 'Alamat Lama',
    ]);

    $initialCustomerCount = Pelanggan::count();

    $payload = [
        'nama' => 'Pelanggan Setia Baru', // Updated name
        'no_telepon' => '085811223344',   // Existing phone
        'alamat' => 'Alamat Baru Lengkap', // Updated address
        'jenis_perangkat' => 'PC Desktop',
        'merk_tipe' => 'Custom PC Core i7 RTX 4060',
        'keluhan' => 'Blue screen CRITICAL_PROCESS_DIED saat boot Windows',
    ];

    $response = $this->actingAs($petugas)->post(route('petugas.servis.store'), $payload);

    // Customer count must remain the same (no duplicate created)
    expect(Pelanggan::count())->toBe($initialCustomerCount);

    // Customer details updated
    $existingCustomer->refresh();
    expect($existingCustomer->nama)->toBe('Pelanggan Setia Baru')
        ->and($existingCustomer->alamat)->toBe('Alamat Baru Lengkap');

    // Service linked to this existing customer
    $latestServis = $existingCustomer->servis()->latest('id_servis')->first();
    expect($latestServis)->not->toBeNull()
        ->and($latestServis->merk_tipe)->toBe('Custom PC Core i7 RTX 4060');

    $response->assertRedirect(route('petugas.servis.tanda-terima', $latestServis));
});

test('registering service validates all required fields and phone number format', function () {
    $petugas = User::factory()->petugas()->create();

    $response = $this->actingAs($petugas)->post(route('petugas.servis.store'), [
        'nama' => '',
        'no_telepon' => 'invalid-abc',
        'alamat' => '',
        'jenis_perangkat' => '',
        'merk_tipe' => '',
        'keluhan' => '',
    ]);

    $response->assertSessionHasErrors([
        'nama',
        'no_telepon',
        'alamat',
        'jenis_perangkat',
        'merk_tipe',
        'keluhan',
    ]);
});

test('petugas can view printable receipt with complete details and print layout', function () {
    $petugas = User::factory()->petugas()->create(['nama' => 'Rina Petugas']);
    $pelanggan = Pelanggan::factory()->create([
        'nama' => 'Slamet Riyadi',
        'no_telepon' => '081233445566',
        'alamat' => 'Jl. Martadireja No. 99, Purwokerto',
    ]);

    $servis = Servis::factory()->create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'jenis_perangkat' => 'Laptop',
        'merk_tipe' => 'Lenovo Legion 5',
        'keluhan' => 'Keyboard tidak merespon sebagian tombol dan backlight mati',
        'status' => StatusServis::Diterima,
    ]);

    $response = $this->actingAs($petugas)->get(route('petugas.servis.tanda-terima', $servis));

    $response->assertStatus(200);
    $response->assertSee('TANDA TERIMA SERVIS');
    $response->assertSee($servis->no_servis);
    $response->assertSee('Slamet Riyadi');
    $response->assertSee('081233445566');
    $response->assertSee('Lenovo Legion 5');
    $response->assertSee('Keyboard tidak merespon sebagian tombol');
    $response->assertSee('Rina Petugas');
    $response->assertSee('Cetak Dokumen / Simpan PDF');
});
