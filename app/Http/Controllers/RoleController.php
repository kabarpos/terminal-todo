<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Constructor untuk mendaftarkan middleware
     */
    public function __construct()
    {
        $this->middleware('permission:manage-roles');
    }

    public function index()
    {
        return Inertia::render('Roles/Index', [
            'roles' => Role::with('permissions')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'is_system' => $role->is_system,
                    'permissions' => $role->permissions->pluck('name'),
                    'created_at' => $role->created_at->format('d M Y')
                ])
        ]);
    }

    public function create()
    {
        return Inertia::render('Roles/Create', [
            'permissions' => Permission::all()->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'group' => explode(' ', $permission->name)[0] // Mengambil group dari nama permission
            ])->groupBy('group')
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            DB::beginTransaction();

            // Log untuk debugging
            \Log::info('Creating new role with permissions:', [
                'role_name' => $validated['name'],
                'permissions' => $validated['permissions']
            ]);

            $role = Role::create(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions']);

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('message', 'Role berhasil dibuat');

        } catch (ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error when creating role:', [
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating role: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat role');
        }
    }

    public function edit(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'Role sistem tidak dapat diubah');
        }

        return Inertia::render('Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ],
            'permissions' => Permission::all()->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'group' => explode(' ', $permission->name)[0]
            ])->groupBy('group')
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'Role sistem tidak dapat diubah');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            DB::beginTransaction();

            // Log untuk debugging
            \Log::info('Updating role with permissions:', [
                'role_id' => $role->id,
                'role_name' => $validated['name'],
                'permissions' => $validated['permissions']
            ]);

            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions']);

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('message', 'Role berhasil diperbarui');

        } catch (ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error when updating role:', [
                'role_id' => $role->id,
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating role:', [
                'role_id' => $role->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memperbarui role. ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'Role sistem tidak dapat dihapus');
        }

        try {
            DB::beginTransaction();
            
            // Remove all permissions from role
            $role->syncPermissions([]);
            
            // Delete the role
            $role->delete();

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('message', 'Role berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus role. ' . $e->getMessage());
        }
    }
}