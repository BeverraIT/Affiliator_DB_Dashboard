<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SendSample;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpKontenController extends Controller
{
    public function index(Request $request)
    {
        // INPUT REQUEST
        $search     = trim((string) $request->get('q', ''));
        $sku_utama  = $request->get('sku_utama');
        $startDate  = $request->get('start_date');
        $endDate    = $request->get('end_date');
        $user       = Auth::user();

        // QUERY
        $query = SendSample::query()->with([
            'creator',
            'store',
            'product',
            'user'
        ]);

        // FILTER ROLE
        if ($user->role !== 'super admin') {
            $query->byPic($user->id);
        }

        // FILTER SKU UTAMA
        if (!empty($sku_utama)) {
            $query->whereHas('product', function($q) use ($sku_utama) {
                $q->where('sku_utama', $sku_utama);
            });
        }

        // FILTER TANGGAL
        $query->when($startDate, function ($q) use ($startDate) {
            $q->whereDate('tanggal_kirim', '>=', $startDate);
        });

        $query->when($endDate, function ($q) use ($endDate) {
            $q->whereDate('tanggal_kirim', '<=', $endDate);
        });

        // SEARCH, SORTING & PAGINATION
        $items = $query->pendingContent()
            ->search($search)
            ->where('status_chat', 1)
            ->whereHas('creator', function ($q) {
                $q->where('is_blocked', 0);
            })
            ->orderByRaw('tanggal_aktual IS NULL, tanggal_aktual DESC')
            ->orderByRaw('deal_date IS NULL, deal_date DESC')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // OPSI SKU UTAMA
        $skuUtamaOptions = Product::select('sku_utama')
            ->whereNotNull('sku_utama')
            ->distinct()
            ->orderBy('sku_utama')
            ->get();

        return view('admin.follow-up-konten.index', compact(
            'items',
            'search',
            'skuUtamaOptions',
            'sku_utama'
        ));
    }


    public function storeVideoDibuat(Request $request, SendSample $sendSample)
    {
        if ((int) $sendSample->perjanjian_video == 0) {
            return back()->with('error', 'Item ini tidak memiliki perjanjian video.');
        }

        $validated = $request->validate([
            'video_dibuat' => ['required', 'integer', 'min:0'],
        ]);

        $sendSample->update([
            'video_dibuat' => $validated['video_dibuat'],
        ]);

        return back()->with('success', 'Jumlah "Video Dibuat" berhasil diupdate.');
    }

    
    public function markFollowUpMingguan(SendSample $sendSample)
    {
        if ((int) $sendSample->status_follow_up_konten_mingguan === 1) {
            return back()->with('error', 'Item ini sudah ditandai follow up mingguan.');
        }

        $sendSample->update([
            'status_follow_up_konten_mingguan' => 1,
        ]);

        return back()->with('success', 'Berhasil ditandai: Sudah Follow Up (mingguan).');
    }
}