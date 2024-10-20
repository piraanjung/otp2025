<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $permissionSql = Permission::all();
        $permissions = collect($permissionSql)->groupBy("permission_group");
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required']);

        Permission::create($validated);

        return to_route('admin.permissions.index')->with('message', 'Permission created.');
    }

    public function edit(Permission $permission)
    {
        $roles = Role::all();
        return view('admin.permissions.edit', compact('permission', 'roles'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate(['name' => 'required', 'description'=> 'required' ,'permission_group'=> 'required']);
        $permission->update($validated);

        return to_route('admin.permissions.index')->with(['message', 'Permission updated.', 'color'=> 'success']);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        // FunctionsController::reset_auto_increment_when_deleted('permissions');
        return back()->with(['message', 'Permission deleted.', 'color'=> 'danger']);
    }

    public function assignRole(Request $request, Permission $permission)
    {
        if ($permission->hasRole($request->role)) {
            return back()->with(['message', 'Role exists.', 'color'=> 'success']);
        }

        $permission->assignRole($request->role);
        return back()->with(['message', 'Role assigned.', 'color'=> 'success']);
    }

    public function removeRole(Permission $permission, Role $role)
    {
        if ($permission->hasRole($role)) {
            $permission->removeRole($role);
            return back()->with(['message', 'Role removed.', 'color'=> 'success']);
        }

        return back()->with(['message', 'Role not exists.', 'color'=> 'success']);
    }
}
