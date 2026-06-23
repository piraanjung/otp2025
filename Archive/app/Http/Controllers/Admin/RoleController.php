<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeUnit\FunctionUnit;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::whereNotIn('name', ['super admin'])->get();
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        session(['orgInfos' => $orgInfos]);

        return view('admin.roles.index', compact('roles', 'orgInfos'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => ['required', 'min:3']]);
        Role::create($validated);

        return to_route('admin.roles.index')->with(['message', 'Role Created successfully.', 'color'=> 'success']);
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate(['name' => ['required', 'min:3']]);
        $role->update($validated);

        return to_route('admin.roles.index')->with(['message', 'Role Updated successfully.', 'color'=> 'success']);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return back()->with(['message', 'Role deleted.', 'color'=> 'success']);
    }

    public function givePermission(Request $request, Role $role)
    {
        if($role->hasPermissionTo($request->permission)){
            return back()->with(['message', 'Permission exists.', 'color'=> 'success']);
        }
        $role->givePermissionTo($request->permission);
        return back()->with(['message', 'Permission added.', 'color'=> 'success']);
    }

    public function revokePermission(Role $role, Permission $permission)
    {
        if($role->hasPermissionTo($permission)){
            $role->revokePermissionTo($permission);
            return back()->with(['message', 'Permission revoked.', 'color'=> 'success']);
        }
        return back()->with(['message', 'Permission not exists.', 'color'=> 'success']);
    }
}
