<?php

use App\Enums\RolePengguna;
use App\Enums\StatusServis;

test('status servis berikutnya transition matches strictly sequential order', function () {
    expect(StatusServis::Diterima->berikutnya())->toBe(StatusServis::Diperiksa)
        ->and(StatusServis::Diperiksa->berikutnya())->toBe(StatusServis::Diperbaiki)
        ->and(StatusServis::Diperbaiki->berikutnya())->toBe(StatusServis::Selesai)
        ->and(StatusServis::Selesai->berikutnya())->toBe(StatusServis::Diambil)
        ->and(StatusServis::Diambil->berikutnya())->toBeNull();
});

test('status servis has correct urutan, label, and warna', function () {
    expect(StatusServis::Diterima->urutan())->toBe(1)
        ->and(StatusServis::Diterima->label())->toBe('Diterima')
        ->and(StatusServis::Diterima->warna())->toBe('amber');

    expect(StatusServis::Diperiksa->urutan())->toBe(2)
        ->and(StatusServis::Diperiksa->label())->toBe('Dalam pemeriksaan')
        ->and(StatusServis::Diperiksa->warna())->toBe('blue');

    expect(StatusServis::Diperbaiki->urutan())->toBe(3)
        ->and(StatusServis::Diperbaiki->label())->toBe('Dalam perbaikan')
        ->and(StatusServis::Diperbaiki->warna())->toBe('purple');

    expect(StatusServis::Selesai->urutan())->toBe(4)
        ->and(StatusServis::Selesai->label())->toBe('Selesai')
        ->and(StatusServis::Selesai->warna())->toBe('green');

    expect(StatusServis::Diambil->urutan())->toBe(5)
        ->and(StatusServis::Diambil->label())->toBe('Diambil')
        ->and(StatusServis::Diambil->warna())->toBe('gray');
});

test('role pengguna has correct labels', function () {
    expect(RolePengguna::Admin->label())->toBe('Administrator')
        ->and(RolePengguna::Petugas->label())->toBe('Petugas Servis')
        ->and(RolePengguna::Teknisi->label())->toBe('Teknisi');
});
