<?php

namespace Database\Factories;

use App\Models\Sparepart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sparepart>
 */
class SparepartFactory extends Factory
{
    protected $model = Sparepart::class;

    public function definition(): array
    {
        return [
            'nama_sparepart' => fake()->words(3, true),
            'harga' => fake()->numberBetween(50000, 1500000),
            'stok' => fake()->numberBetween(1, 25),
        ];
    }
}
