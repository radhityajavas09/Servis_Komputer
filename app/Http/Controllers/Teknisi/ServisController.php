<?php

namespace App\Http\Controllers\Teknisi;

use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teknisi\UpdateDiagnosisRequest;
use App\Models\Servis;
use App\Models\Sparepart;
use App\Services\PengerjaanServisService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class ServisController extends Controller
{
    /**
     * Display a listing of services for technicians.
     */
    public function index(Request $request): View
    {
        $query = Servis::query()
            ->with(['pelanggan', 'teknisi'])
            ->latest('id_servis');

        // Search by service number, customer name, or device
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

        // Filter by service status
        $statusFilter = $request->input('status');
        if ($statusFilter && StatusServis::tryFrom($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $servisList = $query->paginate(10)->withQueryString();

        // Calculate metrics for technician overview
        $totalServis = Servis::count();
        $antrean = Servis::where('status', StatusServis::Diterima)->count();
        $sedangDikerjakan = Servis::whereIn('status', [
            StatusServis::Diperiksa,
            StatusServis::Diperbaiki,
        ])->count();
        $selesai = Servis::where('status', StatusServis::Selesai)->count();

        return view('teknisi.servis.index', [
            'servisList' => $servisList,
            'search' => $request->input('q', ''),
            'statusFilter' => $statusFilter,
            'totalServis' => $totalServis,
            'antrean' => $antrean,
            'sedangDikerjakan' => $sedangDikerjakan,
            'selesai' => $selesai,
        ]);
    }

    /**
     * Show service inspection, diagnosis, spare parts input, and status transition page.
     */
    public function show(Servis $servis): View
    {
        $servis->load(['pelanggan', 'petugas', 'teknisi', 'detail.sparepart']);

        $availableSpareparts = Sparepart::orderBy('nama_sparepart')->get();

        return view('teknisi.servis.show', [
            'servis' => $servis,
            'availableSpareparts' => $availableSpareparts,
        ]);
    }

    /**
     * Save/update diagnosis, actions, service fee, and dynamic spare parts usage.
     */
    public function updateDiagnosis(
        UpdateDiagnosisRequest $request,
        Servis $servis,
        PengerjaanServisService $service
    ): RedirectResponse {
        try {
            $service->updateDiagnosis($servis, $request->validated(), $request->user());

            return redirect()
                ->route('teknisi.servis.show', $servis)
                ->with('success', 'Diagnosis dan rincian servis berhasil disimpan.');
        } catch (InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Advance service status to the next sequential step.
     */
    public function updateStatus(
        Request $request,
        Servis $servis,
        PengerjaanServisService $service
    ): RedirectResponse {
        try {
            $service->majuStatus($servis, $request->user());

            return redirect()
                ->route('teknisi.servis.show', $servis)
                ->with('success', 'Status servis berhasil diperbarui menjadi '.$servis->status->label().'.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
