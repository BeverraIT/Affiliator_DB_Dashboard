<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesBulkDelete;
use App\Http\Controllers\Concerns\ResolvesListQuery;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    use HandlesBulkDelete;
    use ResolvesListQuery;

    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        [$sort, $dir] = $this->resolveSort($request, ['id', 'name', 'email'], 'name');

        $users = User::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")))
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'sort', 'dir'));
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

        Cache::forget('dropdown_pics');

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
        Cache::forget('dropdown_pics');

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

        return $this->bulkDelete(User::class, $ids, 'user');
    }
}
