<?php

namespace Database\Factories;

use App\Enums\StatusServis;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Servis>
 */
class ServisFactory extends Factory
{
    protected $model = Servis::class;

    public function definition(): array
    {
        return [
            'no_servis' => 'SRV-'.fake()->unique()->numberBetween(100, 999),
            'id_pelanggan' => Pelanggan::factory(),
            'id_petugas' => User::factory()->petugas(),
            'id_teknisi' => null,
            'jenis_perangkat' => 'Laptop',
            'merk_tipe' => fake()->randomElement(['ASUS VivoBook 14', 'Lenovo IdeaPad Slim 3', 'Acer Aspire 5']),
            'keluhan' => fake()->sentence(8),
            'diagnosis' => null,
            'tindakan' => null,
            'status' => StatusServis::Diterima,
            'tgl_masuk' => now()->toDateString(),
            'tgl_selesai' => null,
            'tgl_diambil' => null,
            'biaya_jasa' => 0,
            'total_biaya' => 0,
        ];
    }
}
