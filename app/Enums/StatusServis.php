<?php

namespace App\Enums;

enum StatusServis: string
{
    case Diterima = 'diterima';
    case Diperiksa = 'diperiksa';
    case Diperbaiki = 'diperbaiki';
    case Selesai = 'selesai';
    case Diambil = 'diambil';

    public function label(): string
    {
        return match ($this) {
            self::Diterima => 'Diterima',
            self::Diperiksa => 'Dalam pemeriksaan',
            self::Diperbaiki => 'Dalam perbaikan',
            self::Selesai => 'Selesai',
            self::Diambil => 'Diambil',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Diterima => 'amber',
            self::Diperiksa => 'blue',
            self::Diperbaiki => 'purple',
            self::Selesai => 'green',
            self::Diambil => 'gray',
        };
    }

    public function berikutnya(): ?self
    {
        return match ($this) {
            self::Diterima => self::Diperiksa,
            self::Diperiksa => self::Diperbaiki,
            self::Diperbaiki => self::Selesai,
            self::Selesai => self::Diambil,
            self::Diambil => null,
        };
    }

    public function urutan(): int
    {
        return match ($this) {
            self::Diterima => 1,
            self::Diperiksa => 2,
            self::Diperbaiki => 3,
            self::Selesai => 4,
            self::Diambil => 5,
        };
    }
}
