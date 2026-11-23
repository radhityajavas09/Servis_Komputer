<?php

namespace App\Http\Controllers;

use App\Enums\StatusServis;
use App\Models\Servis;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LacakServisController extends Controller
{
    /**
     * Halaman publik untuk melacak status servis perangkat menggunakan nomor tiket / kode akses.
     * Tidak memerlukan nomor WhatsApp lagi, cukup nomor tiket saja.
     */
    public function index(Request $request): View
    {
        $noServis = $request->query('no_servis');
        $servis = null;
        $error = null;
        $maskedNama = null;
        $maskedTelepon = null;
        $garansiInfo = null;
        $waLink = null;

        if ($noServis) {
            $queryNo = strtoupper(trim($noServis));

            // Jika pelanggan hanya mengetikkan angka (misal: "1" atau "002"), otomatis format ke SRV-001
            if (preg_match('/^\d+$/', $queryNo)) {
                $queryNo = 'SRV-'.str_pad($queryNo, 3, '0', STR_PAD_LEFT);
            }

            $servis = Servis::with(['pelanggan', 'teknisi', 'petugas', 'detail.sparepart'])
                ->where('no_servis', $queryNo)
                ->first();

            if (! $servis) {
                $error = "Nomor tiket servis '{$queryNo}' tidak ditemukan. Silakan periksa kembali kode tiket yang Anda masukkan.";
            } else {
                // Masking nama dan nomor telepon untuk privasi
                $namaParts = explode(' ', trim($servis->pelanggan->nama));
                $maskedNama = count($namaParts) > 1
                    ? $namaParts[0].' '.substr($namaParts[1], 0, 1).'***'
                    : $namaParts[0];

                $rawPhone = $servis->pelanggan->no_telepon;
                $phoneLen = strlen($rawPhone);
                $maskedTelepon = $phoneLen > 7
                    ? substr($rawPhone, 0, 4).'****'.substr($rawPhone, -3)
                    : $rawPhone;

                // Hitung masa garansi toko (30 hari setelah selesai/diambil)
                if (in_array($servis->status, [StatusServis::Selesai, StatusServis::Diambil])) {
                    $tglPatokan = $servis->tgl_diambil ?? $servis->tgl_selesai ?? now()->toDateString();
                    $tglGaransi = Carbon::parse($tglPatokan)->addDays(30);
                    $sisaHari = (int) now()->diffInDays($tglGaransi, false);

                    $garansiInfo = [
                        'berlaku_sampai' => $tglGaransi->translatedFormat('d M Y'),
                        'sisa_hari' => $sisaHari,
                        'aktif' => $sisaHari >= 0,
                    ];
                }

                // Tautan WhatsApp resmi untuk konfirmasi langsung
                $pesanWa = urlencode("Halo Admin Servis Komputer, saya pemilik tiket servis *{$servis->no_servis}* ({$servis->merk_tipe}). Saya ingin konfirmasi perihal pengerjaan/biaya perangkat saya.");
                $waLink = "https://wa.me/6285601448610?text={$pesanWa}";
            }
        }

        return view('lacak.index', [
            'searchQuery' => $noServis,
            'servis' => $servis,
            'error' => $error,
            'maskedNama' => $maskedNama,
            'maskedTelepon' => $maskedTelepon,
            'garansiInfo' => $garansiInfo,
            'waLink' => $waLink,
        ]);
    }

    /**
     * Akses langsung via URL: /lacak/SRV-001
     */
    public function show(string $no_servis): View
    {
        return $this->index(new Request(['no_servis' => $no_servis]));
    }

    /**
     * Pelanggan menambahkan catatan tambahan atau memperbarui keluhan pada tiket yang sedang aktif.
     */
    public function updateKeluhan(Request $request, string $no_servis): RedirectResponse
    {
        $queryNo = strtoupper(trim($no_servis));
        $servis = Servis::where('no_servis', $queryNo)->firstOrFail();

        // Jika pengerjaan sudah selesai atau unit sudah diambil, catatan terkunci
        if (in_array($servis->status, [StatusServis::Selesai, StatusServis::Diambil])) {
            return redirect()
                ->route('lacak.show', $servis->no_servis)
                ->with('error', 'Perangkat sudah berstatus selesai/diambil sehingga catatan pengerjaan tidak dapat diubah lagi.');
        }

        $validated = $request->validate([
            'catatan_tambahan' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'catatan_tambahan.required' => 'Catatan tambahan wajib diisi.',
            'catatan_tambahan.min' => 'Catatan tambahan minimal 3 karakter.',
            'catatan_tambahan.max' => 'Catatan tambahan maksimal 1000 karakter.',
        ]);

        $tanggal = now()->translatedFormat('d M H:i');
        $catatanBaru = trim($validated['catatan_tambahan']);

        $servis->keluhan = $servis->keluhan
            ? $servis->keluhan."\n\n[Catatan Tambahan Pelanggan • {$tanggal}]: ".$catatanBaru
            : "[Catatan Pelanggan • {$tanggal}]: ".$catatanBaru;

        $servis->save();

        return redirect()
            ->route('lacak.show', $servis->no_servis)
            ->with('success', 'Catatan tambahan Anda berhasil disimpan dan langsung diteruskan ke teknisi!');
    }
}
