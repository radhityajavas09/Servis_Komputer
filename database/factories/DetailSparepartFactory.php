<?php

namespace Database\Factories;

use App\Models\DetailSparepart;
use App\Models\Servis;
use App\Models\Sparepart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetailSparepart>
 */
class DetailSparepartFactory extends Factory
{
    protected $model = DetailSparepart::class;

    public function definition(): array
    {
        $harga = 100000;
        $jumlah = 1;

        return [
            'id_servis' => Servis::factory(),
            'id_sparepart' => Sparepart::factory(),
            'jumlah' => $jumlah,
            'subtotal' => $harga * $jumlah,
        ];
    }
}
