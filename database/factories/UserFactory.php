<?php

namespace Database\Factories;

use App\Enums\RolePengguna;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => RolePengguna::Teknisi,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => RolePengguna::Admin,
        ]);
    }

    public function petugas(): static
    {
        return $this->state(fn () => [
            'role' => RolePengguna::Petugas,
        ]);
    }

    public function teknisi(): static
    {
        return $this->state(fn () => [
            'role' => RolePengguna::Teknisi,
        ]);
    }
}
