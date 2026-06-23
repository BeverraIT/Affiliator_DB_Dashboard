<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesBulkDelete;
use App\Http\Controllers\Concerns\ResolvesListQuery;
use App\Http\Requests\StoreContentStyleRequest;
use App\Http\Requests\UpdateContentStyleRequest;
use App\Models\ContentStyle;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ContentStyleController extends Controller
{
    use HandlesBulkDelete;
    use ResolvesListQuery;

    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        [$sort, $dir] = $this->resolveSort($request, ['id', 'name'], 'name');

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
        ContentStyle::create(['name' => $request->validated()['name']]);

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
        $contentStyle->update(['name' => $request->validated()['name']]);

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

        return $this->bulkDelete(ContentStyle::class, $request->input('ids'), 'konten style');
    }
}
