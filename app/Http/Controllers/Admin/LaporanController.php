<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RolePengguna;
use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Models\DetailSparepart;
use App\Models\Servis;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Display service report with filters, aggregated metrics, and Chart.js graphs.
     */
    public function index(Request $request): View
    {
        $filters = $this->extractFilters($request);
        $query = $this->buildFilteredQuery($filters);

        // Paginated records for table view (10 per page)
        $servisList = (clone $query)->paginate(10)->withQueryString();

        // Metrics across filtered scope
        $summary = $this->calculateSummary($query);

        // Chart 1: Status Distribution
        $statusDistribution = $this->getStatusDistribution($query);

        // Chart 2: Monthly Trend (Last 6 Months)
        $monthlyTrend = $this->getMonthlyTrend();

        // Chart 3: Top 5 Suku Cadang Terbanyak
        $topSpareparts = $this->getTopSpareparts();

        // List of technicians for filter dropdown
        $teknisiList = User::where('role', RolePengguna::Teknisi)
            ->orderBy('nama')
            ->get();

        return view('admin.laporan.index', [
            'servisList' => $servisList,
            'filters' => $filters,
            'summary' => $summary,
            'statusDistribution' => $statusDistribution,
            'monthlyTrend' => $monthlyTrend,
            'topSpareparts' => $topSpareparts,
            'teknisiList' => $teknisiList,
        ]);
    }

    /**
     * Printable view of the filtered service report.
     */
    public function cetak(Request $request): View
    {
        $filters = $this->extractFilters($request);
        $query = $this->buildFilteredQuery($filters);

        // Fetch all matching records without pagination for printing
        $servisList = $query->get();
        $summary = $this->calculateSummary($query);

        return view('admin.laporan.cetak', [
            'servisList' => $servisList,
            'filters' => $filters,
            'summary' => $summary,
            'tanggalCetak' => now()->translatedFormat('d F Y, H:i'),
        ]);
    }

    /**
     * @return array{tgl_awal: ?string, tgl_akhir: ?string, status: ?string, id_teknisi: ?string, q: ?string}
     */
    private function extractFilters(Request $request): array
    {
        return [
            'tgl_awal' => $request->input('tgl_awal'),
            'tgl_akhir' => $request->input('tgl_akhir'),
            'status' => $request->input('status'),
            'id_teknisi' => $request->input('id_teknisi'),
            'q' => $request->input('q'),
        ];
    }

    /**
     * Build base query with eager loading and all applied filters.
     *
     * @param  array{tgl_awal: ?string, tgl_akhir: ?string, status: ?string, id_teknisi: ?string, q: ?string}  $filters
     */
    private function buildFilteredQuery(array $filters): Builder
    {
        $query = Servis::query()
            ->with(['pelanggan', 'teknisi', 'petugas', 'detail.sparepart'])
            ->latest('tgl_masuk');

        if (! empty($filters['tgl_awal'])) {
            $query->whereDate('tgl_masuk', '>=', $filters['tgl_awal']);
        }

        if (! empty($filters['tgl_akhir'])) {
            $query->whereDate('tgl_masuk', '<=', $filters['tgl_akhir']);
        }

        if (! empty($filters['status']) && StatusServis::tryFrom($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['id_teknisi'])) {
            $query->where('id_teknisi', $filters['id_teknisi']);
        }

        if (! empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('no_servis', 'like', "%{$search}%")
                    ->orWhere('merk_tipe', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function (Builder $sub) use ($search) {
                        $sub->where('nama', 'like', "%{$search}%")
                            ->orWhere('no_telepon', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    /**
     * @return array{
     *     total_servis: int,
     *     total_pendapatan: int,
     *     total_jasa: int,
     *     total_sparepart: int,
     *     selesai_count: int,
     *     proses_count: int
     * }
     */
    private function calculateSummary(Builder $query): array
    {
        $base = clone $query;

        $totalServis = $base->count();

        // Pendapatan dihitung dari servis yang sudah berstatus 'diambil' (lunas)
        $totalPendapatan = (clone $base)
            ->where('status', StatusServis::Diambil)
            ->sum('total_biaya');

        $totalJasa = (clone $base)
            ->where('status', StatusServis::Diambil)
            ->sum('biaya_jasa');

        $totalSparepart = $totalPendapatan - $totalJasa;
        if ($totalSparepart < 0) {
            $totalSparepart = 0;
        }

        $selesaiCount = (clone $base)
            ->whereIn('status', [StatusServis::Selesai, StatusServis::Diambil])
            ->count();

        $prosesCount = (clone $base)
            ->whereIn('status', [StatusServis::Diterima, StatusServis::Diperiksa, StatusServis::Diperbaiki])
            ->count();

        return [
            'total_servis' => $totalServis,
            'total_pendapatan' => (int) $totalPendapatan,
            'total_jasa' => (int) $totalJasa,
            'total_sparepart' => (int) $totalSparepart,
            'selesai_count' => $selesaiCount,
            'proses_count' => $prosesCount,
        ];
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function getStatusDistribution(Builder $query): array
    {
        $base = clone $query;

        $labels = [
            'Diterima',
            'Dalam Pemeriksaan',
            'Dalam Perbaikan',
            'Selesai',
            'Sudah Diambil',
        ];

        $counts = [
            (clone $base)->where('status', StatusServis::Diterima)->count(),
            (clone $base)->where('status', StatusServis::Diperiksa)->count(),
            (clone $base)->where('status', StatusServis::Diperbaiki)->count(),
            (clone $base)->where('status', StatusServis::Selesai)->count(),
            (clone $base)->where('status', StatusServis::Diambil)->count(),
        ];

        return [
            'labels' => $labels,
            'data' => $counts,
        ];
    }

    /**
     * @return array{labels: array<int, string>, masuk: array<int, int>, selesai: array<int, int>}
     */
    private function getMonthlyTrend(): array
    {
        $labels = [];
        $masukData = [];
        $selesaiData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;

            $labels[] = $date->translatedFormat('M Y');

            $masukData[] = Servis::whereYear('tgl_masuk', $year)
                ->whereMonth('tgl_masuk', $month)
                ->count();

            $selesaiData[] = Servis::whereYear('tgl_selesai', $year)
                ->whereMonth('tgl_selesai', $month)
                ->whereIn('status', [StatusServis::Selesai, StatusServis::Diambil])
                ->count();
        }

        return [
            'labels' => $labels,
            'masuk' => $masukData,
            'selesai' => $selesaiData,
        ];
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function getTopSpareparts(): array
    {
        $topItems = DetailSparepart::query()
            ->select('id_sparepart', DB::raw('SUM(jumlah) as total_qty'))
            ->with('sparepart')
            ->groupBy('id_sparepart')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $labels = [];
        $data = [];

        foreach ($topItems as $item) {
            $labels[] = $item->sparepart?->nama_sparepart ?? 'Item #'.$item->id_sparepart;
            $data[] = (int) $item->total_qty;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
