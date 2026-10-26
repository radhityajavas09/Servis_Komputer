<?php

namespace App\Http\Controllers\Petugas;

use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Http\Requests\Petugas\PenyerahanServisRequest;
use App\Models\Servis;
use App\Services\PenyerahanServisService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class ServisController extends Controller
{
    /**
     * Display a listing of services with status filtering, search, and metrics.
     */
    public function index(Request $request): View
    {
        $query = Servis::query()
            ->with(['pelanggan', 'petugas', 'teknisi'])
            ->latest('id_servis');

        // Search by service number, customer name, or device model
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_servis', 'like', "%{$search}%")
                    ->orWhere('merk_tipe', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function ($sub) use ($search) {
                        $sub->where('nama', 'like', "%{$search}%")
                            ->orWhere('no_telepon', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        $statusFilter = $request->input('status');
        if ($statusFilter && StatusServis::tryFrom($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $servisList = $query->paginate(10)->withQueryString();

        // Calculate summary metrics
        $totalServis = Servis::count();
        $dalamProses = Servis::whereIn('status', [
            StatusServis::Diterima,
            StatusServis::Diperiksa,
            StatusServis::Diperbaiki,
        ])->count();
        $siapDiambil = Servis::where('status', StatusServis::Selesai)->count();
        $sudahDiambil = Servis::where('status', StatusServis::Diambil)->count();

        return view('petugas.servis.index', [
            'servisList' => $servisList,
            'search' => $request->input('q', ''),
            'statusFilter' => $statusFilter,
            'totalServis' => $totalServis,
            'dalamProses' => $dalamProses,
            'siapDiambil' => $siapDiambil,
            'sudahDiambil' => $sudahDiambil,
        ]);
    }

    /**
     * Display comprehensive details of a specific service.
     */
    public function detail(Servis $servis): View
    {
        $servis->load(['pelanggan', 'petugas', 'teknisi', 'detail.sparepart']);

        return view('petugas.servis.detail', [
            'servis' => $servis,
        ]);
    }

    /**
     * Show device handover confirmation form.
     */
    public function serahkan(Servis $servis): View|RedirectResponse
    {
        if ($servis->status !== StatusServis::Selesai) {
            return redirect()
                ->route('petugas.servis.detail', $servis)
                ->with('error', 'Perangkat hanya dapat diserahkan jika pengerjaan sudah berstatus selesai.');
        }

        $servis->load(['pelanggan', 'petugas', 'teknisi', 'detail.sparepart']);

        return view('petugas.servis.serahkan', [
            'servis' => $servis,
        ]);
    }

    /**
     * Process device handover and mark status as 'diambil'.
     */
    public function prosesSerahkan(
        PenyerahanServisRequest $request,
        Servis $servis,
        PenyerahanServisService $service
    ): RedirectResponse {
        try {
            $service->serahkan($servis);

            return redirect()
                ->route('petugas.servis.detail', $servis)
                ->with('success', 'Perangkat berhasil diserahkan kepada pelanggan.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
