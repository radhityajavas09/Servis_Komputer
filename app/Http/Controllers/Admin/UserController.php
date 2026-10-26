<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RolePengguna;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users with search, role filter, and pagination.
     */
    public function index(Request $request): View
    {
        $query = User::query()
            ->withCount(['servisSebagaiPetugas', 'servisSebagaiTeknisi'])
            ->latest('id_user');

        // Search by name or email
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            if (RolePengguna::tryFrom($role)) {
                $query->where('role', $role);
            }
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.user.index', [
            'users' => $users,
            'search' => $request->input('q', ''),
            'selectedRole' => $request->input('role', ''),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()->route('admin.user.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.user.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['nama', 'email', 'role']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Remove the specified user from storage with strict business rule checks.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // 1. Tidak boleh menghapus akun sendiri
        if ($user->id_user === $request->user()->id_user) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // 2. Tidak boleh menghapus Administrator terakhir
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus Administrator terakhir dalam sistem.');
        }

        // 3. User yang sudah tercatat di servis tidak dapat dihapus
        if ($user->servisSebagaiPetugas()->exists() || $user->servisSebagaiTeknisi()->exists()) {
            return back()->with('error', 'User tidak dapat dihapus karena sudah tercatat pada data servis.');
        }

        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'Data berhasil dihapus');
    }
}
