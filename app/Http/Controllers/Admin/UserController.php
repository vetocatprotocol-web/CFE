<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->with('role');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        try {
            $tempPassword = $validated['password'] ?? Str::random(12);

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($tempPassword),
                'role_id' => $validated['role_id'],
                'status' => 'ACTIVE',
            ]);

            $message = 'Pengguna berhasil ditambahkan.';
            if (! isset($validated['password'])) {
                $message .= ' Password sementara: '.$tempPassword;
            }

            return redirect()->route('admin.users.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan pengguna: '.$e->getMessage());
        }
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        try {
            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role_id' => $validated['role_id'],
            ];

            if (! empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            return redirect()->route('admin.users.index')
                ->with('success', 'Pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui pengguna: '.$e->getMessage());
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $user->update(['status' => 'INACTIVE']);

            return redirect()->route('admin.users.index')
                ->with('success', 'Pengguna berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menonaktifkan pengguna: '.$e->getMessage());
        }
    }

    public function resetPassword(User $user): RedirectResponse
    {
        try {
            $tempPassword = Str::random(12);
            $user->update(['password' => Hash::make($tempPassword)]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Password berhasil direset. Password baru: '.$tempPassword);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mereset password: '.$e->getMessage());
        }
    }
}
