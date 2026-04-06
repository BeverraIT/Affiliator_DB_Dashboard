<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentStyleRequest;
use App\Http\Requests\UpdateContentStyleRequest;

use App\Models\ContentStyle;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;


class ContentStyleController extends Controller
{
    public function index(Request $request)
    {
        $q      = (string) $request->query('q', '');
        $sort   = $request->query('sort', 'name');
        $dir    = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = [
            'id',
            'name',
        ];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        $contentStyles = ContentStyle::search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();
        
        return view('admin.content-styles.index', compact('contentStyles', 'q', 'sort', 'dir'));
    }


    public function create()
    {
        return view('admin.content-styles.create');
    }


    public function store(StoreContentStyleRequest $request)
    {
        $data = $request->validated();
        ContentStyle::create([
            'name' => $data['name'],
        ]);

        return redirect()
            ->route('admin.content-styles.index')
            ->with('success', 'Konten style berhasil ditambahkan.');
    }


    public function edit(ContentStyle $contentStyle)
    {
        return view('admin.content-styles.edit', compact('contentStyle'));
    }


    public function update(UpdateContentStyleRequest $request, ContentStyle $contentStyle)
    {
        $data = $request->validated();
        $contentStyle->update([
            'name' => $data['name'],
        ]);

        return redirect()
            ->route('admin.content-styles.index')
            ->with('success', 'Konten style berhasil diperbarui.');
    }


    public function destroy(ContentStyle $contentStyle)
    {
        try {
            $contentStyle->delete();

            return redirect()
                ->route('admin.content-styles.index')
                ->with('success', 'Konten style berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.content-styles.index')
                ->with('delete_error', 'Tidak bisa menghapus: konten style masih dipakai oleh data lain.');
        }
    }

    
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:contentstyles,id',
        ]);

        try {
            $ids = $request->input('ids');
            ContentStyle::whereIn('id', $ids)->delete();

            return redirect()
                ->back()
                ->with('success', count($ids) . ' konten style berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}