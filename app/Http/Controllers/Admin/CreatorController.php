<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CreatorsExport;
use App\Exports\TemplateExport;
use App\Http\Controllers\Concerns\HandlesBulkDelete;
use App\Http\Controllers\Concerns\RedirectsSafely;
use App\Http\Controllers\Concerns\ResolvesListQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreatorRequest;
use App\Http\Requests\UpdateCreatorRequest;
use App\Imports\CreatorsImport;
use App\Models\Age;
use App\Models\Category;
use App\Models\ContentStyle;
use App\Models\Creator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CreatorController extends Controller
{
    use HandlesBulkDelete;
    use RedirectsSafely;
    use ResolvesListQuery;

    /** Aksi destruktif hanya untuk super admin — cek yang sama dengan gate di Blade. */
    private function authorizeSuperAdmin(): void
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'super admin') {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $filterUmur = $request->query('filter_umur', []);
        $filterKategori = $request->query('filter_kategori', []);
        $filterStyle = $request->query('filter_style', []);
        $filterNoWa = $request->query('filter_no_wa');
        $filterUser = $request->query('filter_user', []);
        $minGmv = $request->query('min_gmv');
        $maxGmv = $request->query('max_gmv');

        [$sort, $dir] = $this->resolveSort(
            $request,
            ['id', 'username', 'gmv', 'itemsterjual', 'channel', 'genderpengikut'],
            'id',
            'desc'
        );

        $query = Creator::with([
            'user:id,name',
            'contentStyles:id,name',
            'categories:id,name',
            'ages:id,name',
        ]);

        if (! empty($filterUmur)) {
            $query->whereHas('ages', fn ($sq) => $sq->whereIn('ages.id', $filterUmur));
        }

        if (! empty($filterKategori)) {
            $query->whereHas('categories', fn ($sq) => $sq->whereIn('categories.id', $filterKategori));
        }

        if (! empty($filterStyle)) {
            $query->whereHas('contentStyles', fn ($sq) => $sq->whereIn('contentstyles.id', $filterStyle));
        }

        if (! empty($filterUser)) {
            $query->whereIn('user_id', $filterUser);
        }

        $query->when($minGmv, fn ($sq) => $sq->where('gmv', '>=', preg_replace('/[^0-9]/', '', $minGmv)));
        $query->when($maxGmv, fn ($sq) => $sq->where('gmv', '<=', preg_replace('/[^0-9]/', '', $maxGmv)));

        if ($request->has('filter_top_creator')) {
            $query->where('top_creator', 1);
        }

        if ($request->has('filter_is_blocked')) {
            $query->where('is_blocked', 1);
        }

        $query->when($filterNoWa, fn ($sq) => $sq->whereNull('nomerwa'));

        $creators = $query->search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        $ageOptions = Age::orderBy('name')->get();
        $categoryOptions = Category::orderBy('name')->get();
        $styleOptions = ContentStyle::orderBy('name')->get();
        $userOptions = User::orderBy('name')->get();

        return view('admin.kreator.index', compact(
            'creators', 'q', 'sort', 'dir',
            'ageOptions', 'categoryOptions', 'styleOptions', 'userOptions',
            'filterUmur', 'filterKategori', 'filterStyle', 'filterNoWa', 'filterUser',
            'minGmv', 'maxGmv',
        ));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $contentStyles = ContentStyle::orderBy('name')->get();
        $ages = Age::orderBy('id')->get();

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

        $categories = Category::orderBy('name')->pluck('name', 'id');
        $contentStyles = ContentStyle::orderBy('name')->pluck('name', 'id');
        $ages = Age::orderBy('name')->pluck('name', 'id');

        $selectedAgeIds = $creator->ages->modelKeys();
        $selectedCategoryIds = $creator->categories->modelKeys();
        $selectedStyleIds = $creator->contentStyles->modelKeys();

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
            $creator->update($validated->except(['ages', 'categories', 'contentstyles']));
            $creator->ages()->sync($validated->collect('ages'));
            $creator->categories()->sync($validated->collect('categories'));
            $creator->contentStyles()->sync($validated->collect('contentstyles'));
        });

        return $this->redirectSafely($request, 'admin.kreator.index')
            ->with('success', 'Kreator berhasil diperbarui.');
    }

    public function destroy(Creator $creator)
    {
        $this->authorizeSuperAdmin();

        DB::transaction(function () use ($creator) {
            $creator->ages()->detach();
            $creator->categories()->detach();
            $creator->contentStyles()->detach();
            $creator->delete();
        });

        return redirect()
            ->route('admin.kreator.index')
            ->with('success', 'Kreator berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:creators,id',
        ]);

        $ids = $request->input('ids');

        try {
            // Lepas pivot dulu agar tidak meninggalkan baris orphan di tabel relasi.
            DB::transaction(function () use ($ids) {
                DB::table('age_creator')->whereIn('creator_id', $ids)->delete();
                DB::table('category_creator')->whereIn('creator_id', $ids)->delete();
                DB::table('content_style_creator')->whereIn('creator_id', $ids)->delete();
                Creator::whereIn('id', $ids)->delete();
            });

            return back()->with('success', count($ids).' kreator berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Gagal bulk delete kreator', ['exception' => $e]);

            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new CreatorsImport;
            Excel::import($import, $request->file('file'));

            $msg = "Berhasil import {$import->imported} kreator.";
            if ($import->skipped > 0) {
                $msg .= " {$import->skipped} dilewati (username sudah ada).";
            }

            return back()->with('success', $msg);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $count = count($e->failures());
            $first = $e->failures()[0] ?? null;
            $detail = $first ? " Contoh baris {$first->row()}: ".implode(', ', $first->errors()) : '';

            return back()->with('error', "Gagal upload: {$count} baris tidak valid.".$detail);

        } catch (\Exception $e) {
            Log::error('Gagal import kreator', ['exception' => $e]);

            return back()->with('error', 'Terjadi kesalahan saat import. Periksa kembali format file dan datanya.');
        }
    }

    public function downloadTemplate()
    {
        $headers = ['username', 'channel', 'gmv', 'itemsterjual', 'genderpengikut', 'namawa', 'nomerwa', 'kategori', 'umur', 'konten_style'];

        return Excel::download(new TemplateExport($headers), 'Template_Kreator.xlsx');
    }

    public function export(Request $request)
    {
        return Excel::download(new CreatorsExport($request), 'Kreator_'.date('Ymd_His').'.xlsx');
    }

    public function blokir(Creator $creator)
    {
        $this->authorizeSuperAdmin();

        $creator->update([
            'is_blocked' => 1,
        ]);

        return redirect()->back()->with('success', 'Kreator berhasil diblokir.');
    }

    public function checkUsername(Request $request)
    {
        try {
            // Normalisasi sama dengan Store/UpdateCreatorRequest agar hasil cek AJAX
            // konsisten dengan rule unique saat submit.
            $username = Creator::normalizeUsername((string) $request->input('username'));
            $exists = $username !== '' && Creator::where('username', $username)->exists();

            return response()->json([
                'success' => true,
                'exists' => $exists,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server saat memvalidasi username. Silakan coba lagi.',
            ], 500);
        }
    }

    public function autoFill(Request $request)
    {
        $username = $request->input('username');
        if (! $username) {
            return response()->json([
                'success' => false,
                'message' => 'Username tidak boleh kosong',
            ]);
        }

        $url = 'https://www.fastmoss.com/api/author/search';
        $currentTime = (string) now()->timestamp;
        $nonce = (string) random_int(10000000, 99999999);
        $params = [
            'words' => $username,
            '_time' => $currentTime,
            'cnonce' => $nonce,
        ];
        $headers = [
            'accept' => 'application/json, text/plain, */*',
            'source' => 'pc',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',
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

                $salesChannels = [
                    'video' => $author['video_28d_sale_amount'] ?? 0,
                    'live' => $author['live_28d_sale_amount'] ?? 0,
                    'kartu produk' => $author['product_card_sale_amount'] ?? 0,
                ];
                $topChannel = array_keys($salesChannels, max($salesChannels))[0];

                $fansPortrait = $author['fansPortrait'] ?? [];
                $genders = $fansPortrait['follower_genders'] ?? [];

                $malePct = 0.0;
                $femalePct = 0.0;

                foreach ($genders as $g) {
                    if (($g['key'] ?? '') === 'male') {
                        $malePct = (float) ($g['value'] ?? 0);
                    } elseif (($g['key'] ?? '') === 'female') {
                        $femalePct = (float) ($g['value'] ?? 0);
                    }
                }

                if (abs($malePct - $femalePct) <= 1) {
                    $genderStatus = 'seimbang';
                } elseif ($malePct > $femalePct) {
                    $genderStatus = 'laki-laki';
                } else {
                    $genderStatus = 'perempuan';
                }

                $ages = $fansPortrait['follower_ages'] ?? [];
                usort($ages, fn ($a, $b) => ((float) ($b['value'] ?? 0)) <=> ((float) ($a['value'] ?? 0)));
                $top2Ages = array_values(array_filter(array_column(array_slice($ages, 0, 2), 'key')));

                $kategori = $author['sale_category_l1_name'] ?? [];
                $gmvShow = $author['sale_28d_amount_show'] ?? '0';

                $gmv = 0;
                if ($gmvShow) {
                    $str = str_replace([' ', 'rp'], '', strtolower($gmvShow));
                    $multiplier = 1;

                    if (str_contains($str, 'jt')) {
                        $multiplier = 1_000_000;
                        $str = str_replace('jt', '', $str);
                    } elseif (str_contains($str, 't')) {
                        $multiplier = 1_000_000_000_000;
                        $str = str_replace('t', '', $str);
                    } elseif (str_contains($str, 'm')) {
                        $multiplier = 1_000_000_000;
                        $str = str_replace('m', '', $str);
                    } elseif (str_contains($str, 'rb')) {
                        $multiplier = 1_000;
                        $str = str_replace('rb', '', $str);
                    }

                    $str = str_replace(['.', ','], ['', '.'], $str);
                    $gmv = round((float) $str * $multiplier);
                }

                $itemTerjual = $author['sale_28d_count'] ?? 0;

                return response()->json([
                    'success' => true,
                    'channel' => $topChannel,
                    'gender' => $genderStatus,
                    'umur' => $top2Ages,
                    'kategori' => $kategori,
                    'gmv' => $gmv,
                    'item_terjual' => $itemTerjual,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Gagal terhubung ke API Fastmoss']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
        }
    }
}
