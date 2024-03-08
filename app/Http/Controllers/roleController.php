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
            'name' => 'string|unique:roles|required',
            'permissions'=>'required|array'
        ]);
        $user = Auth::user();
        if (!$user->can('role-create')) {
            return response()->json(['message' => 'You are not allowed to create role'], 401);

        }
        $role = Role::create(['name' => $request->name]);
        $permission_list = [];
        foreach ($request->permissions as $permission){
            $p = Permission::where('name',$permission)->first();
            if ($p){
                array_push($permission_list,$p->id);
            }
        }
        $role->syncPermissions($permission_list);
        return response()->json(['message' => 'Role created', 'role'=>$role,'permissions'=>$permission_list], 201);

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
            'role_names' => 'required|array',

        ]);
        $admin = Auth::user();
        if (!$admin->can('assign-role')) {
            return response()->json(['message' => 'You are not allowed to assign role.'], 401);
        }
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json(['message' => 'No user found.'], 404);

        }

        $role_list = [];
        foreach ($request->role_names as $r) {
            $role = Role::where('name', $r)->first();
            if ($role) {
                array_push($role_list,$r);
            }
        }


        try {
            $user->syncRoles($role_list);
            return response()->json(['message' => 'Role assigned to user','user'=>$user,'roles'=>$role_list], 200);

        }catch (\Exception $exception){
            return response()->json(['message' => 'Something went wrong.'], 500);

        }


    }

    public function assignPermission(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
            'permission_id' => 'required'
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

    public function getRoleList(Request $request)
    {
        $admin = Auth::user();
        if (!$admin->can('role-list')) {
            return response()->json(['roles' => []], 200);
        }
        $roles = Role::all();
        return response()->json(['roles' => $roles], 200);
    }
    public function getPermissionList(Request $request)
    {
        $admin = Auth::user();
        if (!$admin->can('permission-list')) {
            return response()->json(['permissions' => []], 200);
        }
        $roles = Permission::all();
        return response()->json(['permissions' => $roles], 200);
    }
    public function editRole(Request $request)
    {
        $request->validate([
            'permissions'=>'required|array',
            'name'=>'string',
            'id'=>'required'
        ]);
        $user = Auth::user();
        if (!$user->can('role-edit')) {
            return response()->json(['message' => 'You are not allowed to create role'], 401);

        }
        $role = Role::where('id',$request->id)->first();
        if(!$role){
            return response()->json(['message' => 'Role not found.'], 404);

        }

        if($role->name=='admin'){
            return response()->json(['message' => 'You are not allowed to create role'], 401);

        }
        if($request->has('name')){
            $role = Role::where('name',$request->name)->first();
            if(!$role){
                $role=Role::create(['name'=>$request->name]);
            }
            $role->name = $request->name;
            $role->save();
        }
        $permission_list = [];
        foreach ($request->permissions as $permission){
            $p = Permission::where('name',$permission)->first();
            if ($p){
                array_push($permission_list,$p->id);
            }
        }
        $role->syncPermissions($permission_list);
        return response()->json(['message' => 'Role updated', 'role'=>$role,'permissions'=>$permission_list], 201);

    }

    public function rolePermissions(Request $request){
        $admin = Auth::user();
        if (!$admin->can('permission-list')) {
            return response()->json(['permissions' => []], 200);
        }
        $role = Role::where('id',$request->id)->first();
        if(!$role){
            return response()->json(['message' => 'Role not found.'], 404);

        }
        $permissions = $role->permissions;
        return response()->json(['permissions' => $permissions], 200);

    }
}
