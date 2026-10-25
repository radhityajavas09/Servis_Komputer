<?php

namespace App\Models;

use App\Enums\RolePengguna;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => RolePengguna::class,
        ];
    }

    public function servisSebagaiPetugas(): HasMany
    {
        return $this->hasMany(Servis::class, 'id_petugas', 'id_user');
    }

    public function servisSebagaiTeknisi(): HasMany
    {
        return $this->hasMany(Servis::class, 'id_teknisi', 'id_user');
    }

    public function isAdmin(): bool
    {
        return $this->role === RolePengguna::Admin || $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === RolePengguna::Petugas || $this->role === 'petugas';
    }

    public function isTeknisi(): bool
    {
        return $this->role === RolePengguna::Teknisi || $this->role === 'teknisi';
    }

    public function getRoleLabelAttribute(): string
    {
        if ($this->role instanceof RolePengguna) {
            return $this->role->label();
        }

        return RolePengguna::tryFrom((string) $this->role)?->label() ?? (string) $this->role;
    }

    public function getInisialAttribute(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->nama));
        if (! empty($words) && count($words) >= 2 && ! empty($words[0]) && ! empty($words[1])) {
            return strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1));
        }

        $clean = trim((string) $this->nama);

        return strtoupper(mb_substr($clean !== '' ? $clean : 'US', 0, 2));
    }
}
