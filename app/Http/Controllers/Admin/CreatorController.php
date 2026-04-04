<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContentStyle;
use App\Models\Creator;
use App\Models\Age;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCreatorRequest;
use App\Http\Requests\UpdateCreatorRequest;
use App\Http\Requests\CheckUsernameRequest;
use App\Imports\CreatorsImport;
use App\Exports\CreatorsExport;
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
        $query = Creator::query()
            ->with([
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
            $data['user_id'] = auth()->id();

            $creator = Creator::create($data->all());
            $creator->ages()->sync($validated->collect('ages'));
            $creator->categories()->sync($validated->collect('categories'));
            $creator->contentStyles()->sync($validated->collect('contentstyles'));
        });

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

    // Ini baru di refactor tapi belum di tes, jadi kemungkinan ada error. Tolong dicek lagi ya kalau mau dipakai
    public function checkUsername(CheckUsernameRequest $request)
    {
        try {
            $exists = Creator::where('username', $request->input('username'))->exists();
            
            return response()->json([
                'success' => true,
                'exists'  => $exists
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error saat checkUsername: ' . $e->getMessage());

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

        // Parameter Query String
        $params = [
            "page" => "1",
            "pagesize" => "10",
            "df" => "ZnNfaHR0cHM6Ly93d3cuZmFzdG1vc3MuY29tX3Bz",
            "region" => "ID",
            "order" => "12,2",
            "shop_window" => "1",
            "words" => $username,
            "_time" => "1773295942",
            "cnonce" => "83508282"
        ];

        // Headers
        $headers = [
            "accept" => "application/json, text/plain, */*",
            "accept-language" => "en-GB,en-US;q=0.9,en;q=0.8",
            "cookie" => 'NEXT_LOCALE=id; region=ID; utm_country=ID; utm_south=google; utm_id=kw_id_0923_01; utm_lang=id; userTimeZone=Asia%2FJakarta; fp_visid=912b5bfd6424bbde69459cee5dc9fda0; _gcl_aw=GCL.1773290988.CjwKCAjwpcTNBhA5EiwAdO1S9lt_4DP4gt6ISqyWQ5WgM4iB_ehBXxS9O1jKwfdAqCeHI6kSLpmdTRoCzycQAvD_BwE; _gcl_gs=2.1.k1$i1773290985$u149463969; _gcl_au=1.1.474744362.1773290988; _ga=GA1.1.1586303629.1773290988; gg_client_id=1586303629.1773290988; _clck=7sfctb%5E2%5Eg4a%5E0%5E2262; _tt_enable_cookie=1; _ttp=01KKG629BYXGPQATJYM0B7TPQC_.tt.1; _fbp=fb.1.1773290989053.502956957458350040; _rdt_uuid=1773290988327.48840761-48dd-46bb-a8f0-7c86575933f3; _uetsid=e6568ea01dce11f1b3d9ed65d492f8b8|7ys4d2|2|g4a|0|2262; ttcsid_CJMMQFRC77U1G7J3JGP0=1773293735736::d6Y0pYVYLQDa2CgcBd32.2.1773293893983.1; ttcsid=1773293735735::O_1M44FYXWiOeS1MogEx.2.1773293893986.0; ttcsid_CJOP1H3C77UDO397C3M0=1773293735736::KUBn_mXhM1XtC4DXb9FU.2.1773293893986.1; _uetvid=e65681b01dce11f1b8da851d4e4a9640|gerv6r|1773295735236|2|1|bat.bing.com/p/insights/c/i; _clsk=47t0aw%5E1773295735707%5E2%5E1%5Ewww.clarity.ms%2Feus2-c%2Fcollect; _ga_GD8ST04HB5=GS2.1.s1773295734$o3$g1$t1773295941$j60$l0$h1569402406; _ga_J8P3E5KDGJ=GS2.1.s1773295734$o3$g1$t1773295941$j60$l0$h1621288018',
            "fm-sign" => "2ea8c6a30b24f13fb7a524aad92611b1",
            "lang" => "ID_ID",
            "referer" => "https://www.fastmoss.com/id/influencer/search?shop_window=1&page=1&words={$username}",
            "region" => "ID",
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
            Log::error("Parsing Error untuk {$username}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
        }
    }
}