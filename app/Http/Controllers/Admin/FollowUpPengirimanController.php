<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\SendSample;
use App\Models\Product;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class FollowUpPengirimanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $selectedSkuUtama = $request->get('sku_utama');
        
        $user = Auth::user();

        // QUERY
        $query = SendSample::query()
            ->with([
                'creator',
                'store',
                'product',
                'user'
            ])
            ->needFollowUp();

        // Filter PIC
        $query->visibleTo($user);

        // Filter Tanggal
        $query->when($request->start_date, function ($q) use ($request) {
            $q->where('tanggal_kirim', '>=', $request->start_date);
        });

        $query->when($request->end_date, function ($q) use ($request) {
            $q->where('tanggal_kirim', '<=', $request->end_date);
        });

        // Filter SKU
        $query->bySkuUtama($selectedSkuUtama);

        // SEARCHING
        $items = $query->search($q)
            ->orderBy('estimasi_sampai', 'asc')
            ->paginate(10)
            ->withQueryString();

        // OPSI SKU
        $skuUtamaOptions = Product::select('sku_utama')
            ->whereNotNull('sku_utama')
            ->distinct()
            ->orderBy('sku_utama')
            ->get();

        $today = Carbon::today();

        return view('admin.follow-up-pengiriman.index', compact(
            'items',
            'today',
            'q',
            'skuUtamaOptions',
            'selectedSkuUtama', 
        ));
    }

    
    public function update(Request $request, SendSample $sendSample)
    {
        $validated = $request->validate([
            'tanggal_aktual'    => ['required', 'date'],
        ]);

        $sendSample->update($validated);

        return back()->with(
            'success', 
            'Tanggal aktual berhasil disimpan. Data dihapus dari daftar follow up.'
        );
    }
}