<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RolePengguna;
use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main administrator dashboard with statistics, recent activities, and stock warnings.
     */
    public function __invoke(): View
    {
        // 4 Main metrics for top stat cards
        $totalServis = Servis::count();
        $baruDiterima = Servis::where('status', StatusServis::Diterima)->count();
        $dalamPengerjaan = Servis::whereIn('status', [
            StatusServis::Diperiksa,
            StatusServis::Diperbaiki,
        ])->count();
        $selesaiDanDiambil = Servis::whereIn('status', [
            StatusServis::Selesai,
            StatusServis::Diambil,
        ])->count();

        // Secondary metrics
        $totalPendapatan = Servis::where('status', StatusServis::Diambil)->sum('total_biaya');
        $totalPelanggan = Pelanggan::count();
        $stokKritisCount = Sparepart::where('stok', '<=', 5)->count();

        // Recent service activities (eager loaded)
        $aktivitasTerbaru = Servis::with(['pelanggan', 'teknisi'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        // Spare parts with low stock
        $sparepartKritis = Sparepart::orderBy('stok')
            ->take(5)
            ->get();

        // Team counts by role
        $timCounts = [
            'admin' => User::where('role', RolePengguna::Admin)->count(),
            'petugas' => User::where('role', RolePengguna::Petugas)->count(),
            'teknisi' => User::where('role', RolePengguna::Teknisi)->count(),
        ];

        return view('admin.dashboard', [
            'totalServis' => $totalServis,
            'baruDiterima' => $baruDiterima,
            'dalamPengerjaan' => $dalamPengerjaan,
            'selesaiDanDiambil' => $selesaiDanDiambil,
            'totalPendapatan' => $totalPendapatan,
            'totalPelanggan' => $totalPelanggan,
            'stokKritisCount' => $stokKritisCount,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'sparepartKritis' => $sparepartKritis,
            'timCounts' => $timCounts,
        ]);
    }
}
