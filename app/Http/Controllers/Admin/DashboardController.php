<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\SendSample;
use App\Models\Creator;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Exports\SkuExport;

use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today  = \Carbon\Carbon::today();
        $period = $request->query('period', '7d');

        if ($period === 'custom') {
            $dateFrom = $request->query('date_from');
            $dateTo   = $request->query('date_to');
            $curStart = $dateFrom ? \Carbon\Carbon::parse($dateFrom) : $today->copy();
            $curEnd   = $dateTo   ? \Carbon\Carbon::parse($dateTo)   : $today->copy();
            $startFmt    = $curStart->locale('id')->isoFormat('D MMM YYYY');
            $endFmt      = $curEnd->locale('id')->isoFormat('D MMM YYYY');
            $periodLabel = $startFmt === $endFmt ? $startFmt : "{$startFmt} – {$endFmt}";
        } else {
            [$curStart, $curEnd, $periodLabel] = match ($period) {
                '30d'     => [$today->copy()->subDays(29), $today->copy(), '30 hari terakhir'],
                'kemarin' => [$today->copy()->subDay(), $today->copy()->subDay(), 'Kemarin'],
                default   => [$today->copy()->subDays(6), $today->copy(), '7 hari terakhir'],
            };
        }
        $startDate     = $curStart->format('Y-m-d');
        $endDate       = $curEnd->format('Y-m-d');
        $currentPeriod = $period;

        $baseQuery = SendSample::filterDate($startDate, $endDate, 'deal_date');
        $totalPengeluaran = (clone $baseQuery)
            ->join('products', 'send_sample.product_id', '=', 'products.id')
            ->sum(DB::raw('send_sample.qty * products.harga'));
        $stats = (object) [
            'totalKreator'     => (clone $baseQuery)->distinct()->count('creator_id'),
            'totalSampel'      => (clone $baseQuery)->count(),
            'totalPengeluaran' => $totalPengeluaran,
        ];
        $allProducts = (clone $baseQuery)
            ->reorder()
            ->select(
                'products.sku_utama',
                'products.harga',
                DB::raw('SUM(send_sample.qty) as total_qty'),
                DB::raw('SUM(send_sample.qty * products.harga) as total_pengeluaran')
            )
            ->join('products', 'send_sample.product_id', '=', 'products.id')
            ->groupBy('products.sku_utama', 'products.harga')
            ->orderByDesc('total_qty')
            ->get();

        if ($request->has('export') && $request->export === 'sku') {
            $filename = "Laporan_SKU_" . date('Ymd_His') . ".xlsx";
            return Excel::download(new SkuExport($allProducts), $filename);
        }

        $topUser = (clone $baseQuery)
            ->reorder()
            ->select('pic', DB::raw('COUNT(*) as total_dikirim'))
            ->groupBy('pic')
            ->orderByDesc('total_dikirim')
            ->with('user:id,name')
            ->get();

        $topCreator = (clone $baseQuery)
            ->reorder()
            ->select('creator_id', DB::raw('count(*) as total_dikirim'))
            ->groupBy('creator_id')
            ->orderByDesc('total_dikirim')
            ->limit(10)
            ->with('creator:id,username')
            ->get();

        $creatorQuery = Creator::query();
        $creatorQuery->filterDate($startDate, $endDate);

        $totalCreator       = (clone $creatorQuery)->count();
        $creatorPerDayQuery = clone $creatorQuery;

        $creatorPerDay = $creatorPerDayQuery
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as total_creator'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $top10Products = $allProducts->take(10);
        $chartLabels = $top10Products->pluck('sku_utama')->toArray();
        $chartValues = $top10Products->pluck('total_qty')->toArray();
        $allChartLabels = $allProducts->pluck('sku_utama')->toArray();
        $allChartValues = $allProducts->pluck('total_qty')->toArray();

        return view('admin.dashboard.index', compact(
            'stats', 'chartLabels', 'chartValues', 'allChartLabels', 'allChartValues',
            'topUser', 'topCreator', 'creatorPerDay', 'totalCreator', 'top10Products', 'allProducts',
            'curStart', 'curEnd', 'currentPeriod', 'periodLabel'
        ));
    }
}