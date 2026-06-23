<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesBulkDelete;
use App\Http\Controllers\Concerns\ResolvesListQuery;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HandlesBulkDelete;
    use ResolvesListQuery;

    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        [$sort, $dir] = $this->resolveSort($request, ['id', 'name', 'created_at'], 'name');

        $categories = Category::search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.category.index', compact('categories', 'q', 'sort', 'dir'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create(['name' => $request->validated()['name']]);

        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update(['name' => $request->validated()['name']]);

        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        return $this->bulkDelete(Category::class, $request->input('ids'), 'kategori');
    }
}
