<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreatorRequest;
use App\Http\Requests\UpdateCreatorRequest;
use App\Http\Requests\CheckUsernameRequest;

use App\Models\Category;
use App\Models\ContentStyle;
use App\Models\Creator;
use App\Models\Age;
use App\Models\User;

use App\Imports\CreatorsImport;
use App\Exports\CreatorsExport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;


class CreatorController extends Controller
{
    public function index(Request $request)
    {
        // INPUT REQUEST
        $q = (string) $request->query('q', '');

        // INPUT FILTER
        $filter_umur        = $request->query('filter_umur', []);
        $filter_kategori    = $request->query('filter_kategori', []);
        $filter_style       = $request->query('filter_style', []);
        $filter_no_wa       = $request->query('filter_no_wa');
        $filter_user        = $request->query('filter_user', []);
        $min_gmv            = $request->query('min_gmv');
        $max_gmv            = $request->query('max_gmv');

        // SORTING
        $sort = (string) $request->query('sort', 'id');
        $dir = strtolower((string) $request->query('dir', 'desc')) == 'asc' ? 'asc' : 'desc';
        $allowedSort = [
            'id',
            'username',
            'gmv',
            'itemsterjual',
            'channel',
            'genderpengikut',
        ];

        if(!in_array($sort, $allowedSort, true)) {
            $sort = 'username';
        }

        // QUERY
        $query = Creator::with([
                'user:id,name',
                'contentStyles:id,name',
                'categories:id,name',
                'ages:id,name',
            ]);

        // Filter Umur
        if (!empty($filter_umur)) {
            $query->whereHas('ages', function ($q) use ($filter_umur) {
                $q->whereIn('ages.id', $filter_umur);
            });
        }

        // Filter Kategori
        if (!empty($filter_kategori)) {
            $query->whereHas('categories', function ($q) use ($filter_kategori) {
                $q->whereIn('categories.id', $filter_kategori);
            });
        }

        // Filter Content Style
        if (!empty($filter_style)) {
            $query->whereHas('contentStyles', function ($q) use ($filter_style) {
                $q->whereIn('contentstyles.id', $filter_style);
            });
        }

        // Filter User
        if (!empty($filter_user)) {
            $query->whereIn('user_id', $filter_user);
        }

        // Filter GMV Range
        $query->when($min_gmv, function ($q) use ($min_gmv) {
            $cleanMin = preg_replace('/[^0-9]/', '', $min_gmv);
            $q->where('gmv', '>=', $cleanMin);
        });

        $query->when($max_gmv, function ($q) use ($max_gmv) {
            $cleanMax = preg_replace('/[^0-9]/', '', $max_gmv);
            $q->where('gmv', '<=', $cleanMax);
        });

        // Filter Top Kreator
        if ($request->has('filter_top_creator')) {
            $query->where('top_creator', 1);
        }

        // Filter Kreator Diblokir
        if ($request->has('filter_is_blocked')) {
            $query->where('is_blocked', 1);
        }

        // Filter Tanpa WA
        $query->when($filter_no_wa, function ($q) {
            $q->whereNull('nomerwa');
        });

        // SEARCH & SORTING
        $creators = $query->search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString(); 

        $q = trim($q);

        // DATA OPSI
        $ageOptions      = Age::orderBy('name')->get(); 
        $categoryOptions = Category::orderBy('name')->get();
        $styleOptions    = ContentStyle::orderBy('name')->get();
        $userOptions     = User::orderBy('name')->get();

        return view('admin.kreator.index', compact(
            'creators', 
            'q',
            'sort',
            'dir',
            'ageOptions',
            'categoryOptions',
            'styleOptions',
            'userOptions',
            'filter_umur',
            'filter_kategori',
            'filter_style',
            'filter_no_wa',
            'min_gmv',
            'max_gmv',
        ));
    }


    public function create()
    {
        $categories     = Category::orderBy('name')->get();
        $contentStyles  = ContentStyle::orderBy('name')->get();
        $ages           = Age::orderBy('id')->get();

        return view('admin.kreator.create', compact(
            'categories',
            'contentStyles',
            'ages'
        ));
    }


    public function store(StoreCreatorRequest $request)
    {
        $validated = $request->safe();
        DB::transaction(function () use ($validated) {
            $data = $validated->collect()->except(['ages', 'categories', 'contentstyles']);
            $data['user_id'] = Auth::id();

            $creator = Creator::create($data->all());
            $creator->ages()->sync($validated->collect('ages'));
            $creator->categories()->sync($validated->collect('categories'));
            $creator->contentStyles()->sync($validated->collect('contentstyles'));
        });

        // DB::transaction(function () use ($validated) {
        //     $data = $validated->collect()->except(['ages', 'categories', 'contentstyles']);
        //     $data['user_id'] = Auth::id();

        //     $creator = auth()->user()->creators()->create($data->all());
        //     $creator->ages()->sync($validated->collect('ages'));
        //     $creator->categories()->sync($validated->collect('categories'));
        //     $creator->contentStyles()->sync($validated->collect('contentstyles'));
        // });

        return redirect()
            ->route('admin.kreator.index')
            ->with('success', 'Kreator berhasil ditambahkan.');
    }


    public function show(Creator $creator)
    {
        $creator->load([
            'ages:id,name',
            'categories:id,name',
            'contentStyles:id,name',
            'sendSamples.product:id,sku_utama',
            'sendSamples.store:id,name',
        ]);

        return view('admin.kreator.show', compact('creator'));
    }


    public function edit(Creator $creator)
    {
        $creator->load([
            'ages:id,name',
            'categories:id,name',
            'contentStyles:id,name',
        ]);

        $categories     = Category::orderBy('name')->pluck('name', 'id');
        $contentStyles  = ContentStyle::orderBy('name')->pluck('name', 'id');
        $ages           = Age::orderBy('name')->pluck('name', 'id');

        $selectedAgeIds         = $creator->ages->modelKeys();
        $selectedCategoryIds    = $creator->categories->modelKeys();
        $selectedStyleIds       = $creator->contentStyles->modelKeys();

        return view('admin.kreator.edit', compact(
            'creator',
            'categories',
            'contentStyles',
            'ages',
            'selectedAgeIds',
            'selectedCategoryIds',
            'selectedStyleIds'
        ));
    }

    public function update(UpdateCreatorRequest $request, Creator $creator)
    {
        $validated = $request->safe();

        DB::transaction(function () use ($creator, $validated) {
            $creator->update(
                $validated->except([
                    'ages',
                    'categories',
                    'contentstyles'
            ]));
            $creator->ages()->sync($validated->collect('ages'));
            $creator->categories()->sync($validated->collect('categories'));
            $creator->contentStyles()->sync($validated->collect('contentstyles'));
        });

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)
                ->with('success', 'Kreator berhasil diperbarui.');
        }

        return redirect()
            ->route('admin.kreator.index')
            ->with('success', 'Kreator berhasil diperbarui.');
    }


    public function destroy(Creator $creator)
    {
        $creator->delete();

        return redirect()
            ->route('admin.kreator.index')
            ->with('success', 'Kreator berhasil dihapus.');
    }


    public function bulkDestroy(Request $request) 
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:creators,id'
        ]);

        Creator::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Data terpilih berhasil dihapus');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new CreatorsImport, $request->file('file'));

            return back()->with('success', 'Berhasil upload kreator massal!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            return back()->with('error', 'Gagal upload. Cek baris excel anda.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new CreatorsExport($request), 'creators.xlsx');
    }

    public function blokir(Creator $creator)
    {
        $creator->update([
            'is_blocked' => 1
        ]);

        return redirect()->back()->with('success', 'Kreator berhasil diblokir.');
    }

    public function checkUsername(Request $request)
    {
        try {
            $exists = Creator::where('username', $request->input('username'))->exists();
            
            return response()->json([
                'success' => true,
                'exists'  => $exists
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server saat memvalidasi username. Silakan coba lagi.'
            ], 500);
        }
    }

    public function autoFill(Request $request)
    {
        $username = $request->input('username');

        if (!$username) {
            return response()->json([
                'success' => false, 
                'message' => 'Username tidak boleh kosong'
            ]);
        }

        $url = "https://www.fastmoss.com/api/author/search";
        $currentTime = (string) now()->timestamp;
        $nonce = (string) random_int(10000000, 99999999);

        // Parameter Query String
        $params = [
            "words" => $username,
            "_time" => $currentTime,
            "cnonce" => $nonce
        ];

        // Headers
        $headers = [
            "accept" => "application/json, text/plain, */*",
            "source" => "pc",
            "user-agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36"
        ];

        try {
            $response = Http::withHeaders($headers)->get($url, $params);

            if ($response->successful()) {
                $jsonData = $response->json();
                $authorList = $jsonData['data']['author_list'] ?? [];

                if (empty($authorList)) {
                    return response()->json(['success' => false, 'message' => 'Data kreator tidak ditemukan']);
                }

                $author = $authorList[0];

                // 1. CHANNEL PENJUALAN TERTINGGI
                $salesChannels = [
                    'video' => $author['video_28d_sale_amount'] ?? 0,
                    'live' => $author['live_28d_sale_amount'] ?? 0,
                    'kartu produk' => $author['product_card_sale_amount'] ?? 0,
                ];
                $topChannel = array_keys($salesChannels, max($salesChannels))[0];

                // 2. GENDER PENGIKUT
                $fansPortrait = $author['fansPortrait'] ?? [];
                $genders = $fansPortrait['follower_genders'] ?? [];
                
                $malePct = 0.0;
                $femalePct = 0.0;
                
                foreach ($genders as $g) {
                    if (($g['key'] ?? '') === 'male') {
                        $malePct = (float)($g['value'] ?? 0);
                    } elseif (($g['key'] ?? '') === 'female') {
                        $femalePct = (float)($g['value'] ?? 0);
                    }
                }

                if (abs($malePct - $femalePct) <= 1) {
                    $genderStatus = "seimbang";
                } elseif ($malePct > $femalePct) {
                    $genderStatus = "laki-laki";
                } else {
                    $genderStatus = "perempuan";
                }

                // 3. UMUR PENGIKUT (2 TERTINGGI)
                $ages = $fansPortrait['follower_ages'] ?? [];
                usort($ages, function($a, $b) {
                    return ((float)($b['value'] ?? 0)) <=> ((float)($a['value'] ?? 0));
                });
                
                $top2Ages = [];
                foreach (array_slice($ages, 0, 2) as $ageData) {
                    if (isset($ageData['key'])) {
                        $top2Ages[] = $ageData['key'];
                    }
                }

                $kategoriData = $author['sale_category_l1_name'] ?? [];
                
                $gmvShow = $author['sale_28d_amount_show'] ?? '0';
                $gmvData = 0;

                if ($gmvShow) {
                    $str = strtolower(str_replace(['RP', 'Rp', 'rp', ' '], '', $gmvShow));
                    
                    $multiplier = 1;

                    // CEK 'jt' (Juta) HARUS DI ATAS 't' (Triliun) AGAR TIDAK BENTROK
                    if (str_contains($str, 'jt')) { 
                        $multiplier = 1000000;
                        $str = str_replace('jt', '', $str);
                    } elseif (str_contains($str, 't')) { // Triliun
                        $multiplier = 1000000000000;
                        $str = str_replace('t', '', $str);
                    } elseif (str_contains($str, 'm')) { // Miliar
                        $multiplier = 1000000000;
                        $str = str_replace('m', '', $str);
                    } elseif (str_contains($str, 'rb')) { // Ribu
                        $multiplier = 1000;
                        $str = str_replace('rb', '', $str);
                    }

                    // Hapus titik (jika ada pemisah ribuan) lalu ubah koma jadi titik (sebagai desimal)
                    $str = str_replace('.', '', $str);
                    $str = str_replace(',', '.', $str);
                    
                    // Ubah jadi desimal PHP (float), kalikan, dan bulatkan
                    $gmvData = round((float)$str * $multiplier);
                }

                $itemTerjualData = $author['sale_28d_count'] ?? 0;

                // Mengembalikan response JSON
                return response()->json([
                    'success'  => true,
                    'channel'  => $topChannel,
                    'gender'   => $genderStatus,
                    'umur'     => $top2Ages,
                    'kategori' => $kategoriData,
                    'gmv'           => $gmvData,
                    'item_terjual'  => $itemTerjualData
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Gagal terhubung ke API Fastmoss']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
        }
    }
}