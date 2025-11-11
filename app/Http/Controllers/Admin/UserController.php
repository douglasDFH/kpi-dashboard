<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{

    /**
     * Display a listing of users
     */
    public function index(): View
    {
        $users = User::with('role')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user
     */
    public function create(): View
    {
        $roles = Role::where('is_active', true)->orderBy('display_name')->get();

        return view('admin.users.create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        try {
            $user = User::create($validated);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'Usuario creado exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user): View
    {
        $user->load('role');

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing user
     */
    public function edit(User $user): View
    {
        $roles = Role::where('is_active', true)->orderBy('display_name')->get();

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        try {
            $user->update($validated);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Soft delete the specified user
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting own account
        if ($user->id === Auth::user()->id()) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'No puedes eliminar tu propia cuenta']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado');
    }
}