<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servis;
use Illuminate\View\View;

class ServisController extends Controller
{
    /**
     * Display a comprehensive read-only service detail view for Administrator.
     */
    public function show(Servis $servis): View
    {
        $servis->load(['pelanggan', 'petugas', 'teknisi', 'detail.sparepart']);

        return view('admin.servis.show', [
            'servis' => $servis,
        ]);
    }

    /**
     * Display printable receipt or invoice for Administrator.
     */
    public function cetak(Servis $servis): View
    {
        $servis->load(['pelanggan', 'petugas', 'teknisi', 'detail.sparepart']);

        return view('petugas.servis.tanda-terima', [
            'servis' => $servis,
            'backUrl' => route('admin.servis.show', $servis),
        ]);
    }
}
