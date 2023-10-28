<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeterType;
use App\Models\NumberSequence;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index($user_type)
    {
        $users = User::role("user")->get();
        $staffs = User::with('roles')
                ->join('user_profile','user_profile.user_id','=','users.id')
                ->get()->filter(
                fn ($user) => $user->roles->whereIn('name', ["admin", "tabwater man", "finance"])->toArray()
            );

        return view('admin.users.index', compact('users', 'staffs'));
    }

    public function create(){
        $meter_sq_number = NumberSequence::get('meternumber');
        $zones = Zone::all();
        $meter_types = MeterType::all();
        $meternumber = $this->createInvoiceNumberString($meter_sq_number[0]->meternumber);
        return view('admin.users.create', compact('meternumber', 'zones', 'meter_types'));
    }

    public function show(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users.role', compact('user', 'roles', 'permissions'));
    }

    public function assignRole(Request $request, User $user)
    {
        if ($user->hasRole($request->role)) {
            return back()->with('message', 'Role exists.');
        }

        $user->assignRole($request->role);
        return back()->with('message', 'Role assigned.');
    }

    public function removeRole(User $user, Role $role)
    {
        if ($user->hasRole($role)) {
            $user->removeRole($role);
            return back()->with('message', 'Role removed.');
        }

        return back()->with('message', 'Role not exists.');
    }

    public function givePermission(Request $request, User $user)
    {
        if ($user->hasPermissionTo($request->permission)) {
            return back()->with('message', 'Permission exists.');
        }
        $user->givePermissionTo($request->permission);
        return back()->with('message', 'Permission added.');
    }

    public function revokePermission(User $user, Permission $permission)
    {
        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
            return back()->with('message', 'Permission revoked.');
        }
        return back()->with('message', 'Permission does not exists.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('admin')) {
            return back()->with('message', 'you are admin.');
        }
        $user->delete();

        return back()->with('message', 'User deleted.');
    }

    private function createInvoiceNumberString($id)
    {
        $invString = '';
        if ($id < 10) {
            $invString = '0000' . $id;
        } else if ($id >= 10 && $id < 100) {
            $invString = '000' . $id;
        } else if ($id >= 100 && $id < 1000) {
            $invString = '00' . $id;
        } elseif ($id >= 1000 && $id < 9999) {
            $invString = '0' . $id;
        } else {
            $invString = $id;
        }
        return "HS-".$invString;

    }
}
