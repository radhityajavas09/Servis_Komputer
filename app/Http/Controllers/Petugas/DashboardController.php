<?php

namespace App\Http\Controllers\Petugas;

use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Servis;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Petugas dashboard focusing on customer service, intake, and device handovers.
     */
    public function __invoke(): View
    {
        // 4 Main metrics for Petugas
        $servisHariIni = Servis::whereDate('tgl_masuk', now()->toDateString())->count();
        $menungguAntrean = Servis::where('status', StatusServis::Diterima)->count();
        $siapDiserahkan = Servis::where('status', StatusServis::Selesai)->count();
        $sudahDiambil = Servis::where('status', StatusServis::Diambil)->count();

        // Services ready for handover to customer
        $antreanSerah = Servis::with(['pelanggan', 'teknisi'])
            ->where('status', StatusServis::Selesai)
            ->latest('tgl_selesai')
            ->take(5)
            ->get();

        // Recent registrations
        $servisTerbaru = Servis::with(['pelanggan', 'teknisi'])
            ->latest('id_servis')
            ->take(5)
            ->get();

        // Recent registered customers
        $pelangganTerbaru = Pelanggan::withCount('servis')
            ->latest('id_pelanggan')
            ->take(5)
            ->get();

        return view('petugas.dashboard', [
            'servisHariIni' => $servisHariIni,
            'menungguAntrean' => $menungguAntrean,
            'siapDiserahkan' => $siapDiserahkan,
            'sudahDiambil' => $sudahDiambil,
            'antreanSerah' => $antreanSerah,
            'servisTerbaru' => $servisTerbaru,
            'pelangganTerbaru' => $pelangganTerbaru,
        ]);
    }
}
