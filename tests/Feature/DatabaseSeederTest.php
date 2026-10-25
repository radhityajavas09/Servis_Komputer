<?php

use App\Enums\RolePengguna;
use App\Enums\StatusServis;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

test('database seeder produces exact expected counts, accounts, and distribution', function () {
    $this->seed(DatabaseSeeder::class);

    // 1. Tiga User
    expect(User::count())->toBe(3);

    $admin = User::where('email', 'admin@servis.test')->first();
    $petugas = User::where('email', 'petugas@servis.test')->first();
    $teknisi = User::where('email', 'teknisi@servis.test')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->role)->toBe(RolePengguna::Admin)
        ->and(Hash::check('password', $admin->password))->toBeTrue();

    expect($petugas)->not->toBeNull()
        ->and($petugas->role)->toBe(RolePengguna::Petugas)
        ->and(Hash::check('password', $petugas->password))->toBeTrue();

    expect($teknisi)->not->toBeNull()
        ->and($teknisi->role)->toBe(RolePengguna::Teknisi)
        ->and(Hash::check('password', $teknisi->password))->toBeTrue();

    // 2. Delapan Sparepart
    expect(Sparepart::count())->toBe(8);

    // Cek item stok menipis (<= 3) dan habis (= 0)
    expect(Sparepart::where('stok', '<=', 3)->where('stok', '>', 0)->count())->toBeGreaterThanOrEqual(1)
        ->and(Sparepart::where('stok', 0)->count())->toBeGreaterThanOrEqual(1);

    // 3. Enam Pelanggan
    expect(Pelanggan::count())->toBe(6);

    // 4. Tepat 7 Servis
    expect(Servis::count())->toBe(7);

    // Distribusi status: 1 diterima, 1 diperiksa, 2 diperbaiki, 2 selesai, 1 diambil
    expect(Servis::where('status', StatusServis::Diterima)->count())->toBe(1)
        ->and(Servis::where('status', StatusServis::Diperiksa)->count())->toBe(1)
        ->and(Servis::where('status', StatusServis::Diperbaiki)->count())->toBe(2)
        ->and(Servis::where('status', StatusServis::Selesai)->count())->toBe(2)
        ->and(Servis::where('status', StatusServis::Diambil)->count())->toBe(1);

    // Dua servis pertama sesuai spesifikasi
    $s1 = Servis::where('no_servis', 'SRV-001')->first();
    $s2 = Servis::where('no_servis', 'SRV-002')->first();

    expect($s1)->not->toBeNull()
        ->and($s1->pelanggan->nama)->toBe('Nadia Putri')
        ->and($s1->merk_tipe)->toBe('ASUS VivoBook 14 · X1404ZA')
        ->and($s1->tgl_masuk->format('Y-m-d'))->toBe('2026-10-21');

    expect($s2)->not->toBeNull()
        ->and($s2->pelanggan->nama)->toBe('Rizky Maulana')
        ->and($s2->merk_tipe)->toBe('Lenovo IdeaPad Slim 3 · 15IAH8')
        ->and($s2->tgl_masuk->format('Y-m-d'))->toBe('2026-10-21');

    // Konsistensi total biaya = biaya_jasa + sum(detail subtotal)
    foreach (Servis::with('detail')->get() as $srv) {
        $detailSum = $srv->detail->sum('subtotal');
        expect($srv->total_biaya)->toBe($srv->biaya_jasa + $detailSum);
    }
});
