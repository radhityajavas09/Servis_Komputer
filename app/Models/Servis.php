<?php

namespace App\Models;

use App\Enums\StatusServis;
use Database\Factories\ServisFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servis extends Model
{
    /** @use HasFactory<ServisFactory> */
    use HasFactory;

    protected $table = 'servis';

    protected $primaryKey = 'id_servis';

    protected $fillable = [
        'no_servis',
        'id_pelanggan',
        'id_petugas',
        'id_teknisi',
        'jenis_perangkat',
        'merk_tipe',
        'keluhan',
        'diagnosis',
        'tindakan',
        'status',
        'tgl_masuk',
        'tgl_selesai',
        'tgl_diambil',
        'biaya_jasa',
        'total_biaya',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusServis::class,
            'tgl_masuk' => 'date',
            'tgl_selesai' => 'date',
            'tgl_diambil' => 'date',
            'biaya_jasa' => 'integer',
            'total_biaya' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'no_servis';
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_petugas', 'id_user');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_teknisi', 'id_user');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailSparepart::class, 'id_servis', 'id_servis');
    }
}
