<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class roleController extends Controller
{
    //

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'string|unique:roles|required'
        ]);
        $user = Auth::user();
        if (!$user->can('role-create')) {
            return response()->json(['message' => 'You are not allowed to create role'], 401);

        }
        $role = Role::create(['name' => $request->name]);
        return response()->json(['message' => 'Role created', 'role' => ['id' => $role->id, 'name' => $role->name]], 201);

    }

    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'string|unique:permissions|required'
        ]);
        $user = Auth::user();
        if (!$user->can('permission-create')) {
            return response()->json(['message' => 'You are not allowed to create permission'], 401);

        }
        $permission = Permission::create(['name' => $request->name]);
        return response()->json(['message' => 'Permission created', 'role' => ['id' => $permission->id, 'name' => $permission->name]], 201);

    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'numeric|required',
            'role_id'=>'numeric|required'
        ]);
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json(['message' => 'No user found.'], 404);

        }
        $role = Role::where('id', $request->role_id)->first();
        if (!$role) {
            return response()->json(['message' => 'No role found.'], 404);

        }
        $admin = Auth::user();
        if (!$admin->can('assign-role')) {
            return response()->json(['message' => 'You are not allowed to create permission'], 401);
        }
        $user->assignRole([$role->id]);
        return response()->json(['message' => 'Role assigned to user','user'=>$user,'role'=>$role], 200);



    }

    public function assignPermission(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
            'permission_id'=>'required'
        ]);
        $permission = Permission::where('id', $request->permission_id)->first();
        if (!$permission) {
            return response()->json(['message' => 'No permission found.'], 404);

        }
        $role = Role::where('id', $request->role_id)->first();
        if (!$role) {
            return response()->json(['message' => 'No role found.'], 404);

        }
        $admin = Auth::user();
        if (!$admin->can('assign-permission')) {
            return response()->json(['message' => 'You are not allowed to assign permission'], 401);
        }
        $role->givePermissionTo($permission);
        return response()->json(['message' => 'Permission assigned'], 200);

    }
}
