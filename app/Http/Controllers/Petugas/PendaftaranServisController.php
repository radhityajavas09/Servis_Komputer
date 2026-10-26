<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Petugas\StorePendaftaranServisRequest;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Services\NomorServisGenerator;
use App\Services\PendaftaranServisService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PendaftaranServisController extends Controller
{
    /**
     * Show the service registration form with customer list for autofill.
     */
    public function create(NomorServisGenerator $generator): View
    {
        $pelangganList = Pelanggan::query()
            ->orderBy('nama')
            ->get(['id_pelanggan', 'nama', 'no_telepon', 'alamat']);

        $latestNoServis = Servis::query()->orderByDesc('id_servis')->value('no_servis');
        $nextNoServis = $generator->nextFrom($latestNoServis);

        return view('petugas.servis.pendaftaran', [
            'pelangganList' => $pelangganList,
            'nextNoServis' => $nextNoServis,
        ]);
    }

    /**
     * Store a newly registered service in storage.
     */
    public function store(
        StorePendaftaranServisRequest $request,
        PendaftaranServisService $service
    ): RedirectResponse {
        $servis = $service->daftarkan($request->validated(), $request->user());

        return redirect()
            ->route('petugas.servis.tanda-terima', $servis)
            ->with('success', 'Pendaftaran servis '.$servis->no_servis.' berhasil disimpan. Silakan cetak tanda terima.');
    }

    /**
     * Display printable receipt for a registered service.
     */
    public function tandaTerima(Servis $servis): View
    {
        $servis->load(['pelanggan', 'petugas']);

        return view('petugas.servis.tanda-terima', [
            'servis' => $servis,
        ]);
    }
}
