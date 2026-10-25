<?php

use App\Enums\StatusServis;
use App\Models\DetailSparepart;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Database\QueryException;

test('user model helpers, accessors, and relations work correctly', function () {
    $admin = User::factory()->admin()->create(['nama' => 'Bambang Sudarmanto']);
    $petugas = User::factory()->petugas()->create(['nama' => 'Dewi Petugas']);
    $teknisi = User::factory()->teknisi()->create(['nama' => 'Budi Teknisi']);

    expect($admin->isAdmin())->toBeTrue()
        ->and($admin->isPetugas())->toBeFalse()
        ->and($admin->role_label)->toBe('Administrator')
        ->and($admin->inisial)->toBe('BS');

    expect($petugas->isPetugas())->toBeTrue()
        ->and($petugas->inisial)->toBe('DP');

    expect($teknisi->isTeknisi())->toBeTrue()
        ->and($teknisi->inisial)->toBe('BT');

    $pelanggan = Pelanggan::factory()->create();

    $servis = Servis::create([
        'no_servis' => 'SRV-888',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'id_teknisi' => $teknisi->id_user,
        'jenis_perangkat' => 'Laptop',
        'merk_tipe' => 'ThinkPad T480',
        'keluhan' => 'Keyboard tidak merespons',
        'status' => StatusServis::Diterima,
        'tgl_masuk' => now()->toDateString(),
        'biaya_jasa' => 0,
        'total_biaya' => 0,
    ]);

    expect($petugas->servisSebagaiPetugas)->toHaveCount(1)
        ->and($teknisi->servisSebagaiTeknisi)->toHaveCount(1)
        ->and($servis->petugas->id_user)->toBe($petugas->id_user)
        ->and($servis->teknisi->id_user)->toBe($teknisi->id_user)
        ->and($servis->pelanggan->id_pelanggan)->toBe($pelanggan->id_pelanggan);
});

test('servis, sparepart, and detail_sparepart relations and casts work correctly', function () {
    $pelanggan = Pelanggan::factory()->create();
    $petugas = User::factory()->petugas()->create();
    $sparepart = Sparepart::factory()->create([
        'nama_sparepart' => 'SSD NVMe 256GB',
        'harga' => 350000,
        'stok' => 5,
    ]);

    $servis = Servis::create([
        'no_servis' => 'SRV-889',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'jenis_perangkat' => 'Laptop',
        'merk_tipe' => 'MacBook Air M1',
        'keluhan' => 'Baterai service recommended',
        'status' => StatusServis::Diperbaiki,
        'tgl_masuk' => now()->toDateString(),
        'biaya_jasa' => 150000,
        'total_biaya' => 500000,
    ]);

    $detail = DetailSparepart::create([
        'id_servis' => $servis->id_servis,
        'id_sparepart' => $sparepart->id_sparepart,
        'jumlah' => 1,
        'subtotal' => 350000,
    ]);

    expect($servis->detail)->toHaveCount(1)
        ->and($servis->detail->first()->sparepart->nama_sparepart)->toBe('SSD NVMe 256GB')
        ->and($sparepart->detailServis)->toHaveCount(1)
        ->and($sparepart->detailServis->first()->servis->no_servis)->toBe('SRV-889')
        ->and($detail->jumlah)->toBe(1)
        ->and($detail->subtotal)->toBe(350000);
});

test('foreign key restrict prevents deleting records that are in use', function () {
    $pelanggan = Pelanggan::factory()->create();
    $petugas = User::factory()->petugas()->create();

    Servis::create([
        'no_servis' => 'SRV-890',
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'jenis_perangkat' => 'Laptop',
        'merk_tipe' => 'Dell Latitude',
        'keluhan' => 'No power',
        'status' => StatusServis::Diterima,
        'tgl_masuk' => now()->toDateString(),
        'biaya_jasa' => 0,
        'total_biaya' => 0,
    ]);

    expect(fn () => $pelanggan->delete())->toThrow(QueryException::class);
});
