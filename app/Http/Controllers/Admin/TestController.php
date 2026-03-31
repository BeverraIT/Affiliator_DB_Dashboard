<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Test;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::query()
            ->paginate(10)
            ->withQueryString();

        return view('admin.dashboard.index', compact('tests'));
    }

    public function create()
    {
        return view('admin.dashboard.index');
    }

    public function store(StoreTestRequest $request)
    {
        $data = $request->validated();
        Test::create([
            'name'  => $data['name'],
        ]);

        return redirect()
            ->route('admin.dashboard.index')
            ->with('success', 'Test berhasil ditambahkan.');
    }

    public function edit(Test $test)
    {
        return view('admin.dashboard.index', compact('test'));
    }

    public function update(UpdateTestRequest $request, Test $test)
    {
        $data = $request->validated();
        $test->update([
            'name'  => $data['name'],
        ]);

        return redirect()
            ->route('admin.dashboard.index')
            ->with('success', 'Test berhasil diperbarui.');
    }

    public function destroy(Test $test)
    {
        $test->delete();

        return redirect()
            ->route('admin.dashboard.index')
            ->with('success', 'Test berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:tests,id',
        ]);

        try {
            $ids = $request->input('ids');
            Test::whereIn('id', $ids)->delete();

            return redirect()->back()
                ->with('success', count($ids) . ' Test berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function checkUsername(Request $request)
    {
        $exists = Creator::where('username', $request->username)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }
}