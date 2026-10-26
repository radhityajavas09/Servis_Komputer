<?php

namespace App\Services;

use App\Enums\StatusServis;
use App\Models\DetailSparepart;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PengerjaanServisService
{
    /**
     * Simpan atau perbarui diagnosis, tindakan, biaya jasa, dan pemakaian sparepart dinamis.
     * Mengembalikan stok sparepart lama terlebih dahulu sebelum menerapkan stok baru (dalam DB transaction).
     *
     * @param  array{
     *     diagnosis: ?string,
     *     tindakan: ?string,
     *     biaya_jasa: int,
     *     spareparts?: array<int, array{id_sparepart: int, jumlah: int}>
     * }  $data
     */
    public function updateDiagnosis(Servis $servis, array $data, User $teknisi): Servis
    {
        // 1. Cek apakah servis sudah terkunci
        if (in_array($servis->status, [StatusServis::Selesai, StatusServis::Diambil], true)) {
            throw new InvalidArgumentException('Servis yang telah selesai atau diambil terkunci dan tidak dapat diubah lagi.');
        }

        return DB::transaction(function () use ($servis, $data, $teknisi) {
            // 2. Set teknisi penanggung jawab jika belum tercatat
            if (! $servis->id_teknisi) {
                $servis->id_teknisi = $teknisi->id_user;
            }

            // 3. Kembalikan stok dari detail sparepart lama
            $oldDetails = $servis->detail()->lockForUpdate()->get();
            foreach ($oldDetails as $oldDetail) {
                Sparepart::where('id_sparepart', $oldDetail->id_sparepart)
                    ->lockForUpdate()
                    ->increment('stok', $oldDetail->jumlah);
            }

            // Hapus detail lama
            $servis->detail()->delete();

            // 4. Proses pemakaian sparepart baru
            $totalSparepart = 0;
            $items = $data['spareparts'] ?? [];

            // Gabungkan jika ada ID sparepart yang dipilih berulang
            $consolidated = [];
            foreach ($items as $item) {
                $id = (int) ($item['id_sparepart'] ?? 0);
                $qty = (int) ($item['jumlah'] ?? 0);
                if ($id > 0 && $qty > 0) {
                    $consolidated[$id] = ($consolidated[$id] ?? 0) + $qty;
                }
            }

            foreach ($consolidated as $idSparepart => $jumlah) {
                /** @var Sparepart $sparepart */
                $sparepart = Sparepart::where('id_sparepart', $idSparepart)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Validasi kecukupan stok
                if ($sparepart->stok < $jumlah) {
                    throw new InvalidArgumentException("Stok {$sparepart->nama_sparepart} tidak cukup (tersisa {$sparepart->stok})");
                }

                // Kurangi stok di database
                $sparepart->decrement('stok', $jumlah);

                // Buat baris detail sparepart
                $subtotal = $sparepart->harga * $jumlah;
                $totalSparepart += $subtotal;

                DetailSparepart::create([
                    'id_servis' => $servis->id_servis,
                    'id_sparepart' => $sparepart->id_sparepart,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ]);
            }

            // 5. Update data servis utama
            $biayaJasa = (int) ($data['biaya_jasa'] ?? 0);
            $totalBiaya = $biayaJasa + $totalSparepart;

            $servis->update([
                'id_teknisi' => $servis->id_teknisi,
                'diagnosis' => $data['diagnosis'] ?? null,
                'tindakan' => $data['tindakan'] ?? null,
                'biaya_jasa' => $biayaJasa,
                'total_biaya' => $totalBiaya,
            ]);

            return $servis;
        });
    }

    /**
     * Memajukan status pengerjaan servis bertahap satu langkah ke depan:
     * - diterima -> diperiksa
     * - diperiksa -> diperbaiki
     * - diperbaiki -> selesai (wajib isi diagnosis & tindakan, tgl_selesai otomatis hari ini)
     */
    public function majuStatus(Servis $servis, User $teknisi): Servis
    {
        return DB::transaction(function () use ($servis, $teknisi) {
            // Set teknisi jika belum ada
            if (! $servis->id_teknisi) {
                $servis->id_teknisi = $teknisi->id_user;
            }

            $currentStatus = $servis->status;

            switch ($currentStatus) {
                case StatusServis::Diterima:
                    $nextStatus = StatusServis::Diperiksa;
                    $servis->update([
                        'status' => $nextStatus,
                        'id_teknisi' => $servis->id_teknisi,
                    ]);
                    break;

                case StatusServis::Diperiksa:
                    $nextStatus = StatusServis::Diperbaiki;
                    $servis->update([
                        'status' => $nextStatus,
                        'id_teknisi' => $servis->id_teknisi,
                    ]);
                    break;

                case StatusServis::Diperbaiki:
                    // Syarat menjadi selesai: diagnosis dan tindakan sudah terisi
                    if (empty(trim((string) $servis->diagnosis)) || empty(trim((string) $servis->tindakan))) {
                        throw new InvalidArgumentException('Diagnosis dan tindakan perbaikan wajib diisi sebelum menyelesaikan status servis.');
                    }

                    $nextStatus = StatusServis::Selesai;
                    $servis->update([
                        'status' => $nextStatus,
                        'id_teknisi' => $servis->id_teknisi,
                        'tgl_selesai' => now()->toDateString(),
                    ]);
                    break;

                case StatusServis::Selesai:
                    throw new InvalidArgumentException('Servis sudah selesai. Penyerahan ke status "Diambil" hanya dapat dilakukan oleh Petugas.');
                case StatusServis::Diambil:
                    throw new InvalidArgumentException('Servis telah diambil oleh pelanggan dan tidak dapat diubah statusnya lagi.');
                default:
                    throw new InvalidArgumentException('Status servis saat ini tidak valid.');
            }

            return $servis;
        });
    }
}
