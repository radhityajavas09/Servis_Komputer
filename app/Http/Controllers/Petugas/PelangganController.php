<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Petugas\StorePelangganRequest;
use App\Http\Requests\Petugas\UpdatePelangganRequest;
use App\Models\Pelanggan;
use App\Models\Servis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    /**
     * Display a listing of customers with search, statistics, and service counts.
     */
    public function index(Request $request): View
    {
        $query = Pelanggan::query()
            ->withCount('servis')
            ->latest('id_pelanggan');

        // Search by name, phone number, or address
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $pelanggan = $query->paginate(10)->withQueryString();

        // Calculate summary statistics
        $totalPelanggan = Pelanggan::count();
        $pelangganAktif = Pelanggan::whereHas('servis', function ($q) {
            $q->whereIn('status', ['diterima', 'diperiksa', 'diperbaiki', 'selesai']);
        })->count();
        $totalServis = Servis::count();

        return view('petugas.pelanggan.index', [
            'pelanggan' => $pelanggan,
            'search' => $request->input('q', ''),
            'totalPelanggan' => $totalPelanggan,
            'pelangganAktif' => $pelangganAktif,
            'totalServis' => $totalServis,
        ]);
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        return view('petugas.pelanggan.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StorePelangganRequest $request): RedirectResponse
    {
        Pelanggan::create($request->validated());

        return redirect()->route('petugas.pelanggan.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Display customer detail with full service history.
     */
    public function show(Pelanggan $pelanggan): View
    {
        $pelanggan->load([
            'servis' => function ($q) {
                $q->latest('tgl_masuk')->with(['petugas', 'teknisi']);
            },
        ]);

        return view('petugas.pelanggan.show', [
            'pelanggan' => $pelanggan,
        ]);
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Pelanggan $pelanggan): View
    {
        return view('petugas.pelanggan.edit', [
            'pelanggan' => $pelanggan,
        ]);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(UpdatePelangganRequest $request, Pelanggan $pelanggan): RedirectResponse
    {
        $pelanggan->update($request->validated());

        return redirect()->route('petugas.pelanggan.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Remove the specified customer from storage.
     * Prevents deletion if customer is referenced in any service records.
     */
    public function destroy(Pelanggan $pelanggan): RedirectResponse
    {
        if ($pelanggan->servis()->exists()) {
            return back()->with('error', 'Pelanggan tidak dapat dihapus karena memiliki riwayat servis.');
        }

        $pelanggan->delete();

        return redirect()->route('petugas.pelanggan.index')->with('success', 'Data berhasil dihapus');
    }
}
