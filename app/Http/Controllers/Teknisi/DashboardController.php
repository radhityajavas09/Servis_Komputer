<?php

namespace App\Http\Controllers\Teknisi;

use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Models\Servis;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Teknisi dashboard focusing on active workbench jobs, queue, and completed repairs.
     */
    public function __invoke(Request $request): View
    {
        $teknisiId = $request->user()->id_user;

        // 4 Key metrics for the technician
        $antreanMasuk = Servis::where('status', StatusServis::Diterima)->count();
        $sedangSayaKerjakan = Servis::where('id_teknisi', $teknisiId)
            ->whereIn('status', [StatusServis::Diperiksa, StatusServis::Diperbaiki])
            ->count();
        $selesaiOlehSaya = Servis::where('id_teknisi', $teknisiId)
            ->whereIn('status', [StatusServis::Selesai, StatusServis::Diambil])
            ->count();
        $totalSelesaiBengkel = Servis::whereIn('status', [StatusServis::Selesai, StatusServis::Diambil])
            ->count();

        // Active repairs assigned to this technician
        $pekerjaanAktif = Servis::with(['pelanggan'])
            ->where('id_teknisi', $teknisiId)
            ->whereIn('status', [StatusServis::Diperiksa, StatusServis::Diperbaiki])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Fresh incoming tickets waiting for inspection
        $antreanMenunggu = Servis::with(['pelanggan'])
            ->where('status', StatusServis::Diterima)
            ->latest('id_servis')
            ->take(5)
            ->get();

        // Recently completed jobs by this technician
        $riwayatSelesai = Servis::with(['pelanggan'])
            ->where('id_teknisi', $teknisiId)
            ->whereIn('status', [StatusServis::Selesai, StatusServis::Diambil])
            ->latest('tgl_selesai')
            ->take(5)
            ->get();

        return view('teknisi.dashboard', [
            'antreanMasuk' => $antreanMasuk,
            'sedangSayaKerjakan' => $sedangSayaKerjakan,
            'selesaiOlehSaya' => $selesaiOlehSaya,
            'totalSelesaiBengkel' => $totalSelesaiBengkel,
            'pekerjaanAktif' => $pekerjaanAktif,
            'antreanMenunggu' => $antreanMenunggu,
            'riwayatSelesai' => $riwayatSelesai,
        ]);
    }
}
