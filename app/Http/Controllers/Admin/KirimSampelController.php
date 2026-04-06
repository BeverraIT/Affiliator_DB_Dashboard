<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSendSampleRequest;
use App\Http\Requests\UpdateSendSampleRequest;

use App\Models\SendSample;
use App\Models\Creator;
use App\Models\Store;
use App\Models\Product;
use App\Models\User;

use App\Imports\SendSampleImport;
use App\Exports\SendSamplesExport;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;


class KirimSampelController extends Controller
{
    public function index(Request $request)
    {
        // INPUT REQUEST
        $q = (string) $request->get('q', '');

        // Input Filter
        $filter_creators    = $request->get('filter_creators', []);
        $filter_pics        = $request->get('filter_pics', []);
        $filter_stores      = $request->get('filter_stores', []);
        $filter_sku         = $request->get('filter_sku', []);
        $filter_id_pesanan  = $request->get('filter_id_pesanan');
        $filter_status_chat = $request->get('filter_status_chat');

        $allowedSort = [
            'deal_date',
            'tanggal_kirim',
            'estimasi_sampai',
            'tanggal_aktual',
            'qty',
            'id',
        ];
        $sort = $request->get('sort', 'deal_date');
        $sort = in_array($sort, $allowedSort, true) ? $sort : 'deal_date';
        $dir = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $query = SendSample::query()
            ->select([
                'id',
                'deal_date',
                'tanggal_kirim',
                'estimasi_sampai',
                'tanggal_aktual',
                'qty',
                'id_pesanan',
                'perjanjian_livestream',
                'perjanjian_video',
                'creator_id',
                'store_id',
                'product_id',
                'pic',
                'status_chat',
            ])
            ->with([
                'user:id,name',
                'store:id,name',
                'creator:id,username',
                'product:id,sku,sku_utama,harga',
            ]);

        $user = Auth::user();
        if ($user && $user->role !== 'super admin') {
            $query->where('pic', $user->id);
        }

        $query->when($request->start_date, function ($q) use ($request) {
            $q->whereDate('tanggal_kirim', '>=', $request->start_date);
        });

        $query->when($request->end_date, function ($q) use ($request) {
            $q->whereDate('tanggal_kirim', '<=', $request->end_date);
        });

        // Filter Status Resi
        if ($filter_id_pesanan === 'filled') {
            $query->whereNotNull('id_pesanan')->where('id_pesanan', '!=', '');
        } elseif ($filter_id_pesanan === 'empty') {
            $query->where(function($q) {
                $q->whereNull('id_pesanan')->orWhere('id_pesanan', '=', ''); 
            });
        }

        // Filter Status Chat
        if ($filter_status_chat === 'filled') {
            $query->whereNotNull('status_chat')->where('status_chat', '!=', '');
        } elseif ($filter_status_chat === 'empty') {
            $query->where(function($q) {
                $q->whereNull('status_chat')->orWhere('status_chat', '=', '');
            });
        }

        // Filter Kreator
        if (!empty($filter_creators)) {
            $query->whereIn('creator_id', $filter_creators);
        }

        // Filter PIC
        if (!empty($filter_pics)) {
            $query->whereIn('pic', $filter_pics);
        }

        // Filter Toko
        if (!empty($filter_stores)) {
            $query->whereIn('store_id', $filter_stores);
        }

        // Filter SKU Utama
        if (!empty($filter_sku)) {
            $query->whereHas('product', function($q) use ($filter_sku) {
                $q->whereIn('sku_utama', $filter_sku);
            });
        }

        // SEARCH & PAGINATION
        $sendSamples = $query
            ->search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        // OPSI
        $optCreators = Creator::select('id', 'username')
            ->orderBy('username')
            ->get();
        $optStores = Store::select('id', 'name')
            ->orderBy('name')
            ->get();
        $optPics = User::select('id', 'name')
            ->orderBy('name')
            ->get();
        $optSku = Product::select('sku_utama')
            ->whereNotNull('sku_utama')
            ->distinct()
            ->orderBy('sku_utama')
            ->get();

        return view('admin.kirim-sampel.index', compact(
            'sendSamples',
            'q',
            'sort',
            'dir',
            'optCreators',
            'optStores',
            'optPics',
            'optSku',
            'filter_creators',
            'filter_pics',
            'filter_stores',
            'filter_sku',
            'filter_id_pesanan',
            'filter_status_chat',
        ));
    }


    public function create()
    {
        $creators   = Creator::where('is_blocked', 0)->orderBy('username')->get();
        $stores     = Store::orderBy('name')->get();
        $products   = Product::orderBy('sku')->get();
        $users      = User::orderBy('name')->get();

        return view('admin.kirim-sampel.create', compact(
            'creators',
            'stores',
            'products',
            'users'
        ));
    }


    public function store(StoreSendSampleRequest $request)
    {
        $data = $request->validated();
        $tglKirim = Carbon::parse($data['tanggal_kirim']);
        $data['estimasi_sampai'] = $tglKirim->addDays(7)->format('Y-m-d');

        try {
            DB::transaction(function () use ($data) {
                SendSample::create($data);
            });

            if ($request->filled('redirect_to')) {
                return redirect($request->redirect_to)
                    ->with('success', 'Data kirim sampel berhasil ditambahkan.');
            }

            return redirect()
                ->route('admin.kirim-sampel.index')
                ->with('success', 'Kirim sampel berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }


    public function edit(SendSample $sendSample)
    {
        $creators   = Creator::orderBy('username')->get();
        $stores     = Store::orderBy('name')->get();
        $products   = Product::orderBy('sku')->get();
        $users      = User::orderBy('name')->get();

        return view('admin.kirim-sampel.edit', compact(
            'sendSample',
            'creators',
            'stores',
            'products',
            'users',
        ));
    }


    public function update(UpdateSendSampleRequest $request, SendSample $sendSample)
    {
        $data = $request->validated();

        if (isset($data['tanggal_kirim'])) {
            $data['estimasi_sampai'] = Carbon::parse($data['tanggal_kirim'])->addDays(7)->format('Y-m-d');
        }

        try {
            DB::transaction(function () use ($sendSample, $data) {
                $sendSample->update($data);
            });

            if ($request->filled('redirect_to')) {
                return redirect($request->redirect_to)
                    ->with('success', 'Data kirim sampel berhasil diperbarui.');
            }

            return redirect()
                ->route('admin.kirim-sampel.index')
                ->with('success', 'Data kirim sampel berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }


    public function show(SendSample $sendSample)
    {
        $sendSample->load([
            'creator',
            'store',
            'product',
            'user',
            'approver'
        ]);

        return view('admin.kirim-sampel.show', compact('sendSample'));
    }


    public function destroy(SendSample $sendSample)
    {
        $sendSample->delete();

        return redirect()
            ->route('admin.kirim-sampel.index')
            ->with('success', 'Data kirim sampel berhasil dihapus.');
    }


    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:send_sample,id',
        ]);

        try {
            $ids = $request->input('ids');
            SendSample::whereIn('id', $ids)->delete();

            return redirect()->back()
                ->with('success', count($ids) . ' data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('delete_error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }


    public function followUpChat(Request $request)
    {
        // INPUT REQUEST
        $search     = trim((string) $request->input('q', ''));
        $sku_utama  = $request->input('sku_utama');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');
        $user       = Auth::user();

        // QUERY
        $query = SendSample::query()->with([
            'creator',
            'store',
            'product',
            'user',
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

        // SEARCH, SORTING, & PAGINATION
        $items = $query->followUpChat()
            ->search($search)
            ->paginate(10)
            ->withQueryString();

        // OPSI SKU UTAMA
        $skuUtamaOptions = Product::select('sku_utama')
            ->whereNotNull('sku_utama')
            ->distinct()
            ->orderBy('sku_utama')
            ->get();

        return view('admin.follow-up-chat.index', compact(
            'items',
            'search',
            'skuUtamaOptions',
            'sku_utama'
        ));
    }


    public function chatStatus(SendSample $sendSample)
    {
        if ($sendSample->status_chat === 1) {
            return back()->with('error', 'Item ini sudah tidak ada kendala.');
        }

        $sendSample->update([
            'status_chat' => 1,
            'tanggal_dichat' => now(),
        ]);

        return back()->with('success', 'Berhasil ditandai: Sudah tidak ada kendala.');
    }


    public function import(Request $request) 
    {
        $request->validate([
            'file'  => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new SendSampleImport, $request->file('file'));
            
            return back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }


    public function export(Request $request)
    {
        $columns = [
            'deal_date',
            'pic',
            'approve',
            'store_id',
            'creator_id',
            'perjanjian_livestream',
            'perjanjian_video',
            'product_id',
            'qty',
            'id_pesanan',
            'tanggal_kirim',
            'estimasi_sampai',
            'tanggal_aktual',
            'video_dibuat',
            'live_dibuat',
        ];

        $relations = [
            'user',
            'approver',
            'store',
            'creator',
            'product',
        ];

        $query = SendSample::query()
            ->select($columns)
            ->with($relations);

        return Excel::download(new SendSamplesExport($query), 'send-samples.xlsx');
    }
}