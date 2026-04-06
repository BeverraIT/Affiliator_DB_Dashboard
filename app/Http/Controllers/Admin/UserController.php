<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $sort = (string) $request->query('sort', 'name');
        $dir = strtolower((string) $request->query('dir', 'asc')) == 'asc' ? 'asc' : 'desc';
        
        $allowedSort = [
            'id',
            'name',
            'email'
        ];
        if(!in_array($sort, $allowedSort, true)) {
            $sort = 'name';
        }

        $users = User::query()
            ->search($q)
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'q',
            'sort',
            'dir'
        ));
    }


    public function create()
    {
        return view('admin.users.create');
    }


    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }


    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }


    public function update(UpdateUserRequest $request, User $user)
    {

        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }


    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $ids = $request->input('ids');
        
        if (in_array(Auth::id(), $ids)) {
            return redirect()->back()
                ->with('error', 'Gagal: Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
            User::whereIn('id', $ids)->delete();

            return redirect()->back()
                ->with('success', count($ids) . ' user berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}