<?php

namespace App\Services;

use App\Enums\StatusServis;
use App\Models\Servis;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PenyerahanServisService
{
    /**
     * Memproses penyerahan perangkat servis ke pelanggan.
     * Hanya dapat dilakukan jika status servis adalah 'selesai'.
     */
    public function serahkan(Servis $servis): Servis
    {
        if ($servis->status !== StatusServis::Selesai) {
            throw new InvalidArgumentException('Perangkat hanya dapat diserahkan jika pengerjaan sudah berstatus selesai.');
        }

        return DB::transaction(function () use ($servis) {
            $servis->update([
                'status' => StatusServis::Diambil,
                'tgl_diambil' => now()->toDateString(),
            ]);

            return $servis;
        });
    }
}
