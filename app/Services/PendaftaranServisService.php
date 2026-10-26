<?php

namespace App\Services;

use App\Enums\StatusServis;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PendaftaranServisService
{
    public function __construct(
        protected NomorServisGenerator $nomorGenerator
    ) {}

    /**
     * Daftarkan servis baru di dalam transaksi database yang aman.
     * Menggunakan data pelanggan yang sudah ada jika nomor telepon cocok, atau membuat baru.
     *
     * @param  array{
     *     nama: string,
     *     no_telepon: string,
     *     alamat: string,
     *     jenis_perangkat: string,
     *     merk_tipe: string,
     *     keluhan: string
     * }  $data
     */
    public function daftarkan(array $data, User $petugas): Servis
    {
        return DB::transaction(function () use ($data, $petugas) {
            // 1. Cari atau daftarkan pelanggan berdasarkan nomor telepon
            $pelanggan = Pelanggan::where('no_telepon', $data['no_telepon'])->first();

            if (! $pelanggan) {
                $pelanggan = Pelanggan::create([
                    'nama' => $data['nama'],
                    'no_telepon' => $data['no_telepon'],
                    'alamat' => $data['alamat'],
                ]);
            } else {
                // Perbarui nama atau alamat jika terdapat perubahan terbaru
                $pelanggan->update([
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'],
                ]);
            }

            // 2. Generate nomor servis unik secara aman dengan lockForUpdate
            $noServis = $this->nomorGenerator->generate();

            // 3. Simpan tiket servis dengan status awal 'diterima'
            return Servis::create([
                'no_servis' => $noServis,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_petugas' => $petugas->id_user,
                'id_teknisi' => null,
                'jenis_perangkat' => $data['jenis_perangkat'],
                'merk_tipe' => $data['merk_tipe'],
                'keluhan' => $data['keluhan'],
                'diagnosis' => null,
                'tindakan' => null,
                'status' => StatusServis::Diterima,
                'tgl_masuk' => now()->toDateString(),
                'tgl_selesai' => null,
                'tgl_diambil' => null,
                'biaya_jasa' => 0,
                'total_biaya' => 0,
            ]);
        });
    }
}
