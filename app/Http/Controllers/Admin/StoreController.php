<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesBulkDelete;
use App\Http\Controllers\Concerns\ResolvesListQuery;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StoreController extends Controller
{
    use HandlesBulkDelete;
    use ResolvesListQuery;

    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        [$sort, $dir] = $this->resolveSort($request, ['id', 'name'], 'name');

        $stores = Store::query()
            ->search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.toko.index', compact('stores', 'q', 'sort', 'dir'));
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

        Store::create(['name' => trim($data['name'])]);
        Cache::forget('dropdown_stores');

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
        Cache::forget('dropdown_stores');

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

        return $this->bulkDelete(Store::class, $request->input('ids'), 'toko');
    }
}
