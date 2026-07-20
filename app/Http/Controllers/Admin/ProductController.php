<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductsExport;
use App\Exports\TemplateExport;
use App\Http\Controllers\Concerns\HandlesBulkDelete;
use App\Http\Controllers\Concerns\ResolvesListQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\ProductsImport;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    use HandlesBulkDelete;
    use ResolvesListQuery;

    /** Dropdown SKU utama di halaman kirim sampel di-cache; invalidasi tiap data produk berubah. */
    private function clearSkuDropdownCache(): void
    {
        Cache::forget('dropdown_skus');
    }

    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        [$sort, $dir] = $this->resolveSort($request, ['id', 'sku_utama', 'sku', 'harga'], 'sku');

        $products = Product::query()
            ->search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.product.index', compact(
            'products',
            'q',
            'sort',
            'dir'
        ));
    }

    public function create()
    {
        return view('admin.product.create');
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        Product::create([
            'sku_utama' => $data['sku_utama'],
            'sku' => $data['sku'],
            'harga' => $data['harga'],
        ]);
        $this->clearSkuDropdownCache();

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('admin.product.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $product->update([
            'sku_utama' => $data['sku_utama'],
            'sku' => $data['sku'],
            'harga' => $data['harga'],
        ]);
        $this->clearSkuDropdownCache();

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        $this->clearSkuDropdownCache();

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        $response = $this->bulkDelete(Product::class, $request->input('ids'), 'produk');
        $this->clearSkuDropdownCache();

        return $response;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new ProductsImport;
            Excel::import($import, $request->file('file'));
            $this->clearSkuDropdownCache();

            $detail = [];
            if ($import->skippedDuplicate > 0) {
                $detail[] = "{$import->skippedDuplicate} baris dilewati karena SKU sudah ada.";
            }
            if ($import->skippedInvalid > 0) {
                $detail[] = "{$import->skippedInvalid} baris dilewati karena data tidak valid (SKU kosong / harga bukan angka).";
            }
            $detail = implode(' ', $detail);

            if ($import->imported === 0 && $detail !== '') {
                return back()->with('error', trim('Tidak ada produk baru yang diimport. '.$detail));
            }

            return back()->with('success', trim("Berhasil import {$import->imported} produk. ".$detail));
        } catch (\InvalidArgumentException $e) {
            // Pesan ramah dari guard header template.
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal import produk', ['exception' => $e]);

            return back()->with('error', 'Terjadi kesalahan saat import. Periksa kembali format file dan datanya.');
        }
    }

    public function downloadTemplate()
    {
        $headers = ['sku_utama', 'sku', 'harga'];

        return Excel::download(new TemplateExport($headers), 'Template_Produk.xlsx');
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'Produk_'.date('Ymd_His').'.xlsx');
    }
}
