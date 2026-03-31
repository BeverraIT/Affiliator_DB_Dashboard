<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $sort = (string) $request->query('sort', 'name');
        $dir  = strtolower((string) $request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSort = [
            'id',
            'sku_utama',
            'sku',
            'harga'
        ];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'sku';
        }

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

        try {
            DB::transaction(fn() => Product::whereIn('id', $request->ids)->delete());

            return back()->with('success', 'Data yang dipilih berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return back()->with('success', 'Berhasil upload produk massal!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->with('error', 'Gagal upload. Cek baris excel anda (mungkin SKU duplikat).');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
}