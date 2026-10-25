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
}
