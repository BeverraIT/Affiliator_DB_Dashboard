<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

use App\Models\Category;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $q      = (string) $request->query('q', '');
        $sort   = $request->query('sort', 'name');
        $dir    = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = [
            'id',
            'name',
            'created_at'
        ];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        $categories = Category::search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.category.index', compact(
            'categories',
            'q',
            'sort',
            'dir'
        ));
    }


    public function create()
    {
        return view('admin.category.create');
    }


    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        Category::create([
            'name' => $data['name'],
        ]);

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
        $data = $request->validated();
        $category->update([
            'name' => $data['name'],
        ]);

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

        try {
            $ids = $request->input('ids');
            Category::whereIn('id', $ids)->delete();

            return redirect()->back()
                ->with('success', count($ids) . ' kategori berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}