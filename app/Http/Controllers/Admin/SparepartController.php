<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSparepartRequest;
use App\Http\Requests\Admin\UpdateSparepartRequest;
use App\Models\Sparepart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SparepartController extends Controller
{
    /**
     * Display a listing of spare parts with search, stock filters, and statistics.
     */
    public function index(Request $request): View
    {
        $query = Sparepart::query()
            ->withCount('detailServis')
            ->latest('id_sparepart');

        // Search by sparepart name
        if ($search = $request->input('q')) {
            $query->where('nama_sparepart', 'like', "%{$search}%");
        }

        // Filter by stock status
        $stockFilter = $request->input('stock');
        if ($stockFilter === 'menipis') {
            $query->where('stok', '>', 0)->where('stok', '<', 5);
        } elseif ($stockFilter === 'habis') {
            $query->where('stok', '<=', 0);
        } elseif ($stockFilter === 'tersedia') {
            $query->where('stok', '>=', 5);
        }

        $spareparts = $query->paginate(10)->withQueryString();

        // Calculate summary metrics for header cards
        $totalItems = Sparepart::count();
        $lowStockCount = Sparepart::where('stok', '>', 0)->where('stok', '<', 5)->count();
        $outOfStockCount = Sparepart::where('stok', '<=', 0)->count();

        return view('admin.sparepart.index', [
            'spareparts' => $spareparts,
            'search' => $request->input('q', ''),
            'stockFilter' => $stockFilter,
            'totalItems' => $totalItems,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
        ]);
    }

    /**
     * Show the form for creating a new spare part.
     */
    public function create(): View
    {
        return view('admin.sparepart.create');
    }

    /**
     * Store a newly created spare part in storage.
     */
    public function store(StoreSparepartRequest $request): RedirectResponse
    {
        Sparepart::create($request->validated());

        return redirect()->route('admin.sparepart.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Show the form for editing the specified spare part.
     */
    public function edit(Sparepart $sparepart): View
    {
        return view('admin.sparepart.edit', [
            'sparepart' => $sparepart,
        ]);
    }

    /**
     * Update the specified spare part in storage.
     */
    public function update(UpdateSparepartRequest $request, Sparepart $sparepart): RedirectResponse
    {
        $sparepart->update($request->validated());

        return redirect()->route('admin.sparepart.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Remove the specified spare part from storage.
     * Restricts deletion if spare part has ever been utilized in service records.
     */
    public function destroy(Sparepart $sparepart): RedirectResponse
    {
        if ($sparepart->detailServis()->exists()) {
            return back()->with('error', 'Sparepart tidak dapat dihapus karena sudah digunakan pada data servis.');
        }

        $sparepart->delete();

        return redirect()->route('admin.sparepart.index')->with('success', 'Data berhasil dihapus');
    }
}
