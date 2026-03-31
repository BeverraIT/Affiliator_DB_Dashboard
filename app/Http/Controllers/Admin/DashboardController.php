<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SendSample;
use App\Models\Creator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = null;
        $endDate = null;

        // 1. Parsing Date Range Filter
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $startDate = $dates[0];
            $endDate = count($dates) > 1 ? $dates[1] : $dates[0];
        }

        // 2. Base Query untuk Stats Card & Tables
        $baseQuery = SendSample::filterDate($startDate, $endDate, 'deal_date');

        // 3. Menghitung Stats
        $totalPengeluaran = (clone $baseQuery)
            ->join('products', 'send_sample.product_id', '=', 'products.id')
            ->sum(DB::raw('send_sample.qty * products.harga'));

        $stats = (object) [
            'totalKreator'     => (clone $baseQuery)->distinct()->count('creator_id'),
            'totalSampel'      => (clone $baseQuery)->count(),
            'totalPengeluaran' => $totalPengeluaran,
        ];

        // 4. Top Products (Table)
        $topProducts = (clone $baseQuery)
            ->reorder()
            ->select('products.sku_utama', DB::raw('SUM(send_sample.qty) as total_qty'))
            ->join('products', 'send_sample.product_id', '=', 'products.id')
            ->groupBy('products.sku_utama')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // 5. Top PIC (Table)
        $topUser = (clone $baseQuery)
            ->reorder()
            ->select('pic', DB::raw('COUNT(*) as total_dikirim'))
            ->groupBy('pic')
            ->orderByDesc('total_dikirim')
            ->with('user:id,name')
            ->get();

        // 6. Top Creator (Table)
        $topCreator = (clone $baseQuery)
            ->reorder()
            ->select('creator_id', DB::raw('count(*) as total_dikirim'))
            ->groupBy('creator_id')
            ->orderByDesc('total_dikirim')
            ->limit(10)
            ->with('creator:id,username')
            ->get();

        // 7. Grafik Creator Per Day
        $creatorQuery = Creator::query();
        if ($startDate && $endDate) {
            $creatorQuery->filterDate($startDate, $endDate);
        }

        $totalCreator = (clone $creatorQuery)->count();

        $creatorPerDayQuery = clone $creatorQuery;
        if (!($startDate && $endDate)) {
            $creatorPerDayQuery->recent(7);
        }

        $creatorPerDay = $creatorPerDayQuery
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as total_creator'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 8. Prepare Chart Data for Top Products
        $chartLabels = $topProducts->pluck('sku_utama')->toArray();
        $chartValues = $topProducts->pluck('total_qty')->toArray();

        return view('admin.dashboard.index', compact(
            'stats', 'chartLabels', 'chartValues', 'topUser', 
            'topCreator', 'creatorPerDay', 'totalCreator'
        ));
    }
}