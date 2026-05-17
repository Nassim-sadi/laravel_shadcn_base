<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::query()
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->with('permissions')
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($roles);
    }

    public function store(RoleRequest $request)
    {
        $this->authorize('create', Role::class);

        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
        ]);

        if (!empty($validated['permissions'])) {
            $permissions = Permission::whereIn('name', $validated['permissions'])->get();
            $role->givePermissionTo($permissions);
        }

        activity_log('role.created', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'user_id' => auth()->id(),
        ]);

        return response()->json($role, 201);
    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);

        $role->load('permissions', 'users');
        return response()->json($role);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $this->authorize('update', $role);

        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'] ?? $role->name,
            'description' => $validated['description'] ?? $role->description,
        ]);

        if (isset($validated['permissions'])) {
            $permissions = Permission::whereIn('name', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        activity_log('role.updated', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'user_id' => auth()->id(),
        ]);

        return response()->json($role);
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        if (in_array($role->name, ['super_admin', 'admin', 'user'])) {
            throw ValidationException::withMessages([
                'name' => ["Cannot delete system role: {$role->name}"],
            ]);
        }

        $roleName = $role->name;
        $role->delete();

        activity_log('role.deleted', [
            'role_name' => $roleName,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Role deleted successfully']);
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $permissions = Permission::whereIn('name', $validated['permissions'])->get();
        $role->syncPermissions($permissions);

        activity_log('role.permissions_assigned', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'permissions' => $validated['permissions'],
            'user_id' => auth()->id(),
        ]);

        return response()->json($role);
    }
}