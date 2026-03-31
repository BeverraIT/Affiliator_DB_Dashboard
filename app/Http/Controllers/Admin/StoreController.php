<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateStoreRequest;


class StoreController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $sort = $request->query('sort', 'name');
        $dir  = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSorts = [
            'id',
            'name'
        ];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        $stores = Store::query()
            ->search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.toko.index', compact(
            'stores',
            'q',
            'sort',
            'dir'
        ));
    }


    public function create()
    {
        return view('admin.toko.create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:stores,name'],
        ], [
            'name.required' => 'Nama toko wajib diisi.',
            'name.unique'   => 'Nama toko sudah terdaftar.',
        ]);

        Store::create([
            'name' => trim($data['name']),
        ]);

        return redirect()
            ->route('admin.toko.index')
            ->with('success', 'Toko berhasil ditambahkan.');
    }


    public function edit(Store $store)
    {
        return view('admin.toko.edit', compact('store'));
    }


    public function update(UpdateStoreRequest $request, Store $store)
    {
        $store->update($request->validated());

        return redirect()
            ->route('admin.toko.index')
            ->with('success', 'Toko berhasil diperbarui.');
    }


    public function destroy(Store $store)
    {
        $name = $store->name;
        $store->delete();

        return redirect()
            ->route('admin.toko.index')
            ->with('success', 'Toko "' . $name . '" berhasil dihapus.');
    }

    
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:stores,id',
        ]);

        try {
            $ids = $request->input('ids');
            Store::whereIn('id', $ids)->delete();

            return redirect()->back()
                ->with('success', count($ids) . ' toko berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}