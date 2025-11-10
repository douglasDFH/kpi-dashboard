<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        // Búsqueda por nombre o email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por rol
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filtro por estado
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::where('is_active', true)->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        $user = User::create($validated);

        // Registrar en auditoría
        AuditLog::logAction(
            'created',
            User::class,
            $user->id,
            "Usuario creado: {$user->name}",
            [],
            $user->toArray()
        );

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role', 'auditLogs');
        $auditLogs = $user->auditLogs()->latest()->paginate(10);
        
        return view('users.show', compact('user', 'auditLogs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->get();
        $permissions = \App\Models\Permission::all()->groupBy('module');
        $userPermissions = $user->customPermissions->pluck('id')->toArray();
        
        return view('users.edit', compact('user', 'roles', 'permissions', 'userPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $oldValues = $user->toArray();

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        // Sincronizar permisos personalizados
        if ($request->has('permissions')) {
            $user->customPermissions()->sync($request->permissions);
        } else {
            $user->customPermissions()->sync([]);
        }

        // Registrar en auditoría
        AuditLog::logAction(
            'updated',
            User::class,
            $user->id,
            "Usuario actualizado: {$user->name}",
            $oldValues,
            $user->fresh()->toArray()
        );

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevenir que el usuario se elimine a sí mismo
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $oldValues = $user->toArray();
        $userName = $user->name;

        // Registrar en auditoría antes de eliminar
        AuditLog::logAction(
            'deleted',
            User::class,
            $user->id,
            "Usuario eliminado: {$userName}",
            $oldValues,
            []
        );

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(User $user)
    {
        // Prevenir que el usuario se desactive a sí mismo
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes desactivar tu propio usuario.'
            ], 400);
        }

        $oldStatus = $user->is_active;
        $user->is_active = !$user->is_active;
        $user->save();

        // Registrar en auditoría
        AuditLog::logAction(
            'updated',
            User::class,
            $user->id,
            "Estado del usuario cambiado: {$user->name}",
            ['is_active' => $oldStatus],
            ['is_active' => $user->is_active]
        );

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
            'message' => $user->is_active ? 'Usuario activado' : 'Usuario desactivado'
        ]);
    }
}
