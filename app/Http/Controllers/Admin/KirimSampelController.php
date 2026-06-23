<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ResolvesListQuery;

use App\Http\Requests\StoreSendSampleRequest;
use App\Http\Requests\UpdateSendSampleRequest;

use App\Models\SendSample;
use App\Models\Creator;
use App\Models\Store;
use App\Models\Product;
use App\Models\User;

use App\Imports\SendSampleImport;
use App\Exports\SendSamplesExport;
use App\Exports\TemplateExport;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use Maatwebsite\Excel\Facades\Excel;

class KirimSampelController extends Controller
{
    use ResolvesListQuery;

    /** Kolom yang boleh dipakai untuk sorting daftar. */
    private const SORTABLE_COLUMNS = [
        'deal_date', 'tanggal_kirim', 'estimasi_sampai', 'tanggal_aktual', 'qty', 'id',
    ];

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        [$sort, $dir] = $this->resolveSort($request, self::SORTABLE_COLUMNS, 'deal_date', 'desc');

        $query = SendSample::query()
            ->select([
                'id', 'deal_date', 'tanggal_kirim', 'estimasi_sampai', 'tanggal_aktual',
                'qty', 'id_pesanan', 'perjanjian_livestream', 'perjanjian_video',
                'creator_id', 'store_id', 'product_id', 'seller_product_id', 'pic', 'status_chat',
            ])
            ->with([
                'user:id,name',
                'store:id,name',
                'creator:id,username',
                'product:id,sku,sku_utama,harga',
            ]);

        $this->applyFilters($query, $request);

        $sendSamples = $query->search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        $optSku = Product::select('sku_utama')
            ->whereNotNull('sku_utama')
            ->distinct()
            ->orderBy('sku_utama')
            ->get();

        $optStores = Cache::remember('dropdown_stores', 86400, fn () => Store::select('id', 'name')->orderBy('name')->get());
        $optPics = Cache::remember('dropdown_pics', 86400, fn () => User::select('id', 'name')->orderBy('name')->get());

        $filterCreators   = $request->get('filter_creators', []);
        $filterPics       = $request->get('filter_pics', []);
        $filterStores     = $request->get('filter_stores', []);
        $filterSku        = $request->get('filter_sku', []);
        $filterIdPesanan  = $request->get('filter_id_pesanan');
        $filterStatusChat = $request->get('filter_status_chat');

        return view('admin.kirim-sampel.index', compact(
            'sendSamples', 'q', 'sort', 'dir',
            'optStores', 'optPics', 'optSku',
            'filterCreators', 'filterPics', 'filterStores', 'filterSku',
            'filterIdPesanan', 'filterStatusChat',
        ));
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        $user = Auth::user();
        if ($user && $user->role !== 'super admin') {
            $query->where('pic', $user->id);
        }

        $query->when($request->start_date, fn ($sq) => $sq->where('tanggal_kirim', '>=', $request->start_date));
        $query->when($request->end_date, fn ($sq) => $sq->where('tanggal_kirim', '<=', $request->end_date));

        // Status Resi (id_pesanan)
        $idPesanan = $request->get('filter_id_pesanan');
        if ($idPesanan === 'filled') {
            $query->whereNotNull('id_pesanan')->where('id_pesanan', '!=', '');
        } elseif ($idPesanan === 'empty') {
            $query->where(fn ($sq) => $sq->whereNull('id_pesanan')->orWhere('id_pesanan', '=', ''));
        }

        // Status Chat
        $statusChat = $request->get('filter_status_chat');
        if ($statusChat === 'filled') {
            $query->whereNotNull('status_chat')->where('status_chat', '!=', '');
        } elseif ($statusChat === 'empty') {
            $query->where(fn ($sq) => $sq->whereNull('status_chat')->orWhere('status_chat', '=', ''));
        }

        $filterCreators = $request->get('filter_creators', []);
        if (! empty($filterCreators)) {
            $query->whereIn('creator_id', $filterCreators);
        }

        $filterPics = $request->get('filter_pics', []);
        if (! empty($filterPics)) {
            $query->whereIn('pic', $filterPics);
        }

        $filterStores = $request->get('filter_stores', []);
        if (! empty($filterStores)) {
            $query->whereIn('store_id', $filterStores);
        }

        $filterSku = $request->get('filter_sku', []);
        if (! empty($filterSku)) {
            $query->whereHas('product', fn ($sq) => $sq->whereIn('sku_utama', $filterSku));
        }
    }

    // Select2 AJAX: server-side creator search, menggantikan render ribuan <option>.
    public function searchCreators(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $creators = Creator::query()
            ->where('is_blocked', 0)
            ->when($q !== '', fn ($query) => $query->where('username', 'like', "%{$q}%"))
            ->orderBy('username')
            ->limit(20)
            ->get(['id', 'username']);

        return response()->json([
            'results' => $creators->map(fn ($c) => ['id' => $c->id, 'text' => $c->username])->all(),
        ]);
    }

    // Select2 AJAX: server-side product search.
    public function searchProducts(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $products = Product::query()
            ->when($q !== '', fn ($query) => $query->where('sku', 'like', "%{$q}%"))
            ->orderBy('sku')
            ->limit(20)
            ->get(['id', 'sku']);

        return response()->json([
            'results' => $products->map(fn ($p) => ['id' => $p->id, 'text' => $p->sku])->all(),
        ]);
    }

    public function create()
    {
        // Kreator & produk kini dimuat via AJAX (searchCreators/searchProducts),
        // tidak lagi render semua <option>. Toko & user kecil → tetap dimuat.
        $stores = Store::select('id', 'name')->orderBy('name')->get();
        $users  = User::select('id', 'name')->orderBy('name')->get();

        $selectedCreator = old('creator_id')
            ? Creator::select('id', 'username')->find(old('creator_id'))
            : null;
        $selectedProduct = old('product_id')
            ? Product::select('id', 'sku')->find(old('product_id'))
            : null;

        return view('admin.kirim-sampel.create', compact('stores', 'users', 'selectedCreator', 'selectedProduct'));
    }

    public function store(StoreSendSampleRequest $request)
    {
        $data = $request->validated();
        $data['estimasi_sampai'] = Carbon::parse($data['tanggal_kirim'])->addDays(7)->format('Y-m-d');

        try {
            SendSample::create($data);

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
        $stores = Store::select('id', 'name')->orderBy('name')->get();
        $users  = User::select('id', 'name')->orderBy('name')->get();

        // Pra-pilih untuk Select2 AJAX: utamakan old() (gagal validasi), jika tidak pakai nilai tersimpan.
        $selectedCreatorId = old('creator_id', $sendSample->creator_id);
        $selectedProductId = old('product_id', $sendSample->product_id);
        $selectedCreator = $selectedCreatorId ? Creator::select('id', 'username')->find($selectedCreatorId) : null;
        $selectedProduct = $selectedProductId ? Product::select('id', 'sku')->find($selectedProductId) : null;

        return view('admin.kirim-sampel.edit', compact('sendSample', 'stores', 'users', 'selectedCreator', 'selectedProduct'));
    }

    public function update(UpdateSendSampleRequest $request, SendSample $sendSample)
    {
        $data = $request->validated();

        if (isset($data['tanggal_kirim'])) {
            $data['estimasi_sampai'] = Carbon::parse($data['tanggal_kirim'])->addDays(7)->format('Y-m-d');
        }

        try {
            $sendSample->update($data);

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
        $sendSample->load(['creator', 'store', 'product', 'user', 'approver']);

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

            return back()->with('success', count($ids) . ' data berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('delete_error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function followUpChat(Request $request)
    {
        $q        = trim((string) $request->input('q', ''));
        $skuUtama = $request->input('sku_utama');
        $user     = Auth::user();

        $query = SendSample::query()->with(['creator', 'store', 'product', 'user']);

        if ($user->role !== 'super admin') {
            $query->byPic($user->id);
        }

        if (! empty($skuUtama)) {
            $query->whereHas('product', fn ($sq) => $sq->where('sku_utama', $skuUtama));
        }

        $query->when($request->start_date, fn ($sq) => $sq->where('tanggal_kirim', '>=', $request->start_date));
        $query->when($request->end_date,   fn ($sq) => $sq->where('tanggal_kirim', '<=', $request->end_date));

        $items = $query->followUpChat()
            ->search($q)
            ->paginate(10)
            ->withQueryString();

        $skuUtamaOptions = Product::select('sku_utama')
            ->whereNotNull('sku_utama')
            ->distinct()
            ->orderBy('sku_utama')
            ->get();

        return view('admin.follow-up-chat.index', compact('items', 'q', 'skuUtamaOptions', 'skuUtama'));
    }

    public function chatStatus(SendSample $sendSample)
    {
        if ($sendSample->status_chat === 1) {
            return back()->with('error', 'Item ini sudah tidak ada kendala.');
        }

        $sendSample->update([
            'status_chat'    => 1,
            'tanggal_dichat' => now(),
        ]);

        return back()->with('success', 'Berhasil ditandai: Sudah tidak ada kendala.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new SendSampleImport;
            Excel::import($import, $request->file('file'));

            $msg = 'Data kirim sampel berhasil diimport.';
            if ($import->skipped > 0) {
                $msg .= " {$import->skipped} baris dilewati karena toko/kreator/SKU/PIC tidak cocok.";
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = ['deal_date', 'pic', 'approve', 'toko', 'kreator', 'perjanjian_livestream', 'perjanjian_video', 'sku', 'qty', 'id_pesanan', 'tanggal_kirim', 'tanggal_aktual', 'video_dibuat', 'live_dibuat'];

        return Excel::download(new TemplateExport($headers), 'Template_KirimSampel.xlsx');
    }

    public function export(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        [$sort, $dir] = $this->resolveSort($request, self::SORTABLE_COLUMNS, 'deal_date', 'desc');

        $query = SendSample::query()
            ->select([
                'id', 'deal_date', 'tanggal_kirim', 'estimasi_sampai', 'tanggal_aktual',
                'qty', 'id_pesanan', 'perjanjian_livestream', 'perjanjian_video',
                'creator_id', 'store_id', 'product_id', 'pic', 'status_chat',
                'approve', 'video_dibuat', 'live_dibuat',
            ])
            ->with([
                'user:id,name',
                'store:id,name',
                'creator:id,username',
                'product:id,sku,sku_utama,harga',
                'approver:id,name',
            ]);

        $this->applyFilters($query, $request);
        $query->search($q)->orderBy($sort, $dir);

        return Excel::download(new SendSamplesExport($query), 'Data_Kirim_Sampel_' . date('Ymd_His') . '.xlsx');
    }

    public function updatePicMassal(Request $request)
    {
        $request->validate([
            'old_pic'    => 'required',
            'store_id'   => 'required',
            'new_pic_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $idsToUpdate = DB::table('send_sample as ss')
                ->join('stores as st', 'ss.store_id', '=', 'st.id')
                ->join('users as us', 'ss.pic', '=', 'us.id')
                ->whereColumn('ss.video_dibuat', '<', 'ss.perjanjian_video')
                ->where('us.id', $request->old_pic)
                ->where('st.id', $request->store_id)
                ->pluck('ss.id');

            if ($idsToUpdate->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang sesuai dengan filter tersebut.',
                ], 404);
            }

            $updatedCount = DB::table('send_sample')
                ->whereIn('id', $idsToUpdate)
                ->update(['pic' => $request->new_pic_id]);

            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => 'Berhasil melakukan update PIC massal.',
                'jumlah_diupdate' => $updatedCount,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat melakukan update massal.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
