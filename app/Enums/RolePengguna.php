<?php

namespace App\Enums;

enum RolePengguna: string
{
    case Admin = 'admin';
    case Petugas = 'petugas';
    case Teknisi = 'teknisi';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Petugas => 'Petugas Servis',
            self::Teknisi => 'Teknisi',
        };
    }
}
