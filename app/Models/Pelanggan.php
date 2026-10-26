<?php

namespace App\Models;

use Database\Factories\PelangganFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    /** @use HasFactory<PelangganFactory> */
    use HasFactory;

    protected $table = 'pelanggan';

    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        'nama',
        'no_telepon',
        'alamat',
    ];

    public function servis(): HasMany
    {
        return $this->hasMany(Servis::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Get two-letter uppercase initial for customer avatar.
     */
    public function getInisialAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->nama));
        $first = strtoupper(substr($words[0] ?? '', 0, 1));
        $second = isset($words[1]) ? strtoupper(substr($words[1], 0, 1)) : '';

        return $first.$second ?: 'PL';
    }

    /**
     * Get sanitized WhatsApp link for customer phone number.
     */
    public function getWaLinkAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->no_telepon);
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return 'https://wa.me/'.$phone;
    }
}
