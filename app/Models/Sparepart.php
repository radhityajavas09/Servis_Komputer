<?php

namespace App\Models;

use Database\Factories\SparepartFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sparepart extends Model
{
    /** @use HasFactory<SparepartFactory> */
    use HasFactory;

    protected $table = 'sparepart';

    protected $primaryKey = 'id_sparepart';

    protected $fillable = [
        'nama_sparepart',
        'harga',
        'stok',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'stok' => 'integer',
        ];
    }

    public function detailServis(): HasMany
    {
        return $this->hasMany(DetailSparepart::class, 'id_sparepart', 'id_sparepart');
    }

    /**
     * Check if stock is low (< 5 and > 0).
     */
    public function isStokMenipis(): bool
    {
        return $this->stok > 0 && $this->stok < 5;
    }

    /**
     * Check if stock is completely depleted.
     */
    public function isStokHabis(): bool
    {
        return $this->stok <= 0;
    }

    /**
     * Get price formatted in Indonesian Rupiah.
     */
    public function getHargaRupiahAttribute(): string
    {
        return 'Rp '.number_format($this->harga, 0, ',', '.');
    }
}
