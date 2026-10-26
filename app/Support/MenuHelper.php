<?php

namespace App\Support;

use App\Enums\RolePengguna;

class MenuHelper
{
    /**
     * Get menu items for a specific role or string role.
     *
     * @return array<int, array{label: string, route: string, icon: string, active_patterns: array<int, string>}>
     */
    public static function getMenuForRole(RolePengguna|string|null $role): array
    {
        $roleValue = $role instanceof RolePengguna ? $role->value : (string) $role;

        return match ($roleValue) {
            'admin' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'icon' => 'chart',
                    'active_patterns' => ['admin.dashboard'],
                ],
                [
                    'label' => 'Data User',
                    'route' => 'admin.user.index',
                    'icon' => 'user',
                    'active_patterns' => ['admin.user.*'],
                ],
                [
                    'label' => 'Data Sparepart',
                    'route' => 'admin.sparepart.index',
                    'icon' => 'package',
                    'active_patterns' => ['admin.sparepart.*'],
                ],
                [
                    'label' => 'Laporan Servis',
                    'route' => 'admin.laporan.index',
                    'icon' => 'clipboard',
                    'active_patterns' => ['admin.laporan.*'],
                ],
                [
                    'label' => 'Ubah Profil',
                    'route' => 'profil.edit',
                    'icon' => 'pencil',
                    'active_patterns' => ['profil.*'],
                ],
            ],
            'petugas' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'petugas.dashboard',
                    'icon' => 'chart',
                    'active_patterns' => ['petugas.dashboard'],
                ],
                [
                    'label' => 'Data Pelanggan',
                    'route' => 'petugas.pelanggan.index',
                    'icon' => 'user',
                    'active_patterns' => ['petugas.pelanggan.*'],
                ],
                [
                    'label' => 'Pendaftaran Servis',
                    'route' => 'petugas.servis.tambah',
                    'icon' => 'plus',
                    'active_patterns' => ['petugas.servis.tambah'],
                ],
                [
                    'label' => 'Data Servis',
                    'route' => 'petugas.servis.index',
                    'icon' => 'clipboard',
                    'active_patterns' => ['petugas.servis.index', 'petugas.servis.detail', 'petugas.servis.serahkan'],
                ],
                [
                    'label' => 'Ubah Profil',
                    'route' => 'profil.edit',
                    'icon' => 'pencil',
                    'active_patterns' => ['profil.*'],
                ],
            ],
            'teknisi' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'teknisi.dashboard',
                    'icon' => 'chart',
                    'active_patterns' => ['teknisi.dashboard'],
                ],
                [
                    'label' => 'Daftar Servis Masuk',
                    'route' => 'teknisi.servis.index',
                    'icon' => 'wrench',
                    'active_patterns' => ['teknisi.servis.*'],
                ],
                [
                    'label' => 'Ubah Profil',
                    'route' => 'profil.edit',
                    'icon' => 'pencil',
                    'active_patterns' => ['profil.*'],
                ],
            ],
            default => [],
        };
    }
}
