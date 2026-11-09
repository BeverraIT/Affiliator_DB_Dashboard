<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\SendSample;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpKontenController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $skuUtama = $request->get('sku_utama');
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $user      = Auth::user();

        $query = SendSample::query()->with([
            'creator',
            'store',
            'product',
            'user'
        ]);

        $query->visibleTo($user);

        if (! empty($skuUtama)) {
            $query->whereHas('product', fn ($sq) => $sq->where('sku_utama', $skuUtama));
        }

        $query->when($startDate, fn ($sq) => $sq->where('tanggal_kirim', '>=', $startDate));
        $query->when($endDate,   fn ($sq) => $sq->where('tanggal_kirim', '<=', $endDate));

        $items = $query->pendingContent()
            ->search($q)
            ->where('status_chat', 1)
            ->whereHas('creator', fn ($sq) => $sq->where('is_blocked', 0))
            ->orderByRaw('tanggal_aktual IS NULL, tanggal_aktual DESC')
            ->orderByRaw('deal_date IS NULL, deal_date DESC')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $skuUtamaOptions = Product::select('sku_utama')
            ->whereNotNull('sku_utama')
            ->distinct()
            ->orderBy('sku_utama')
            ->get();

        return view('admin.follow-up-konten.index', compact(
            'items',
            'q',
            'skuUtamaOptions',
            'skuUtama'
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

    public function updateProductId(Request $request, SendSample $sendSample)
    {
        $request->validate(['seller_product_id' => 'required|string|max:255']);

        $sendSample->update(['seller_product_id' => $request->seller_product_id]);

        return back()->with('success', 'ID Produk berhasil diperbarui.');
    }
}