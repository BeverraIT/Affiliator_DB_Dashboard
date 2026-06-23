<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesBulkDelete;
use App\Http\Controllers\Concerns\ResolvesListQuery;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

use App\Models\Product;

use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use App\Exports\TemplateExport;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    use HandlesBulkDelete;
    use ResolvesListQuery;

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
            'sku'       => $data['sku'],
            'harga'     => $data['harga'],
        ]);

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
            'sku'       => $data['sku'],
            'harga'     => $data['harga'],
        ]);

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }


    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Produk berhasil dihapus.');
    }


    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        return $this->bulkDelete(Product::class, $request->input('ids'), 'produk');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return back()->with('success', 'Berhasil upload produk massal!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $count = count($e->failures());
            $first = $e->failures()[0] ?? null;
            $detail = $first ? " Contoh baris {$first->row()}: " . implode(', ', $first->errors()) : '';
            return back()->with('error', "Gagal upload: {$count} baris tidak valid (cek SKU duplikat/format)." . $detail);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function downloadTemplate()
    {
        $headers = ['sku_utama', 'sku', 'harga'];

        return Excel::download(new TemplateExport($headers), 'Template_Produk.xlsx');
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'Produk_' . date('Ymd_His') . '.xlsx');
    }
}