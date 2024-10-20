<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
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
       return $user = User::where('id',Auth::user()->id)->getRoleNames();
        $roles = Role::whereNotIn('name', ['super admin'])->get();
        return view('admin.roles.index', compact('roles'));
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
        FunctionsController::reset_auto_increment_when_deleted('roles');
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
